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
use Wlfpanda1012\AliyunSts\StsService;

use function Hyperf\Config\config;

$service = new StsService([
    'access_key_id' => config('sts.access_key_id'),
    'access_key_secret' => config('sts.access_key_secret'),
    'endpoint' => config('sts.endpoint'),
]);
$service->setAssumeRoleRequest(config: [
    'RoleArn' => config('sts.role_arn'),
    'roleSessionName' => config('sts.role_session_name'),
    'durationSeconds' => config('sts.duration_seconds'),
    'policy' => '',
]);

$service->assumeRole();

var_dump($service->getAssumeRoleResponse());
