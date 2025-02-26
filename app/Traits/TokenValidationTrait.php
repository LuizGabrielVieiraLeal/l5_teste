<?php
namespace App\Traits;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

trait TokenValidationTrait
{
    protected function validateToken($token)
    {
        try {
            $key = getenv('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}