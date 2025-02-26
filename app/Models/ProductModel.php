<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'price'];
    protected $returnType = 'object';

    public function orders()
    {
        return $this->belongsToMany(OrderModel::class, 'order_products', 'product_id', 'order_id')
                    ->withPivot('quantity');
    }

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'price' => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'O campo "name" é obrigatório.',
            'min_length' => 'O campo "name" deve ter pelo menos 3 caracteres.',
            'max_length' => 'O campo "name" não pode ter mais de 255 caracteres.',
        ],
        'price' => [
            'required' => 'O campo "price" é obrigatório.',
            'numeric' => 'O campo "price" deve ser um número.',
            'greater_than' => 'O campo "price" deve ser maior que zero.',
        ],
    ];
}