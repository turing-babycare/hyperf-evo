<?php

namespace Turing\HyperfEvo\Traits;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;


trait LogTrails
{
    /**
     * @inject
     * @var StdoutLoggerInterface
     */
    protected StdoutLoggerInterface $logger;

    /**
     * 记录错误日志
     * @param Throwable $throwable
     * @param string $error_code 错误编码 用于定位业务错误
     */
    protected function errorLogger(Throwable $throwable,string $error_code=''){
        if(empty($error_code)){
            $this->logger->error(sprintf('[%s] %s in %s:%s', get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()));
        }else{
            $this->logger->error(sprintf('[%s]-[%s] %s in %s:%s',$error_code, get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()));
        }
    }

    /**
     * @param $info
     * @param string $code 用于定位业务
     */
    protected function infoLogger(string $info,string $code=''){
        if(empty($code)){
            $this->logger->info(sprintf('%s', $info));
        }else{
            $this->logger->info(sprintf('[%s]-%s',$code, $info));
        }
    }
}