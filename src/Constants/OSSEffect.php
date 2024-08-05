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

use Hyperf\Constants\Annotation\Constants;

#[Constants]
enum OSSEffect: string
{
    case ALLOW = 'Allow';
    case DENY = 'Deny';
}