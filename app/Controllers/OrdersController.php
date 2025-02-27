<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ResponseTrait;
use App\Traits\PriceTrait;
use Exception;

class OrdersController extends ResourceController
{
    use ResponseTrait, PriceTrait;

    protected function createOrderProducts($orderId, $products)
    {
        $opModel = model('OrderProductModel');
        $opModel->where('order_id', $orderId)->delete();

        foreach ($products as $product) {
            $opModel->insert([
                'order_id'   => $orderId,
                'product_id' => $product->product_id,
                'quantity'   => $product->quantity
            ]);
        }
    }

    public function __construct()
    {
        $this->model = model('OrderModel');
    }

    // Listar pedidos (GET api/orders)
    public function index()
    {
        try {
            $userId = $this->request->uid;
            $userRole = $this->request->urole;
            $perPage = $this->request->getGet('perPage') ?? 10;
            $page = $this->request->getGet('page') ?? 1;

            $userRole === 'admin' ?
                $data = $this->model->paginate($perPage, 'default', $page) :
                $data = $this->model->where('user_id', $userId)->paginate($perPage, 'default', $page);

            return $this->formatResponse($this->request->getGet(), 200, 'Lista de pedidos retornada com sucesso', ['data' => $data, 'pager' => $this->model->pager->getDetails()]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

   // Mostrar um pedido específico (GET api/orders/{id})
    public function show($id = null)
    {
        try {
            $userId = $this->request->uid;
            $userRole = $this->request->urole;
            $data = $this->model->find($id);

            if (!$data) return $this->formatResponse($this->request->getGet(), 404, 'Pedido não encontrado', ['data' => $data]);
            if ($userRole != 'admin' && $userId != $data['user_id']) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');
           
            return $this->formatResponse($this->request->getGet(), 200, 'Pedido encontrado com sucesso', $data);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    // Criar um novo pedido (POST api/orders)
    public function create()
    {
        try {
            $data = $this->request->getJSON();
            $orderId = $this->model->insert($data);

            if (!$orderId) return $this->formatResponse($this->request->getGet(), 400, 'Falha ao criar produto', $this->model->errors());
            if (!empty($data->products)) $this->createOrderProducts($orderId, $data->products);

            $data->total_value = $this->calculate($orderId);

            return $this->formatResponse($this->request->getGet(), 201, 'Pedido criado com sucesso', ['data' => $this->model->find($orderId)]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }

    // Cancelar ou modificar pedido
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON();
            $order = $this->model->find($id);
            $userId = $this->request->uid;
            $userRole = $this->request->urole;

            if($userRole != 'admin' && $userId != $order->user->id) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');
            if (!empty($data->products)) $this->createOrderProducts($order['id'], $data->products);

            $data->total_value = $this->calculate($order['id']);

            if ($this->model->update($id, $data)) return $this->formatResponse($this->request->getGet(), 200, 'Pedido atualizado com sucesso', ['data' => $this->model->find($order['id'])]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }
}
