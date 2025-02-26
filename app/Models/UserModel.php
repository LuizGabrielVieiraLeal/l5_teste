<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'role', 'name', 'document'];

    protected $beforeInsert = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[255]',
        'password' => 'required|min_length[3]|max_length[255]',
        'name' => 'required|min_length[3]|max_length[255]',
        'document' => 'required|regex_match[/^\d{3}\.\d{3}\.\d{3}-\d{2}$|^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/]',
        'role' => 'required|in_list[admin,common]',
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'O campo "username" é obrigatório.',
            'min_length' => 'O campo "username" deve ter pelo menos 3 caracteres.',
            'max_length' => 'O campo "username" não pode ter mais de 255 caracteres.',
        ],
        'password' => [
            'required' => 'O campo "password" é obrigatório.',
            'min_length' => 'O campo "password" deve ter pelo menos 3 caracteres.',
            'max_length' => 'O campo "password" não pode ter mais de 255 caracteres.',
        ],
        'name' => [
            'required' => 'O campo "name" é obrigatório.',
            'min_length' => 'O campo "name" deve ter pelo menos 3 caracteres.',
            'max_length' => 'O campo "name" não pode ter mais de 255 caracteres.',
        ],
        'document' => [
            'required' => 'O campo "document" é obrigatório.',
            'regex_match' => 'O campo "document" deve estar no formato XXX.XXX.XXX-XX (CPF) ou XX.XXX.XXX/0001-XX (CNPJ).',
        ],
        'role' => [
            'required' => 'O campo "role" é obrigatório.',
            'in_list' => 'O campo "role" deve ser "admin" ou "common".',
        ],
    ];
}