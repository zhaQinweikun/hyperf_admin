<?php
namespace App\Controller\Admin;

use App\Request\LoginRequest;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use App\Service\Admin as AdminService;

class LoginController extends CommonController
{
    /**
     * @Inject()
     * @var  ValidatorFactoryInterface
     */
    protected $validationFactory;
    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @Inject()
     * @var AdminService
     */
    protected $adminSerivce;
//RequestInterface
//    protected $request ;
    public function login(LoginRequest $validate)
    {
        $data = $validate->validated();
        $data['last_login_ip'] =  $this->request->getServerParams()['remote_addr'];
        $param = $this->adminSerivce->login($data);
        return $this->response->json($param,200);
    }

    public function logout()
    {
      $res =   $this->adminSerivce->logout();
      return $this->response->json($res,200);
    }

}
