<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
        .tips_error{color:red;}
        .table-bordered th,.table-bordered td{
            text-align: center;
        }
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
	//全局变量
	var GV = {
	    ROOT: "/",
	    WEB_ROOT: "/",
	    JS_ROOT: "public/js/",
	    APP:'<?php echo (MODULE_NAME); ?>'/*当前应用名*/
	};
	</script>
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script>
    	$(function(){
    		$("[data-toggle='tooltip']").tooltip();
    	});
    </script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
<style type="text/css">
	.edui-default{
		width:80%;
	}
</style>
</head>
<body>
	<div class="wrap js-check-wrap" id="app">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('Help/index');?>">用户帮助</a></li>
			<li class="active"><a href="<?php echo U('Help/add');?>">帮助添加</a></li>
		</ul>
		<form id="helpAdd" method="post" class="form-horizontal" enctype="multipart/form-data">
			<fieldset>
				<div class="control-group">
					<label class="control-label">标题</label>
					<div class="controls">
						<input type="text" name="title" maxlength="120" placeholder="请输入标题">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">图片</label>
					<div class="controls">
						<input type="hidden" name="image" id="thumb" value="">
						<a href="javascript:upload_one_image('图片上传','#thumb');">
							<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb-preview" width="135" style="cursor: hand" />
						</a>
						<input type="button" class="btn btn-small" onclick="$('#thumb-preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">排序</label>
					<div class="controls">
						<input type="number" name="listorder" placeholder="请输入项目排序，选填">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">简介</label>
					<div class="controls">
						<textarea name="description"></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">内容</label>
					<div class="controls">
						<script type="text/plain" id="content" name="content"></script>
					</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<button type="button" class="btn btn-success" @click="add()"><?php echo L('SAVE');?></button>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/vue.js"></script>
	<script src="/public/js/content_addtop.js"></script>
	<script src="/public/js/define_my.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //编辑器路径定义
        var editorURL = GV.WEB_ROOT;
	</script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
	<script type="text/javascript">
        $(function() {
            Wind.use('validate','artDialog', function() {
                //编辑器
                editorcontent = new baidu.editor.ui.Editor();
                editorcontent.render('content');
                try {
                    editorcontent.sync();
                } catch (err) {
                }
                //增加编辑器验证规则
                jQuery.validator.addMethod('editorcontent', function() {
                    try {
                        editorcontent.sync();
                    } catch (err) {
                    }
                    return editorcontent.hasContents();
                });
            });
            ////-------------------------
        });

        var app = new Vue({
            el:"#app",
            data:{info:{}},
            created:function(){},
            methods:{
                add:function () {
                    var dataValues=$('#helpAdd').serialize();
                    $.ajax({
                        type:'POST',
                        url:'<?php echo U("Admin/Help/add_post");?>',
                        data:dataValues,
                        dataType:'json',
                        success:function (res) {
                            if(res.rs == 0){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout(function(){
                                    location.href='<?php echo U("Admin/Help/index");?>';
                                },2000);
                            } else {
                                $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                            }
                        }

                    })
                }
            }
        });
	</script>
</body>
</html>