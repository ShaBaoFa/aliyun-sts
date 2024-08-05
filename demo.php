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
require __DIR__ . '/vendor/autoload.php';

use Wlfpanda1012\AliyunSts\StsService;

class demo
{
    public function main(): void
    {
        $service = new StsService([
            'access_key_id' => 'xxxxxx',
            'access_key_secret' => 'xxxxxxx',
            'endpoint' => 'sts.cn-hangzhou.aliyuncs.com',
        ]);
        $service->setAssumeRoleRequest([
            'RoleArn' => 'acs:ram::xxxxx:role/xxxxx',
            'RoleSessionName' => 'test_seesion',
            'DurationSeconds' => 3000,
            'ExternalId' => 'test_id',
            'Policy' => [
                'Statement' => [
                    [
                        'Action' => [
                            'oss:GetObject',
                            'oss:PutObject',
                            'oss:DeleteObject',
                            'oss:ListParts',
                            'oss:AbortMultipartUpload',
                            'oss:ListObjects',
                        ],
                        'Effect' => 'Allow',
                        'Resource' => [
                            'acs:oss:*:*:wlf-upload-file',
                            'acs:oss:*:*:wlf-upload-file/*',
                        ],
                    ],
                ],
                'Version' => '1'
            ],
        ]);
        $request = $service->getAssumeRoleRequest();
        $service->assumeRole($request);

        var_dump($service->getAssumeRoleResponse());
    }
}

$demo = new demo();
$demo->main();
