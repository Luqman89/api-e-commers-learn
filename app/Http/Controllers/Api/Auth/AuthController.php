<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Api\Auth\AuthResource;
use App\Services\Api\Auth\AuthService as AuthAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthAuthService $service)
    {
       try {
            $result = $service->register($request->validated());
            
            return ApiResponse::success('Success Registered', [
                "users" => new AuthResource($result['users']),
                "token" => $result['token'],
            ]);
        } catch (Exception $e) {
            return ApiResponse::error('Registration failed. Please try again.', 500);
        }
    }

    public function login(LoginRequest $request, AuthAuthService $service)
    {
       try {
            $result = $service->login($request->validated());
            return ApiResponse::success('Success Login', [
                "users" => new AuthResource($result['users']),
                "token" => $result['token']
            ]);
        } catch (ValidationException $e) {
            // Kembalikan error validasi asli (422)
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong on our end.', 500);
        }
    }

    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success('Berhasil logout dan token telah dihapus');
    }
}
