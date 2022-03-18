<?php


namespace App\Controller\Admin;


use App\Constants\ErrorCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Di\Annotation\Inject;

class IndexController extends CommonController
{
    /**
     * @Inject
     * @var \Hyperf\Contract\SessionInterface
     */
    protected $session;
    public function index(ResponseInterface $response)
    {
        $authorization = $this->request->getHeaderLine('Authorization') ?? '';
        $username = $this->session->get($authorization) ?? '';
        return $response->json([$username], ErrorCode::SUCCESS_CODE);
    }
}
