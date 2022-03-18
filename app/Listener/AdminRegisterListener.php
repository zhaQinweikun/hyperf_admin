<?php


namespace App\Listener;


use App\Event\AdminRegister;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Di\Annotation\Inject;
use App\Model\Admin;

class AdminRegisterListener implements ListenerInterface
{

    /**
     * @Inject
     * @var  Admin
     */
    protected $adminModel;
    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            AdminRegister::class
        ];
    }

    /**
     *
     * @param  AdminRegister  $event
     */
    public function process(object $event)
    {
        // TODO: Implement process() method.
        $event = json_decode($event->data,true);
        $where['id'] = $event['id'];
        $data = [
            'last_login_ip' => $event['last_ip'],
            'update_time' => date('Y-m-d H:i:s', $event['update_time']),
        ];
        //记录数据信息
        $res =$this->adminModel->where($where)->update($data);
        //发送短信==
    }
}
