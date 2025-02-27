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

    /**
     * Retorna uma lista de pedidos com paginação.
     *
     * @api {get} /api/orders Listar Pedidos
     * @apiName ListOrders
     * @apiGroup Orders
     *
     * @apiQuery {Number} [page=1] Número da página a ser retornada.
     * @apiQuery {Number} [perPage=10] Número de itens por página.
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *         "parametros": [],
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Lista de pedidos retornada com sucesso"
     *         },
     *         "retorno": {
     *             "data": [
     *                 {
     *                     "id": "1",
     *                     "status": "cancelado",
     *                     "user_id": "25",
     *                     "total_value": "100.00",
     *                     "created_at": "2025-02-27 09:55:57",
     *                     "updated_at": "2025-02-27 17:33:12"
     *                 },
     *                 {
     *                     "id": "2",
     *                     "status": "pendente",
     *                     "user_id": "27",
     *                     "total_value": "130.00",
     *                     "created_at": "2025-02-27 09:58:43",
     *                     "updated_at": "2025-02-27 09:58:43"
     *                 }
     *             ],
     *             "pager": {
     *                 "currentUri": {},
     *                 "uri": {},
     *                 "hasMore": false,
     *                 "total": 2,
     *                 "perPage": 10,
     *                 "pageCount": 1,
     *                 "pageSelector": "page",
     *                 "currentPage": 1,
     *                 "next": null,
     *                 "previous": null,
     *                 "segment": 0
     *             }
     *         }
     *     }
     */
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

   /**
     * Retorna um pedido.
     *
     * @api {get} /api/orders/1 Busca Pedido
     * @apiName ShowOrders
     * @apiGroup Orders
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *         "parametros": [],
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Pedido encontradoa com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                     "id": "1",
     *                     "status": "cancelado",
     *                     "user_id": "25",
     *                     "total_value": "100.00",
     *                     "created_at": "2025-02-27 09:55:57",
     *                     "updated_at": "2025-02-27 17:33:12"
     *                }
     *          }
     */
    public function show($id = null)
    {
        try {
            $userId = $this->request->uid;
            $userRole = $this->request->urole;
            $data = $this->model->find($id);

            if (!$data) return $this->formatResponse($this->request->getGet(), 404, 'Pedido não encontrado', $data);
            if ($userRole != 'admin' && $userId != $data['user_id']) return $this->formatResponse($this->request->getGet(), 403, 'Acesso negado');
           
            return $this->formatResponse($this->request->getGet(), 200, 'Pedido encontrado com sucesso', ['data' => $data]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Criação de pedido.
     *
     * @api {post} /api/orders Criar Pedido
     * @apiName CreateOrder
     * @apiGroup Orders
     *
     * @apiBody {String} status Status do pedido (pendente ou pago).
     * @apiBody {Number} user_id Id do usuário.
     * @apiBody {Array} products Lista de produtos do pedido con suas respectivas quantidades.
     * @apiBody {Object} product item do pedido.
     * @apiBody {String} product.product_id Id do produto
     * @apiBody {Number} product.quantity Quantidade do produto
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": {
     *             "status": "pendente",
     *              "user_id": 25,
     *              "products": [
	 *		            {
	 * 			            "product_id": 6,
	 *			            "quantity": 1
	 *		            }
     *              ]
     *         },
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Pedido criado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "id": 1,
     *                 "status": "pendente",
     *                 "user_id": "25",
     *                 "total_value": "100.00",
     *                 "created_at": "2025-02-27 09:55:57",
     *                 "updated_at": "2025-02-27 19:44:16"
     *             }
     *         }
     *     }
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON();
            $orderId = $this->model->insert($data);

            if (!$orderId) return $this->formatResponse($this->request->getGet(), 400, 'Falha ao criar produto', $this->model->errors());
            if (!empty($data->products)) $this->createOrderProducts($orderId, $data->products);

            $data->total_value = $this->calculate($orderId);

            return $this->formatResponse($this->request->getJSON(), 201, 'Pedido criado com sucesso', ['data' => $this->model->find($orderId)]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Atualização de pedido.
     *
     * @api {put} /api/orders/1 Atualizar Pedido
     * @apiName UpdateOrder
     * @apiGroup Orders
     *
     * @apiBody {String} status Status do pedido.
     * @apiBody {Array} products Lista de produtos do pedido con suas respectivas quantidades.
     * @apiBody {Object} product item do pedido.
     * @apiBody {String} product.product_id Id do produto
     * @apiBody {Number} product.quantity Quantidade do produto
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": {
     *             "status": "pago"
     *         },
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Pedido atualizado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "id": 1,
     *                 "status": "pago",
     *                 "user_id": "25",
     *                 "total_value": "100.00",
     *                 "created_at": "2025-02-27 09:55:57",
     *                 "updated_at": "2025-02-27 19:44:16"
     *             }
     *         }
     *     }
     */
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

            if ($this->model->update($id, $data)) return $this->formatResponse($this->request->getJSON(), 200, 'Pedido atualizado com sucesso', ['data' => $this->model->find($order['id'])]);
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
