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

    // Atualizar um usuário existente (PUT api/users/{id})
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            if($this->request->uid != $id) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');
            if (isset($data['password'])) unset($data['password']);
            if ($this->model->update($id, $data)) return $this->formatResponse($this->request->getGet(), 200, 'Usuário atualizado com sucesso', ['data' => $this->model->find($id)]);
            return $this->formatResponse($this->request->getGet(), 400, 'Falha ao atualizar usuário', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Excluir um usuário (DELETE api/users/{id})
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