# 项目简介

这是一个基于微信小程序的发卡系统，支持通过观看激励视频广告，设置每日观看视频次数以及时间段内观看次数限制，免费领取卡密，包含前端小程序和后端 PHP 管理系统。

# 演示小程序：

![](https://tc.ailingsi.top/i/2026/06/21/6a37679e8e5fe.jpg)

# 一、功能说明

## 后台功能

* 分类管理：添加 / 编辑卡密分类，设置观看视频次数
* 卡密管理：批量导入、查看使用记录
* 用户管理：查看用户列表和领取记录
* 系统设置：配置小程序参数、广告 ID、限制规则

## 小程序功能

* 发卡页：选择分类，观看视频，领取卡密
* 记录页：查看个人领取记录
* 教程页：使用教程和说明
* 广告页：展示广告

## 领取模式

* 直接领取：视频次数设为 0，无需观看视频
* 单次视频：观看 1 次视频后领取
* 多次视频：观看多次视频后领取（可设置任意次数）

## 限制机制

* 每日观看视频次数限制（可后台设置）
* 时间段内观看次数限制（防刷机制）
* 每个分类独立计数

# 二、后端搭建（使用宝塔面板）

## 环境要求

* PHP 7.4 或以上版本
* MySQL 5.6 或以上版本
* 已安装宝塔面板

## 创建网站

1. 登录宝塔面板
2. 点击【网站】→【添加站点】
3. 填写域名（例如：[fkxcx.yourdomain.com](https://fkxcx.yourdomain.com)）
4. 选择 PHP 版本（建议 7.4）
5. 创建数据库（记录数据库名、用户名、密码）

## 上传后端文件

将 发卡小程序后端 文件夹内的所有文件上传到网站根目录，文件结构如下：

```
网站根目录/
├── admin/      # 后台管理
├── Api/        # API接口
├── includes/   # 核心文件
├── install/    # 安装程序
└── index.php   # 首页
```

## 设置目录权限

在宝塔面板中设置 `install/` 目录权限为 755。

## 配置数据库连接

编辑 `includes/config.php` 文件：

```
$dbconfig=array(
'host' => 'localhost',
'port' => 3306,
'user' => '你的数据库用户名',
'pwd' => '你的数据库密码',
'dbname' => '你的数据库名'
);
```

## 安装程序

访问：`http://你的域名/install`，按向导填写：

* 数据库地址：[localhost](https://localhost)
* 数据库端口：3306
* 数据库名称：刚才创建的数据库名
* 数据库用户名：数据库用户名
* 数据库密码：数据库密码点击安装，等待完成。

## 登录后台管理

访问：`http://你的域名/admin`

* 默认账号：admin
* 默认密码：123456
* 登录后立即修改密码！

## 后台基础配置

1. **系统设置**：
   * 小程序名称
   * 微信小程序 AppID 和 AppSecret
   * 激励视频广告 ID（从微信广告平台获取）
   * 每日观看视频次数限制
   * 时间段内观看次数限制
2. **卡密分类管理**：
   * 添加卡密分类
   * 设置每个分类需要观看的视频次数
   * 设置分类介绍和使用提示
3. **卡密管理**：
   * 批量导入卡密
   * 查看卡密使用记录

# 三、前端搭建（微信开发者工具）

## 准备工作

1. 下载并安装【微信开发者工具】
2. 注册微信小程序账号
3. 获取小程序 AppID

## 配置小程序信息

1. 登录微信公众平台
2. 进入【开发】→【开发管理】→【开发设置】，记录 AppID 和 AppSecret
3. 配置服务器域名：`request合法域名` 填写 `https://你的后端域名`（必须使用 HTTPS）

## 配置广告位

1. 登录微信公众平台
2. 进入【流量主】→【广告管理】
3. 创建激励视频广告位，获取广告 ID
4. 创建插屏广告位，获取广告 ID

## 导入项目

1. 打开微信开发者工具
2. 选择【小程序】→【导入项目】
3. 选择 发卡小程序前端 文件夹
4. 填写 AppID（使用你的小程序 AppID），点击导入

## 前端配置修改项

1. 修改 app.js

```
App({
onLaunch: function onLaunch() {},
globalData: {
request_url: "https://你的后端域名.com" // 改成你的后端域名（必须HTTPS）
}
});
```

2. 修改 project.config.json

```
{
"appid": "你的小程序AppID", // 改成你的AppID
"projectname": "发卡小程序"
}
```

3. 修改广告ID（共需修改8处）

pages/index/index.wxml - 横幅广告ID
pages/index/index.js - 插屏广告ID

```
interstitialAd = wx.createInterstitialAd({<br>adUnitId: "你的广告ID"<br>})
```

pages/featured/featured.wxml - 横幅广告ID
pages/featured/featured.js - 插屏广告ID

```
interstitialAd = wx.createInterstitialAd({
adUnitId: '你的广告IDID'
})
```

pages/record/record.wxml - 横幅广告ID
pages/record/record.js - 插屏广告ID

```
interstitialAd = wx.createInterstitialAd({
adUnitId: '你的广告ID'
})
```

pages/tutorial/tutorial.wxml - 横幅广告ID
pages/tutorial/tutorial.js - 插屏广告ID

```
interstitialAd = wx.createInterstitialAd({
adUnitId: '你的广告ID'
})
```

## 编译预览

1. 点击工具栏【编译】，检查报错
2. 点击【预览】，手机微信扫码测试
3. 测试功能：分类切换、观看视频、领取卡密、查看记录

## 上传发布

1. 点击【上传】，填写版本号和项目备注
2. 登录微信公众平台，进入【版本管理】
3. 提交审核，审核通过后发布

# 四、注意事项

* 域名必须使用 HTTPS：微信小程序要求服务器必须使用 HTTPS 协议
* 配置服务器域名：在微信公众平台配置 request 合法域名
* 广告位申请：需要小程序满足流量主开通条件才能使用广告功能
* 安全设置：
  * 修改后台默认密码
  * 定期备份数据库
  * 删除或重命名 install 目录
* 测试建议：
  * 先在开发版本充分测试
  * 准备足够的测试卡密
  * 测试各种异常情况
* 数据库备份：定期在宝塔面板备份数据库

# 五、常见问题

* Q: 小程序提示 "网络请求失败"？
* A: 检查后端域名是否配置正确，是否使用 HTTPS，是否在微信公众平台配置了服务器域名。
* Q: 广告加载失败？
* A: 检查广告 ID 是否正确，小程序是否开通流量主，广告位是否审核通过。
* Q: 卡密领取失败？
* A: 检查数据库中是否有可用卡密，用户是否完成视频观看要求。
* Q: 后台无法登录？
* A: 检查数据库连接是否正常，验证码是否正确输入。

# 联系方式

* 作者：灵思
* QQ：2309624439
* 微信：zdls773
* 博客：[http://blog.ailingsi.top/](http://blog.ailingsi.top/)
* 小白不会搭建的：[https://qy.ailingsi.top/?#/goods?gid=6320](https://qy.ailingsi.top/?#/goods?gid=6320)

搭建完成，恭喜你！拥有了一个完整的发卡小程序系统！

---

# 前台展示

![](https://tc.ailingsi.top/i/2026/06/11/6a2ab214a58c5.png)

![](https://tc.ailingsi.top/i/2026/06/11/6a2ab24dc06e3.png)

![](https://tc.ailingsi.top/i/2026/04/27/2026/01/QQ20260215-083613.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767324380448.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767324417332.png)

# 后台展示

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325430779.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325534319-1024x453.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325562159-1024x426.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325584325-1024x430.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/图片-1024x489.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325627824-1024x456.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767511881764-1024x625.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767511929601-1024x598.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767511775961-1024x628.png)

![](https://tc.ailingsi.top/i/2026/04/27/2025/12/QQ_1767325708135-1024x422.png)

© 2026 灵思发卡小程序
