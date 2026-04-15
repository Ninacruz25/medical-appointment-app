<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Usamos el trait para refrescar la base de datos en cada test

uses(RefreshDatabase::class);

test('un usuario no puede eliminarse a sí mismo', function () {
    // 1 creamos un usuario de prueba
    $user = User::factory()->create([
        // JestStream exige este campo para funcionar
        'email_verified_at' => now(),
    ]);

    // 2 Simulamos que el usuario está autenticado
    $this->actingAs($user);

    // 3 Intentamos eliminar al usuario autenticado
    $response = $this->delete(route('admin.users.destroy', $user));
    
    // 4 Esperamos a que el servidor bloquee esta accion por seguridad
    $response->assertStatus(403);

    // 5 Verificamos que el usuario sigue existiendo en la base de datos 
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
