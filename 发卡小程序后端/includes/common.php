<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
define('IN_CRONLITE', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('TEMPLATE_ROOT',ROOT.'/template/');
define('SYS_KEY', '86b4aa7d2e16334e92126abb4703f918');
date_default_timezone_set("PRC");
$date = date('Y-m-d H:i:s');
session_start();

if(!file_exists('../install/install.lock')){
    header("Location: /install");
}

$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';

require SYSTEM_ROOT.'config.php';
include_once(SYSTEM_ROOT."db.class.php");
$DB=new DB($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);


$core_engine_file = SYSTEM_ROOT.'core_engine_prod.php';
if (!file_exists($core_engine_file)) {
    $core_engine_file = SYSTEM_ROOT.'core_engine.php';
}
require $core_engine_file;

$sql = $DB->query("select * from site_config");
while($r = $DB->fetch($sql)){
    $conf[$r['k']] = $r['v'];
}


$password_hash='!@#%!s!';
include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."core.func.php");
include_once(SYSTEM_ROOT."member.php");
?>