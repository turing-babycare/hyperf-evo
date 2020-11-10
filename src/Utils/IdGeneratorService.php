<?php


declare(strict_types=1);
namespace Turing\HyperfEvo\Utils;


use Hyperf\Di\Annotation\Inject;


class IdGeneratorService
{
    /**
     * @inject
     * @var Hyperf\Snowflake\IdGeneratorInterface
     */
    protected Hyperf\Snowflake\IdGeneratorInterface $idGenerator;

    /**
     * 获取全局唯一id 基于 https://hyperf.wiki/2.0/#/zh-cn/snowflake
     * @return int
     */
    public function getUuid(){
        return $this->idGenerator->generate();
    }

    /**
     * @param int $id
     * @return \Hyperf\Snowflake\Meta
     */
    public function getMeta(int $id){
        return $this->idGenerator->degenerate($id);
    }
}