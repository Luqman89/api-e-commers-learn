<?php 

namespace App\Helpers;

class ApiResponse 
{
    public static function success($message, $data = [], $code = 200) {
        $response = [
            "status"    => (string) $code,
            "message"   => $message
        ];
        // Jika data memiliki pagination (meta/links), kita gabungkan agar tidak double key 'data'
        if(is_array($data) && (isset($data['links']) || isset($data['meta']))) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }


    public static function error($message, $code = 400, $errors = null) {
        return response()->json([
            'status'    => (string) $code,
            'message'   => $message,
            'errors'    => $errors,
        ], $code);
    }
}