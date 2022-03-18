<?php


namespace App\Exception\Handler;


use App\Exception\AdminException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use App\Constants\ErrorCode;

class AdminExceptionHandler extends  ExceptionHandler
{

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // TODO: Implement handle() method.
        if($throwable instanceof AdminException){
            //格式化输出
            $data = json_encode([
                'code' => $throwable->getCode(),
                'msg' => $throwable->getMessage()
            ],JSON_UNESCAPED_UNICODE);
            //阻止异常冒泡;
            $this->stopPropagation();
            return $response->withStatus($throwable->getCode())->withBody(new SwooleStream($data));
        }
        // 交给下一个异常处理器
        return  $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        // TODO: Implement isValid() method.
        return true;
    }
}
