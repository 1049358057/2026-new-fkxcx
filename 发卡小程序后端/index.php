<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
if(!file_exists('install/install.lock')){
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>灵思发卡</title>
</head>
<body>
    程序还没安装，访问 http://blog.ailingsi.top/
</body>
</html>
HTML;
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>灵思发卡</title>
</head>
<body> 
    后台为：http://你的域名/admin<br> 左灵右思 <a href="http://blog.ailingsi.top">访问</a> 
</body>
</html>