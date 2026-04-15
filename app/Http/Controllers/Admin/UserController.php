<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9]+$/|unique:users',
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'address' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);
        $roleId = $data['role_id'];
        unset($data['role_id']);
        
        $user = User::create($data);
        $user->roles()->attach($roleId);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado exitosamente',
            'text' => 'El nuevo usuario ha sido creado y asignado a su rol correspondiente.',
        ]);
        return redirect(route('admin.users.index'))->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9]+$/|unique:users,id_number,' . $user->id,
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'address' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        $roleId = $data['role_id'];
        unset($data['role_id']); 
        
        $user->update($data);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $user->roles()->sync($roleId);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado exitosamente',
            'text' => 'El usuario ha sido actualizado y asignado a su rol correspondiente.',
        ]);

        return redirect()->route('admin.users.edit', $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        //No permitir que el usuario se elimine a sí mismo
        if (Auth::id() === $user->id) {
            abort(403, 'You cannot delete your self.');
        }
        //Eliminar roles asociados al usuario
        $user->roles()->detach();
        $user->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario eliminado exitosamente',
            'text' => 'El usuario ha sido eliminado de forma permanente.',
        ]);
        return redirect()->route('admin.users.index');
    }
}
