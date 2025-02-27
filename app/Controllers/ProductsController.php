<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Traits\ResponseTrait;
use Exception;

class ProductsController extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->model = model('ProductModel');
    }

     // Listar produtos (GET api/products)
    public function index()
    {
        try {
            $perPage = $this->request->getGet('perPage') ?? 10;
            $page = $this->request->getGet('page') ?? 1;
            $data = $this->model->paginate($perPage, 'default', $page);

            return $this->formatResponse($this->request->getGet(), 200, 'Lista de produtos retornada com sucesso', ['data' => $data, 'pager' => $this->model->pager->getDetails()]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Mostrar um produto específico (GET api/products/{id})
    public function show($id = null)
    {
        try {
            $data = $this->model->find($id);

            if (!$data) $this->formatResponse($this->request->getGet(), 404, 'Produto não encontrado', ['data' => $data]);
            return $this->formatResponse($this->request->getGet(), 200, 'Produto encontrado com sucesso', $data);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Criar um novo produto (POST api/products)
    public function create()
    {
        try {
            $data = $this->request->getJSON();
            $productId = $this->model->insert($data);

            if ($productId) return $this->formatResponse($this->request->getGet(), 201, 'Produto criado com sucesso', ['data' => $this->model->find($productId)]);
            return $this->formatResponse($this->request->getGet(), 400, 'Falha ao criar produto', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Atualizar um produto existente (PUT api/products/{id})
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON();

            if ($this->model->update($id, $data)) return $this->formatResponse($this->request->getGet(), 200, 'Produto atualizado com sucesso', ['data' => $this->model->find($id)]);
            return $this->formatResponse($this->request->getGet(), 400, 'Falha ao atualizar produto', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Excluir um produto (DELETE api/products/{id})
    public function delete($id = null)
    {
        try {
            if ($this->model->delete($id)) return $this->formatResponse($this->request->getGet(), 200, 'Produto removido com sucesso', ['data' => $this->model->find($id)]);
            return $this->formatResponse($this->request->getGet(), 500, 'Falha ao remover produto', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }
}