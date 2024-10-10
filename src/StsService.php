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
use DateTime;
use Hyperf\Stringable\Str;
use Wlfpanda1012\AliyunSts\Exception\InvalidArgumentException;
use Wlfpanda1012\CommonSts\Contract\StoragePolicyGenerator;
use Wlfpanda1012\CommonSts\Contract\StsAdapter;
use Wlfpanda1012\CommonSts\Response\StsTokenResponse;

class StsService implements StsAdapter, StoragePolicyGenerator
{
    protected Sts $sts;

    protected RuntimeOptions $runtimeOptions;

    protected string $roleArn;

    public function __construct(array $options = [])
    {
        $config = new Config([
            'accessKeyId' => $options['access_key_id'],
            'accessKeySecret' => $options['access_key_secret'],
            'endpoint' => $options['endpoint'] ?? 'sts.cn-hangzhou.aliyuncs.com',
        ]);
        $this->roleArn = $options['role_arn'];
        $this->sts = new Sts($config);
    }

    public function getToken(array $policy, array $config = []): StsTokenResponse
    {
        // 适配 easy-sts
        $request = $this->generateAssumeRoleRequest(policy: json_encode($policy), roleSessionName: md5(json_encode($policy)), durationSeconds: $config['duration_seconds'] ?? 3600, externalId: $config['external_id'] ?? null);
        $response = $this->sts->assumeRole($request);
        $credentials = $response->body->credentials;
        return new StsTokenResponse(
            $credentials->accessKeyId,
            $credentials->accessKeySecret,
            new DateTime($credentials->expiration),
            $credentials->securityToken
        );
    }

    public function storagePolicy(string $effect, array $actions, array|string $path, array $config = []): array
    {
        $resource = [];
        if (is_array($path)) {
            foreach ($path as $item) {
                $resource[] = $this->assembleResource($item, $config);
            }
        } else {
            $resource[] = $this->assembleResource($path, $config);
        }
        return $this->generatePolicy([$this->generateStatement($effect, $actions, $resource)]);
    }

    public function generateAssumeRoleRequest(string $policy, string $roleSessionName, int $durationSeconds = 3600, ?string $externalId = null): AssumeRoleRequest
    {
        $map['RoleArn'] = $this->roleArn;
        $map['DurationSeconds'] = $durationSeconds ?? 3600;
        $map['Policy'] = $policy;
        $map['RoleSessionName'] = $roleSessionName;
        $externalId && $map['ExternalId'] = $externalId;
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
        $credentials = $assumeRoleResponse->body->credentials->toMap();
        $credentials['Expiration'] = strtotime($credentials['Expiration']);
        return $this->convertKeysToSnakeCase($credentials);
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

    public function generatePolicy(array $statement): array
    {
        return [
            'Version' => '1',
            'Statement' => $statement,
        ];
    }

    protected function convertKeysToStudlyCase(array $array): array
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

    protected function convertKeysToSnakeCase(array $array): array
    {
        $convertedArray = [];
        foreach ($array as $key => $value) {
            // 将键名转换为 StudlyCase 格式
            $newKey = is_string($key) ? Str::snake($key) : $key;
            // 如果值是数组，则递归调用
            if (is_array($value)) {
                $value = $this->{__FUNCTION__}($value);
            }

            // 将转换后的键和值放入新数组
            $convertedArray[$newKey] = $value;
        }

        return $convertedArray;
    }

    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $this->rejectFunkyWhiteSpace($path);
        return $this->normalizeRelativePath($path);
    }

    private function assembleResource(string $path, array $config): string
    {
        /**
         * 默认设置最大范围,通过 RAM 用户 基础范围 + 文件url 控制.
         */

        return sprintf('acs:oss:%s:%s:%s/%s', $config['region_id'] ?? '*', $config['account_uid'] ?? '*', $config['bucket'] ?? '*', $this->normalizePath($path));
    }

    private function rejectFunkyWhiteSpace(string $path): void
    {
        if (preg_match('#\p{C}+#u', $path)) {
            throw new InvalidArgumentException('Invalid characters in path: ' . $path);
        }
    }

    private function normalizeRelativePath(string $path): string
    {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;
                case '..':
                    if (empty($parts)) {
                        throw new InvalidArgumentException('Invalid path: ' . $path);
                    }
                    array_pop($parts);
                    break;
                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
