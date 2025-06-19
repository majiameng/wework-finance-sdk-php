<?php

declare(strict_types=1);
/**
 * ProviderInterface
 */
namespace tinymeng\WeWorkFinanceSDK\Contract;

interface ProviderInterface
{
    /**
     * 设置配置.
     * @param array $config 企业微信配置
     * @return $this ...
     */
    public function setConfig(array $config): ProviderInterface;

    /**
     * 获取配置.
     * @return array ...
     */
    public function getConfig(): array;

    /**
     * 获取会话记录数据.
     * @param int $seq 起始位置
     * @param int $limit 限制条数
     * @return string ...
     */
    public function getChatData(int $seq, int $limit): string;

    /**
     * 解密数据.
     * @param string $randomKey 通过openssl解密后的key
     * @param string $encryptStr chats 的加密数据
     * @return string ...
     */
    public function decryptData(string $randomKey, string $encryptStr): string;

    /**
     * 获取媒体资源.
     * @param string $sdkFileId 资源id
     * @param string $path 文件存储路径
     * @return \SplFileInfo ...
     */
    public function getMediaData(string $sdkFileId, string $path): \SplFileInfo;
}
