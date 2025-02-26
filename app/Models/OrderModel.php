<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['status', 'user_id', 'total_value', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(ProductModel::class, 'order_products', 'order_id', 'product_id')
                    ->withPivot('quantity');
    }

    protected $validationRules = [
        'user_id' => 'required|integer|greater_than[0]',
        'status' => 'required|in_list[pendente,pago,cancelado]',
        'total_value' => 'required|numeric|greater_than_equal_to[0]|decimal[2]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'O campo "user_id" é obrigatório.',
            'integer' => 'O campo "user_id" deve ser um número inteiro.',
            'greater_than' => 'O campo "user_id" deve ser maior que 0.',
        ],
        'status' => [
            'required' => 'O campo "status" é obrigatório.',
            'in_list' => 'O campo "status" deve ser "pendente", "pago" ou "cancelado".',
        ],
        'total_value' => [
            'required' => 'O campo "total_value" é obrigatório.',
            'numeric' => 'O campo "total_value" deve ser um valor numérico.',
            'greater_than_equal_to' => 'O campo "total_value" deve ser maior ou igual a 0.',
            'decimal' => 'O campo "total_value" deve ter no máximo duas casas decimais.',
        ],
    ];
}