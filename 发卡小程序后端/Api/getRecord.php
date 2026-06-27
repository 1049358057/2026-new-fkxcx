<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
error_reporting(0);
include __DIR__ . '/../includes/common.php';
require __DIR__ . '/../includes/wxLogin.php';
$code = isset($_GET['code']) && !empty($_GET['code']) ? $_GET['code'] : exit(json_encode(array('code' => -1, 'msg' => '缺失code参数值')));

$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}
$wxOpenid = $wxLogin['openid'];

$myUser = $DB->get_row("select * from kami_user where openid='{$wxOpenid}'");
if (!$myUser) {
    $spl = "insert into `kami_user` (`gtkid`,`openid`,`rate`,`addtime`,`lasttime`,`ip`) values ('" . getGTK($wxOpenid) . "','" . $wxOpenid . "',100,'" . $date . "','" . $date . "','" . real_ip() . "')";
    if (!$DB->query($spl)) {
        exit('{"code":-1,"msg":"注册失败！"}');
    }
    
    $myUser = $DB->get_row("select * from kami_user where openid='{$wxOpenid}'");
}

$mode = (int)$conf['examine'];
$datas['code'] = 200;
$datas['userid'] = $myUser['gtkid'];
$datas['examine'] = $mode;
$datas['recordList'] = [];


error_log("查询记录 - openid: {$wxOpenid}");

$sql = "select * from kami_faka where users='{$wxOpenid}' and usetime is not null ORDER BY usetime DESC";
error_log("查询SQL: " . $sql);

$recordDatas = $DB->query($sql);
if ($recordDatas) {
    $count = 0;
    while ($item = $DB->fetch($recordDatas)) {
        $count++;
        error_log("记录项 {$count}: " . json_encode($item));
        $name = $DB->get_row("select * from kami_class where cid='{$item['cid']}'");
        if ($name) {
            $datas['recordList'][] = [
                'cid' => $item['cid'],
                'name' => $name['name'],
                'usetip' => $name['usetip'],
                'km' => $item['km'],
                'usetime' => $item['usetime'],
                'mode' => $item['mode'],
            ];
        }
    }
    error_log("最终返回记录数: " . count($datas['recordList']));
}

exit(json_encode($datas));
