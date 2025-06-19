<?php

declare(strict_types=1);
namespace tinymeng\WeWorkFinanceSDK\Provider;

use tinymeng\WeWorkFinanceSDK\Contract\ProviderInterface;
use tinymeng\WeWorkFinanceSDK\Exception\FinanceSDKException;
use tinymeng\WeWorkFinanceSDK\Exception\InvalidArgumentException;

/**
 * PHPExtProvider
 */
class PHPExtProvider extends AbstractProvider
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \WxworkFinanceSdk
     */
    private $financeSdk;

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): ProviderInterface
    {
        $this->config = array_merge($this->config, $config);
        $this->setFinanceSDK();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getChatData(int $seq, int $limit): string
    {
        return $this->financeSdk->getChatData($seq, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function decryptData(string $randomKey, string $encryptStr): string
    {
        return $this->financeSdk->decryptData($randomKey, $encryptStr);
    }

    /**
     * {@inheritdoc}
     * @throws FinanceSDKException
     */
    public function getMediaData(string $sdkFileId, string $path): \SplFileInfo
    {
        try {
            $this->financeSdk->downloadMedia($sdkFileId, $path);
        } catch (\WxworkFinanceSdkExcption $e) {
            throw new FinanceSDKException('获取文件失败' . $e->getMessage(), $e->getCode());
        }
        return new \SplFileInfo($path);
    }


    /**
     * 获取php-ext-sdk.
     * @param array $config ...
     */
    protected function setFinanceSDK(array $config = []): void
    {
        if (! extension_loaded('wxwork_finance_sdk')) {
            throw new FinanceSDKException('缺少ext-wxwork_finance_sdk扩展');
        }

        $this->config = array_merge($this->config, $config);
        if (! isset($this->config['corpid'])) {
            throw new InvalidArgumentException('缺少配置:corpid');
        }
        if (! isset($this->config['secret'])) {
            throw new InvalidArgumentException('缺少配置:secret');
        }
        $options                                                     = ['timeout' => 30];
        isset($this->config['proxy']) && $options['proxy_host']      = $this->config['proxy'];
        isset($this->config['passwd']) && $options['proxy_password'] = $this->config['passwd'];
        isset($this->config['timeout']) && $options['timeout']       = $this->config['timeout'];

        $this->financeSdk = new \WxworkFinanceSdk(
            $this->config['corpid'],
            $this->config['secret'],
            $options
        );
    }
}
