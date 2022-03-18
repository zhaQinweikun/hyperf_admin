<?php


namespace App\Service;

use Hyperf\Di\Annotation\Inject;

class Log
{
    /**
     * @Inject()
     * @var \App\Model\Log
     */
    protected $logModel;
    public  function inserts($data)
    {
      return  $this->logModel->addOne($data);
    }
}
