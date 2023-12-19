<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $password = Hash::make($request->input('password'));

        $newUser = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $password,
        ]);

        return new UserResource($newUser);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'string|min:1',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'string|min:6',
        ]);

        $userData = [];

        if ($request->filled('name')) {
            $userData['name'] = $request->input('name');
        }

        if ($request->filled('email')) {
            $userData['email'] = $request->input('email');
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->input('password'));
        }

        if (empty($userData)) {
            return response()->json(['message' => 'Ningún dato proporcionado para actualizar'], 422);
        }

        // Realizar la actualización directamente en la instancia del modelo y obtenerla después
        $user->fill($userData)->save();
        $updatedUser = $user->fresh();

        // Verificar si el usuario se encontró antes de devolver el recurso
        if ($updatedUser) {
            return new UserResource($updatedUser);
        } else {
            // Manejar el caso en que no se pueda encontrar el usuario actualizado
            return response()->json(['message' => 'Usuario no encontrado después de la actualización'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $targetUser)
    {
        // Verifica si hay un usuario autenticado
        if (auth()->check()) {
            // Accede a la propiedad 'id' solo si hay un usuario autenticado
            if (auth()->user()->id === $targetUser->id || auth()->user()->id === 11) {

                // Eliminar las tareas asociadas al usuario
                $targetUser->todotask()->delete();

                // Eliminar al propio usuario
                $targetUser->delete();

                return response()->json(['message' => 'Usuario y tareas asociadas eliminadas correctamente']);
            } else {
                return response()->json(['error' => 'No tienes permisos para eliminar este usuario'], 403);
            }
        } else {
            return response()->json(['error' => 'No hay usuario autenticado'], 401);
        }
    }
}
