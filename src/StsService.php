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
use JetBrains\PhpStorm\ArrayShape;

class StsService
{
    protected Sts $sts;

    protected RuntimeOptions $runtimeOptions;

    protected string $roleArn;

    #[ArrayShape([
        'access_key_id' => 'string',
        'access_key_secret' => 'string',
        'endpoint' => 'string',
    ])]
    public function __construct(array $options = [])
    {
        $config = new Config([
            'accessKeyId' => $options['access_key_id'],
            'accessKeySecret' => $options['access_key_secret'],
            'endpoint' => $options['endpoint'],
        ]);
        $this->roleArn = $options['role_arn'];
        $this->sts = new Sts($config);
    }

    public function generateAssumeRoleRequest(string $policy,string $roleSessionName = null ,int $durationSeconds = 3600, ?string $externaId = null): AssumeRoleRequest
    {
        $map['RoleArn'] = $this->roleArn;
        $map['DurationSeconds'] = $durationSeconds;
        $map['Policy'] = $policy;
        $roleSessionName && $map['RoleSessionName'] = $roleSessionName;
        $externaId && $map['ExternalId'] = $externaId;
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
        return $this->sts->assumeRole($request);
    }

    public function getCredentials(AssumeRoleResponse $assumeRoleResponse): array
    {
        return $assumeRoleResponse->body->credentials->toMap();
    }

    public function generateStatement(string $effect, array $action, array $resource, ?array $condition = null): array
    {
        $statement = [
            'Effect' => $effect,
            'Action' => $action,
            'Resource' => $resource,
        ];
        $condition && $statement = array_merge($statement, ['Condition' => $condition]);
        return $statement;
    }

    public function generatePolicy(array $statement): string
    {
        return json_encode([
            'Version' => '1',
            'Statement' => $statement,
        ]);
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
