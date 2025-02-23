<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Authcontroller extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Invalid credentials"
            ], 401);
        }

        $token = $user->createToken($request->email, ['Auth-Token'])->plainTextToken;

        return response()->json([
            "message" => "Login successfully",
            "token_type" => "Bearer",
            "token_value" => $token,
        ], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:255|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $status = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($status) {
                DB::commit();
                return response()->json([
                    "message" => "User registered successfully",
                ], 201);
            } else {
                DB::rollBack();
                Log::alert("Something went wrong while registering user");
                return response()->json([
                    "message" => 'Something went wrong!',
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::alert($e->getMessage());
            return response()->json([
                "message" => 'Internal server error',
            ], 500);
        }
    }

    public function profile(Request $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                "message" => "Profile fetched successfully",
                "data"  => $request->user(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();

        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                "message" => "Logout successfully",
            ],200);
        } else {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }
    }
}
