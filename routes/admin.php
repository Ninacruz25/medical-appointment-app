<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

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
});