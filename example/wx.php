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
];

$seq = $_GET['seq']??1;
$limit = $_GET['limit']??10;

try {
    $wxFinanceSDK = WxFinanceSDK::init($corpConfig);
    // 获取会话记录数据(解密)
    $list = $wxFinanceSDK->getDecryptChatData($seq,$limit);

    foreach ($list as $key=>$item){
        if($wxFinanceSDK->isMedia($item['msgtype'])){
            $list[$key]['media_path'] = $wxFinanceSDK->getDownloadMediaData($item[$item['msgtype']],$item['msgtype']);
        }
    }
    var_dump($list);

}catch (FinanceSDKException $exception){
    echo $exception->getMessage();exit();
}

