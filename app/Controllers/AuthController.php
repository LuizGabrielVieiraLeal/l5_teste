<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Traits\ResponseTrait;
use Firebase\JWT\JWT;
use Exception;
use OpenApi\Annotations as OA;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected function generateToken($user)
    {
        $key = getenv('JWT_SECRET_KEY');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'uid' => $user['id'],
            'role' => $user['role']
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function __construct()
    {
        $this->model = model('UserModel');
    }

    // Login de usuário (POST api/users/login)
    public function login()
    {
        try {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $user = $this->model->where('username', $username)->first();

            if (!$user || !password_verify($password, $user['password'])) return $this->formatResponse($this->request->getGet(), 401, 'Credenciais inválidas');

            $token = $this->generateToken($user);

            return $this->formatResponse(
                $this->request->getGet(),
                200,
                'Login realizado com sucesso',
                ['data' => ['token' => $token, 'user' => $user]]
            );
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
        
    }

    public function register()
    {
        try {
            $data = $this->request->getJSON(true);
            $userId = $this->model->insert($data);

            if(!$userId) return $this->formatResponse($this->request->getGet(), 400, 'Falha ao criar usuário', $this->model->errors());

            $user = $this->model->find($userId);
            $token = $this->generateToken($user);

            return $this->formatResponse(
                $this->request->getGet(),
                201,
                'Usuário criado com sucesso',
                ['data' => ['token' => $token, 'user' => $user]]
            );
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }
}
