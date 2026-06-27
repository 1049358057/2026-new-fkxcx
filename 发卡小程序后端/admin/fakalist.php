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
$title = '发卡库存管理';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '';
$rs = $DB->query('SELECT * FROM kami_class WHERE 1 order by sort asc');
$select = '<option value="0">所有</option>';
while ($res = $DB->fetch($rs)) {
    $select .= '<option value="' . $res['cid'] . '">' . $res['name'] . '</option>';
}
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my == 'move') {
    $type = $_POST['type'];
    if (!$type || $type == '批量操作') {
        exit('<script language=\'javascript\'>alert(\'请选择操作类型！\');history.go(-1);</script>');
    }
    $checkbox = $_POST['checkbox'];
    if (!$checkbox || !is_array($checkbox)) {
        exit('<script language=\'javascript\'>alert(\'请选择要操作的项目！\');history.go(-1);</script>');
    }

    $success_count = 0;
    $operation_name = '';

    foreach ($checkbox as $cid) {
        $cid = intval($cid); 
        if ($type == (-1)) {
            if ($DB->query('update kami_class set active=1 where cid=\'' . $cid . '\' limit 1')) {
                $success_count++;
            }
            $operation_name = '上架';
        } elseif ($type == (-2)) {
            if ($DB->query('update kami_class set active=0 where cid=\'' . $cid . '\' limit 1')) {
                $success_count++;
            }
            $operation_name = '下架';
        } elseif ($type == (-3)) {
            
            $DB->query('DELETE FROM kami_faka WHERE cid=\'' . $cid . '\'');
            if ($DB->query('DELETE FROM kami_class WHERE cid=\'' . $cid . '\' limit 1')) {
                $success_count++;
            }
            $operation_name = '删除';
        }
    }

    if ($success_count > 0) {
        exit('<script language=\'javascript\'>alert(\'成功' . $operation_name . '' . $success_count . '个分类！\');window.location.href=\'fakalist.php\';</script>');
    } else {
        exit('<script language=\'javascript\'>alert(\'操作失败，请重试！\');history.go(-1);</script>');
    }
} else {
    if ($_GET['cid']) {
        $cid = intval($_GET['cid']);
        $numrows = $DB->count('SELECT count(*) from kami_class where cid=\'' . $cid . '\'');
        $sql = ' cid=\'' . $cid . '\'';
    } else {
        $numrows = $DB->count('SELECT count(*) from kami_class where 1 ');
        $sql = ' active=1 or active=0';
    }
    echo '<div class="container page-content">
        <div class="row">
            <div class="col-12">
                <!-- 页面标题 -->
                <div class="glass-card fade-in-up mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-warehouse fa-2x text-primary me-3"></i>
                            <div>
                                <h2 class="mb-1">发卡库存管理</h2>
                                <p class="text-muted mb-0">管理卡密分类库存，查看销售情况</p>
                            </div>
                        </div>
                        <a href="./classlist.php" class="btn btn-outline-primary">
                            <i class="fas fa-layer-group me-2"></i>分类管理
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- 筛选和搜索区域 -->
                <div class="glass-card fade-in-up mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form action="fakalist.php" method="GET" class="filter-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fas fa-filter me-2 text-primary"></i>筛选分类
                                        </label>
                                        <select name="cid" class="form-control" default="' . $_GET['cid'] . '">
                                            ' . $select . '
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>筛选与搜索
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="./fakakms.php?my=add" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>添加卡密
                                </a>
                                <a href="./classlist.php" class="btn btn-outline-info">
                                    <i class="fas fa-cog me-2"></i>分类详情
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- 库存列表 -->
                <div class="glass-card fade-in-up">
                    <form name="form1" method="post" action="fakalist.php?my=move">
                        <div class="table-responsive">
                            <table class="table table-hover inventory-table">
                                <thead>
                                    <tr>
                                        <th width="80">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2" id="chkAll1">
                                                <span>ID</span>
                                            </div>
                                        </th>
                                        <th>分类名称</th>
                                        <th width="120">剩余卡密</th>
                                        <th width="120">已售出</th>
                                        <th width="120">状态</th>
                                        <th width="200">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
';
    $pagesize = 30;
    $pages = intval($numrows / $pagesize);
    if ($numrows % $pagesize) {
        $pages++;
    }
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    } else {
        $page = 1;
    }

    
    $link = '';
    if (isset($_GET['cid']) && $_GET['cid']) {
        $link = '&cid=' . intval($_GET['cid']);
    }
    $offset = $pagesize * ($page - 1);
    $rs = $DB->query('SELECT a.*,(select count(b.cid) from kami_faka as b where b.cid=a.cid and users IS NULL and usetime IS NULL) as leftcount,(select count(b.cid) from kami_faka as b where b.cid=a.cid and users IS NOT NULL) as sellcount FROM kami_class as a WHERE' . $sql . ' order by sort asc limit ' . $offset . ',' . $pagesize);
    while ($res = $DB->fetch($rs)) {
        echo '<tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="form-check-input" name="checkbox[]" value="' . $res['cid'] . '">
                        <span class="id-number fw-bold text-primary">' . $res['cid'] . '</span>
                    </div>
                </td>
                <td>
                    <div class="category-name">
                        <span class="fw-semibold">' . $res['name'] . '</span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-info fs-6 px-3 py-2">' . $res['leftcount'] . '</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-success fs-6 px-3 py-2">' . $res['sellcount'] . '</span>
                </td>
                <td class="text-center">
                    ' . ($res['active'] == 1 ?
                        '<span class="badge bg-success status-badge" style="cursor:pointer" onclick="setActive(' . $res['cid'] . ',0)">
                            <i class="fas fa-eye me-1"></i>上架中
                        </span>' :
                        '<span class="badge bg-warning status-badge" style="cursor:pointer" onclick="setActive(' . $res['cid'] . ',1)">
                            <i class="fas fa-eye-slash me-1"></i>已下架
                        </span>') . '
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="./fakakms.php?cid=' . $res['cid'] . '" class="btn btn-outline-info" title="查看卡密">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="./fakakms.php?my=add&cid=' . $res['cid'] . '" class="btn btn-outline-success" title="添加卡密">
                            <i class="fas fa-plus"></i>
                        </a>
                        <a href="./shopedit.php?my=delete&cid=' . $res['cid'] . '" class="btn btn-outline-danger" title="删除分类" onclick="return confirm(\'你确实要删除此商品吗？\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
';
    }
}
echo '                  </tbody>
                            </table>
                        </div>

                        <!-- 批量操作区域 -->
                        <div class="batch-operations mt-3 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fw-semibold">批量操作</span>
                                        <div class="vr"></div>
                                        <span class="text-muted selection-status">选择项目进行批量操作</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2 justify-content-end">
                                        <select name="type" class="form-select" style="width: auto;">
                                            <option selected>批量操作</option>
                                            <option value="-1">改为上架中</option>
                                            <option value="-2">改为已下架</option>
                                            <option value="-3">删除选中</option>
                                        </select>
                                        <button type="submit" name="Submit" class="btn btn-primary">
                                            <i class="fas fa-check me-2"></i>确定
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
';
?>

<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
// 改进的全选功能
$(document).ready(function() {
    // 全选/取消全选功能
    $('#chkAll1').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('input[name="checkbox[]"]').prop('checked', isChecked);
        updateBatchOperationStatus();
    });

    // 单个复选框变化时更新全选状态
    $(document).on('change', 'input[name="checkbox[]"]', function() {
        var totalCheckboxes = $('input[name="checkbox[]"]').length;
        var checkedCheckboxes = $('input[name="checkbox[]"]:checked').length;

        $('#chkAll1').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateBatchOperationStatus();
    });

    // 更新批量操作状态
    function updateBatchOperationStatus() {
        var checkedCount = $('input[name="checkbox[]"]:checked').length;
        var $batchArea = $('.batch-operations');
        var $submitBtn = $('button[name="Submit"]');

        if (checkedCount > 0) {
            $batchArea.removeClass('opacity-50');
            $submitBtn.prop('disabled', false);
            $('.text-muted').text('已选择 ' + checkedCount + ' 个项目');
        } else {
            $batchArea.addClass('opacity-50');
            $submitBtn.prop('disabled', true);
            $('.text-muted').text('选择项目进行批量操作');
        }
    }

    // 批量操作表单提交验证
    $('form[name="form1"]').on('submit', function(e) {
        var selectedCount = $('input[name="checkbox[]"]:checked').length;
        var operationType = $('select[name="type"]').val();

        if (selectedCount === 0) {
            e.preventDefault();
            alert('请选择要操作的项目！');
            return false;
        }

        if (!operationType || operationType === '批量操作') {
            e.preventDefault();
            alert('请选择操作类型！');
            return false;
        }

        // 删除操作需要确认
        if (operationType === '-3') {
            if (!confirm('确定要删除选中的 ' + selectedCount + ' 个分类吗？\n\n注意：删除分类会同时删除该分类下的所有卡密！')) {
                e.preventDefault();
                return false;
            }
        }

        // 显示加载状态
        var $submitBtn = $('button[name="Submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>处理中...');
    });

    // 初始化状态
    updateBatchOperationStatus();
});

// 保留原有的setActive函数
function setActive(cid,active) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=setClass&cid='+cid+'&active='+active,
        dataType : 'json',
        success : function(data) {
            window.location.reload();
        },
        error:function(data){
            alert('操作失败，请重试！');
            return false;
        }
    });
}

function setActive(cid,active) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=setClass&cid='+cid+'&active='+active,
        dataType : 'json',
        success : function(data) {
            window.location.reload();
        },
        error:function(data){
            alert('操作失败，请重试！');
            return false;
        }
    });
}

var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default")||0);
}
</script>

<?php

if ($pages > 1) {
    echo '<div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="分页导航">
                        <ul class="pagination pagination-lg">';

    $first = 1;
    $prev = $page - 1;
    $next = $page + 1;
    $last = $pages;

    
    if ($page > 1) {
        echo '<li class="page-item">
                <a class="page-link" href="fakalist.php?page=' . $first . $link . '" title="首页">
                    <i class="fas fa-angle-double-left"></i>
                </a>
              </li>';
        echo '<li class="page-item">
                <a class="page-link" href="fakalist.php?page=' . $prev . $link . '" title="上一页">
                    <i class="fas fa-angle-left"></i>
                </a>
              </li>';
    } else {
        echo '<li class="page-item disabled">
                <span class="page-link">
                    <i class="fas fa-angle-double-left"></i>
                </span>
              </li>';
        echo '<li class="page-item disabled">
                <span class="page-link">
                    <i class="fas fa-angle-left"></i>
                </span>
              </li>';
    }

    
    $start_page = max(1, $page - 2);
    $end_page = min($pages, $page + 2);

    
    if ($page <= 3) {
        $end_page = min($pages, 5);
    }

    
    if ($page > $pages - 3) {
        $start_page = max(1, $pages - 4);
    }

    
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
            echo '<li class="page-item active">
                    <span class="page-link">' . $i . '</span>
                  </li>';
        } else {
            echo '<li class="page-item">
                    <a class="page-link" href="fakalist.php?page=' . $i . $link . '">' . $i . '</a>
                  </li>';
        }
    }

    
    if ($page < $pages) {
        echo '<li class="page-item">
                <a class="page-link" href="fakalist.php?page=' . $next . $link . '" title="下一页">
                    <i class="fas fa-angle-right"></i>
                </a>
              </li>';
        echo '<li class="page-item">
                <a class="page-link" href="fakalist.php?page=' . $last . $link . '" title="尾页">
                    <i class="fas fa-angle-double-right"></i>
                </a>
              </li>';
    } else {
        echo '<li class="page-item disabled">
                <span class="page-link">
                    <i class="fas fa-angle-right"></i>
                </span>
              </li>';
        echo '<li class="page-item disabled">
                <span class="page-link">
                    <i class="fas fa-angle-double-right"></i>
                </span>
              </li>';
    }

    echo '      </ul>
                </nav>
            </div>
        </div>
    </div>';

    
    echo '<div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center mt-2">
                    <small class="text-muted">
                        第 ' . $page . ' 页，共 ' . $pages . ' 页，总计 ' . $numrows . ' 条记录
                    </small>
                </div>
            </div>
        </div>';
}
?>

<?php include './footer.php'; ?>
