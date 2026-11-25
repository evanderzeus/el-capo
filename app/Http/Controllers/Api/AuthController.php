<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    // POST /api/register
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
        ]);

        User::create($data);

        return $this->ok('Registrasi berhasil', null, 201);
    }

    // POST /api/login  (field "email" boleh berisi email atau username)
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->input('email'))
            ->orWhere('username', $request->input('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->fail('Email/username atau password salah', 401);
        }

        $remember = $request->boolean('remember');
        if ($remember) {
            // Cookie "Ingat Saya" berlaku 7 hari
            Auth::guard()->setRememberDuration(60 * 24 * 7);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->ok('Login berhasil', [
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request): JsonResponse
    {
        Auth::logout(); // juga menghapus cookie remember
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->ok('Logout berhasil');
    }
}
