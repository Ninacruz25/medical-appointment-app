<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\InsuranceController;
use App\Livewire\Admin\ConsultationManager;
use App\Livewire\Admin\AppointmentCreate;

// Candado directo y explcícito en este archivo
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', function(){
        return view('admin.dashboard');
    })->name('dashboard');

    // GESTIÓN DE ROLES
    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);

    Route::resource('patients', PatientController::class);

    Route::resource('insurances', InsuranceController::class);

    Route::resource('doctors', DoctorController::class);
    Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('doctors.schedules');
    Route::post('doctors/{doctor}/schedules', [DoctorController::class, 'saveSchedules'])->name('doctors.schedules.save');

    Route::get('appointments/create', AppointmentCreate::class)->name('appointments.create');
    Route::resource('appointments', AppointmentController::class)->except(['create']);
    Route::get('appointments/{appointment}/consultation', ConsultationManager::class)->name('appointments.consultation');
});