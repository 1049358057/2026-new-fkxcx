<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/
@header('Content-Type: text/html; charset=UTF-8');
?>
    <!DOCTYPE html>
    <html lang="zh-cn">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <meta name="description" content="发卡小程序管理后台"/>
        <meta name="author" content="发卡系统"/>
        <title><?php echo $title ?> - 发卡系统管理后台</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

        <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet"/>

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

        <link href="./assets/css/admin-glassmorphism.css" rel="stylesheet"/>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- 自定义JavaScript -->
        <script>
            // 页面加载动画
            $(document).ready(function() {
                $('.fade-in-up').each(function(index) {
                    $(this).css('animation-delay', (index * 0.1) + 's');
                });
            });
        </script>
    </head>
<body class="admin-page">
<?php if ($islogin == 1) { ?>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="./">
                <i class="fas fa-credit-card me-2"></i>
                <span>灵思发卡系统管理中心</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="切换导航">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./">
                            <i class="fas fa-home me-1"></i>平台首页
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="cardManageDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-th-large me-1"></i>卡密管理
                        </a>
                        <ul class="dropdown-menu glass-card">
                            <li><a class="dropdown-item" href="./classlist.php">
                                <i class="fas fa-list me-2"></i>卡密分类
                            </a></li>
                            <li><a class="dropdown-item" href="./fakalist.php">
                                <i class="fas fa-warehouse me-2"></i>库存管理
                            </a></li>
                            <li><a class="dropdown-item" href="./fakakms.php?my=add">
                                <i class="fas fa-plus me-2"></i>添加卡密
                            </a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="./ulist.php">
                            <i class="fas fa-users me-1"></i>用户信息
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="./set.php?mod=site">
                            <i class="fas fa-cog me-1"></i>系统设置
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="./login.php?logout">
                            <i class="fas fa-sign-out-alt me-1"></i>退出登录
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php } ?>