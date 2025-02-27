<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ResponseTrait;
use Exception;

class UsersController extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->model = model('UserModel');
    }

    /**
     * Atualização de usuário.
     *
     * @api {put} /api/users/1 Atualizar Usuário
     * @apiName UpdateUser
     * @apiGroup Users
     *
     * @apiBody {String} name Nome de usuário.
     * @apiBody {String} role Função do usuário.
     * @apiBody {String} document Documento do usuário.
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": {
     *             "username": "testador",
     *             "password": "senhaSegura123"
     *         },
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Usuário atualizado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "id": 30,
     *                 "name": "Fulano",
     *                 "username": "testador",
     *                 "password": "$2y$10$9PYU9/5GbP6ouRzh4LybxeUk/kOGJ1DSsULdM.Wx3ILKzbXKFf6JC",
     *                 "role": "common",
     *                 "document": "156.196.857-71"
     *             }
     *         }
     *     }
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            if($this->request->uid != $id) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');

            if (isset($data['username'])) unset($data['username']);
            if (isset($data['password'])) unset($data['password']);
            
            if ($this->model->update($id, $data)) return $this->formatResponse($data, 200, 'Usuário atualizado com sucesso', ['data' => $this->model->find($id)]);
            return $this->formatResponse($this->request->getGet(), 400, 'Falha ao atualizar usuário', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getJSON(true),
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Exclusão de usuário.
     *
     * @api {del} /api/users/1 Remover Usuário
     * @apiName DeleteUser
     * @apiGroup Users
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": [],
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Usuário removido com sucesso"
     *         },
     *         "retorno": {
     *             "data": null
     *         }
     *     }
     */
    public function delete($id = null)
    {
        try {
            if($this->request->uid != $id) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');
            if ($this->model->delete($id)) return $this->formatResponse($this->request->getGet(), 200, 'Usuário removido com sucesso', ['data' => $this->model->find($id)]);
            return $this->formatResponse($this->request->getGet(), 400, 'Falha ao remover usuário', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }   
}