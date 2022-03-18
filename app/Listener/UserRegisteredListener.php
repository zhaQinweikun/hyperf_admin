<?php


namespace App\Listener;

use App\Event\UserRegistered;
use Hyperf\Event\Contract\ListenerInterface;

class UserRegisteredListener implements ListenerInterface
{

    public function listen(): array
    {
        // TODO: Implement listen() method.
        return  [
            UserRegistered::class
        ];
    }

    /**
     * @param  UserRegister  $event
     */
    public function process(object $event)
    {
        // TODO: Implement process() method.
        var_dump($event);
    }
}
