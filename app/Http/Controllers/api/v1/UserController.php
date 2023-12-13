<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\Hash;

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
