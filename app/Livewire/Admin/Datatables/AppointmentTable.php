<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()
            ->selectRaw('appointments.*, pu.name as patient_name, du.name as doctor_name')
            ->join('patients', 'patients.id', '=', 'appointments.patient_id')
            ->join('users as pu', 'pu.id', '=', 'patients.user_id')
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->join('users as du', 'du.id', '=', 'doctors.user_id');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        $statusColors = [
            1 => 'bg-green-100 text-green-800',
            2 => 'bg-blue-100 text-blue-800',
            3 => 'bg-red-100 text-red-800',
        ];
        $statusLabels = [
            1 => 'Programado',
            2 => 'Completado',
            3 => 'Cancelado',
        ];

        return [
            Column::make('ID', 'id')
                ->sortable(),

            // Sin column key → Rappasoft no intenta SELECT appointments.patient_name
            Column::make('Paciente')
                ->label(fn($row) => $row->patient_name ?? '—'),

            Column::make('Doctor')
                ->label(fn($row) => $row->doctor_name ?? '—'),

            Column::make('Fecha', 'date')
                ->label(fn($row) => \Carbon\Carbon::parse($row->date)->format('d/m/Y'))
                ->sortable(),

            Column::make('Hora', 'start_time')
                ->label(fn($row) => substr($row->start_time, 0, 5))
                ->sortable(),

            Column::make('Estado', 'status')
                ->label(function ($row) use ($statusColors, $statusLabels) {
                    $s     = (int) $row->status;
                    $color = $statusColors[$s] ?? 'bg-gray-100 text-gray-800';
                    $label = $statusLabels[$s] ?? 'Desconocido';
                    return "<span class=\"px-2 py-1 rounded-full text-xs font-semibold {$color}\">{$label}</span>";
                })
                ->html()
                ->sortable(),

            Column::make('Acciones')
                ->label(fn($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
