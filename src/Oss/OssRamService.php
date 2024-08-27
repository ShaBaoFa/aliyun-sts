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

    public function allowGetObject(array|string $path, int $durationSeconds = 3600, array $options = []): array
    {
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $this->processActions($options, OSSAction::ALL_GET), $path, $durationSeconds);
    }

    public function denyGetObject(array|string $path, int $durationSeconds = 3600, array $options = []): array
    {
        return $this->handleObjectAndReturnToken(OSSEffect::DENY, $this->processActions($options, OSSAction::ALL_GET), $path, $durationSeconds);
    }

    public function denyPutObject(array|string $path, int $durationSeconds = 3600, array $options = []): array
    {
        return $this->handleObjectAndReturnToken(OSSEffect::DENY, $this->processActions($options, OSSAction::ALL_PUT), $path, $durationSeconds);
    }

    public function allowPutObject(array|string $path, int $durationSeconds = 3600, array $options = []): array
    {
        return $this->handleObjectAndReturnToken(OSSEffect::ALLOW, $this->processActions($options, OSSAction::ALL_PUT), $path, $durationSeconds);
    }

    private function processActions(array $options, OSSAction $defaultActions): array
    {
        $actions = [];

        if (isset($options['actions'])) {
            if (is_array($options['actions'])) {
                foreach ($options['actions'] as $item) {
                    if ($item instanceof OSSAction) {
                        $actions[] = $item->value;
                    }
                }
            } elseif ($options['actions'] instanceof OSSAction) {
                $actions[] = $options['actions']->value;
            }
        }

        if (empty($actions)) {
            $actions = [$defaultActions->value];
        }

        return $actions;
    }

    private function handleObjectAndReturnToken(OSSEffect $effect, array $actions, array|string $path, int $durationSeconds = 3600): array
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
        $response = $this->assumeRole($this->generateAssumeRoleRequest(policy: $policy, roleSessionName: md5($policy), durationSeconds: $durationSeconds));
        return $this->getCredentials($response);
    }

    private function assembleResource(string $path): string
    {
        return sprintf('acs:oss:%s:%s:%s/%s', $this->region_id, $this->account_uid, $this->bucket, $this->normalizePath($path));
    }
}
