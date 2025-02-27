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

    /**
     * Registra um novo usuário no sistema.
     *
     * @api {post} /api/auth/register Registrar Usuário
     * @apiName RegisterUser
     * @apiGroup Autenticação
     *
     * @apiBody {String} name Nome do usuário.
     * @apiBody {String} username Nome de usuário (deve ser único).
     * @apiBody {String} password Senha do usuário.
     * @apiBody {String} role Função do usuário no sistema (deve ser "admin" ou "common").
     * @apiBody {String} document Documento do usuário (opcional).
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *         "parametros": {
     *             "name": "Fulano",
     *             "username": "testador",
     *             "password": "senhaSegura123",
     *             "role": "common",
     *             "document": "156.196.857-71"
     *         },
     *         "cabecalho": {
     *             "status": 201,
     *             "message": "Usuário criado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
     *                 "user": {
     *                     "id": 30,
     *                     "name": "Fulano",
     *                     "username": "testador",
     *                     "password": "$2y$10$9PYU9/5GbP6ouRzh4LybxeUk/kOGJ1DSsULdM.Wx3ILKzbXKFf6JC",
     *                     "role": "common",
     *                     "document": "156.196.857-71"
     *                 }
     *             }
     *         }
     *     }
     */
    public function register()
    {
        try {
            $data = $this->request->getJSON(true);
            $userId = $this->model->insert($data);

            if(!$userId) return $this->formatResponse($this->request->getGet(), 400, 'Falha ao criar usuário', $this->model->errors());

            $user = $this->model->find($userId);
            $token = $this->generateToken($user);

            return $this->formatResponse(
                $data,
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

    /**
     * Login de usuário no sistema.
     *
     * @api {post} /api/auth/login Logar Usuário
     * @apiName LoginUser
     * @apiGroup Autenticação
     *
     * @apiBody {String} username Nome de usuário.
     * @apiBody {String} password Senha do usuário.
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *         "parametros": {
     *             "username": "testador",
     *             "password": "senhaSegura123"
     *         },
     *         "cabecalho": {
     *             "status": 201,
     *             "message": "Usuário criado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
     *                 "user": {
     *                     "id": 30,
     *                     "name": "Fulano",
     *                     "username": "testador",
     *                     "password": "$2y$10$9PYU9/5GbP6ouRzh4LybxeUk/kOGJ1DSsULdM.Wx3ILKzbXKFf6JC",
     *                     "role": "common",
     *                     "document": "156.196.857-71"
     *                 }
     *             }
     *         }
     *     }
     */
    public function login()
    {
        try {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $user = $this->model->where('username', $username)->first();

            if (!$user || !password_verify($password, $user['password'])) return $this->formatResponse($this->request->getGet(), 401, 'Credenciais inválidas');

            $token = $this->generateToken($user);

            return $this->formatResponse(
                $this->request->getJSON(true),
                200,
                'Login realizado com sucesso',
                ['data' => ['token' => $token, 'user' => $user]]
            );
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getJSON(true),
                500,
                $e->getMessage()
            );
        }
        
    }
}
