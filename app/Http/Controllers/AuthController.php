<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;




class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validator->errors()], 401);
            }
            $token = auth()->attempt($validator->validated());
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'invalid email or password'], 400);
            }
            return $this->createToken($token);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'api exception', 'exception' => $th->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|between:2,100|required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
            }
            $user = User::create(array_merge($validator->validated()));
            return response()->json(['status' => true, 'user' => $user, 'message' => 'user created successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'api exception', 'exception' => $th->getMessage()], 500);
        }
    }
    public function logout()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        Cache::put('blacklist:' . $token, true, config('jwt.ttl'));

        return response()->json(['status' => true, 'message' => 'log out successful'], 200);
    }
    public function refresh(Request $request)
    {
        return $this->createToken(auth()->refresh());
    }
    public function user()
    {
        return response()->json((auth()->user()), 200);
    }
    private function createToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
