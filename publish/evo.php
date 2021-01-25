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
        'default_target_service' => 'ask',
        'publish_service' => 'thirdparty',
        'publish_url' => '/tmq/publish',
        'consume_url' => '/tmq/consume',
    ],
];
