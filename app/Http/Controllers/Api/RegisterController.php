<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegisterRequest;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));

        return response()->json([
            'token' => $user->createToken($request->input('device_name'))->plainTextToken,
        ], 201);
    }
}
