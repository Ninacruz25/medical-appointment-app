<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentCreate extends Component
{
    // Búsqueda
    public $searchDate        = '';
    public $searchHour        = '';
    public $searchSpecialtyId = '';
    public $searched          = false;
    public $doctorsWithSlots  = [];

    // Slot seleccionado
    public $selectedDoctorId   = null;
    public $selectedDoctorName = '';
    public $selectedDate       = '';
    public $selectedStartTime  = '';
    public $selectedEndTime    = '';

    // Datos de la cita
    public $patientId = '';
    public $reason    = '';

    public function searchAvailability(): void
    {
        $this->validate([
            'searchDate' => 'required|date|after_or_equal:today',
            'searchHour' => 'required|string',
        ], [
            'searchDate.required'        => 'La fecha es obligatoria.',
            'searchDate.after_or_equal'  => 'La fecha no puede ser anterior a hoy.',
            'searchHour.required'        => 'La hora es obligatoria.',
        ]);

        $hour  = (int) ($this->searchHour ?? 0);
        $slots = [
            sprintf('%02d:00', $hour),
            sprintf('%02d:15', $hour),
            sprintf('%02d:30', $hour),
            sprintf('%02d:45', $hour),
        ];

        // Día de la semana de la fecha buscada, en el formato que usa doctor_schedules
        $dayMap = [0 => 'dom', 1 => 'lun', 2 => 'mar', 3 => 'mie', 4 => 'jue', 5 => 'vie', 6 => 'sab'];
        $dayKey = $dayMap[Carbon::parse($this->searchDate)->dayOfWeek];

        $query = Doctor::with(['user', 'specialty']);
        if (!empty($this->searchSpecialtyId)) {
            $query->where('specialty_id', (int) $this->searchSpecialtyId);
        }
        $doctors = $query->get();
        $doctorIds = $doctors->pluck('id');

        // Slots configurados por doctor para ese día de la semana
        $scheduledByDoctor = DoctorSchedule::whereIn('doctor_id', $doctorIds)
            ->where('day', $dayKey)
            ->get()
            ->groupBy('doctor_id')
            ->map(fn($rows) => $rows->pluck('start_time')
                ->map(fn($t) => substr($t, 0, 5))
                ->toArray()
            );

        // Citas ya existentes en esa fecha (slots ocupados)
        $takenByDoctor = Appointment::where('date', $this->searchDate)
            ->whereIn('doctor_id', $doctorIds)
            ->get()
            ->groupBy('doctor_id')
            ->map(fn($appts) => $appts->pluck('start_time')
                ->map(fn($t) => substr($t, 0, 5))
                ->toArray()
            );

        $this->doctorsWithSlots = $doctors
            ->map(function ($doctor) use ($slots, $scheduledByDoctor, $takenByDoctor) {
                $scheduled = $scheduledByDoctor->get($doctor->id, []);
                $taken     = $takenByDoctor->get($doctor->id, []);

                // Solo muestra slots que el doctor tiene en su horario Y que no están ocupados
                $available = array_values(array_filter(
                    $slots,
                    fn($s) => in_array($s, $scheduled) && !in_array($s, $taken)
                ));

                return [
                    'id'        => $doctor->id,
                    'name'      => $doctor->user->name,
                    'specialty' => $doctor->specialty?->name ?? '—',
                    'initials'  => $this->initials($doctor->user->name),
                    'slots'     => $available,
                ];
            })
            ->filter(fn($d) => count($d['slots']) > 0)
            ->values()
            ->toArray();

        $this->searched = true;

        // Limpiar selección previa al buscar de nuevo
        $this->selectedDoctorId   = null;
        $this->selectedDoctorName = '';
        $this->selectedStartTime  = '';
        $this->selectedEndTime    = '';
    }

    public function selectSlot(int $doctorId, string $doctorName, string $startTime): void
    {
        $this->selectedDoctorId   = $doctorId;
        $this->selectedDoctorName = $doctorName;
        $this->selectedDate       = $this->searchDate;
        $this->selectedStartTime  = $startTime;
        $this->selectedEndTime    = Carbon::createFromFormat('H:i', $startTime)->addMinutes(15)->format('H:i');
    }

    public function store(): void
    {
        $this->validate([
            'selectedDoctorId'  => 'required|integer',
            'selectedStartTime' => 'required|string',
            'selectedDate'      => 'required|date',
            'patientId'         => 'required|exists:patients,id',
            'reason'            => 'required|string|min:3|max:1000',
        ], [
            'selectedDoctorId.required'  => 'Debes seleccionar un horario disponible primero.',
            'selectedStartTime.required' => 'Debes seleccionar un horario disponible primero.',
            'selectedDate.required'      => 'Debes seleccionar una fecha.',
            'patientId.required'         => 'Debes seleccionar un paciente.',
            'reason.required'            => 'El motivo de la consulta es obligatorio.',
            'reason.min'                 => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        Appointment::create([
            'patient_id' => $this->patientId,
            'doctor_id'  => $this->selectedDoctorId,
            'date'       => $this->selectedDate,
            'start_time' => $this->selectedStartTime,
            'end_time'   => $this->selectedEndTime,
            'duration'   => 15,
            'reason'     => $this->reason,
            'status'     => 1,
        ]);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita registrada',
            'text'  => 'La cita ha sido registrada correctamente.',
        ]);

        $this->redirect(route('admin.appointments.index'));
    }

    private function initials(string $name): string
    {
        $words = array_filter(explode(' ', trim($name)));
        $words = array_values($words);
        return strtoupper(
            substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1)
        );
    }

    public function render()
    {
        return view('livewire.admin.appointment-create', [
            'patients'    => Patient::with('user')->orderBy('id')->get(),
            'specialties' => Specialty::orderBy('name')->get(),
        ])->layout('layouts.admin', [
            'title'       => 'Nueva cita',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['name' => 'Citas',     'href' => route('admin.appointments.index')],
                ['name' => 'Nuevo'],
            ],
        ]);
    }
}
