# 微信会话内容存档

## 要求
* 需要PHP扩展 ext-wxwork_finance_sdk 或者 ext-ffi，二选一
* ext-wxwork_finance_sdk 安装详见: https://github.com/pangdahua/php7-wxwork-finance-sdk
* ext-ffi PHP编译安装时 `—with-ffi`

## 使用
```
## 企业配置
$corpConfig = [
    'corpid'       => 'xxxx',k
    'secret'       => 'xxxx',
    'private_keys' => [
        '密钥版本号' => '私钥',
    ],
];

## 1、实例化
$sdk = MoChat\WeWorkFinanceSDK\WxFinanceSDK::init($corpConfig, $srcConfig);

## 获取聊天记录
$chatData = $sdk->getDecryptChatData($seq, $limit);

## 解析media
$medium = $sdk->getMediaData($sdkFileId, $ext)
```

## 测试
* php./example/wx.php

## FFI 预加载
* 可自己行修改 php-ffi.driver，独立C的头文件到 `opcache.preload`