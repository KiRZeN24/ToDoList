<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Todotask;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;

class ToDoTaskTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        // Crear un usuario manualmente con ID 1
        $user = User::create([
            'id' => 1,
            'name' => 'Nombre de Usuario',
            'email' => 'usuario@example.com',
            'password' => Hash::make('contrasena'),
        ]);

        // Verificar que el usuario existe
        $this->assertNotNull($user, 'El usuario con ID 1 no existe en la base de datos.');

        // Autenticar al usuario
        $this->actingAs($user, 'sanctum');

        // Simular una solicitud HTTP para obtener las tareas con el parámetro user_id
        $response = $this->get('/api/v1/todotask', ['user_id' => $user->id]);

        // Verificar que la respuesta sea exitosa y contenga la información esperada
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function testStore()
    {
        // Crear un usuario
        $user = User::create([
            'name' => 'Nombre de Usuario',
            'email' => 'usuario@example.com',
            'password' => Hash::make('contrasena'),
        ]);

        // Datos para la nueva tarea
        $taskData = [
            'user_id' => $user->id,
            'texto' => 'Nueva tarea',
            'status' => 0,
        ];

        // Hacer la solicitud POST al endpoint de store
        $response = $this->postJson('/api/todotasks', $taskData);

        // Verificar que la respuesta tiene el código 201
        $response->assertStatus(201);

        // Verificar que la tarea fue creada en la base de datos
        $this->assertDatabaseHas('todotasks', $taskData);
    }

    public function testUpdate()
    {
        // Crear una tarea
        $task = Todotask::create([
            'user_id' => 1,
            'texto' => 'Tarea existente',
            'status' => 0,
        ]);

        // Datos para actualizar la tarea
        $updatedData = ['texto' => 'Texto actualizado', 'status' => 1];

        // Hacer la solicitud PUT al endpoint de update
        $response = $this->putJson("/api/todotasks/{$task->id}", $updatedData);

        // Verificar que la respuesta tiene el código 200
        $response->assertStatus(200);

        // Verificar que la tarea fue actualizada en la base de datos
        $this->assertDatabaseHas('todotasks', array_merge(['id' => $task->id], $updatedData));
    }

    public function testDestroy()
    {
        // Crear una tarea
        $task = Todotask::create([
            'user_id' => 1,
            'texto' => 'Tarea existente',
            'status' => 0,
        ]);

        // Hacer la solicitud DELETE al endpoint de destroy
        $response = $this->deleteJson("/api/todotasks/{$task->id}");

        // Verificar que la respuesta tiene el código 200
        $response->assertStatus(200);

        // Verificar que la tarea fue eliminada de la base de datos
        $this->assertDatabaseMissing('todotasks', ['id' => $task->id]);
    }
}
