<?php
/**
 * Created by index.php.
 * User: yaoyiqiang
 * Date: 2018/1/31
 * Time: PM5:33
 */
require_once "./vendor/autoload.php";

use Aliyun\Client\AliyunSmsClient;

$templateParams = [
    "code" => "123456",
    "product" => "博普云"
];
//$response = AliyunSmsClient::sendSms("13699293644","博普云","SMS_33315280",$templateParams);
//$response = AliyunSmsClient::querySendDetails("13699293644");
var_dump(json_encode($response));