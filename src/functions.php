<?php

/**
 * 生成随机的短信验证码
 * @param int $length 短信验证码的位数
 * @return string
 */
if (!function_exists('randomSmsCode')) {

    function randomSmsCode($length = 6)
    {
        $key = '';
        $pattern = '1234567890';
        for ($i = 0; $i < $length; ++$i) {
            $key .= $pattern{mt_rand(0, 9)};    // 生成php随机数
        }
        return $key;
    }
}