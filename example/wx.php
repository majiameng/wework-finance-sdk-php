<?php

use tinymeng\WeWorkFinanceSDK\Exception\FinanceSDKException;
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
    /**
     * includePath（使用php-ffi扩展时）
     * 可选 ：默认使用组件内SDK（默认SDK只支持Liunx），如果想使用其他版本SDK，请填写对应SDK路径
     * 官网下载SDK https://developer.work.weixin.qq.com/document/path/91774
     */
    'includePath' => '',
];

## 包配置
$srcConfig = [
    'default'   => 'php-ext',// 两种方式的切换： php-ext 或 php-ffi
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

