<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Resources\UserResource;
use App\Models\Traits\HasKey;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginAuthRequest $request) {
        try {
            if (!Auth::attempt($request->validated())) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = User::where("email", $request->email)->first();
            $token = $user->createToken('api-token')->plainTextToken;

            if($user->key == null) {
                $user->key = 'use_' . Str::random(25);
                $user->saveQuietly();
            }

            return UserResource::make($user)->additional(["token" => $token]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function register(RegisterAuthRequest $request) {
        try {
            $user = User::create(array_merge(
                Arr::except($request->validated(), ["password"]),
                ['password' => Hash::make($request->password)]
            ));


            $token = $user->createToken('api-token')->plainTextToken;
            Auth::login($user);
            return UserResource::make($user)->additional(["token" => $token]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
