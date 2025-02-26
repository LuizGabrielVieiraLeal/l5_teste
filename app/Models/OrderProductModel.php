<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderProductModel extends Model
{
    protected $table = 'order_products';
    protected $allowedFields = ['order_id', 'product_id', 'quantity'];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }

    protected $validationRules = [
        'order_id' => 'required|integer|greater_than[0]',
        'product_id' => 'required|integer|greater_than[0]',
        'quantity' => 'required|integer|greater_than[0]',
    ];

    protected $validationMessages = [
        'order_id' => [
            'required' => 'O campo "order_id" é obrigatório.',
            'integer' => 'O campo "order_id" deve ser um número inteiro.',
            'greater_than' => 'O campo "order_id" deve ser maior que 0.',
        ],
        'product_id' => [
            'required' => 'O campo "product_id" é obrigatório.',
            'integer' => 'O campo "product_id" deve ser um número inteiro.',
            'greater_than' => 'O campo "product_id" deve ser maior que 0.',
        ],
        'quantity' => [
            'required' => 'O campo "quantity" é obrigatório.',
            'integer' => 'O campo "quantity" deve ser um número inteiro.',
            'greater_than' => 'O campo "quantity" deve ser maior que 0.',
        ],
    ];
}