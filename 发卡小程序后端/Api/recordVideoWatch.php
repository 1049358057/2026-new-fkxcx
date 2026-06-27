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

$code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : exit('{"code":-1,"msg":"参数不完整！"}');
$cid = isset($_REQUEST['cid']) && !empty($_REQUEST['cid']) ? $_REQUEST['cid'] : exit('{"code":-1,"msg":"参数不完整！"}');
$wxLogin = wxLogin::wx_login($code);
$wxLogin = json_decode($wxLogin, true);
if ($wxLogin['code'] != 200) {
    exit('{"code":-1,"msg":"' . $wxLogin['msg'] . '！"}');
}

$openid = $wxLogin['openid'];


$daily_limit = isset($conf['daily_video_limit']) ? intval($conf['daily_video_limit']) : 10;


$interval_limit = isset($conf['interval_video_limit']) ? intval($conf['interval_video_limit']) : 3;
$interval_minutes = isset($conf['interval_minutes']) ? intval($conf['interval_minutes']) : 30;


$today = date('Y-m-d');
$daily_record = $DB->get_row("select * from kami_daily_video where openid='{$openid}' and watch_date='{$today}'");

if ($daily_record) {
    if ($daily_record['watch_count'] >= $daily_limit) {
        exit(json_encode(array('code' => -1, 'msg' => "您今日观看视频次数已达上限（{$daily_limit}次），请明天再来！", 'daily_limit' => true)));
    }
    
    
    $new_count = $daily_record['watch_count'] + 1;
    $DB->query("update kami_daily_video set watch_count={$new_count} where id={$daily_record['id']}");
} else {
    
    $DB->query("insert into kami_daily_video (openid, watch_date, watch_count) values ('{$openid}', '{$today}', 1)");
    $new_count = 1;
}


$interval_record = $DB->get_row("select * from kami_interval_video where openid='{$openid}'");
$now = strtotime($date);

if ($interval_record) {
    $start_time = strtotime($interval_record['start_time']);
    $time_diff = ($now - $start_time) / 60; 
    
    if ($time_diff < $interval_minutes) {
        
        if ($interval_record['watch_count'] >= $interval_limit) {
            
            $next_available = date('Y-m-d H:i:s', $start_time + $interval_minutes * 60);
            exit(json_encode(array(
                'code' => -1, 
                'msg' => "您在{$interval_minutes}分钟内观看视频次数已达上限（{$interval_limit}次），请在{$next_available}后再试！", 
                'interval_limit' => true
            )));
        }
        
        
        $interval_count = $interval_record['watch_count'] + 1;
        $DB->query("update kami_interval_video set watch_count={$interval_count} where id={$interval_record['id']}");
    } else {
        
        $DB->query("update kami_interval_video set start_time='{$date}', watch_count=1 where id={$interval_record['id']}");
        $interval_count = 1;
    }
} else {
    
    $DB->query("insert into kami_interval_video (openid, start_time, watch_count) values ('{$openid}', '{$date}', 1)");
    $interval_count = 1;
}


$class_info = $DB->get_row("select * from kami_class where active=1 and cid={$cid}");
if (!$class_info) {
    exit(json_encode(array('code' => -1, 'msg' => '当前分类不存在')));
}

$required_times = $class_info['video_times'];


$video_record = $DB->get_row("select * from kami_video_record where openid='{$openid}' and cid={$cid}");

if ($video_record) {
    
    $current_times = $video_record['watch_times'] + 1;
    $status = ($current_times >= $required_times) ? 1 : 0;
    
    $DB->query("update kami_video_record set watch_times={$current_times}, last_watch_time='{$date}', status={$status} where id={$video_record['id']}");
} else {
    
    $current_times = 1;
    $status = ($current_times >= $required_times) ? 1 : 0;
    
    $DB->query("insert into kami_video_record (openid, cid, watch_times, last_watch_time, status) values ('{$openid}', {$cid}, {$current_times}, '{$date}', {$status})");
}


$result = array(
    'code' => 200,
    'msg' => '记录成功',
    'data' => array(
        'watched_times' => $current_times,
        'required_times' => $required_times,
        'is_completed' => ($current_times >= $required_times) ? true : false,
        'remaining_times' => max(0, $required_times - $current_times),
        'daily_watch_count' => $new_count,
        'daily_limit' => $daily_limit,
        'daily_remaining' => max(0, $daily_limit - $new_count),
        'interval_watch_count' => $interval_count,
        'interval_limit' => $interval_limit,
        'interval_minutes' => $interval_minutes
    )
);

exit(json_encode($result)); 