<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function login(Request $request) {
        $validate = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validate)) {
            return response()->json(['message' => 'email atau password salah'], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json(['acces_token' => $token, 'token_type' => 'Bearer']);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                ->symbols()
                ->numbers()
                ->mixedCase()
                ->uncompromised()
            ],
        ], [
            'name.required' => 'Name tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.confirmed' => 'Password tidak sama',
            'password.min' => 'Password minimal 8 karakter',
            'password.symbols' => 'Password harus mengandung simbol',
            'password.numbers' => 'Password harus mengandung angka',
            'password.mixedCase' => 'Password harus mengandung huruf besar, kecil, dan simbol',
            'password.uncompromised' => 'Password tidak boleh sudah digunakan',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => RoleEnum::USER,
        ]);

        return response()->json(['message' => 'Berhasil dibuat akun']);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil Logout']);
    }
}
