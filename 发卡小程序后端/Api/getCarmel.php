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
$cid = isset($_GET['cid']) && !empty($_GET['cid']) ? $_GET['cid'] : exit(json_encode(array('code' => -1, 'msg' => '缺失cid参数值')));

$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}

$openid = $wxLogin['openid'];
$classCid = $DB->get_row("select * from kami_class where active=1 and cid={$cid}");
if (!$classCid) {
    exit(json_encode(array('code' => -1, 'msg' => '当前领取的卡密类型不存在')));
}
$cidCarmel = $DB->get_row("select * from kami_faka where cid={$cid} and usetime is null");
if (!$cidCarmel) {
    exit(json_encode(array('code' => -1, 'msg' => '此类型卡密已经被领完了，请联系管理员加卡密。')));
}
$carmel = $cidCarmel['km'];


if ($classCid['video_times'] > 0) {
    $video_record = $DB->get_row("select * from kami_video_record where openid='{$openid}' and cid={$cid}");
    if (!$video_record || $video_record['watch_times'] < $classCid['video_times'] || $video_record['status'] != 1) {
        $watched = $video_record ? $video_record['watch_times'] : 0;
        $remaining = $classCid['video_times'] - $watched;
        exit(json_encode(array('code' => -1, 'msg' => "您需要再观看{$remaining}次视频广告才能获取卡密！")));
    }
}



$mode = ($classCid['video_times'] == 0) ? 0 : 2;


error_log("准备更新卡密: kid={$cidCarmel['kid']}, cid={$cid}, openid={$openid}, mode={$mode}");

$update_sql = "update kami_faka set usetime='$date',users='$openid',mode=$mode where kid={$cidCarmel['kid']} and cid={$cid}";
error_log("更新SQL: " . $update_sql);

if ($DB->query($update_sql)) {
    
    $verify = $DB->get_row("select * from kami_faka where kid={$cidCarmel['kid']}");
    error_log("更新后验证: " . json_encode($verify));
    
    
    if ($classCid['video_times'] > 0) {
        $DB->query("update kami_video_record set watch_times=0, status=0 where openid='{$openid}' and cid={$cid}");
    }
    exit(json_encode(array('code' => 200, 'msg' => '领取成功', 'carmel' => $carmel)));
} else {
    error_log("更新失败: " . $DB->error());
    exit(json_encode(array('code' => -1, 'msg' => '领取失败，请稍后重试。')));
}


