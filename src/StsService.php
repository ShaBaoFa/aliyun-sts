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
use Hyperf\Stringable\Str;

use function Hyperf\Config\config;

class StsService
{
    protected Sts $sts;

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

    public function generateAssumeRoleRequest(array $map = []): AssumeRoleRequest
    {
        /**
         * 保证与 AssumeRoleRequest::fromMap 保持一致.
         */
        $map = $this->convertKeysToStudlyCase($map);

        if (! isset($map['RoleArn'])) {
            $map['RoleArn'] = config('sts.role_arn');
        }
        if (! isset($map['RoleSessionName'])) {
            $map['RoleSessionName'] = config('sts.role_session_name');
        }
        if (! isset($map['DurationSeconds'])) {
            $map['DurationSeconds'] = config('sts.duration_seconds');
        }
        if (! isset($map['ExternalId'])) {
            $map['ExternalId'] = config('sts.external_id');
        }
        if (empty($map['Policy'])) {
            $map['Policy'] = config('sts.policy');
        }

        return AssumeRoleRequest::fromMap($map);
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

    public function assumeRole(AssumeRoleRequest $request): AssumeRoleResponse
    {
        $this->assumeRoleResponse = $this->sts->assumeRole($request);
        return $this->getAssumeRoleResponse();
    }

    public function getCredentials() {}

    public function getAssumeRoleResponse(): AssumeRoleResponse
    {
        return $this->assumeRoleResponse;
    }

    public function generateStatement(string $effect, array $action, array $resource, array $condition = []): array
    {
        $statement = [
            'Effect' => $effect,
            'Action' => $action,
            'Resource' => $resource,
        ];
        if (! empty($condition)) {
            $statement = array_merge($statement, ['Condition' => $condition]);
        }
        return $statement;
    }

    public function generatePolicy(array $statement): array
    {
        return [
            'policy' => [
                'Version' => '1',
                'Statement' => $statement,
            ],
        ];
    }

    private function convertKeysToStudlyCase(array $array): array
    {
        $convertedArray = [];
        foreach ($array as $key => $value) {
            // 将键名转换为 StudlyCase 格式
            $newKey = is_string($key) ? Str::studly($key) : $key;
            // 如果值是数组，则递归调用
            if (is_array($value)) {
                $value = $this->{__FUNCTION__}($value);
            }

            // 将转换后的键和值放入新数组
            $convertedArray[$newKey] = $value;
        }

        return $convertedArray;
    }
}
