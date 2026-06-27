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
$title = '分类管理';
include('./head.php');
if ($islogin!=1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
echo '<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- 页面标题 -->
            <div class="glass-card fade-in-up mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-layer-group fa-2x text-primary me-3"></i>
                        <div>
                            <h2 class="mb-1">卡密分类管理</h2>
                            <p class="text-muted mb-0">管理卡密分类，设置观看次数和使用说明</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus me-2"></i>添加分类
                    </button>
                </div>
            </div>
        </div>
    </div>
';
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my=='add_submit') {
    $name = $_POST['name'];
    if ($name==NULL) {
        exit('<script language=\'javascript\'>alert(\'保存错误,请确保每项都不为空!\');history.go(-1);</script>');
    } else {
        
        $maxSort = $DB->get_row('SELECT MAX(sort) as max_sort FROM kami_class');
        $newSort = ($maxSort && $maxSort['max_sort']) ? $maxSort['max_sort'] + 1 : 1;

        $sql = 'insert into `kami_class` (`name`,`active`,`sort`) values (\'' . $name . '\',\'1\',\'' . $newSort . '\')';
        if ($cid = $DB->insert($sql)) {
            exit('<script language=\'javascript\'>alert(\'添加分类成功！\');window.location.href=\'classlist.php\';</script>');
        } else {
            exit('<script language=\'javascript\'>alert(\'添加分类失败！' . $DB->error() . '\');history.go(-1);</script>');
        }
    }
} elseif ($my=='edit_submit') {
    $cid = $_GET['cid'];
    $rows = $DB->get_row('select * from kami_class where cid=\'' . $cid . '\' limit 1');
    if (!$rows) {
        exit('<script language=\'javascript\'>alert(\'当前记录不存在！\');history.go(-1);</script>');
    }
    $name = $_POST['name'];
    if ($name==NULL) {
        exit('<script language=\'javascript\'>alert(\'保存错误,请确保每项都不为空!\');history.go(-1);</script>');
    } elseif ($DB->query('update kami_class set name=\'' . $name . '\' where cid=\'' . $cid . '\'')) {
        exit('<script language=\'javascript\'>alert(\'修改分类成功！\');window.location.href=\'classlist.php\';</script>');
    } else {
        exit('<script language=\'javascript\'>alert(\'修改商品失败！' . $DB->error() . '\');history.go(-1);</script>');
    }
} elseif ($my=='delete') {
    $cid = $_GET['cid'];
    $sql = 'DELETE FROM kami_class WHERE cid=\'' . $cid . '\'';
    if ($DB->query($sql)) {
        exit('<script language=\'javascript\'>alert(\'删除成功！\');window.location.href=\'classlist.php\';</script>');
    } else {
        exit('<script language=\'javascript\'>alert(\'删除失败！' . $DB->error() . '\');history.go(-1);</script>');
    }
} else {
    $numrows = $DB->count('SELECT count(*) from kami_class');
    $sql = ' 1';
    echo $con;
    echo '<div class="glass-card fade-in-up">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">
                <i class="fas fa-list me-2"></i>分类列表
                <span class="badge bg-primary ms-2">' . $numrows . '</span>
            </h4>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>刷新
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="120">排序</th>
                        <th>分类名称</th>
                        <th width="150">商品图片</th>
                        <th width="100">状态</th>
                        <th width="100">视频次数</th>
                        <th width="300">操作</th>
                    </tr>
                </thead>
                <tbody>
';
    $pagesize = 30;
    $pages = intval($numrows / $pagesize);
    if ($numrows % $pagesize) {
    }
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    } else {
        $page = 1;
    }
    $offset = $pagesize * ($page - 1);
    $rs = $DB->query('SELECT * FROM kami_class WHERE' . $sql . ' order by sort asc');
    while ($res = $DB->fetch($rs)) {
        echo '<tr>
            <td>
                <div class="btn-group-vertical btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary sort_btn" title="移到顶部" data-cid="' . $res['cid'] . '" data-sort="0">
                        <i class="fas fa-angle-double-up"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary sort_btn" title="上移" data-cid="' . $res['cid'] . '" data-sort="1">
                        <i class="fas fa-angle-up"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary sort_btn" title="下移" data-cid="' . $res['cid'] . '" data-sort="2">
                        <i class="fas fa-angle-down"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary sort_btn" title="移到底部" data-cid="' . $res['cid'] . '" data-sort="3">
                        <i class="fas fa-angle-double-down"></i>
                    </button>
                </div>
            </td>
            <td>
                <form action="classlist.php?my=edit_submit&cid=' . $res['cid'] . '" method="POST" class="edit-form d-flex align-items-center gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control category-name-input" name="name" value="' . $res['name'] . '" placeholder="分类名称" required>
                        <button type="submit" class="btn btn-success save-btn" title="保存分类名称">
                            <i class="fas fa-check me-1"></i>
                            <span class="btn-text">保存</span>
                        </button>
                    </div>
                </form>
            </td>
            <td>
                <div class="image-upload-container">
                    <input type="file" id="file' . $res['cid'] . '" accept="image/*" style="display:none" onchange="fileUpload(' . $res['cid'] . ')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="product-image-preview" style="width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #f5f5f5;">
                            <img id="preview' . $res['cid'] . '" src="' . ($res['image'] ? $res['image'] : '../../images/km.png') . '" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="btn-group-vertical btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="fileSelect(' . $res['cid'] . ')" title="上传图片">
                                <i class="fas fa-upload"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-show-image" data-cid="' . $res['cid'] . '" title="设置图片URL">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                ' . ($res['active']==1 ?
                '<span class="badge bg-success btn-set-active" style="cursor:pointer" data-cid="' . $res['cid'] . '" data-active="0">
                    <i class="fas fa-eye me-1"></i>显示中
                </span>' :
                '<span class="badge bg-warning btn-set-active" style="cursor:pointer" data-cid="' . $res['cid'] . '" data-active="1">
                    <i class="fas fa-eye-slash me-1"></i>已隐藏
                </span>') . '
            </td>
            <td class="text-center">
                <div class="video-times-display">
                    <button class="btn btn-outline-primary btn-sm btn-show-video-times" data-cid="' . $res['cid'] . '" title="点击修改观看次数">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-play-circle me-2"></i>
                            <span class="times-number">' . $res['video_times'] . '</span>
                            <span class="times-unit ms-1">次</span>
                        </div>
                    </button>
                </div>
            </td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="./fakalist.php?cid=' . $res['cid'] . '" class="btn btn-outline-warning" title="卡密管理">
                        <i class="fas fa-credit-card"></i>
                    </a>
                    <button class="btn btn-outline-info btn-show-introduce" data-cid="' . $res['cid'] . '" title="分类介绍">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-show-usetip" data-cid="' . $res['cid'] . '" title="使用提示">
                        <i class="fas fa-question-circle"></i>
                    </button>
                    <a href="./classlist.php?my=delete&cid=' . $res['cid'] . '" class="btn btn-outline-danger"
                       onclick="return confirm(\'确定要删除此分类吗？\')" title="删除">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>';
    }
}
echo '              </tbody>
            </table>
        </div>
    </div>

    <!-- 添加分类模态框 -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">
                        <i class="fas fa-plus me-2"></i>添加新分类
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="classlist.php?my=add_submit" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="className" class="form-label">分类名称</label>
                            <input type="text" class="form-control" id="className" name="name" placeholder="请输入分类名称" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>添加分类
                        </button>
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
$(document).ready(function() {
    // 修复可能的脚本冲突问题
    console.log("页面加载完成，所有按钮已激活");
    
    // 测试layer.js是否正确加载
    if(typeof layer === 'undefined') {
        console.error("layer.js未正确加载，尝试重新加载");
        // 动态加载layer.js
        var script = document.createElement('script');
        script.src = "//cdn.staticfile.org/layer/2.3/layer.js";
        document.head.appendChild(script);
        
        script.onload = function() {
            console.log("layer.js已重新加载");
            // 显示一个测试对话框
            layer.msg('layer.js加载成功');
        };
    } else {
        console.log("layer.js已正确加载");
        // 显示一个测试对话框
        layer.msg('管理面板已加载完成');
    }
    
    // 使用jQuery事件绑定，确保按钮点击事件正常工作
    $(document).on('click', '.btn-show-video-times', function() {
        var cid = $(this).data('cid');
        showVideoTimes(cid);
    });
    
    $(document).on('click', '.btn-show-introduce', function() {
        var cid = $(this).data('cid');
        showIntroduce(cid);
    });
    
    $(document).on('click', '.btn-show-usetip', function() {
        var cid = $(this).data('cid');
        showUseTip(cid);
    });
    
    $(document).on('click', '.btn-set-active', function() {
        var cid = $(this).data('cid');
        var active = $(this).data('active');
        setActive(cid, active);
    });
    
    // 排序按钮事件
    $(document).on('click', '.sort_btn', function() {
        var cid = $(this).data('cid');
        var sortType = $(this).data('sort');
        sort(cid, sortType);
    });
    
    // 图片设置按钮事件
    $(document).on('click', '.btn-show-image', function() {
        var cid = $(this).data('cid');
        showImageUrl(cid);
    });
});

function setActive(cid,active) {
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=setClass&cid='+cid+'&active='+active,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

function showVideoTimes(cid){
    console.log("showVideoTimes函数被调用，cid="+cid);
    var title = '需要观看的激励视频次数';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getVideoTimes',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.prompt({title: '填写'+title, value: data.result, formType: 0}, function(times, index){
                    var ii = layer.load(2, {shade:[0.1,'#fff']});
                    $.ajax({
                        type : 'POST',
                        url : 'ajax.php?act=setVideoTimes',
                        data : {cid:cid,times:times},
                        dataType : 'json',
                        success : function(data) {
                            layer.close(ii);
                            if(data.code == 0){
                                layer.msg('填写'+title+'成功');
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1000);
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        error:function(data){
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}

function showIntroduce(cid){
    console.log("showIntroduce函数被调用，cid="+cid);
    var title = '分类介绍';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getIntroduce',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                // 使用layer.open创建一个自定义的HTML编辑器弹窗
                layer.open({
                    type: 1,
                    title: '编辑分类介绍（支持HTML格式）',
                    area: ['800px', '600px'],
                    content: '<div style="padding: 20px;">' +
                             '<div style="margin-bottom: 10px;">' +
                             '<strong>提示：</strong>支持HTML标签，如 &lt;br&gt; &lt;p&gt; &lt;strong&gt; &lt;em&gt; &lt;span style="color:red"&gt; 等' +
                             '</div>' +
                             '<textarea id="introduce-editor" style="width: 100%; height: 400px; border: 1px solid #ddd; padding: 10px; font-family: monospace;">' +
                             data.result +
                             '</textarea>' +
                             '<div style="margin-top: 15px; text-align: right;">' +
                             '<button type="button" class="btn btn-default" onclick="layer.closeAll()">取消</button> ' +
                             '<button type="button" class="btn btn-primary" onclick="saveIntroduce(' + cid + ')">保存</button>' +
                             '</div>' +
                             '</div>',
                    success: function(layero, index) {
                        // 弹窗打开后的回调
                        console.log('分类介绍编辑器已打开');
                    }
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}

function saveIntroduce(cid) {
    var text = document.getElementById('introduce-editor').value;
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=setIntroduce',
        data : {cid:cid,text:text},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('保存分类介绍成功');
                layer.closeAll();
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}
function showUseTip(cid){
    console.log("showUseTip函数被调用，cid="+cid);
    var title = '卡密使用提示';
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=getUseTip',
        data : {cid:cid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.prompt({title: '填写'+title, value: data.result, formType: 2}, function(text, index){
                    var ii = layer.load(2, {shade:[0.1,'#fff']});
                    $.ajax({
                        type : 'POST',
                        url : 'ajax.php?act=setUseTip',
                        data : {cid:cid,text:text},
                        dataType : 'json',
                        success : function(data) {
                            layer.close(ii);
                            if(data.code == 0){
                                layer.msg('填写'+title+'成功');
                            }else{
                                layer.alert(data.msg);
                            }
                        },
                        error:function(data){
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                });
            }else{
                layer.alert(data.msg);
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}
function sort(cid,sort) {
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=setClassSort&cid='+cid+'&sort='+sort,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				window.location.reload();
			}else{
				layer.msg('操作失败');
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function getImage(cid) {
	layer.confirm('是否从该分类下的商品图片获取一张作为分类图片？', {
		btn: ['确定'] //按钮
	}, function(){
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=getClassImage&cid='+cid,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('获取图片成功');
				$("input[name='img"+cid+"']").val(data.url);
			}else{
				layer.alert('该分类下商品都没有图片');
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
	});
}
function editClass(cid) {
	var name = $("input[name='name"+cid+"']").val();
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClass&cid='+cid,
		data : {name:name},
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function saveAll() {
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClassAll',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			alert('保存成功！');
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function saveAllImages() {
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=editClassImages',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			alert('保存成功！');
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function fileSelect(cid){
	$("#file"+cid).trigger("click");
}

function fileUpload(cid){
	var fileObj = $("#file"+cid)[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
		return;
	}
	
	// 验证文件类型
	var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
	if (allowedTypes.indexOf(fileObj.type) === -1) {
		layer.msg('只允许上传图片文件（jpg、png、gif）');
		return;
	}
	
	// 验证文件大小（5MB）
	if (fileObj.size > 5 * 1024 * 1024) {
		layer.msg('图片大小不能超过5MB');
		return;
	}
	
	var formData = new FormData();
	formData.append("file", fileObj);
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	
	$.ajax({
		url: "ajax.php?act=uploadimg",
		data: formData,
		type: "POST",
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('上传图片成功');
				// 更新预览图
				$("#preview"+cid).attr('src', data.url);
				// 保存到数据库
				saveClassImage(cid, data.url);
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
			return false;
		}
	});
}

function showImageUrl(cid) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=getClassImage',
		data : {cid:cid},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.prompt({
					title: '设置商品图片URL',
					value: data.result,
					formType: 2,
					area: ['500px', '150px']
				}, function(imageUrl, index){
					if(imageUrl.trim() === '') {
						layer.msg('图片URL不能为空');
						return;
					}
					saveClassImage(cid, imageUrl);
					layer.close(index);
				});
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
			return false;
		}
	});
}

function saveClassImage(cid, imageUrl) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=setClassImage',
		data : {cid:cid, image:imageUrl},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('保存图片成功');
				// 更新预览图
				$("#preview"+cid).attr('src', imageUrl);
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
			return false;
		}
	});
}

// 保存按钮交互效果
$(document).ready(function() {
	// 为所有保存按钮添加点击效果
	$('.edit-form').on('submit', function(e) {
		var $form = $(this);
		var $btn = $form.find('.save-btn');
		var $icon = $btn.find('i');
		var originalIcon = $icon.attr('class');

		// 添加加载状态
		$btn.addClass('loading');
		$icon.removeClass('fa-check').addClass('fa-spinner fa-spin');

		// 禁用按钮防止重复提交
		$btn.prop('disabled', true);

		// 不阻止表单提交，让它正常提交
		// 表单会自动跳转，所以不需要恢复状态
	});

	// 输入框焦点效果
	$('.category-name-input').on('focus', function() {
		$(this).closest('.input-group').addClass('focused');
	}).on('blur', function() {
		$(this).closest('.input-group').removeClass('focused');
	});

	// 输入框变化时的视觉反馈
	$('.category-name-input').on('input', function() {
		var $input = $(this);
		var $saveBtn = $input.siblings('.save-btn');

		if ($input.val().trim() !== $input.data('original-value')) {
			$saveBtn.addClass('btn-warning').removeClass('btn-success');
			$saveBtn.find('.btn-text').text('保存');
			$saveBtn.find('i').removeClass('fa-check').addClass('fa-save');
		} else {
			$saveBtn.removeClass('btn-warning').addClass('btn-success');
			$saveBtn.find('.btn-text').text('保存');
			$saveBtn.find('i').removeClass('fa-save').addClass('fa-check');
		}
	});

	// 存储原始值
	$('.category-name-input').each(function() {
		$(this).data('original-value', $(this).val());
	});
});
</script>

<?php include './footer.php'; ?>
