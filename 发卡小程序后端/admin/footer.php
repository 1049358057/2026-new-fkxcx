<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
?>

<!-- 页面底部 -->
<footer class="mt-5 py-4">
    <div class="container">
        <div class="glass-card text-center">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">发卡系统管理后台</h6>
                            <small class="text-muted">让卡密管理更简单</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                        <?php if (isset($conf['icp_number']) && !empty($conf['icp_number'])): ?>
                        <a href="https://beian.miit.gov.cn" target="_blank" class="text-muted text-decoration-none me-3">
                            <i class="fas fa-shield-alt me-1"></i>
                            <?php echo htmlspecialchars($conf['icp_number']); ?>
                        </a>
                        <?php endif; ?>
                        <small class="text-muted">
                            © <?php echo date('Y'); ?> 发卡系统
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- 返回顶部按钮 -->
<button id="backToTop" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none;">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// 返回顶部功能
$(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
        $('#backToTop').fadeIn();
    } else {
        $('#backToTop').fadeOut();
    }
});

$('#backToTop').click(function() {
    $('html, body').animate({scrollTop: 0}, 600);
    return false;
});

// 工具提示初始化
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// 表格响应式处理
$(document).ready(function() {
    $('.table-responsive').each(function() {
        if ($(this).find('table').width() > $(this).width()) {
            $(this).addClass('table-scroll');
        }
    });
});
</script>

</body>
</html>
