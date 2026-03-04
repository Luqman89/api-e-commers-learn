<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    'Validation error',
                    422,
                    $e->errors()
                );
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::error(
                    'Unauthenticated',
                    401
                );
            }

            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();

                $message = match ($statusCode) {
                    400 => 'Bad request',
                    401 => 'Unauthenticated',
                    403 => 'Akses ditolak',
                    404 => 'Endpoint tidak ditemukan',
                    405 => 'Method tidak diizinkan',
                    429 => 'Terlalu banyak request',
                    default => 'Terjadi kesalahan HTTP',
                };
                return ApiResponse::error($message, $statusCode);
            }

            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan server',
                500
            );
        }

        return parent::render($request, $e);
    }
}
