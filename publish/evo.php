<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'service_client' => [
        'service_list' => json_decode(env('EVO_SERVICES', '{}'), true),
        'config' => [
            'timeout' => 10,
        ],
    ],
    'tmq' => [
        'default_target_service' => 'ask', //默认发送的目标服务名称
        'publish_service' => 'thirdparty', //TMQ服务名称
        'publish_url' => '/tmq/publish', //TMQ服务生产消息的接口地址
        'consume_url' => '/tmq/consume', //本地接收消息的地址
        'local_service' => 'ask', //本地服务名称
    ],
];
