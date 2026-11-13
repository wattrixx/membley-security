<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AdminBaseController extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->noStore();
    }

    protected function postString(string $key): string
    {
        $value = $this->request->getPost($key);

        return is_string($value) ? $value : '';
    }

    protected function noStore(): void
    {
        $this->response
            ->setHeader('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');
    }
}
