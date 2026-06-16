<?php

namespace App\Http\Controllers\Admin;

use App\Models\Insurance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.insurances.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.insurances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|min:3|max:255',
            'code'   => 'nullable|string|max:50|unique:insurances,code',
            'phone'  => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'email'  => 'nullable|email|max:100',
            'status' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre del seguro es obligatorio.',
            'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
            'code.unique'   => 'El código de convenio ya está registrado.',
            'email.email'   => 'El formato del correo electrónico es inválido.',
        ]);

        // Si el checkbox de status no viene en el request, se considera true por defecto (o false si fue desmarcado, pero en form es un hidden)
        $data['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        Insurance::create($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Seguro registrado correctamente.',
            'text'  => 'El seguro ha sido creado con éxito.',
        ]);

        return redirect()->route('admin.insurances.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insurance $insurance)
    {
        return view('admin.insurances.edit', compact('insurance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insurance $insurance)
    {
        $data = $request->validate([
            'name'   => 'required|string|min:3|max:255',
            'code'   => 'nullable|string|max:50|unique:insurances,code,' . $insurance->id,
            'phone'  => 'nullable|string|max:20',
            'email'  => 'nullable|email|max:100',
            'status' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre del seguro es obligatorio.',
            'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
            'code.unique'   => 'El código de convenio ya está registrado.',
            'email.email'   => 'El formato del correo electrónico es inválido.',
        ]);

        $data['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $insurance->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Seguro actualizado correctamente.',
            'text'  => 'Los datos del seguro se han modificado con éxito.',
        ]);

        return redirect()->route('admin.insurances.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Seguro eliminado correctamente.',
            'text'  => 'El seguro ha sido removido del sistema.',
        ]);

        return redirect()->route('admin.insurances.index');
    }
}
