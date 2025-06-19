# 微信会话内容存档

## 要求
* 需要PHP扩展 ext-wxwork_finance_sdk 或者 ext-ffi，二选一
* 1. ext-wxwork_finance_sdk 安装详见: https://github.com/pangdahua/php7-wxwork-finance-sdk
* 2. ext-ffi PHP编译安装时 `—with-ffi`

> [安装PHP扩展教程 wxwork_finance_sdk、ffi](https://github.com/majiameng/wework-finance-sdk-php/wiki/installed-extension)

## 使用
```
## 企业配置
$corpConfig = [
    'corpid'       => 'xxxx',
    'secret'       => 'xxxx',
    'private_keys' => [
        '密钥版本号' => '私钥',
    ],
];

## 包配置
$srcConfig = [
    'default'   => 'php-ext',
    'providers' => [
        'php-ext' => [
            'driver' => \tinymeng\WeWorkFinanceSDK\Provider\PHPExtProvider::class,
        ],
        'php-ffi' => [
            'driver' => \tinymeng\WeWorkFinanceSDK\Provider\FFIProvider::class,
        ],
    ],
];

## 1、实例化
$sdk = MoChat\WeWorkFinanceSDK\WxFinanceSDK::init($corpConfig, $srcConfig);

## 获取聊天记录
$chatData = $sdk->getDecryptChatData($seq, $limit);

## 下载媒体资源media
$medium = $sdk->getDownloadMediaData($object, $msgType)
```

## 测试

请查看 [实例文件](https://github.com/majiameng/wework-finance-sdk-php/blob/master/example/wx.php)

* php ./example/wx.php


## FFI 预加载
* 可自己行修改 php-ffi.driver，独立C的头文件到 `opcache.preload`
