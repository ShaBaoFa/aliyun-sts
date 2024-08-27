# aliyun-sts

<p align="center">
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/php-%3E=8.1-brightgreen.svg?maxAge=2592000" alt="Php Version"></a>
  <a href="https://github.com/swoole/swoole-src"><img src="https://img.shields.io/badge/swoole-%3E=5.0-brightgreen.svg?maxAge=2592000" alt="Swoole Version"></a>
</p>

## 介绍
为 `Hyperf` 框架编写的基于阿里云openapi系列 `sts` 接口的最新版本SDK的封装包
并对 `OSS` 服务进行了优化封装

## 安装

```bash
composer require wlfpanda1012/aliyun-sts
```

## 发布配置

```bash
 php bin/hyperf.php vendor:publish wlfpanda1012/aliyun-sts
```

## 如何使用

- 填写 `STS_ACCESS_KEY_ID` `STS_ACCESS_KEY_SECRET` `STS_ROLE_ARN` 到 `.env`

### 如果你想用原生 `STS` 功能

```php
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

$effect = 'Allow';
$action = ['*'];
$resource = ['*'];
$condition = null;
$roleSessionName = 'session_name';
$service = \Hyperf\Support\make(StsService::class, ['option' => \Hyperf\Config\config('sts')]);
// 具体Statement参数请查询阿里云官方文档
$assumeRoleRequest = $service->generateAssumeRoleRequest($service->generatePolicy($service->generateStatement($effect, $action, $resource, $condition)), $roleSessionName);
$credentials = $service->getCredentials($service->assumeRole($assumeRoleRequest));
```

### 如果想用封装好的 `OSS-STS` 功能你只需要

- 为了简化操作以及降低用户的心智负担,建议只传入 `path`
- 如果你对权限时间有要求,可以传入时间 默认 `time` = 3600
- 下载和上传默认使用了通配符的形式.
- 如果你需要更多个性化的Action,提供了 `OSSAction` 供你直观的选择.
- (如果枚举类中没有你想要的类型欢迎提 `PR` 当然也可以直接用通配符降低负担.)

```php
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
use Wlfpanda1012\AliyunSts\Constants\OSSAction;
use Wlfpanda1012\AliyunSts\Oss\OssRamService;

$service = \Hyperf\Support\make(OssRamService::class, ['option' => \Hyperf\Config\config('sts')]);

/**
 * 如果你想获得下载文件的token，可以使用以下代码
 * 支持数组和字符串.
 */
$token = $service->allowGetObject('path/to/file');
$token = $service->allowGetObject('path/to/file', 3600);
$token = $service->allowGetObject(['path/to/file1', 'path/to/file2'], 3600);
/**
 * 如果你不想使用通配行为.
 */
$token = $service->allowGetObject(['path/to/file1', 'path/to/file2'], 3600, ['actions' => OSSAction::GET_OBJECT]);
$token = $service->allowGetObject(['path/to/file1', 'path/to/file2'], 3600, ['actions' => [OSSAction::GET_OBJECT, OSSAction::GET_OBJECT_ACL]]);

/**
 * allowPutObject
 * denyPutObject
 * denyGetObject
 * 功能同上.
 */

```
