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
$code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : exit('{"code":-1,"msg":"参数补齐！"}');
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
}
$DB->query("update kami_user set lasttime='$date' where openid='{$wxOpenid}'");

$rate = $myUser['rate'] / 100;
$daily_limit = isset($conf['daily_video_limit']) ? intval($conf['daily_video_limit']) : 10;
$today = date('Y-m-d');
$daily_record = $DB->get_row("select * from kami_daily_video where openid='{$wxOpenid}' and watch_date='{$today}'");
$daily_watch_count = $daily_record ? $daily_record['watch_count'] : 0;
$interval_limit = isset($conf['interval_video_limit']) ? intval($conf['interval_video_limit']) : 3;
$interval_minutes = isset($conf['interval_minutes']) ? intval($conf['interval_minutes']) : 30;

$classDatas['code'] = 200;
$class = $DB->query('select * from kami_class where active=1 order by sort asc');
foreach ($class as $item) {
    $video_record = $DB->get_row("select * from kami_video_record where openid='{$wxOpenid}' and cid='{$item['cid']}'");
    $watched_times = $video_record ? $video_record['watch_times'] : 0;
    $video_status = $video_record ? $video_record['status'] : 0;
    
    $classDatas['class'][] = [
        'cid' => $item['cid'],
        'name' => $item['name'] . ($conf['examine'] == '1' ? '-' . round($item['money'] * $rate, 2) . '元' : ''),
        'payName' => $item['name'],
        'introduce' => $item['introduce'],
        'usetip' => $item['usetip'],
        'video_times' => intval($item['video_times']),
        'watched_times' => $watched_times,
        'video_status' => $video_status,
        'image' => $item['image'] ? $item['image'] : '',
    ];
}
$classDatas['data']['xcx_name'] = $conf['xcx_name'];
$classDatas['data']['adVideoId'] = $conf['adVideoId'];
$classDatas['data']['adVideoTip'] = $conf['adVideoTip'];
$classDatas['data']['shareTip'] = $conf['shareTip'];
$classDatas['data']['xcxappid'] = $conf['xcxappid'];
$classDatas['data']['xcxpath'] = $conf['xcxpath'];
$classDatas['data']['ruleImg'] = $conf['ruleImg'];
$classDatas['data']['contact'] = $conf['contact'];
$classDatas['data']['examine'] = (int)$conf['examine'];
$classDatas['data']['shareTitle'] = $conf['shareTitle'];
$classDatas['data']['shareImg'] = $conf['shareImg'];
$classDatas['data']['gl1'] = $conf['gl1'];
$classDatas['data']['appid1'] = $conf['appid1'];
$classDatas['data']['path1'] = $conf['path1'];
$classDatas['data']['glimg1'] = $conf['glimg1'];
$classDatas['data']['gl2'] = $conf['gl2'];
$classDatas['data']['appid2'] = $conf['appid2'];
$classDatas['data']['path2'] = $conf['path2'];
$classDatas['data']['glimg2'] = $conf['glimg2'];
$classDatas['data']['gl3'] = $conf['gl3'];
$classDatas['data']['appid3'] = $conf['appid3'];
$classDatas['data']['path3'] = $conf['path3'];
$classDatas['data']['glimg3'] = $conf['glimg3'];
$classDatas['data']['daily_video_limit'] = $daily_limit;
$classDatas['data']['daily_watch_count'] = $daily_watch_count;
$classDatas['data']['daily_remaining'] = max(0, $daily_limit - $daily_watch_count);
$classDatas['data']['interval_video_limit'] = $interval_limit;
$classDatas['data']['interval_minutes'] = $interval_minutes;

exit(json_encode($classDatas));