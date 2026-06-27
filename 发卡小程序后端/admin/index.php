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
$title='系统管理中心';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$total_classes = $DB->count("SELECT COUNT(*) FROM kami_class");
$total_cards = $DB->count("SELECT COUNT(*) FROM kami_faka");
$total_users = $DB->count("SELECT COUNT(*) FROM kami_user");
$used_cards = $DB->count("SELECT COUNT(*) FROM kami_faka WHERE users != ''");
?>

<div class="container page-content">
    <!-- 欢迎横幅 -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card fade-in-up">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-tachometer-alt fa-3x text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">欢迎回来！</h2>
                        <p class="text-muted mb-0">发卡系统管理后台 - 让卡密管理更简单</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 统计卡片 -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card fade-in-up text-center">
                <div class="mb-3">
                    <i class="fas fa-layer-group fa-2x text-primary"></i>
                </div>
                <h4 class="mb-1"><?php echo $total_classes; ?></h4>
                <p class="text-muted mb-0">卡密分类</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card fade-in-up text-center">
                <div class="mb-3">
                    <i class="fas fa-credit-card fa-2x text-success"></i>
                </div>
                <h4 class="mb-1"><?php echo $total_cards; ?></h4>
                <p class="text-muted mb-0">总卡密数</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card fade-in-up text-center">
                <div class="mb-3">
                    <i class="fas fa-users fa-2x text-info"></i>
                </div>
                <h4 class="mb-1"><?php echo $total_users; ?></h4>
                <p class="text-muted mb-0">用户总数</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card fade-in-up text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-2x text-warning"></i>
                </div>
                <h4 class="mb-1"><?php echo $used_cards; ?></h4>
                <p class="text-muted mb-0">已使用卡密</p>
            </div>
        </div>
    </div>

    <!-- 功能模块 -->
    <div class="row">
        <!-- 卡密管理模块 -->
        <div class="col-lg-6 mb-4">
            <div class="glass-card fade-in-up">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-th-large fa-2x text-primary me-3"></i>
                    <h4 class="mb-0">卡密管理</h4>
                </div>
                <p class="text-muted mb-4">管理卡密分类、库存和添加新卡密</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="./classlist.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-list me-2"></i>
                            分类管理
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="./fakalist.php" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-warehouse me-2"></i>
                            库存管理
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="./fakakms.php?my=add" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-plus me-2"></i>
                            添加卡密
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 用户管理模块 -->
        <div class="col-lg-6 mb-4">
            <div class="glass-card fade-in-up">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-users fa-2x text-info me-3"></i>
                    <h4 class="mb-0">用户管理</h4>
                </div>
                <p class="text-muted mb-4">查看和管理用户信息，支持批量操作</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="./ulist.php" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-friends me-2"></i>
                            用户列表
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="./ulist.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-search me-2"></i>
                            搜索用户
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="./ulist.php" class="btn btn-info w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-users-cog me-2"></i>
                            用户管理
                        </a>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<?php include './footer.php'; ?>