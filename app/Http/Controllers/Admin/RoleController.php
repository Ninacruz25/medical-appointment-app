<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que se cree bien
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        // si pasa la validacion, creará el rol
        Role::create([
            'name' => $request->name
        ]);

        // alerta de funcionamiento correcto
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol se ha creado correctamente',
        ]);

        // redireccionar a la vista de roles
        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Validar que se inserte bien y que excluya la fila que se edita
        $request->validate([
            'name' => 'required|unique:roles,name,',
        ]);

        // si pasa la validacion, actualizará el rol
        $role->update([
            'name' => $request->name
        ]);

        // Confirmacion de operacion correcta
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado correctamente',
            'text' => 'El rol se ha actualizado correctamente',
        ]);

        // redireccionar a la vista de roles
        return redirect()->route('admin.roles.edit', $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Borrar el rol
        $role->delete();

        // Confirmacion de operacion correcta
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol se ha eliminado correctamente',
        ]);

        // redireccionar a la vista de roles
        return redirect()->route('admin.roles.index');
    }
}
