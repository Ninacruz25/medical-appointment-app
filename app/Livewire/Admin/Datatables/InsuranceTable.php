<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Insurance;

class InsuranceTable extends DataTableComponent
{
    protected $model = Insurance::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Código', 'code')
                ->sortable()
                ->searchable(),
            Column::make('Teléfono', 'phone')
                ->sortable(),
            Column::make('Correo electrónico', 'email')
                ->sortable(),
            Column::make('Estado', 'status')
                ->sortable()
                ->format(function($value) {
                    if ($value) {
                        return '<span class="px-2.5 py-0.5 inline-flex items-center text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Activo</span>';
                    }
                    return '<span class="px-2.5 py-0.5 inline-flex items-center text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-300">Inactivo</span>';
                })
                ->html(),
            Column::make('Acciones')
                ->label(function($row) {
                    return view('admin.insurances.actions', ['insurance' => $row]);
                })
        ];
    }
}
