<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
include('../includes/common.php');
$title = '用户信息';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

$my = isset($_GET['my']) ? $_GET['my'] : null;

if ($my == 'delete') {
    $id = $_GET['id'];
    $rows = $DB->get_row("select * from kami_user where id='$id' limit 1");
    if (!$rows)
        showmsg('当前记录不存在！', 3);
    $sql = "DELETE FROM kami_user WHERE id='$id'";
    if ($DB->query($sql))
        showmsg('删除成功！<br/><br/><a href="./ulist.php">>>返回用户列表</a>', 1);
    else
        showmsg('删除失败！' . $DB->error(), 4);
} elseif ($my == 'batch_delete' && $_POST['do'] == 'submit') {
    $ids = $_POST['ids'];
    if (empty($ids)) {
        showmsg('请选择要删除的用户！', 3);
    }
    $ids_str = implode(',', array_map('intval', $ids));
    $sql = "DELETE FROM kami_user WHERE id IN ($ids_str)";
    if ($DB->query($sql)) {
        $affected = $DB->affected();
        showmsg("批量删除成功！共删除了 $affected 个用户<br/><br/><a href=\"./ulist.php\">>>返回用户列表</a>", 1);
    } else {
        showmsg('批量删除失败！' . $DB->error(), 4);
    }
} else {
    echo '<div class="container page-content">
        <div class="row">
            <div class="col-12">
                <!-- 页面标题 -->
                <div class="glass-card fade-in-up mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x text-primary me-3"></i>
                        <div>
                            <h2 class="mb-1">用户管理中心</h2>
                            <p class="text-muted mb-0">管理系统用户，查看用户信息和操作记录</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- 搜索和筛选区域 -->
                <div class="glass-card fade-in-up mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <form action="ulist.php" method="GET" class="search-form">
                                <input type="hidden" name="my" value="search">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-search me-2 text-primary"></i>搜索字段
                                        </label>
                                        <select name="column" class="form-control">
                                            <option value="gtkid"' . (isset($_GET['column']) && $_GET['column'] == 'gtkid' ? ' selected' : '') . '>用户ID</option>
                                            <option value="openid"' . (isset($_GET['column']) && $_GET['column'] == 'openid' ? ' selected' : '') . '>OpenID</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-keyboard me-2 text-success"></i>搜索内容
                                        </label>
                                        <input type="text" class="form-control" name="value" value="' . (isset($_GET['value']) ? htmlspecialchars($_GET['value']) : '') . '" placeholder="请输入搜索内容">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-2"></i>搜索
                                            </button>
                                            <a href="./ulist.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-redo me-2"></i>重置
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- 用户列表 -->
                <div class="glass-card fade-in-up">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>用户列表
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-count-info">';

    if ($my == 'search' && isset($_GET['column']) && isset($_GET['value']) && !empty($_GET['value'])) {
        $column = $_GET['column'];
        $value = $_GET['value'];
        
        if (!in_array($column, ['gtkid', 'openid'])) {
            $column = 'gtkid';
        }
        $sql = " `{$column}`='{$value}'";
        $numrows = $DB->count("SELECT count(*) from kami_user WHERE{$sql}");
        $con = '<span class="badge bg-info fs-6 px-3 py-2">包含 "' . htmlspecialchars($value) . '" 的共有 <strong>' . $numrows . '</strong> 个用户</span>';
        $link = '&my=search&column=' . urlencode($column) . '&value=' . urlencode($value);
    } else {
        $numrows = $DB->count("SELECT count(*) from kami_user WHERE 1");
        $sql = " 1";
        $con = '<span class="badge bg-primary fs-6 px-3 py-2">共有 <strong>' . $numrows . '</strong> 个用户</span>';
        $link = '';
    }
    echo $con;
    echo '                            </div>
                            <button type="button" class="btn btn-outline-warning" onclick="batchDelete()">
                                <i class="fas fa-trash-alt me-2"></i>批量删除
                            </button>
                        </div>
                    </div>
                    
                    <form id="batchForm" method="POST" action="ulist.php?my=batch_delete">
                        <input type="hidden" name="do" value="submit">
                        <div class="table-responsive">
                            <table class="table table-hover user-table">
                                <thead>
                                    <tr>
                                        <th width="80">
                                            <div class="d-flex align-items-center flex-row">
                                                <input type="checkbox" class="form-check-input me-2" id="selectAll" onchange="toggleAll(this)">
                                                <span style="white-space: nowrap;">全选</span>
                                            </div>
                                        </th>
                                        <th width="120">用户ID</th>
                                        <th>OpenID</th>
                                        <th width="200">时间信息</th>
                                        <th width="120">操作</th>
                                    </tr>
                                </thead>
                                <tbody>';
    
    
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
    $offset = $pagesize * ($page - 1);

    $rs = $DB->query("SELECT * FROM kami_user WHERE{$sql} order by id desc limit $offset,$pagesize");
    while ($res = $DB->fetch($rs)) {
        echo '<tr>
                <td>
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="form-check-input user-checkbox" name="ids[]" value="' . $res['id'] . '">
                    </div>
                </td>
                <td>
                    <div class="user-id">
                        <span class="fw-bold text-primary">' . htmlspecialchars($res['gtkid']) . '</span>
                    </div>
                </td>
                <td>
                    <div class="openid-display">
                        <span class="text-monospace">' . htmlspecialchars($res['openid']) . '</span>
                    </div>
                </td>
                <td>
                    <div class="time-info">
                        <div class="time-item">
                            <small class="text-muted">注册时间：</small><br>
                            <span class="fw-semibold">' . date('Y-m-d H:i:s', strtotime($res['addtime'])) . '</span>
                        </div>
                        <div class="time-item mt-2">
                            <small class="text-muted">最后登录：</small><br>
                            <span class="fw-semibold">' . ($res['lasttime'] ? date('Y-m-d H:i:s', strtotime($res['lasttime'])) : '从未登录') . '</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="./ulist.php?my=delete&id=' . $res['id'] . '" class="btn btn-outline-danger" onclick="return confirm(\'确定要删除用户 ' . htmlspecialchars($res['gtkid']) . ' 吗？\')" title="删除用户">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>';
    }
    
    echo '                  </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- 分页导航 -->';

    
    if ($pages > 1) {
        echo '<div class="d-flex justify-content-center mt-4">
                <nav aria-label="用户列表分页">
                    <ul class="pagination pagination-lg">';

        $first = 1;
        $prev = $page - 1;
        $next = $page + 1;
        $last = $pages;

        
        if ($page > 1) {
            echo '<li class="page-item">
                    <a class="page-link" href="ulist.php?page=' . $first . $link . '" title="首页">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                  </li>';
            echo '<li class="page-item">
                    <a class="page-link" href="ulist.php?page=' . $prev . $link . '" title="上一页">
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
                        <a class="page-link" href="ulist.php?page=' . $i . $link . '">' . $i . '</a>
                      </li>';
            }
        }

        
        if ($page < $pages) {
            echo '<li class="page-item">
                    <a class="page-link" href="ulist.php?page=' . $next . $link . '" title="下一页">
                        <i class="fas fa-angle-right"></i>
                    </a>
                  </li>';
            echo '<li class="page-item">
                    <a class="page-link" href="ulist.php?page=' . $last . $link . '" title="尾页">
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
            </div>';

        
        echo '<div class="d-flex justify-content-center mt-2">
                <small class="text-muted pagination-info">
                    第 ' . $page . ' 页，共 ' . $pages . ' 页，总计 ' . $numrows . ' 个用户
                </small>
            </div>';
    }

    echo '          </div>
            </div>
        </div>
    </div>
</div>';
}
?>

<style>
.time-info {
    font-size: 0.9rem;
    line-height: 1.4;
}

.time-item {
    margin-bottom: 8px;
}

.time-item:last-child {
    margin-bottom: 0;
}

.time-item small {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.time-item .fw-semibold {
    color: #495057;
    font-size: 0.85rem;
    font-family: 'Courier New', monospace;
}

.openid-display {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    word-break: break-all;
    max-width: 300px;
}

.user-id {
    font-weight: 600;
    font-size: 1rem;
}

.user-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.user-table td {
    vertical-align: middle;
    padding: 12px 8px;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* 确保全选文字横向显示 */
.user-table th .d-flex {
    flex-direction: row !important;
    align-items: center !important;
}

.user-table th span {
    white-space: nowrap !important;
    writing-mode: horizontal-tb !important;
    text-orientation: mixed !important;
}
</style>

<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
$(document).ready(function() {
    console.log('用户管理中心页面加载完成');

    // 初始化工具提示
    $('[title]').tooltip();

    // 表格行悬停效果
    $('.user-table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
});

// 全选/取消全选
function toggleAll(source) {
    var checkboxes = document.querySelectorAll('.user-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
    updateBatchButtonState();
}

// 更新批量操作按钮状态
function updateBatchButtonState() {
    var checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    var batchBtn = document.querySelector('[onclick="batchDelete()"]');

    if (checkedBoxes.length > 0) {
        batchBtn.classList.remove('btn-outline-warning');
        batchBtn.classList.add('btn-warning');
        batchBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>批量删除 (' + checkedBoxes.length + ')';
    } else {
        batchBtn.classList.remove('btn-warning');
        batchBtn.classList.add('btn-outline-warning');
        batchBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>批量删除';
    }
}

// 监听复选框变化
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        updateBatchButtonState();
    }
});

// 批量删除
function batchDelete() {
    var checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkedBoxes.length === 0) {
        layer.alert('请选择要删除的用户！', {
            icon: 0,
            title: '提示'
        });
        return;
    }

    layer.confirm('确定要删除选中的 ' + checkedBoxes.length + ' 个用户吗？<br><span class="text-danger">此操作不可恢复！</span>', {
        btn: ['确定删除', '取消'],
        icon: 3,
        title: '批量删除确认'
    }, function(index) {
        document.getElementById('batchForm').submit();
        layer.close(index);
    });
}

// 监听复选框变化，更新全选状态
document.addEventListener('DOMContentLoaded', function() {
    var selectAllCheckbox = document.getElementById('selectAll');
    var userCheckboxes = document.querySelectorAll('.user-checkbox');

    userCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            var totalCount = userCheckboxes.length;

            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        });
    });
});
</script>

<?php include './footer.php'; ?>
