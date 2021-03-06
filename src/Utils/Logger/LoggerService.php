<?php


namespace Turing\HyperfEvo\Utils\Logger;


use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class LoggerService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('log', 'default');
    }
    /**
     * 记录错误日志
     * @param \Throwable $throwable
     * @param string $error_code 错误编码 用于定位业务错误
     */
    public function errorLogger(\Throwable $throwable,string $error_code=''){
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
    public function infoLogger(string $info,string $code=''){
        if(empty($code)){
            $this->logger->info(sprintf('%s', $info));
        }else{
            $this->logger->info(sprintf('[%s]-%s',$code, $info));
        }
    }

}