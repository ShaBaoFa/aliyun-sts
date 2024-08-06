<?php

declare(strict_types=1);
/**
 * This file is part of web-api.
 *
 * @link     https://blog.wlfpanda1012.com/
 * @github   https://github.com/ShaBaoFa
 * @gitee    https://gitee.com/wlfpanda/web-api
 * @contact  mail@wlfpanda1012.com
 */

namespace Wlfpanda1012\AliyunSts\Oss;

use Wlfpanda1012\AliyunSts\Constants\OSSAction;
use Wlfpanda1012\AliyunSts\Constants\OSSEffect;
use Wlfpanda1012\AliyunSts\StsService;

class OssRamService extends StsService
{
    protected string $bucket;

    public function __construct(array $option)
    {
        parent::__construct($option);
        $this->bucket = $option['bucket'];
    }

    public function handleObjectAndReturnToken(OSSEffect $effect, array $actions, array|string $path): array
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

    public function allowGetObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::GET_OBJECT->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $actions, $path);
    }

    public function allowPutObject(array|string $path, array $options = []): array
    {
        $actions = [OSSAction::PUT_OBJECT->value];
        if (isset($options['actions'])) {
            $actions = array_merge($actions, $options['actions']);
        }
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $actions, $path);
    }

    private function assembleResource(string $path): string
    {
        return 'acs:oss:*:*:' . $this->bucket . '/' . $path;
    }
}
