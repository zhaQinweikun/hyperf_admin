<?php


namespace App\Listener;


use App\Service\Log;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Di\Annotation\Inject;


class AdminLog implements  ListenerInterface
{
    /**
     * @Inject
     * @var Log
     */
    protected $adminService;
    public function listen(): array
    {
        // TODO: Implement listen() method.
        return  [
            \App\Event\AdminLog::class,
        ];
    }

    public function process(object $event)
    {
        // TODO: Implement process() method.
        $event = json_decode($event->data,true);
        $this->adminService->inserts($event);
    }
}
