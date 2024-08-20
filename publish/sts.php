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
    'external_id' => env('STS_EXTERNAL_ID', 'external_id_test'),
    /**
     * 授予RAM用户使用OSS命令行工具访问目录mybucket/hangzhou/2014/和mybucket/hangzhou/2015/并列举目录中文件的权限.
     *
     * RAM用户不清楚目录中有哪些文件，可以使用OSS命令行工具或API直接获取目录信息，此场景下需要添加ListObjects权限。
     */
    'policy' => [
        'Version' => '1',
        'Statement' => [
            [
                'Effect' => 'Allow',
                'Action' => [
                    'oss:GetObject',
                ],
                'Resource' => [
                    'acs:oss:*:*:mybucket/hangzhou/2014/*',
                    'acs:oss:*:*:mybucket/hangzhou/2015/*',
                ],
            ],
            [
                'Effect' => 'Allow',
                'Action' => [
                    'oss:ListObjects',
                ],
                'Resource' => [
                    'acs:oss:*:*:mybucket',
                ],
                'Condition' => [
                    'StringLike' => [
                        'oss:Prefix' => [
                            'hangzhou/2014/*',
                            'hangzhou/2015/*',
                        ],
                    ],
                ],
            ],
        ],
    ],
    /**
     * OSS相关.
     */
    'oss' => [
        'bucket' => env('OSS_BUCKET', '*'),
        'account_uid' => env('OSS_ACCOUNT_UID', '*'),
        'region_id' => env('OSS_REGION_ID', '*'),
        'callback' => [
            'callbackUrl' => env('OSS_CALLBACK_URL', 'http://127.0.0.1:9501/callback'),
            'callbackHost' => env('OSS_CALLBACK_HOST', '127.0.0.1'),
            'callbackBody' => env('OSS_CALLBACK_BODY', 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}'),
            'callbackSNI' => env('OSS_CALLBACK_SNI', false),
            'callbackBodyType' => env('OSS_CALLBACK_BODY_TYPE', 'application/x-www-form-urlencoded'),
        ]
    ],
];
