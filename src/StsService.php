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

namespace Wlfpanda1012\AliyunSts;

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleRequest;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleResponse;
use AlibabaCloud\SDK\Sts\V20150401\Sts;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;

use function Hyperf\Config\config;

class StsService
{
    protected Sts $sts;

    protected AssumeRoleRequest $assumeRoleRequest;

    protected AssumeRoleResponse $assumeRoleResponse;

    protected RuntimeOptions $runtimeOptions;

    public function __construct(array $options = [])
    {
        $config = new Config([
            'accessKeyId' => $options['access_key_id'] ?? config('sts.access_key_id'),
            'accessKeySecret' => $options['access_key_secret'] ?? config('sts.access_key_secret'),
            'endpoint' => $options['endpoint'] ?? config('sts.endpoint'),
        ]);
        $this->sts = new Sts($config);
    }

    public function setAssumeRoleRequest(array $config = []): void
    {
        //        if (! isset($config['RoleArn'])) {
        //            $config['RoleArn'] = config('sts.role_arn');
        //        }
        //        if (! isset($config['roleSessionName'])) {
        //            $config['roleSessionName'] = config('sts.role_session_name');
        //        }
        //        if (! isset($config['durationSeconds'])) {
        //            $config['durationSeconds'] = config('sts.duration_seconds');
        //        }
        //        if (! isset($config['externalId'])) {
        //            $config['externalId'] = config('sts.external_id');
        //        }
        $this->assumeRoleRequest = AssumeRoleRequest::fromMap($config);
    }

    public function setRuntimeOptions(array $config): void
    {
        $this->runtimeOptions = RuntimeOptions::fromMap($config);
    }

    public function getRuntimeOptions(): RuntimeOptions
    {
        return $this->runtimeOptions;
    }

    public function getSts(): Sts
    {
        return $this->sts;
    }

    public function getAssumeRoleRequest(): AssumeRoleRequest
    {
        return $this->assumeRoleRequest;
    }

    public function assumeRole(AssumeRoleRequest $request): AssumeRoleResponse
    {
        if (empty($request)) {
            $this->setAssumeRoleRequest();
        }
        $this->assumeRoleResponse = $this->sts->assumeRole($this->assumeRoleRequest);
        return $this->getAssumeRoleResponse();
    }

    public function getAssumeRoleResponse(): AssumeRoleResponse
    {
        return $this->assumeRoleResponse;
    }

    public function generatePolicy(string $effect, array $action, array $resource, array $condition = []): array
    {
        return [
            'Version' => '1',
            'Statement' => [
                [
                    'Effect' => $effect,
                    'Action' => $action,
                    'Resource' => $resource,
                    'Condition' => $condition,
                ],
            ],
        ];
    }
}
