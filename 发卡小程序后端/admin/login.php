<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：zdls773
qq技术交流群：1082963914
blog网址：http://blog.ailingsi.top/

*/
include("../includes/common.php");
if(isset($_POST['user']) && isset($_POST['pass'])){
	$user=daddslashes($_POST['user']);
	$pass=daddslashes($_POST['pass']);
	$code=daddslashes($_POST['code']);
	if (!$code || ($code != $_SESSION['vc_code'])) {
		unset($_SESSION['vc_code']);
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('验证码错误！');history.go(-1);</script>");
	}elseif($user==$conf['admin_user'] && $pass==$conf['admin_pass']) {
		unset($_SESSION['vc_code']);
		$session=md5($user.$pass.$password_hash);
		$token=authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
		setcookie("admin_token", $token, time() + 604800);
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('登陆管理中心成功！');window.location.href='./index.php';</script>");
	}else {
		unset($_SESSION['vc_code']);
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('用户名或密码不正确！');history.go(-1);</script>");
	}
}elseif(isset($_GET['logout'])){
	setcookie("admin_token", "", time() - 604800);
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./login.php';</script>");
}elseif($islogin==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
$title='用户登录';
include './head.php';
?>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    width: 100%;
    max-width: 480px;
    background: var(--glass-card-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    box-shadow: 0 12px 40px 0 var(--glass-shadow);
    overflow: hidden;
    animation: fadeInUp 0.8s ease forwards;
}

.login-header {
    background: linear-gradient(135deg, var(--glass-gradient-start), var(--glass-gradient-end));
    padding: 30px;
    text-align: center;
    color: white;
}

.login-header h2 {
    margin: 0;
    font-weight: 600;
    font-size: 1.5rem;
}

.login-header .subtitle {
    margin-top: 8px;
    opacity: 0.9;
    font-size: 0.9rem;
}

.login-body {
    padding: 35px;
}

.form-floating {
    margin-bottom: 24px;
}

.form-floating .form-control {
    height: 58px;
    padding: 16px 12px 8px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    color: var(--glass-text-color);
}

.form-floating label {
    color: var(--glass-text-light);
    padding: 16px 12px;
}

.captcha-container {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 24px;
}

.captcha-input {
    flex: 1;
}

.captcha-image {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    border-radius: 8px;
    padding: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.captcha-image:hover {
    background: rgba(255, 255, 255, 0.2);
}

.login-btn {
    width: 100%;
    height: 52px;
    background: linear-gradient(135deg, var(--glass-gradient-start), var(--glass-gradient-end));
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    margin-bottom: 24px;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.login-footer {
    text-align: center;
    padding: 0 30px 30px;
}

.login-footer a {
    color: var(--glass-primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.login-footer a:hover {
    color: var(--glass-primary-dark);
}

/* 响应式设计 */
@media (max-width: 576px) {
    .login-card {
        max-width: 95%;
        margin: 10px;
    }
    
    .login-body {
        padding: 25px;
    }
    
    .captcha-container {
        flex-direction: column;
        gap: 12px;
    }
    
    .captcha-input {
        width: 100%;
    }
    
    .captcha-image {
        align-self: center;
    }
}
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-credit-card fa-2x mb-3"></i>
            <h2>发卡系统</h2>
            <div class="subtitle">管理员登录</div>
        </div>

        <div class="login-body">
            <form action="./login.php" method="post">
                <div class="form-floating">
                    <input type="text" name="user" value="<?php echo @$_POST['user'];?>"
                           class="form-control" id="username" placeholder="用户名" required>
                    <label for="username">
                        <i class="fas fa-user me-2"></i>用户名
                    </label>
                </div>

                <div class="form-floating">
                    <input type="password" name="pass" class="form-control"
                           id="password" placeholder="密码" required>
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>密码
                    </label>
                </div>

                <div class="captcha-container">
                    <div class="form-floating captcha-input">
                        <input type="number" name="code" class="form-control"
                               id="captcha" placeholder="验证码" autocomplete="off" required>
                        <label for="captcha">
                            <i class="fas fa-shield-alt me-2"></i>验证码
                        </label>
                    </div>
                    <div class="captcha-image">
                        <img src="./code.php?r=<?php echo time();?>" height="50"
                             onclick="this.src='./code.php?r='+Math.random();"
                             title="点击更换验证码" style="border-radius: 4px;">
                    </div>
                </div>

                <button type="submit" class="btn login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>登录
                </button>
            </form>
        </div>

        <div class="login-footer">
            <div class="mb-2">
                <a href="http://zy.ailingsi.top/" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>
                    作者：灵思
                </a>
            </div>
            <?php if (isset($conf['icp_number']) && !empty($conf['icp_number'])): ?>
            <div class="mt-2">
                <a href="https://beian.miit.gov.cn" target="_blank" style="color: var(--glass-text-light); font-size: 0.85rem;">
                    <i class="fas fa-shield-alt me-1"></i>
                    <?php echo htmlspecialchars($conf['icp_number']); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>