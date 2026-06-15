<?php

namespace App\Http\Controllers\Admin;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
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

    public function destroy(Doctor $doctor)
    {
        //
    }

    public function schedules(Doctor $doctor)
    {
        // Convierte los registros de BD al formato de key que usa Alpine: "0800_lun"
        $existingSlots = $doctor->schedules()
            ->get()
            ->mapWithKeys(function ($schedule) {
                $timeKey = str_replace(':', '', substr($schedule->start_time, 0, 5));
                return [$timeKey . '_' . $schedule->day => true];
            })
            ->toArray();

        return view('admin.doctors.schedules', compact('doctor', 'existingSlots'));
    }

    public function saveSchedules(Request $request, Doctor $doctor)
    {
        $slots = json_decode($request->input('slots_json', '{}'), true) ?? [];

        $doctor->schedules()->delete();

        foreach ($slots as $slotKey => $checked) {
            if (!$checked) {
                continue;
            }

            // slotKey: "0800_lun" → time "08:00:00", day "lun"
            $parts = explode('_', $slotKey, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$timeKey, $day] = $parts;
            $startTime = sprintf('%s:%s:00', substr($timeKey, 0, 2), substr($timeKey, 2, 2));

            $doctor->schedules()->create([
                'day'        => $day,
                'start_time' => $startTime,
            ]);
        }

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Horario guardado',
            'text'  => 'El horario del doctor ha sido actualizado correctamente.',
        ]);

        return redirect()->route('admin.doctors.schedules', $doctor);
    }
}
