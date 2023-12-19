<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testCrearUsuario()
    {
        // Desactivar temporalmente el middleware Authenticate para evitar la redirección al inicio de sesión
        $this->withoutMiddleware();

        // Datos simulados para el nuevo usuario
        $userData = [
            'name' => 'Daniel',
            'email' => 'daniel@daniel.es',
            'password' => 'contraseña123',
        ];

        // Realizar una solicitud HTTP al método store del controlador
        $response = $this->post('/api/v1/users', $userData);

        // Verificar que la solicitud se haya realizado correctamente (código de estado 201 para creación exitosa)
        $response->assertStatus(201);

        // Verificar que la respuesta contiene los datos del nuevo usuario
        $response->assertJson([
            'data' => [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ],
        ]);

        // Verificar que el usuario realmente se haya almacenado en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    public function testActualizarUsuario()
    {
        // Desactivar temporalmente el middleware Authenticate para evitar la redirección al inicio de sesión
        $this->withoutMiddleware();

        // Crear un usuario de ejemplo en la base de datos
        $user = User::create();

        // Datos simulados para la actualización del usuario
        $updatedUserData = [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo.email@example.com',
            'password' => 'nuevacontraseña',
        ];

        // Realizar una solicitud HTTP al método update del controlador
        $response = $this->put("/api/v1/users/{$user->id}", $updatedUserData);

        // Verificar que la solicitud se haya realizado correctamente (código de estado 200)
        $response->assertStatus(200);

        // Extraer los datos actualizados del usuario de la respuesta
        $responseData = $response->json('data');

        // Verificar que los datos actualizados coinciden con los esperados
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals($updatedUserData['name'], $responseData['name']);

        $this->assertArrayHasKey('email', $responseData);
        $this->assertEquals($updatedUserData['email'], $responseData['email']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedUserData['name'],
            'email' => $updatedUserData['email'],
            'password' => hash::make($updatedUserData['password']),
        ]);

        // Verificar que la contraseña se actualizó correctamente
        if (array_key_exists('password', $updatedUserData)) {
            $this->assertTrue(Hash::check($updatedUserData['password'], $user->fresh()->password));
        }
    }
}
