<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SignInRequest;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SignInController
{
    public function __invoke(SignInRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->input('device_name'))->plainTextToken,
        ]);
    }
}
