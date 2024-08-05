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
use function Hyperf\Support\env;

return [
    'access_key_id' => env('STS_ACCESS_KEY_ID', 'LTAI4---------44pg8'),
    'access_key_secret' => env('STS_ACCESS_KEY_SECRET', 'oibIsys6xl---------ETocTR'),
    'role_arn' => env('STS_ROLE_ARN', 'acs:ram::xxxxxxxxxxx:role/xxx'),
    'role_session_name' => env('STS_ROLE_SESSION_NAME', 'xxxxxxxxxxx'),
    'endpoint' => env('STS_ENDPOINT', 'sts.cn-hangzhou.aliyuncs.com'),
    'duration_seconds' => env('STS_DURATION_SECONDS', 3000),
    'external_id' => env('STS_EXTERNAL_ID', ''),
];
