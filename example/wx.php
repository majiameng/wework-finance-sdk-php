<?php

use tinymeng\WeWorkFinanceSDK\Exception\FinanceSDKException;
use tinymeng\WeWorkFinanceSDK\Provider\FFIProvider;
use tinymeng\WeWorkFinanceSDK\Provider\PHPExtProvider;
use tinymeng\WeWorkFinanceSDK\WxFinanceSDK;

require_once __DIR__ . '/vendor/autoload.php';

## 企业配置
$corpConfig = [
    'corpid'       => '',
    'secret'       => '',
    'private_keys' => [
        1 => '-----BEGIN PRIVATE KEY-----
-----END PRIVATE KEY-----',
    ],
];

## 包配置
$srcConfig = [
    'default'   => 'php-ffi',
    'providers' => [
        'php-ext' => [
            'driver' => PHPExtProvider::class,
        ],
        'php-ffi' => [
            'driver' => FFIProvider::class,
        ],
    ],
];

$seq = $_GET['seq']??1;
$limit = $_GET['limit']??10;

try {
    $wxFinanceSDK = WxFinanceSDK::init($corpConfig,$srcConfig);
    // 获取会话记录数据(解密)
    $list = $wxFinanceSDK->getDecryptChatData($seq,$limit);

    foreach ($list as $key=>$item){
        if($wxFinanceSDK->isMedia($item['msgtype'])){
            // 下载媒体资源
            $list[$key]['media_path'] = $wxFinanceSDK->getDownloadMediaData($item[$item['msgtype']],$item['msgtype']);
        }
    }
    var_dump($list);

}catch (FinanceSDKException $exception){
    echo $exception->getMessage();exit();
}

