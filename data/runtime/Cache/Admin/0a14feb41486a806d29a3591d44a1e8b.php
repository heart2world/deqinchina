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
	.groups_info label{
		display: inline;
		margin-right: 12px;
		height:25px;
		line-height: 25px;
	}
	.groups_info label input{
		margin: 0;
	}
	.visible-radio{
		cursor: default;
	}
	.visible-radio span{
		height: 28px;
		line-height: 28px;
		margin-right: 12px;
	}
	.visible-radio span input{
		margin-right: 3px;
	}
</style>
</head>
<body>
<div class="wrap js-check-wrap" id="app">
	<ul class="nav nav-tabs">
		<li><a href="<?php echo U('Course/index');?>">在线课程</a></li>
		<li class="active"><a href="<?php echo U('Course/add');?>">在线课程添加</a></li>
	</ul>
	<form id="formInfo" method="post" class="form-horizontal" enctype="multipart/form-data">
		<fieldset>
			<div class="control-group">
				<label class="control-label">名称</label>
				<div class="controls">
					<input type="text" style="width:400px;" name="title" id="title" required value="" placeholder="请输入课程名称"/>
					<span class="form-required">*</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">缩略图</label>
				<div class="controls">
					<input type="hidden" name="imgurl" id="thumb" value="">
					<a href="javascript:upload_one_image('图片上传','#thumb');">
						<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb-preview" width="135" style="cursor: hand" />
					</a>
					<input type="button" class="btn btn-small" onclick="$('#thumb-preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">课程类型</label>
				<div class="controls">
					<select name="cate_id">
						<option value="0">请选择课程类型</option>
						<?php if(is_array($cate)): foreach($cate as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">排序</label>
				<div class="controls">
					<input type="number" name="listorder" id="listorder" value="" style="width: 400px" placeholder="请输入序号，选填">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">简介</label>
				<div class="controls">
					<textarea name="remark" rows="2" cols="20" id="remark" class="inputtext" style="height: 100px; width: 500px;"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">可见对象</label>
				<div class="controls groups_info">
					<label class="visible-radio">
						<span><input name="visible_beau" type="radio" value="0" checked />所有人</span>
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">报名时间</label>
				<div class="controls">
					<input type="text" name="begin_time" id="beginTime" placeholder="报名开始时间" style="width: 160px;">
					<span>--</span>
					<input type="text" name="end_time" id="endTime" placeholder="报名截止时间" style="width: 160px;">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">报名地址</label>
				<div class="controls">
					<input type="text" name="address" id="address" value="" style="width: 400px" placeholder="请输入报名地址链接">
				</div>
			</div>
		</fieldset>
		<div class="form-actions">
			<button class="btn btn-info" id="butSubmit" type="button"><?php echo L('SAVE');?></button>
		</div>
	</form>
</div>
<script type="text/javascript" src="/public/js/common.js"></script>
<script type="text/javascript" src="/public/js/laydate/laydate.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
    //时间选择器
    laydate.render({elem: '#beginTime',type: 'datetime'});
    laydate.render({elem: '#endTime',type: 'datetime'});

    $("#butSubmit").on('click',function () {
        var dataValues = $('#formInfo').serialize();
        $.ajax({
            type:'POST',
            url:'<?php echo U("Course/add_post");?>',
            data:dataValues,
            dataType:'json',
            success:function (res) {
                if(res.status === 1){
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.info, time: 2});
                    setTimeout(function(){
                        location.href='<?php echo U("Admin/Course/index");?>';
                    },2000);
                }else{
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.info, time: 2});
                }
            }
        })
    });
</script>
</body>
</html>