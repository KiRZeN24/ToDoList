<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\todotask;
use Illuminate\Http\Request;
use App\Http\Resources\V1\todotaskResource;

class ToDoTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return todotaskResource::collection(Todotask::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validarDatos = $request->validate([
            'texto' => 'required|string',
            'status' =>'required|boolean',
            'user_id' => 'required|integer'
        ]);
        $nuevaTarea = Todotask::create($validarDatos);
        return new todotaskResource($nuevaTarea);
         
    }

    /**
     * Display the specified resource.
     */
    public function show(todotask $todotask)
    {
        return new todotaskResource($todotask);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, todotask $todotask)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(todotask $todotask)
    {
        //
    }
}
