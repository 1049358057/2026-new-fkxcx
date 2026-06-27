<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/


class wxLogin
{
    public static function wx_login($code)
    {
        include(__DIR__ . "/../Api/wechatConfig.php");

        if (empty($appid) || empty($secret)) {
            return json_encode(array('code' => -1, 'msg' => '请在后台完成小程序校验配置'));
        }

        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
        $datas = self::httpGetRequest($url);
        $datas = json_decode($datas, true);
        if (!$datas['openid']) {
            if ($datas['errcode'] == '-1') {
                return json_encode(array('code' => -1, 'msg' => '服务器繁忙，请尝试重新打开小程序！'));
            } else {
                return json_encode(array('code' => $datas['errcode'], 'msg' => '用户信息获取失败，请尝试重新打开小程序！'));
            }
        } else {
            return json_encode(array('code' => 200, 'msg' => '登录成功!', 'openid' => $datas['openid'], 'session_key' => $datas['session_key']));
        }

    }

    public static function httpGetRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $result;
    }
}