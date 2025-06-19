<?php

declare(strict_types=1);
namespace tinymeng\WeWorkFinanceSDK\Provider;

use tinymeng\WeWorkFinanceSDK\Contract\ProviderInterface;
use tinymeng\WeWorkFinanceSDK\Exception\FinanceSDKException;
use tinymeng\WeWorkFinanceSDK\Exception\InvalidArgumentException;

/**
 * AbstractProvider
 * @author: TinyMeng <666@majiameng.com>
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * 获取会话解密记录数据.
     * @param int $seq 起始位置
     * @param int $limit 限制条数
     * @param int $retry 重试次数
     * @return array ...
     * @throws InvalidArgumentException
     * @throws FinanceSDKException
     */
    public function getDecryptChatData(int $seq, int $limit, int $retry = 0): array
    {
        $config = $this->getConfig();
        if (!isset($config['private_keys'])) {
            throw new InvalidArgumentException('缺少配置:private_keys[{"version":"private_key"}]');
        }
        $privateKeys = $config['private_keys'];

        try {
            $chatData = json_decode($this->getChatData($seq, $limit), true)['chatdata'];
            $newChatData = [];
            $lastSeq = 0;
            foreach ($chatData as $i => $item) {
                $lastSeq = $item['seq'];
                if (!isset($privateKeys[$item['publickey_ver']])) {
                    continue;
                }

                $decryptRandKey = null;
                openssl_private_decrypt(
                    base64_decode($item['encrypt_random_key']),
                    $decryptRandKey,
                    $privateKeys[$item['publickey_ver']],
                    OPENSSL_PKCS1_PADDING
                );

                // TODO 无法解密，一般为秘钥不匹配
                // 临时补丁方案，需要改为支持多版本key
                if ($decryptRandKey === null) {
                    continue;
                }

                $newChatData[$i] = json_decode($this->decryptData($decryptRandKey, $item['encrypt_chat_msg']), true);
                $newChatData[$i]['seq'] = $item['seq'];
            }

            if (!empty($chatData) && empty($chatData) && $retry && $retry < 10) {
                return $this->getDecryptChatData($lastSeq, $limit, ++$retry);
            }

            return $newChatData;
        } catch (\Exception $e) {
            throw new FinanceSDKException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 下载媒体资源
     * @param $object
     * @param $msgType
     * @return Object SplFileInfo
     * @throws FinanceSDKException
     */
    public function getDownloadMediaData($object,$msgType)
    {
        try {
            $filePath = $this->getFilePath($object,$msgType);
            return $this->getMediaData($object['sdkfileid'],$filePath);
        } catch (\Exception $e) {
            throw new FinanceSDKException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 是否是媒体资源
     * @param $msgType
     * @return bool
     */
    public function isMedia($msgType): bool
    {
        if(in_array($msgType,['image','voice','video','file','emotion'])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取文件存储路径
     * @param $object
     * @param $megType
     * @return string
     */
    protected function getFilePath($object,$megType)
    {
        $config = $this->getConfig();
        if(empty($config['path'])){
            $config['path'] = sys_get_temp_dir() . '/' ;
        }
        $fileName = '';
        switch ($megType){
            case 'image':
                $fileName = $object['md5sum']. ".jpg";
                break;
            case 'voice':
                $fileName = $object['md5sum']. ".mp3";
                break;
            case 'video':
                $fileName = $object['md5sum']. ".mp4";
                break;
            case 'emotion':
                // 表情类型，png或者gif.1表示gif 2表示png。Uint32类型
                if($object['type'] == 1){
                    $fileName = $object['md5sum']. ".gif";
                }else{
                    $fileName = $object['md5sum']. ".png";
                }
                break;
            case 'file':
                $fileName = $object['filename'];
            default:break;
        }
        return $config['path'].$fileName;
    }

}
