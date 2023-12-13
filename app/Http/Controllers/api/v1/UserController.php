<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;

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
        //
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
            'email' => 'email|unique:users, email, ' . $user->id,
            'password' => 'string|min:6', 
        ]);

        if (!$user) {
            return response()->json(['error' => 'El usuario no existe']);
        }

        $userData = [];

        if ($request->filled('name')) {
            $userData['name'] = $request->input('name');
        }

        if ($request->filled('email')) {
            $userData['email'] = $request->input('email');
        }

        if ($request->filled('password')) {
            $userData['password'] = $request->input('password');
        }

        $user->update($userData);
        
        return new UserResource($user);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
