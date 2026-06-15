<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Consultation;

class ConsultationManager extends Component
{
    public Appointment $appointment;
    public string $activeTab = 'consulta';
    public string $diagnosis = '';
    public string $treatment = '';
    public string $notes = '';
    public array $medicines = [];
    public bool $showPreviousConsultations = false;
    public bool $showMedicalHistory = false;

    public function mount(Appointment $appointment): void
    {
        $this->appointment = $appointment->load([
            'patient.user',
            'patient.bloodType',
            'doctor.user',
            'consultation.prescriptionItems',
        ]);

        if ($existing = $this->appointment->consultation) {
            $this->diagnosis = $existing->diagnosis;
            $this->treatment = $existing->treatment;
            $this->notes     = $existing->notes ?? '';
            $this->medicines = $existing->prescriptionItems->map(fn($item) => [
                'medication' => $item->medication,
                'dose'       => $item->dose,
                'frequency'  => $item->frequency,
            ])->toArray();
        }
    }

    public function addMedicine(): void
    {
        $this->medicines[] = ['medication' => '', 'dose' => '', 'frequency' => ''];
    }

    public function removeMedicine(int $index): void
    {
        array_splice($this->medicines, $index, 1);
        $this->medicines = array_values($this->medicines);
    }

    public function saveConsultation(): void
    {
        $this->validate([
            'diagnosis'              => 'required|string|min:3',
            'treatment'              => 'required|string|min:3',
            'notes'                  => 'nullable|string',
            'medicines.*.medication' => 'required|string',
            'medicines.*.dose'       => 'required|string',
            'medicines.*.frequency'  => 'required|string',
        ], [
            'diagnosis.required'              => 'El diagnóstico es obligatorio.',
            'treatment.required'              => 'El tratamiento es obligatorio.',
            'medicines.*.medication.required' => 'El nombre del medicamento es obligatorio.',
            'medicines.*.dose.required'       => 'La dosis es obligatoria.',
            'medicines.*.frequency.required'  => 'La frecuencia es obligatoria.',
        ]);

        $consultation = Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'diagnosis' => $this->diagnosis,
                'treatment' => $this->treatment,
                'notes'     => $this->notes ?: null,
            ]
        );

        $consultation->prescriptionItems()->delete();

        foreach ($this->medicines as $medicine) {
            $consultation->prescriptionItems()->create($medicine);
        }

        $this->appointment->update(['status' => 2]);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Consulta guardada',
            'text'  => 'La consulta ha sido registrada correctamente.',
        ]);

        $this->redirect(route('admin.appointments.index'));
    }

    public function render()
    {
        $previousConsultations = $this->appointment->patient
            ->consultations()
            ->with(['appointment.doctor.user'])
            ->where('appointment_id', '!=', $this->appointment->id)
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.admin.consultation-manager', [
            'previousConsultations' => $previousConsultations,
        ])->layout('layouts.admin', [
            'title'       => 'Consulta médica',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['name' => 'Citas',     'href' => route('admin.appointments.index')],
                ['name' => 'Consulta'],
            ],
        ]);
    }
}
