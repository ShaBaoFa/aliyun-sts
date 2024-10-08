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

namespace Wlfpanda1012\AliyunSts\Constants;

enum OSSClientCode: string
{
    case OSS_CALLBACK = 'x-oss-callback';
    case OSS_CALLBACK_VAR = 'x-oss-callback-var';
    case OSS_CALLBACK_URL = 'callbackUrl';
    case OSS_CALLBACK_HOST = 'callbackHost';
    case OSS_CALLBACK_BODY = 'callbackBody';
    case OSS_CALLBACK_SNI = 'callbackSNI';
    case OSS_CALLBACK_BODY_TYPE = 'callbackBodyType';
    case OSS_CALLBACK_SEPARATOR = '&';

    /**
     * 设置发起回调请求的自定义参数，由Key和Value组成，Key必须以x:开始。
     */
    case OSS_CALLBACK_CUSTOM_VAR_PREFIX = 'x:';
}
