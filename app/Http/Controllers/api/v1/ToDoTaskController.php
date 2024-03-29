<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\todotask;
use Illuminate\Http\Request;
use App\Http\Resources\V1\todotaskResource;
use App\Models\User;

class ToDoTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $peticion)
    {
        $peticion->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $userID = $peticion->input('user_id');
        $usario = User::findOrFail($userID);
        
        if(!$usario) {
            return response()->json(['error' => 'El usuario no exite'], 404);
        }

        $tareas = Todotask::where('user_id', $userID)->latest()->paginate();
        return todotaskResource::collection($tareas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'texto' => 'required|string|min:1',
            'user_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (!User::find($value)) {
                    $fail("El usuario con el ID $value no existe.");
                }
            }],
        ]);
        $validarDatos['status'] = 0;
        $nuevaTarea = Todotask::create($validarDatos);
        return new todotaskResource($nuevaTarea);
         
    }

    /**
     * Display the specified resource.
     */
    public function show(todotask $todotask)
    {

        if (!$todotask) {
            return response()->json(['error' => 'La tarea no existe'], 404);
        }

        return new todotaskResource($todotask);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, todotask $todotask)
    {
        $request->validate([
            'texto' => 'required|string|min:1',
            'status' => 'integer',
        ]);

        if ($request->filled('user_id')) {
            $usuarios = User::find($request->input('user_id'));
            if (!$usuarios) {
                return response()->json(['error' => 'El usuario no existe'], 404);
            }
        }

        $todotask->update([
            'texto' => $request->input('texto'),
            'status' => $request->input('status', $todotask->status),
        ]);

        return new todotaskResource($todotask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(todotask $todotask)
    {
        $usuarios = User::find($todotask->user_id);

        if (!$usuarios) {
            return response()->json(['error' => 'El usuario de la tarea no existe'], 404);
        }

        $todotask->delete();

        return response()->json(['message' => 'Tarea eliminada exitosamente']);
    }
}
