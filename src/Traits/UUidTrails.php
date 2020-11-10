<?php
declare(strict_types=1);

namespace Turing\HyperfEvo\Traits;

use Hyperf\Snowflake\IdGeneratorInterface;

/**
 * Trait UUidTrails
 * https://hyperf.wiki/2.0/#/zh-cn/snowflake
 * @package Turing\HyperfEvo\Traits
 */
trait UUidTrails
{
    /**
     * @inject
     * @var IdGeneratorInterface
     */
    private  IdGeneratorInterface $idGenerator;

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
