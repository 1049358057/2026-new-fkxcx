<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
error_reporting(0);
include __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
$code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : exit('{"code":-1,"msg":"参数不完整！"}');
$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}

$openid = $wxLogin['openid'];

$tutorial_content = isset($conf['tutorial_content']) ? $conf['tutorial_content'] : '';
$result = array(
    'code' => 200,
    'msg' => '获取成功',
    'tutorial_content' => $tutorial_content
);

exit(json_encode($result)); 