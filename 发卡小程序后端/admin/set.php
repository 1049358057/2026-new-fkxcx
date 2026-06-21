<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
error_reporting(0);
include('../includes/common.php');
include_once '../Api/wechatConfig.php';
$title = '后台管理';
include('./head.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- 页面标题 -->
            <div class="glass-card fade-in-up mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cog fa-2x text-primary me-3"></i>
                    <div>
                        <h2 class="mb-1">系统设置</h2>
                        <p class="text-muted mb-0">配置小程序参数和系统设置</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
';
$mod = (isset($_GET['mod']) ? $_GET['mod'] : NULL);
if ($mod == 'site_n' && $_POST['do'] == 'submit') {
    $xcx_name = $_POST['xcx_name'];
    $adVideoId = $_POST['adVideoId'];
    $adVideoTip = $_POST['adVideoTip'];
    $shareTip = $_POST['shareTip'];
    $xcxappid = $_POST['xcxappid'];
    $xcxpath = $_POST['xcxpath'];
    $ruleImg = $_POST['ruleImg'];
    $contact = $_POST['contact'];
    $examine = $_POST['examine'];
    $shareTitle = $_POST['shareTitle'];
    $shareImg = $_POST['shareImg'];
    $appid = $_POST['appid'];
    $secret = $_POST['secret'];
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $gl1 = $_POST['gl1'];
    $appid1 = $_POST['appid1'];
    $path1 = $_POST['path1'];
    $glimg1 = $_POST['glimg1'];
    $gl2 = $_POST['gl2'];
    $appid2 = $_POST['appid2'];
    $path2 = $_POST['path2'];
    $glimg2 = $_POST['glimg2'];
    $daily_video_limit = $_POST['daily_video_limit'];
    $interval_video_limit = $_POST['interval_video_limit'];
    $interval_minutes = $_POST['interval_minutes'];
    $tutorial_content = $_POST['tutorial_content'];
    $icp_number = $_POST['icp_number'];

    if ($xcx_name == NULL) {
        showmsg('必填项不能为空！', 3);
    }
    $wechatConfig = "<?php
     /*微信小程序校验配置*/
     \$appid='$appid';
     \$secret='$secret';
     ?>";
    if (!file_put_contents('../Api/wechatConfig.php', $wechatConfig)) {
        showmsg('小程序校验配置改失败！');
    }
    saveSetting('tutorial_content', $tutorial_content);
    saveSetting('daily_video_limit', $daily_video_limit);
    saveSetting('interval_video_limit', $interval_video_limit);
    saveSetting('interval_minutes', $interval_minutes);
    saveSetting('icp_number', $icp_number);
    saveSetting('xcx_name', $xcx_name);
    saveSetting('examine', $examine);
    saveSetting('adVideoId', $adVideoId);
    saveSetting('adVideoTip', $adVideoTip);
    saveSetting('shareTip', $shareTip);
    saveSetting('xcxappid', $xcxappid);
    saveSetting('xcxpath', $xcxpath);  
    saveSetting('shareTitle', $shareTitle);
    saveSetting('shareImg', $shareImg);
    saveSetting('ruleImg', $ruleImg);
    saveSetting('contact', $contact);
    saveSetting('admin_user', $user);
    saveSetting('gl1', $gl1);
    saveSetting('appid1', $appid1);
    saveSetting('path1', $path1);
    saveSetting('glimg1', $glimg1);
    saveSetting('gl2', $gl2);
    saveSetting('appid2', $appid2);
    saveSetting('path2', $path2);
    saveSetting('glimg2', $glimg2);
    if (!empty($pwd)) {
        saveSetting('admin_pass', $pwd);
    }
    showmsg('修改成功！', 1);
} else {
    if ($mod == 'site') {
        echo '<div class="glass-card fade-in-up">
            <form action="./set.php?mod=site_n" method="post">
                <input type="hidden" name="do" value="submit"/>

                <!-- 标签页导航 -->
                <ul class="nav nav-tabs mb-4" id="settingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                            <i class="fas fa-cog me-2"></i>基础设置
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab">
                            <i class="fas fa-video me-2"></i>视频设置
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="wechat-tab" data-bs-toggle="tab" data-bs-target="#wechat" type="button" role="tab">
                            <i class="fab fa-weixin me-2"></i>微信设置
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                            <i class="fas fa-user-shield me-2"></i>管理员设置
                        </button>
                    </li>
                </ul>

                <!-- 标签页内容 -->
                <div class="tab-content" id="settingTabsContent">
                    <!-- 基础设置 -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">小程序名称</label>
                                <input type="text" name="xcx_name" value="' . $conf['xcx_name'] . '" class="form-control" required/>
                                <div class="form-text">显示在小程序中的名称</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">卡密获取方式</label>
                                <select class="form-control" name="examine">
                                    <option value="1"' . ($conf['examine'] == 1 ? ' selected' : '') . '>观看视频后直接获取</option>
                                    <option value="2"' . ($conf['examine'] == 2 ? ' selected' : '') . '>观看视频后直接获取</option>
                                    <option value="3"' . ($conf['examine'] == 3 ? ' selected' : '') . '>观看视频后需要分享才能获取</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">教程内容</label>
                                <textarea name="tutorial_content" class="form-control" rows="5" placeholder="请输入使用教程内容，支持HTML格式">' . (isset($conf['tutorial_content']) ? $conf['tutorial_content'] : '') . '</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">备案号</label>
                                <input type="text" name="icp_number" value="' . (isset($conf['icp_number']) ? $conf['icp_number'] : '') . '" class="form-control" placeholder="请输入网站备案号"/>
                                <div class="form-text">将显示在页面底部</div>
                            </div>
                        </div>
                    </div>

                    <!-- 视频设置 -->
                    <div class="tab-pane fade" id="video" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">激励视频ID</label>
                                <input type="text" name="adVideoId" value="' . $conf['adVideoId'] . '" class="form-control"/>
                                <div class="form-text">微信小程序激励视频广告ID</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">每日观看限制</label>
                                <input type="number" name="daily_video_limit" value="' . (isset($conf['daily_video_limit']) ? $conf['daily_video_limit'] : '10') . '" class="form-control" min="1"/>
                                <div class="form-text">用户每天最多可观看的次数</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">时间段观看限制</label>
                                <input type="number" name="interval_video_limit" value="' . (isset($conf['interval_video_limit']) ? $conf['interval_video_limit'] : '3') . '" class="form-control" min="1"/>
                                <div class="form-text">指定时间段内最多观看次数</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">时间段限制（分钟）</label>
                                <input type="number" name="interval_minutes" value="' . (isset($conf['interval_minutes']) ? $conf['interval_minutes'] : '30') . '" class="form-control" min="1"/>
                                <div class="form-text">时间段限制，单位为分钟</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">未观看完视频提示</label>
                                <input type="text" name="adVideoTip" value="' . $conf['adVideoTip'] . '" class="form-control"/>
                                <div class="form-text">用户未观看完视频时的提示内容</div>
                            </div>
                        </div>
                    </div>

                    <!-- 微信设置 -->
                    <div class="tab-pane fade" id="wechat" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">微信小程序AppID</label>
                                <input type="text" name="appid" value="' . $appid . '" class="form-control no-focus-highlight"/>
                                <div class="form-text">微信小程序的AppID</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">微信小程序Secret</label>
                                <input type="text" name="secret" value="' . $secret . '" class="form-control"/>
                                <div class="form-text">微信小程序的Secret</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">分享提示内容</label>
                                <input type="text" name="shareTip" value="' . $conf['shareTip'] . '" class="form-control"/>
                                <div class="form-text">需要分享时的提示内容</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">规则图片URL</label>
                                <input type="text" name="ruleImg" value="' . $conf['ruleImg'] . '" class="form-control"/>
                                <div class="form-text">规则说明图片地址</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">小程序AppID（跳转用）</label>
                                <input type="text" name="xcxappid" value="' . $conf['xcxappid'] . '" class="form-control no-focus-highlight"/>
                                <div class="form-text">用于跳转的小程序AppID</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">小程序路径</label>
                                <input type="text" name="xcxpath" value="' . $conf['xcxpath'] . '" class="form-control"/>
                                <div class="form-text">跳转的小程序页面路径</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">联系方式</label>
                                <input type="text" name="contact" value="' . $conf['contact'] . '" class="form-control"/>
                                <div class="form-text">客服联系方式</div>
                            </div>
                        </div>

                        <h5 class="mb-3">
                            <i class="fas fa-share-alt me-2"></i>分享设置
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">分享标题</label>
                                <input type="text" name="shareTitle" value="';
        echo $conf['shareTitle'];
        echo '" class="form-control"/>
                                <div class="form-text">分享时显示的标题</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">分享图片链接</label>
                                <input type="text" name="shareImg" value="' . $conf['shareImg'] . '" class="form-control"/>
                                <div class="form-text">分享时显示的图片URL</div>
                            </div>
                        </div>
                    </div>

                    <!-- 管理员设置 -->
                    <div class="tab-pane fade" id="admin" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">管理员用户名</label>
                                <input type="text" name="user" value="' . $conf['admin_user'] . '" class="form-control"/>
                                <div class="form-text">管理员登录用户名</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">管理员密码</label>
                                <input type="password" name="pwd" class="form-control" placeholder="留空则不修改"/>
                                <div class="form-text">留空则不修改当前密码</div>
                            </div>
                        </div>

                        <h5 class="mb-3">
                            <i class="fas fa-mobile-alt me-2"></i>关联小程序设置
                        </h5>

                        <!-- 小程序1 -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">推荐小程序 1</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">小程序名称</label>
                                        <input type="text" name="gl1" value="' . $conf['gl1'] . '" class="form-control" placeholder="请输入小程序名称"/>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">AppID</label>
                                        <input type="text" name="appid1" value="' . $conf['appid1'] . '" class="form-control appid-input no-focus-highlight" placeholder="wx1234567890abcdef"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">路径</label>
                                        <input type="text" name="path1" value="' . $conf['path1'] . '" class="form-control" placeholder="pages/index/index"/>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">图片URL</label>
                                        <input type="text" name="glimg1" value="' . $conf['glimg1'] . '" class="form-control" placeholder="https://example.com/image.png"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 小程序2 -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">推荐小程序 2</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">小程序名称</label>
                                        <input type="text" name="gl2" value="' . $conf['gl2'] . '" class="form-control" placeholder="请输入小程序名称"/>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">AppID</label>
                                        <input type="text" name="appid2" value="' . $conf['appid2'] . '" class="form-control appid-input no-focus-highlight" placeholder="wx1234567890abcdef"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">路径</label>
                                        <input type="text" name="path2" value="' . $conf['path2'] . '" class="form-control" placeholder="pages/index/index"/>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">图片URL</label>
                                        <input type="text" name="glimg2" value="' . $conf['glimg2'] . '" class="form-control" placeholder="https://example.com/image.png"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 提交按钮 -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left me-1"></i>返回
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>保存设置
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// 表单验证和交互
$(document).ready(function() {
    // 标签页切换时的动画效果
    $("button[data-bs-toggle=\'tab\']").on("shown.bs.tab", function (e) {
        $(e.target.getAttribute("data-bs-target")).find(".fade-in-up").each(function(index) {
            $(this).css("animation-delay", (index * 0.05) + "s");
        });
    });

    // 设置默认值
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
        $(items[i]).val($(items[i]).attr("default") || 0);
    }

    // 表单提交前验证
    $("form").on("submit", function(e) {
        var requiredFields = $(this).find("[required]");
        var isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass("is-invalid");
                isValid = false;
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert("请填写所有必填项！");
        }
    });
});
</script>

<style>
/* 取消特定输入框的蓝色高亮效果 */
.no-focus-highlight:focus {
    border-color: #ced4da !important;
    box-shadow: none !important;
    outline: none !important;
}

.no-focus-highlight:focus-visible {
    border-color: #ced4da !important;
    box-shadow: none !important;
    outline: none !important;
}

/* 确保在不同状态下都没有高亮 */
.no-focus-highlight:active,
.no-focus-highlight:focus:active {
    border-color: #ced4da !important;
    box-shadow: none !important;
}
</style>';

        echo '</div>';
    }
}

include './footer.php';
?>