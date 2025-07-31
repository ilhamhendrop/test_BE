<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
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
            'role' => 'required'
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
            'role.required' => 'Role tidak boleh kosong'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User berhasil dibuat'], 201);
    }

    public function all() {
        if (Auth::user()->role == RoleEnum::ADMIN->value) {
            $users = User::all();
        } else if (Auth::user()->role == RoleEnum::VERIFIKATOR->value){
            $users = User::where('role', 'user')->get();
        }

        return UserResource::collection($users);
    }

    public function updatePassword(Request $request, $id) {
        $request->validate([
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
            'password.required' => 'Password tidak boleh kosong',
            'password.confirmed' => 'Password tidak sama',
            'password.min' => 'Password minimal 8 karakter',
            'password.symbols' => 'Password harus mengandung simbol',
            'password.numbers' => 'Password harus mengandung angka',
            'password.mixedCase' => 'Password harus mengandung huruf besar, kecil, symbol',
            'password.uncompromised' => 'Password tidak boleh sudah digunakan',
        ]);

        $user = User::find($id);
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password berhasil diubah'], 200);
    }

    public function updateRole(Request $request, $id) {
        $request->validate([
            'role' => 'required',
        ], [
            'role.required' => 'Role tidak boleh kosong',
        ]);

        $user = User::find($id);
        $user->update([
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'Role berhasil diubah'], 200);
    }

    public function verifikasi($id) {
        $user = User::find($id);
        $user->update([
            'email_verified_at' => Carbon::now()
        ]);

        return response()->json(['message' => 'Email berhasil diverifikasi'], 200);
    }
}
