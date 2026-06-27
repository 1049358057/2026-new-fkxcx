<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
include("../includes/common.php");
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

@header('Content-Type: application/json; charset=UTF-8');

switch ($act) {
    case 'setClass':
        $cid = intval($_GET['cid']);
        $active = intval($_GET['active']);
        $DB->query("update kami_class set active='$active' where cid='{$cid}'");
        exit('{"code":0,"msg":"succ"}');
        break;
    case 'setClassSort':
        $cid = intval($_GET['cid']);
        $sort = intval($_GET['sort']);
        if (setClassSort($cid, $sort)) {
            exit('{"code":0,"msg":"succ"}');
        } else {
            exit('{"code":-1,"msg":"失败"}');
        }
        break;
    case 'getIntroduce':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit(json_encode(array('code' => 0, 'result' => $rows['introduce']), JSON_UNESCAPED_UNICODE));
        break;
    case 'setIntroduce':
        $cid = intval($_POST['cid']);
        $text = daddslashes($_POST['text']);
        $rows = $DB->query("update kami_class set introduce='$text' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getUseTip':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . UnicodeEncode($rows['usetip']) . '"}');
        break;
    case 'setUseTip':
        $cid = intval($_POST['cid']);
        $text = daddslashes($_POST['text']);
        $rows = $DB->query("update kami_class set usetip='$text' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getVideoTimes':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . $rows['video_times'] . '"}');
        break;
    case 'setVideoTimes':
        $cid = intval($_POST['cid']);
        $times = intval($_POST['times']);
        if ($times < 0) {
            exit('{"code":-1,"msg":"观看次数必须大于等于0次"}');
        }
        $rows = $DB->query("update kami_class set video_times='$times' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getRate':
        $id = intval($_POST['id']);
        $rows = $DB->get_row("select * from kami_user where id='$id' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前用户不存在！"}');
        exit('{"code":0,"result":"' . $rows['rate'] . '"}');
        break;
    case 'setRate':
        $id = intval($_POST['id']);
        $rate = intval($_POST['rate']);
        if ($rate > 100 || $rate < 0) {
            exit('{"code":-1,"msg":"填写范围1-100"}');
        }
        $rows = $DB->query("update kami_user set rate='$rate' where id='{$id}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'getKm':
        $trade_no = daddslashes($_POST['id']);
        $rows = $DB->get_row("select * from kami_faka where trade_no='$trade_no' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前卡密不存在！"}');
        exit('{"code":0,"result":"' . $rows['km'] . '"}');
        break;
    case 'getClassImage':
        $cid = intval($_POST['cid']);
        $rows = $DB->get_row("select * from kami_class where cid='$cid' limit 1");
        if (!$rows) exit('{"code":-1,"msg":"当前分类不存在！"}');
        exit('{"code":0,"result":"' . $rows['image'] . '"}');
        break;
    case 'setClassImage':
        $cid = intval($_POST['cid']);
        $image = daddslashes($_POST['image']);
        $rows = $DB->query("update kami_class set image='$image' where cid='{$cid}'");
        if (!$rows) exit('{"code":-1,"msg":"保存失败！"}');
        exit('{"code":0,"result":"succ"}');
        break;
    case 'uploadimg':
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
            exit('{"code":-1,"msg":"上传失败，请重试"}');
        }
        
        $file = $_FILES['file'];
        $allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
        
        if (!in_array($file['type'], $allowedTypes)) {
            exit('{"code":-1,"msg":"只允许上传图片文件（jpg、png、gif）"}');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            exit('{"code":-1,"msg":"图片大小不能超过5MB"}');
        }
        
        $uploadDir = ROOT . 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = date('YmdHis') . '_' . rand(1000, 9999) . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $url = $siteurl . '../uploads/' . $filename;
            exit(json_encode(array('code' => 0, 'url' => $url)));
        } else {
            exit('{"code":-1,"msg":"文件保存失败"}');
        }
        break;
    default:
        exit('{"code":-4,"msg":"No Act"}');
        break;
}


function UnicodeEncode($str)
{
    preg_match_all('/./u', $str, $matches);
    $unicodeStr = "";
    foreach ($matches[0] as $m) {
        $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
    }
    return $unicodeStr;
}
