<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Traits\ResponseTrait;
use App\Traits\TokenValidationTrait;
use Exception;

class AdminFilter implements FilterInterface
{
    use ResponseTrait, TokenValidationTrait;

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $token = $request->getHeaderLine('Authorization');
            if (empty($token)) return $this->formatResponse([], 403, 'Acesso negado');

            $token = str_replace('Bearer ', '', $token);
            $decoded = $this->validateToken($token);

            if (!$decoded || $decoded['role'] != 'admin') return $this->formatResponse([], 403, 'Acesso negado');
        } catch (Exception $e) {
            return $this->formatResponse([], 500, $e->getMessage());
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
