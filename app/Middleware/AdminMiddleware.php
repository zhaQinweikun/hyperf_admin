<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Event\AdminLog;
use App\Exception\AdminException;
use App\Service\Log;
use Phper666\JWTAuth\Exception\JWTException;
use Phper666\JWTAuth\Exception\TokenValidException;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Middleware\JWTAuthDefaultSceneMiddleware;
use Phper666\JWTAuth\Util\JWTUtil;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Di\Annotation\Inject;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     *
     * @var
     */
    protected $jwt;
    /**
     * @Inject()
     * @var \Hyperf\Contract\SessionInterface
     */
    protected  $session;
    /**
     * @Inject()
     * @var Log
     */
    protected $logService;
    /**
     * @Inject
     * @var  EventDispatcherInterface
     */
    private $eventDispatcher;
    public function __construct(ContainerInterface $container, JWT $jwt)
    {
        $this->container = $container;
        $this->jwt = $jwt;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //白名单
        $routers = ['/admin/login'];
        $path = $request->getUri()->getPath();
        $token = $request->getHeaderLine('Authorization') ?? '';
        // 判断登录路由不在白名单, 并且 token存在
        if(!in_array($path, $routers)){
            if(!$token){
                throw new AdminException("非法请求!", ErrorCode::ERROR_CODE);
            }
            try {
                //校验token是否存在
                if($this->jwt->verifyTokenAndScene('adminApi', $token)){
                    //管理员操作记录
                    $admin = $this->session->get($token);
                    $data = [
                        'ip' =>  $request->getServerParams()['remote_addr'],
                        'admin' => $admin,
                        'type' => 1,
                        'status' =>1,
                        'action' => $path,
                        'add_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $this->eventDispatcher->dispatch(new AdminLog(json_encode($data)));
                    //其他逻辑处理
                    return $handler->handle($request);
                }
            }catch (\RuntimeException $e){
                throw  new AdminException($e->getMessage(), ErrorCode::ERROR_CODE);
            }
            throw  new AdminException('请登录', ErrorCode::ERROR_CODE);
        }
        //登录
        return $handler->handle($request);
    }

}
