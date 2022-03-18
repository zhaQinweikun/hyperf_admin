<?php
namespace App\Service;

use App\Constants\ErrorCode;

use App\Exception\AdminException;
use Hyperf\Di\Annotation\Inject;
use App\Model\Admin as AdminModel;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Event\AdminRegister;
use App\Event\UserRegistered;
use Phper666\JWTAuth\JWT;

class Admin
{
    /**
     * @Inject()
     * @var AdminModel
     */
    protected $adminModel;

    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private   $eventDispatcher;

    /**
     * @Inject
     * @var  JWT
     */
    protected $jwt;
    /**
     * @Inject
     * @var  \Hyperf\Contract\SessionInterface
     */
    protected $session;
    public function login($data)
    {
        $userInfo = $this->adminModel->findOne(['username' => $data['username']]);
        if(!$userInfo){
            throw new  AdminException("用户不存在!!", ErrorCode::ERROR_CODE);
        }
        $data['password'] = sha1(md5($data['password']). config('constant.admin_password'));
        if($data['password'] != $userInfo->password){
            throw new AdminException("密码错误!!", ErrorCode::ERROR_CODE);
        }
        if($userInfo->deleted != 0){
            throw new AdminException("账号异常!!", ErrorCode::ERROR_CODE);
        }
        $userUpdate = [
            'last_ip' => $data['last_login_ip'],
             'update_time' => time(),
            'id' => $userInfo->id
        ];
        $this->ido(json_encode($userUpdate));
        $userData = [
            'uid' => $userInfo->id,
            'username' => $userInfo->username
        ];
        $token = $this->jwt->getToken('adminApi', $userData);
        $userInfo->token = $token->toString();
        $userInfo->exp = $this->jwt->getTTL($userInfo->token);
        $this->session->set($userInfo->token, $userInfo->username);
        return $userInfo  ;
    }
    public function ido($data)
    {
        $this->eventDispatcher->dispatch(new AdminRegister($data));
    }

    /**
     * 刷新token
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refreshToken()
    {
        $token = $this->jwt->refreshToken();
        $data = [
            'token' => $token->toString(),
            'exp' => $this->jwt->getTTL($token->toString()),
         ];
        return $data;
    }

    public function logout()
    {
        return $this->jwt->logout();
    }

}
