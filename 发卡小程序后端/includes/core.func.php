<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
function setClassSort($cid, $sortType = 0)
{
    global $DB;

    
    $currentClass = $DB->get_row("select * from kami_class where cid='" . $cid . "' limit 1");
    if (!$currentClass) {
        return false;
    }
    $currentSort = $currentClass["sort"];

    switch ($sortType) {
        case 0: 
            $topClass = $DB->get_row("select cid,sort from kami_class order by sort asc limit 1");
            if ($topClass && $topClass['cid'] != $cid) {
                $DB->query("UPDATE kami_class SET sort=sort+1 WHERE sort<" . $currentSort);
                $DB->query("UPDATE kami_class SET sort=" . $topClass["sort"] . " WHERE cid='" . $cid . "'");
            }
            return true;

        case 1: 
            $upperClass = $DB->get_row("select cid,sort from kami_class where sort<'" . $currentSort . "' order by sort desc limit 1");
            if ($upperClass) {
                $DB->query("UPDATE kami_class SET sort=" . $upperClass["sort"] . " WHERE cid='" . $cid . "'");
                $DB->query("UPDATE kami_class SET sort=" . $currentSort . " WHERE cid='" . $upperClass["cid"] . "'");
            }
            return true;

        case 2: 
            $lowerClass = $DB->get_row("select cid,sort from kami_class where sort>'" . $currentSort . "' order by sort asc limit 1");
            if ($lowerClass) {
                $DB->query("UPDATE kami_class SET sort=" . $lowerClass["sort"] . " WHERE cid='" . $cid . "'");
                $DB->query("UPDATE kami_class SET sort=" . $currentSort . " WHERE cid='" . $lowerClass["cid"] . "'");
            }
            return true;

        case 3: 
            $bottomClass = $DB->get_row("select cid,sort from kami_class order by sort desc limit 1");
            if ($bottomClass && $bottomClass['cid'] != $cid) {
                $DB->query("UPDATE kami_class SET sort=sort-1 WHERE sort>" . $currentSort);
                $DB->query("UPDATE kami_class SET sort=" . $bottomClass["sort"] . " WHERE cid='" . $cid . "'");
            }
            return true;

        default:
            return false;
    }
}



function getGTK($skey)
{
    $hash = 5381;
    for ($i = 0; $i < strlen($skey); ++$i) {
        $hash += ($hash << 5) + utf8_unicode($skey[$i]);
    }
    return $hash & 0x7fffffff;
}

function utf8_unicode($c)
{
    switch (strlen($c)) {
        case 1:
            return ord($c);
        case 2:
            $n = (ord($c[0]) & 0x3f) << 6;
            $n += ord($c[1]) & 0x3f;
            return $n;
        case 3:
            $n = (ord($c[0]) & 0x1f) << 12;
            $n += (ord($c[1]) & 0x3f) << 6;
            $n += ord($c[2]) & 0x3f;
            return $n;
        case 4:
            $n = (ord($c[0]) & 0x0f) << 18;
            $n += (ord($c[1]) & 0x3f) << 12;
            $n += (ord($c[2]) & 0x3f) << 6;
            $n += ord($c[3]) & 0x3f;
            return $n;
    }
}

?>