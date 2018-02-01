<?php

namespace Aliyun\Client;

use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;

/**
 * Created by AliyunSms.php.
 * User: yaoyiqiang
 * Date: 2018/1/31
 * Time: PM4:45
 */
class AliyunSmsClient
{
    static $acsClient = null;

    /**
     * 发送短信
     * @param $phone
     * @param $signName
     * @param $templateCode
     * @param array $templateParam
     * @param null $outId
     * @param null $SmsUpExtendCode
     * @return mixed
     */
    public static function sendSms($phone, $signName, $templateCode, array $templateParam, $outId = null, $SmsUpExtendCode = null)
    {
        Config::load();
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($signName);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($templateCode);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        if ($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }
        // 可选，设置流水号
        if ($outId) {
            $request->setOutId($outId);
        }
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        if ($SmsUpExtendCode) {
            $request->setSmsUpExtendCode($SmsUpExtendCode);
        }
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

    /**
     * 批量发送短信
     * @param array $phones
     * @param array $signNames
     * @param $templateCode
     * @param array $templateParams
     * @return mixed
     */
    public static function sendBatchSms(array $phones, array $signNames, $templateCode, array $templateParams)
    {
        Config::load();
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendBatchSmsRequest();

        // 必填:待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumberJson(json_encode($phones));

        // 必填:短信签名-支持不同的号码发送不同的短信签名
        $request->setSignNameJson(json_encode($signNames));

        // 必填:短信模板-可在短信控制台中找到
        $request->setTemplateCode($templateCode);

        // 必填:模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParamJson(json_encode($templateParams));

        // 可选-上行短信扩展码(扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段)
        // $request->setSmsUpExtendCodeJson("[\"90997\",\"90998\"]");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

    /**
     * 短信发送记录查询
     * @param $phone
     * @param string $selectData
     * @param int $count
     * @param int $page
     * @param null $outId
     * @return mixed
     */
    public static function querySendDetails($phone, $selectData = "20180131", $count = 10, $page = 1, $outId = null)
    {
        Config::load();
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();

        // 必填，短信接收号码
        $request->setPhoneNumber($phone);

        // 必填，短信发送日期，格式Ymd，支持近30天记录查询
        $request->setSendDate($selectData);

        // 必填，分页大小
        $request->setPageSize($count);

        // 必填，当前页码
        $request->setCurrentPage($page);

        // 选填，短信发送流水号
        if ($outId) {
            $request->setBizId($outId);
        }

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

    //实例化
    private static function getAcsClient()
    {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        //TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = \Illuminate\Support\Facades\Config::get('sms.AccessKeyId'); // AccessKeyId

        $accessKeySecret = \Illuminate\Support\Facades\Config::get('sms.AccessKeySecret'); // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        if (static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }
}