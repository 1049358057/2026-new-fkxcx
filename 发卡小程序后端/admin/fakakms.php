
<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
include('../includes/common.php');
$title = '卡密列表';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '<div class="modal fade" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索卡密</h4>
      </div>
      <div class="modal-body">
      <form action="fakakms.php" method="GET">
<input type="hidden" name="cid" value="';;
error_reporting(0);
echo $_GET['cid'];
echo '"><br/>
<input type="text" class="form-control" name="kw" placeholder="请输入卡密"><br/>
<input type="submit" class="btn btn-primary btn-block" value="搜索"></form>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';
$rs = $DB->query('SELECT * FROM kami_class WHERE active=1 order by sort asc');
$select = '<option value="0">请选择商品分类</option>';
while ($res = $DB->fetch($rs)) {
    $select .= '<option value="' . $res['cid'] . '">' . $res['name'] . '</option>';
}
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my == 'add') {
        if (isset($_GET['cid'])) {$cid=intval($_GET['cid']);
            $row=$DB->get_row('select * from kami_class where cid=\''.$cid.'\' limit 1');
            $shopname='<option value="'.$cid.'">'.$row['name'].'</option>';
            $cid=$row['cid'];
        } else {$cid=0;
        }
    echo '<div class="container page-content">
        <div class="row">
            <div class="col-12">
                <!-- 页面标题 -->
                <div class="glass-card fade-in-up mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-plus-circle fa-2x text-primary me-3"></i>
                            <div>
                                <h2 class="mb-1">添加卡密</h2>
                                <p class="text-muted mb-0">批量添加卡密到指定分类</p>
                            </div>
                        </div>
                        <a href="' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'fakalist.php') . '" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>返回列表
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- 添加表单 -->
                <div class="glass-card fade-in-up">
                    <form action="./fakakms.php?my=add_submit" method="POST" onsubmit="return checkAdd()">
                        <input type="hidden" name="backurl" value="' . $_SERVER['HTTP_REFERER'] . '"/>

                        <!-- 选择分类 -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-layer-group me-2 text-primary"></i>选择分类
                            </label>
                            <select id="cid" name="cid" class="form-control" default="' . $cid . '" required>
                                ' . $select . '
                            </select>
                            <div class="form-text">请选择要添加卡密的商品分类</div>
                        </div>

                        <!-- 卡密列表 -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-list me-2 text-success"></i>卡密列表
                            </label>
                            <textarea class="form-control" id="kms" name="kms" rows="12"
                                placeholder="请输入卡密，一行一张卡&#10;&#10;示例：&#10;ABCD-1234-EFGH&#10;WXYZ-5678-IJKL&#10;MNOP-9012-QRST"
                                required></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                每行一张卡密，支持任意格式。建议使用统一的格式便于管理。
                            </div>
                        </div>

                        <!-- 选项设置 -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cogs me-2"></i>添加选项
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_check_repeat" name="is_check_repeat" value="1" checked>
                                        <label class="form-check-label" for="is_check_repeat">
                                            <i class="fas fa-shield-alt me-1 text-warning"></i>
                                            检查重复的卡密
                                        </label>
                                        <div class="form-text">开启后会自动检测并跳过重复的卡密</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作按钮 -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    <i class="fas fa-check me-2"></i>确认添加
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="reset" class="btn btn-outline-secondary w-100 btn-lg">
                                    <i class="fas fa-redo me-2"></i>重新填写
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 使用说明 -->
                <div class="glass-card fade-in-up mt-4">
                    <h5 class="mb-3">
                        <i class="fas fa-question-circle me-2 text-info"></i>使用说明
                    </h5>
                    <div class="alert alert-info">
                        <h6>卡密格式要求：</h6>
                        <ul class="mb-2">
                            <li>每行一张卡密</li>
                            <li>支持任意字符组合</li>
                            <li>建议使用统一格式，如：XXXX-XXXX-XXXX</li>
                            <li>避免使用特殊字符和空格</li>
                        </ul>
                        <h6>注意事项：</h6>
                        <ul class="mb-0">
                            <li>添加前请确认分类选择正确</li>
                            <li>建议开启重复检查避免重复添加</li>
                            <li>大批量添加时请分批进行</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
';
} else {
    if ($my == 'add_submit') {
        $cid = intval($_POST['cid']);
        $kms = $_POST['kms'];
        $is_check_repeat = $_POST['is_check_repeat'];
        if (($cid == NULL || $kms == NULL)) {
            showmsg('请确保各项不能为空！', 3);
        } else {
           $kms = str_replace( "\r\n", '[br]', $kms);
            $match = explode('[br]', $kms);
            $c = 0;
            foreach ($match as $val) {
                $km = trim(daddslashes($val));
                if ($km != '') {
                    if ($is_check_repeat == 1) {
                        if (!$DB->get_row('select * from kami_faka where km=\'' . $km . '\' limit 1')) {
                            $sql = $DB->query('INSERT INTO `kami_faka` (`cid`,`km`,`addtime`) VALUES (\'' . $cid . '\',\'' . $km . '\',NOW())');
                           if($sql){
                                $c++;
                            }
                        }
                    }else{
                        $sql = $DB->query('INSERT INTO `kami_faka` (`cid`,`km`,`addtime`) VALUES (\'' . $cid . '\',\'' . $km . '\',NOW())');
                        if($sql){
                            $c++;
                        }
                    }
                }

            }
            showmsg('成功添加<b>' . $c . '</b>张卡密！<br/><br/><a href="' . $_POST['backurl'] . '">>>返回发卡库存列表</a>', 1);
        }
    } else {
        if ($my == 'del') {
            $id = $_GET['id'];
            $sql = $DB->query('DELETE FROM kami_faka WHERE kid=\'' . $id . '\'');
            exit('<script language=\'javascript\'>history.go(-1);</script>');
        } else {
            if ($my == 'qk') {
                $cid = intval($_GET['cid']);
                echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空卡密</h3></div>
<div class="panel-body box">
您确认要清空该商品的所有卡密吗？清空后无法恢复！<br><a href="./fakakms.php?my=qk2&cid=' . $cid . '">确认</a> | <a href="javascript:history.back();">返回</a></div></div>';
            } else {
                if ($my == 'qk2') {
                    $cid = intval($_GET['cid']);
                    echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空卡密</h3></div>
<div class="panel-body box">';
                    if ($DB->query('DELETE FROM kami_faka WHERE cid=\'' . $cid . '\'') == true) {
                        echo '<div class="box">清空成功.</div>';
                    } else {
                        echo '<div class="box">清空失败.</div>';
                    }
                    echo '<hr/><a href="./fakakms.php?cid=' . $cid . '">>>返回卡密列表</a></div></div>';
                } else {
                    if ($my == 'qkuse') {
                        $cid = intval($_GET['cid']);
                        echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空卡密</h3></div>
<div class="panel-body box">
您确认要清空所有卡密吗？清空后无法恢复！<br><a href="./fakakms.php?my=qkuse2&cid=' . $cid . '">确认</a> | <a href="javascript:history.back();">返回</a></div></div>';
                    } else {
                        if ($my == 'qkuse2') {
                            $cid = intval($_GET['cid']);
                            echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">清空卡密</h3></div>
<div class="panel-body box">';
                            if ($DB->query('DELETE FROM kami_faka WHERE cid=\'' . $cid . '\' and users!=0') == true) {
                                echo '<div class="box">清空成功.</div>';
                            } else {
                                echo '<div class="box">清空失败.</div>';
                            }
                            echo '<hr/><a href="./fakakms.php?cid=' . $cid . '">>>返回卡密列表</a></div></div>';
                        } else {
                            if ($my == 'del2') {
                                $checkbox = $_POST['checkbox'];
                                $i = 0;
                                foreach ($checkbox as $kid) {
                                    $DB->query('DELETE FROM kami_faka WHERE kid=\'' . $kid . '\' limit 1');
                                    $i = $i + 1;
                                }
                                exit('<script language=\'javascript\'>alert(\'成功删除' . $i . '张卡密\');history.go(-1);</script>');
                            } else {
                                if (isset($_GET['kw'])) {
                                    $sql = ' `cid`=\'' . $_GET['cid'] . '\' and `km`=\'' . $_GET['kw'] . '\' ';
                                    $link = '&cid=' . $_GET['cid'] . '&kw=' . $_GET['kw'];
                                    $row = $DB->get_row('select * from kami_class where cid=\'' . $_GET['cid'] . '\' limit 1');
                                } else {
                                    if (isset($_GET['kid'])) {
                                        $sql = ' `kid`=\'' . $_GET['kid'] . '\'';
                                        $link = '&kid=' . $_GET['kid'];
                                    } else {
                                        if (isset($_GET['users'])) {
                                            $sql = ' `users`=\'' . $_GET['users'] . '\'';
                                            $link = '&users=' . $_GET['users'];
                                        } else {
                                            if (isset($_GET['cid'])) {
                                                $cid = intval($_GET['cid']);
                                                $row = $DB->get_row('select * from kami_class where cid=\'' . $cid . '\' limit 1');
                                                if (!$row) {
                                                    showmsg('商品不存在', 3);
                                                }
                                                $sql = ' `cid`=\'' . $cid . '\'';
                                                $link = '&cid=' . $cid;
                                            } else {
                                                showmsg('商品不存在', 3);
                                            }
                                        }
                                    }
                                }
                                $numrows = $DB->count('SELECT count(*) from kami_faka WHERE' . $sql);
                                echo '<div class="panel panel-primary">
	<div class="panel-heading">
		';
                                echo $row['name'];
                                echo ' - 卡密库存列表
	</div>
	<div class="panel-body">
	<a href="fakakms.php?my=add&cid=';
                                echo $cid;
                                echo '" class="btn btn-success">加卡</a>
  <a href="fakakms.php?my=qk&cid=';
                                echo $cid;
                                echo '" class="btn btn-danger">清空</a>
  <a href="fakakms.php?my=qkuse&cid=';
                                echo $cid;
                                echo '" class="btn btn-danger">清空已使用</a>
  <a href="#" data-toggle="modal" data-target="#search" id="search" class="btn btn-primary">搜索</a>
  </div>
	<form name="form1" method="post" action="fakakms.php?my=del2">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>卡密</th><th>状态</th><th>添加时间</th><th>使用时间</th><th>操作</th></tr></thead>
          <tbody>
';
                                $pagesize = 30;
                                $pages = intval($numrows / $pagesize);
                                if ($numrows % $pagesize) {
                                    $pages = $pages + 1;
                                }
                                if (isset($_GET['page'])) {
                                    $page = intval($_GET['page']);
                                } else {
                                    $page = 1;
                                }
                                $offset = $pagesize * ($page - 1);
                                $rs = $DB->query('SELECT * FROM kami_faka WHERE' . $sql . ' order by kid desc limit ' . $offset . ',' . $pagesize);
                                $aaa = 'SELECT * FROM kami_faka WHERE' . $sql . ' order by kid desc limit ' . $offset . ',' . $pagesize;

                                while ($res = $DB->fetch($rs)) {
                                    if ($res['usetime'] == NULL) {
                                        $isuse = '<font color="green">未使用</font>';
                                    } else {
                                        $isuse = '<font color="red">已使用</font>(' . $res['users'] . ')';
                                    }

                                    echo '<tr><td><input type="checkbox" name="checkbox[]" id="list1" value="' . $res['kid'] . '" onClick="unselectall1()"><b>' . $res['km'] . '</b></td><td>' . $isuse . '</td><td>' . $res['addtime'] . '</td><td>' . $res['usetime'] . '</td><td><a href="./fakakms.php?my=del&id=' . $res['kid'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此卡密吗？\');">删除</a></td></tr>';
                                }
                                echo '          </tbody>
        </table>
<input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">&nbsp;全选&nbsp;
<input type="submit" name="Submit" value="删除选中">
</div>
</form>
';
                                echo '<ul class="pagination">';
                                $first = 1;
                                $prev = $page - 1;
                                $next = $page + 1;
                                $last = $pages;
                                if ($page > 1) {
                                    echo '<li><a href="fakakms.php?page=' . $first . $link . '">首页</a></li>';
                                    echo '<li><a href="fakakms.php?page=' . $prev . $link . '">&laquo;</a></li>';
                                } else {
                                    echo '<li class="disabled"><a>首页</a></li>';
                                    echo '<li class="disabled"><a>&laquo;</a></li>';
                                }
                                $i = 1;
                                while ($i < $page) {
                                    echo '<li><a href="fakakms.php?page=' . $i . $link . '">' . $i . '</a></li>';
                                    $i = $i + 1;
                                }
                                echo '<li class="disabled"><a>' . $page . '</a></li>';
                                if ($pages >= 10) {
                                    $s = 10;
                                } else {
                                    $s = $pages;
                                }
                                $i = $page + 1;
                                while ($i <= $s) {
                                    echo '<li><a href="fakakms.php?page=' . $i . $link . '">' . $i . '</a></li>';
                                    $i = $i + 1;
                                }
                                echo '';
                                if ($page < $pages) {
                                    echo '<li><a href="fakakms.php?page=' . $next . $link . '">&raquo;</a></li>';
                                    echo '<li><a href="fakakms.php?page=' . $last . $link . '">尾页</a></li>';
                                } else {
                                    echo '<li class="disabled"><a>&raquo;</a></li>';
                                    echo '<li class="disabled"><a>尾页</a></li>';
                                }
                                echo '</ul>';
                            }
                        }
                    }
                }
            }
        }
    }
}
echo '</div>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
var checkflag1 = "false";
function check1(field) {
if (checkflag1 == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag1 = "true";
return "false"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag1 = "false";
return "true"; }
}

function unselectall1()
{
    if(document.form1.chkAll1.checked){
	document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
	checkflag1 = "false";
    } 	
}

function checkAdd(){
	if($("#cid").val()==0||$("#cid").val()==null){
		layer.alert(\'请先选择分类\');return false;
	}
	if($("#kms").val()==\'\'){
		layer.alert(\'卡密列表不能为空\');return false;
	}
}
    
                var items = $("select[default]");
                for (i = 0; i < items.length; i++) {
                    $(items[i]).val($(items[i]).attr("default") || 0);
                }

</script>';
?>