<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function(){
    return view('admin.dashboard');
})->name('dashboard');

// gESTIÓN DE ROLES

Route::resource('roles', RoleController::class);