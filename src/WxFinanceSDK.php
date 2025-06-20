<?php
namespace tinymeng\WeWorkFinanceSDK;
use tinymeng\WeWorkFinanceSDK\Contract\ProviderInterface;
use tinymeng\WeWorkFinanceSDK\Exception\InvalidArgumentException;
use tinymeng\WeWorkFinanceSDK\Provider\FFIProvider;
use tinymeng\WeWorkFinanceSDK\Provider\PHPExtProvider;

/**
 * Class WxFinanceSDK.
 * @author: JiaMeng <666@majiameng.com>
 * @method array getConfig()  获取微信配置
 * @method string getChatData(int $seq, int $limit)  获取会话记录数据(加密)
 * @method string decryptData(string $randomKey, string $encryptStr)  解密数据
 * @method \SplFileInfo getMediaData(string $sdkFileId, string $msgType)  获取媒体资源
 * @method array getDecryptChatData(int $seq, int $limit)  获取会话记录数据(解密)
 * @method Object getDownloadMediaData(array $object, string $msgType)  下载媒体资源
 * @method array isMedia(string $msgType)  获取是否是媒体资源
 */
class WxFinanceSDK
{

    /**
     * @var array
     */
    protected $config;
    protected $wxConfig;


    public function __construct(array $config = [],array $wxConfig = [])
    {
        $default = [
            'default'   => 'php-ext',
            'providers' => [
                'php-ext' => [
                    'driver' => PHPExtProvider::class,
                ],
                'php-ffi' => [
                    'driver' => FFIProvider::class,
                ],
            ],
        ];
        $this->config = empty($config) ? $default : array_merge($default, $config);
        $this->wxConfig = $wxConfig;
    }


    public function __call($name, $arguments)
    {
        $provider = $this->provider($this->config['default']);

        if (method_exists($provider, $name)) {
            return call_user_func_array([$provider, $name], $arguments);
        }

        throw new InvalidArgumentException('WxFinanceSDK::Method not defined. method:' . $name);
    }

    public static function init(array $wxConfig = [], array $driverConfig = []): self
    {
        return new self($driverConfig, $wxConfig);
    }

    /**
     * @param $providerName ...
     * @throws InvalidArgumentException ...
     * @return ProviderInterface ...
     */
    public function provider($providerName): ProviderInterface
    {
        if (! $this->config['providers'] || ! $this->config['providers'][$providerName]) {
            throw new InvalidArgumentException("file configurations are missing {$providerName} options");
        }
        return (new $this->config['providers'][$providerName]['driver']())->setConfig($this->wxConfig);
    }
}