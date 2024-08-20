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
enum OSSAction: string
{
    /**
     * 所有行为.
     */
    case ALL = 'oss:*';

    /**
     * 所有上传行为.
     */
    case ALL_PUT = 'oss:Put*';

    /**
     * 所有下载行为.
     */
    case ALL_GET = 'oss:Get*';

    /**
     * 上传文件（Object）。
     * 通过HTML表单上传的方式将Object上传到指定Bucket。
     * 以追加写的方式上传Object。
     * 在使用Multipart Upload模式传输数据前，通知OSS初始化一个分片上传（Multipart Upload）事件。
     * 根据指定的Object名和uploadId来分块（Part）上传数据。
     * 在将所有数据Part都上传完成后，需调用此接口来完成整个Object的分片上传。
     * 为OSS的目标文件（TargetObject）创建软链接（Symlink）。
     */
    case PUT_OBJECT = 'oss:PutObject';

    /**
     * 取消MultipartUpload事件并删除对应的Part数据。
     */
    case ABORT_MULTIPART_UPLOAD = 'oss:AbortMultipartUpload';

    /**
     * 获取某个Object。
     * 获取某个Object的元数据。
     * 获取Object的元数据信息，包括该Object的ETag、Size、LastModified信息。
     * 对目标文件执行SQL语句，返回执行结果。
     * 获取目标文件的软链接。
     */
    case GET_OBJECT = 'oss:GetObject';

    /**
     * 删除某个Object。
     * 删除同一个Bucket中的多个Object。
     */
    case DELETE_OBJECT = 'oss:DeleteObject';

    /**
     * 修改Bucket下某个Object的ACL。
     */
    case PUT_OBJECT_ACL = 'oss:PutObjectAcl';

    /**
     * 获取Bucket下某个Object的ACL。
     */
    case GET_OBJECT_ACL = 'oss:GetObjectAcl';

    /**
     * 解冻归档存储、冷归档存储或者深度冷归档存储类型的Object。
     */
    case RESTORE_OBJECT = 'oss:RestoreObject';

    /**
     * 设置或更新Object的标签（Tagging）信息。
     */
    case PUT_OBJECT_TAGGING = 'oss:PutObjectTagging';

    /**
     * 获取Object的标签信息。
     */
    case GET_OBJECT_TAGGING = 'oss:GetObjectTagging';

    /**
     * 删除指定Object的标签信息。
     */
    case DELETE_OBJECT_TAGGING = 'oss:DeleteObjectTagging';

    /**
     * 下载指定版本Object。
     */
    case GET_OBJECT_VERSION = 'oss:GetObjectVersion';

    /**
     * 修改Bucket下指定版本Object的ACL。
     */
    case PUT_OBJECT_VERSION_ACL = 'oss:PutObjectVersionAcl';

    /**
     * 获取Bucket下指定版本Object的ACL。
     */
    case GET_OBJECT_VERSION_ACL = 'oss:GetObjectVersionAcl';

    /**
     * 解冻指定版本的归档存储、冷归档存储或者深度冷归档存储类型的Object。
     */
    case RESTORE_OBJECT_VERSION = 'oss:RestoreObjectVersion';

    /**
     * 删除指定版本Object。
     */
    case DELETE_OBJECT_VERSION = 'oss:DeleteObjectVersion';

    /**
     * 设置或更新指定版本Object的标签（Tagging）信息。
     */
    case PUT_OBJECT_VERSION_TAGGING = 'oss:PutObjectVersionTagging';

    /**
     * 获取指定版本Object的标签信息。
     */
    case GET_OBJECT_VERSION_TAGGING = 'oss:GetObjectVersionTagging';

    /**
     * 删除指定版本Object的标签信息。
     */
    case DELETE_OBJECT_VERSION_TAGGING = 'oss:DeleteObjectVersionTagging';

    /**
     * 通过RTMP协议上传音视频数据前，必须先调用该接口创建一个LiveChannel。
     */
    case PUT_LIVE_CHANNEL = 'oss:PutLiveChannel';

    /**
     * 列举指定的LiveChannel。
     */
    case LIST_LIVE_CHANNEL = 'oss:ListLiveChannel';

    /**
     * 删除指定的LiveChannel。
     */
    case DELETE_LIVE_CHANNEL = 'oss:DeleteLiveChannel';

    /**
     * 在启用（enabled）和禁用（disabled）两种状态之间进行切换。
     */
    case PUT_LIVE_CHANNEL_STATUS = 'oss:PutLiveChannelStatus';

    /**
     * 获取指定LiveChannel的配置信息。
     */
    case GET_LIVE_CHANNEL = 'oss:GetLiveChannel';

    /**
     * 获取指定LiveChannel的推流状态信息。
     */
    case GET_LIVE_CHANNEL_STAT = 'oss:GetLiveChannelStat';

    /**
     * 获取指定LiveChannel的推流记录。
     */
    case GET_LIVE_CHANNEL_HISTORY = 'oss:GetLiveChannelHistory';

    /**
     * 为指定的LiveChannel生成一个点播用的播放列表。
     */
    case POST_VOD_PLAYLIST = 'oss:PostVodPlaylist';

    /**
     * 查看指定LiveChannel在指定时间段内推流生成的播放列表。
     */
    case GET_VOD_PLAYLIST = 'oss:GetVodPlaylist';

    /**
     * 将音频和视频数据流推送到RTMP。
     */
    case PUBLISH_RTMP_STREAM = 'oss:PublishRtmpStream';

    /**
     * 基于图片AI技术检测图片标签和置信度。
     */
    case PROCESS_IMM = 'oss:ProcessImm';

    /**
     * 保存处理后的图片至指定Bucket。
     */
    case POST_PROCESS_TASK = 'oss:PostProcessTask';

    /**
     * 复制过程涉及的读权限。即允许OSS读取源Bucket和目标Bucket中的数据与元数据，包括Object、Part、Multipart Upload等。
     */
    case REPLICATE_GET = 'oss:ReplicateGet';

    /**
     * 复制过程涉及的写权限。即允许OSS对目标Bucket复制相关的写入类操作，包括写入Object、Multipart Upload、Part和Symlink，修改元数据信息等。
     */
    case REPLICATE_PUT = 'oss:ReplicatePut';

    /**
     * 复制过程涉及的删除权限。即允许OSS对目标Bucket复制相关的删除操作，包括DeleteObject、AbortMultipartUpload、DeleteMarker等。
     */
    case REPLICATE_DELETE = 'oss:ReplicateDelete';
}
