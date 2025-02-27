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

     /**
     * Retorna uma lista de produtos com paginação.
     *
     * @api {get} /api/products Listar Produtos
     * @apiName ListProducts
     * @apiGroup Products
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
     *             "message": "Lista de produtos retornada com sucesso"
     *         },
     *         "retorno": {
     *             "data": [
     *                 {
     *                     "id": "5",
     *                     "name": "Produto A",
     *                     "description": "Descrição do Produto A",
     *                     "price": "99.90"
     *                 },
     *                 {
     *                     "id": "6",
     *                     "name": "Produto B",
     *                     "description": "Descrição do Produto B",
     *                     "price": "199.90"
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

    /**
     * Retorna um produto.
     *
     * @api {get} /api/products/1 Buscar Produto
     * @apiName ShowProduct
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *         "parametros": [],
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Produto encontrado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
    *                   "id": "1",
    *                   "name": "Produto A",
    *                   "description": "Descrição do Produto A",
    *                   "price": "99.90"
    *               }
     *         }
     *     }
     */
    public function show($id = null)
    {
        try {
            $data = $this->model->find($id);

            if (!$data) $this->formatResponse($this->request->getGet(), 404, 'Produto não encontrado', $data);
            return $this->formatResponse($this->request->getGet(), 200, 'Produto encontrado com sucesso',  ['data' => $data]);
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getGet(),
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Criação de produto.
     *
     * @api {post} /api/products Criar Produto
     * @apiName CreateProduct
     * @apiGroup Products
     *
     * @apiBody {String} name Nome do produto.
     * @apiBody {String} description breve descrição do produto.
     * @apiBody {Number} price Preço do produto.
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *         "parametros": {
     *             "name": "Produto A",
     *              "description": "Ótimo produto",
     *              "price": 10.99
     *         },
     *         "cabecalho": {
     *             "status": 201,
     *             "message": "Pedido criado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "id": 1,
     *                 "name": "Produto A",
     *                 "description": "Ótimo produto",
     *                 "price": "10.99
     *             }
     *         }
     *     }
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON();
            $productId = $this->model->insert($data);

            if ($productId) return $this->formatResponse($data, 201, 'Produto criado com sucesso', ['data' => $this->model->find($productId)]);
            return $this->formatResponse($data, 400, 'Falha ao criar produto', $this->model->errors());
        } catch (Exception $e) {
            return $this->formatResponse(
                $this->request->getJSON(),
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Atualização de usuário.
     *
     * @api {put} /api/products/1 Atualizar Produto
     * @apiName UpdateProduct
     * @apiGroup Products
     *
     * @apiBody {String} name Nome do produto.
     * @apiBody {String} description breve descrição do produto.
     * @apiBody {Number} price Preço do produto.
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": {
     *             "price": "50.99"
     *         },
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Produto atualizado com sucesso"
     *         },
     *         "retorno": {
     *             "data": {
     *                 "id": 30,
     *                 "name": "Produto A",
     *                 "description": "Ótimo produto",
     *                 "price": 50.99
     *             }
     *         }
     *     }
     */
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

     /**
     * Exclusão de produto.
     *
     * @api {del} /api/users/1 Remover Produto
     * @apiName DeleteProduct
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Sucesso:
     *     HTTP/1.1 200 Ok
     *     {
     *         "parametros": [],
     *         "cabecalho": {
     *             "status": 200,
     *             "message": "Produto removido com sucesso"
     *         },
     *         "retorno": {
     *             "data": null
     *         }
     *     }
     */
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