<?php 

namespace App\Services\Api\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name'  => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            $user->assignRole('customer');

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return [
                "users" => $user,
                "token" => $token
            ];
        }catch(Exception $e){
            DB::rollBack();
            Log::error('Register Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function login(array $data): array
    {
        // Tidak butuh Transaction di sini kecuali kamu melakukan insert ke banyak tabel terkait
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            // Biarkan Controller yang menangkap atau biarkan Laravel handle otomatis
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah']
            ]);
        }

        // Hapus token lama & buat baru
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'users' => $user,
            'token' => $token,
        ];
    }
}