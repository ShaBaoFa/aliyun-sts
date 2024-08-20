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

namespace Wlfpanda1012\AliyunSts\Oss;

use Wlfpanda1012\AliyunSts\Constants\OSSAction;
use Wlfpanda1012\AliyunSts\Constants\OSSEffect;
use Wlfpanda1012\AliyunSts\StsService;

class OssRamService extends StsService
{
    protected string $bucket;

    protected string $account_uid;

    protected string $region_id;

    public function __construct(array $option)
    {
        parent::__construct($option);
        $this->bucket = $option['oss']['bucket'] ?? '*';
        $this->account_uid = $option['oss']['account_uid'] ?? '*';
        $this->region_id = $option['oss']['region_id'] ?? '*';
    }

    public function allowGetObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::ALL_GET->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $actions, $path);
    }

    public function denyGetObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::ALL_GET->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::DENY, $actions, $path);
    }

    public function denyPutObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::ALL_PUT->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::DENY, $actions, $path);
    }

    public function allowPutObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::ALL_PUT->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $actions, $path);
    }

    private function handleObjectAndReturnToken(OSSEffect $effect, array $actions, array|string $path): array
    {
        $resource = [];
        if (is_array($path)) {
            foreach ($path as $item) {
                $resource[] = $this->assembleResource($item);
            }
        } else {
            $resource[] = $this->assembleResource($path);
        }
        $policy = $this->generatePolicy([$this->generateStatement($effect->value, $actions, $resource)]);
        $response = $this->assumeRole($this->generateAssumeRoleRequest($policy, md5($policy)));
        return $this->getCredentials($response);
    }

    private function assembleResource(string $path): string
    {
        return sprintf('acs:oss:%s:%s:%s/%s', $this->region_id, $this->account_uid, $this->bucket, $this->normalizePath($path));
    }
}
