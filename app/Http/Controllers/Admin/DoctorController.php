<?php

namespace App\Http\Controllers\Admin;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $specialties = Specialty::all();
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'specialty_id'           => 'required|exists:specialties,id',
            'medical_license_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9]+$/|unique:doctors,medical_license_number,' . $doctor->id,
            'biography'              => 'nullable|string|min:10|max:1000',
        ]);

        $doctor->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Doctor actualizado correctamente.',
            'text'  => 'El doctor ha sido actualizado correctamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }
}
