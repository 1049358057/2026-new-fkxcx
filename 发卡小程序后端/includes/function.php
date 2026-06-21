<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
function curl_get($url)
{
$ch=curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1');
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$content=curl_exec($ch);
curl_close($ch);
return($content);
}
function real_ip(){
$ip = $_SERVER['REMOTE_ADDR'];
if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
	foreach ($matches[0] AS $xip) {
		if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
			$ip = $xip;
			break;
		}
	}
}
return $ip;
}
function get_ip_city($ip)
{
    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';
    $city = curl_get($url . $ip);
	$city = mb_convert_encoding($city, "UTF-8", "GB2312");
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['pro'].$city['city'];
    } else {
        $location = $city['pro'];
    }
	if($location){
		return $location;
	}else{
		return false;
	}
}
function daddslashes($string, $force = 0, $strip = FALSE) {
	
	$magic_quotes_gpc = (version_compare(PHP_VERSION, '5.4.0') >= 0) ? false : (function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false);
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', $magic_quotes_gpc);
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
function getHtmlCode($value, $html = false)
{
    $value = stripslashes($value);
    if ($html) {
        $value = htmlspecialchars($value);
    }
    return $value;
}
function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}
function checkEmail($value)
{
	if (preg_match("/^[\w\.\-]+@\w+([\.\-]\w+)*\.\w+$/", $value) && strlen($value) <= 60) {
		return true;
	} else {
		return false;
	}
}




function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed[mt_rand(0, $max)];
	}
	return $hash;
}
function showmsg($content = '未知的异常',$type = 4,$back = false)
{
switch($type)
{
case 1:
	$panel="success";
break;
case 2:
	$panel="info";
break;
case 3:
	$panel="warning";
break;
case 4:
	$panel="danger";
break;
}

echo '<div class="panel panel-'.$panel.'">
      <div class="panel-heading">
        <h3 class="panel-title">提示信息</h3>
        </div>
        <div class="panel-body">';
echo $content;

if ($back) {
	echo '<hr/><a href="'.$back.'"><< 返回上一页</a>';
}
else
    echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';

echo '</div>
    </div>';
exit;
}
function saveSetting($k, $v){
	global $DB;
	$v = daddslashes($v);
	return $DB->query("REPLACE INTO site_config SET v='$v',k='$k'");
}
function getDatePoint()
{
	global $DB;
	global $conf;
	$_var_2 = $DB->get_column("SELECT v FROM site_config WHERE k='datepoint' limit 1");
	$_var_4 = @unserialize($_var_2);
	if (!isset($_var_4[0]) || $_var_4[0]["date"] != date("md", strtotime("-1 day"))) {
		$_var_5 = date("Y-m-d") . " 00:00:00";
		$_var_6 = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
		$_var_7 = $DB->count("SELECT count(*) FROM site_report WHERE addtime>='" . $_var_6 . "' AND addtime<'" . $_var_5 . "'");
		$_var_8 = $DB->count("SELECT count(*) FROM site_report WHERE addtime>='" . $_var_6 . "' AND addtime<'" . $_var_5 . "' AND `zt`=1");
		$_var_9 = array();
		$_var_9[] = array("date" => date("md", strtotime("-1 day")), "analysis" => $_var_7, "success" => $_var_8);
		$_var_10 = 1;
		while ($_var_10 < 7) {
			if (!$_var_4[$_var_10 - 1]) {
				break;
			}
			$_var_9[$_var_10] = $_var_4[$_var_10 - 1];
			$_var_10 = $_var_10 + 1;
		}
		$_var_2 = serialize($_var_9);
		saveSetting("datepoint", $_var_2);
		$_var_4 = $_var_9;
	}
	$_var_4 = array_reverse($_var_4);
	$_var_13 = array("date" => array(), "analysis" => array(), "success" => array());
	$_var_10 = 1;
	foreach ($_var_4 as $_var_14) {
		$_var_13["date"][] = array($_var_10, $_var_14["date"]);
		$_var_13["analysis"][] = array($_var_10, $_var_14["analysis"]);
		$_var_13["success"][] = array($_var_10, $_var_14["success"]);
		$_var_10 = $_var_10 + 1;
	}
	return $_var_13;
}
function getkm($len = 18)
{
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	$strlen = strlen($str);
	$randstr = "";
	for ($i = 0; $i < $len; $i++) {
		$randstr .= $str[mt_rand(0, $strlen - 1)];
	}
	return $randstr;
}
function remove_xss($val)
{
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	
	
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); 
		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); 
	}
	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	$ra = array_merge($ra1, $ra2);
	$found = true; 
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(&#0{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); 
			$val = preg_replace($pattern, $replacement, $val); 
			if ($val_before == $val) {
				
				$found = false;
			}
		}
	}
	return $val;
}
?>