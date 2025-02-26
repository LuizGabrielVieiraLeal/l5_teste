<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function formatResponse($params, $status, $message, $data = null)
    {
        return $this->respond([
            'parametros' => $params,
            'cabecalho' => [
                'status' => $status,
                'message' => $message
            ],
            'retorno' => $data
        ], $status);
    }
}