<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('admin.appointments.index');
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors  = Doctor::with('user')->get();
        return view('admin.appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id'  => 'required|exists:doctors,id',
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'reason'     => 'required|string|min:3|max:1000',
        ]);

        Appointment::create($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita registrada',
            'text'  => 'La cita ha sido registrada correctamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function show(Appointment $appointment)
    {
        //
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->orderBy('id')->get();
        $doctors  = Doctor::with(['user', 'specialty'])->orderBy('id')->get();

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id'  => 'required|exists:doctors,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'reason'     => 'required|string|min:3|max:1000',
            'status'     => 'required|integer|in:1,2,3',
        ]);

        $appointment->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita actualizada',
            'text'  => 'Los datos de la cita han sido actualizados.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita eliminada',
            'text'  => 'La cita ha sido eliminada correctamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }
}
