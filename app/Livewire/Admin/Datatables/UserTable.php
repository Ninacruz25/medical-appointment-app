<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserTable extends DataTableComponent
{
    // protected $model = User::class;

    // Este método define el modelo
    public function builder(): Builder

    {
        // devuelve la relacion con roles
        return User::query()
        ->with('roles');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nombre", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Número de id", "id_number")
                ->sortable(),
            Column::make("Teléfono", "phone")
                ->sortable(),
            Column::make("Rol", "roles")
                ->label(function($row){
                    return   $row->roles->first()->name ?? 'Sin rol';
                }),
            Column::make('Acciones')
                ->label(function($row){
                    return view('admin.users.actions', 
                    ['user' => $row]);
            })
        ];
    }
}
