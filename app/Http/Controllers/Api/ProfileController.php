<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends ApiController
{
    private function payload($user): array
    {
        return ['user_id' => $user->id, 'username' => $user->username, 'name' => $user->name, 'email' => $user->email];
    }

    // GET /api/profile
    public function show(Request $request): JsonResponse
    {
        return $this->ok('Profil ditemukan', $this->payload($request->user()));
    }

    // PUT /api/profile  (ubah profil dan/atau password)
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'username' => ['sometimes', 'required', 'string', 'min:3', 'max:30', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'max:100'],
            'current_password' => ['required_with:password'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                return $this->fail('Password saat ini salah', 422);
            }
        } else {
            unset($data['password']);
        }
        unset($data['current_password']);

        $user->update($data);

        return $this->ok('Profil berhasil diperbarui', $this->payload($user->fresh()));
    }
}
