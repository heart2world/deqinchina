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
	.top li{
		list-style: none;
		float: left;
		width:33%;
	}
	.bottom li{
		list-style: none;
		float: left;
	}
	ul{
		width:100%;
		display:inline-block;
		margin: 0;
	}
	.kr-mar{
		margin-top: 12px;
	}
</style>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('Bbs/index');?>">BBS管理</a></li>
			<li class="active"><a href="">帖子详情</a></li>
		</ul>
		<div>
			<ul class="top">
				<li>
					<div>
						<img style="width:30%;border-radius: 50px;" src="<?php echo sp_get_image_preview_url('admin/20180815/5b73e89c85980.jpg');?>">
						<!--<img style="width:30%;border-radius: 50px;" src="<?php echo sp_get_image_preview_url($user['avatar']);?>">-->
						<div style="width:60%;float: right;">
							<label><b><?php echo ($user['user_nicename']); ?></b></label>
							<label><?php echo ($info['create_time']); ?></label>
						</div>
					</div>
				</li>
				<li>
					<b style="font-size: 18px;"><?php echo ($info["title"]); ?></b>
					<p style="margin-top: 6px;">
						<?php $images = explode(',',$info['images']) ?>
						<?php if(is_array($images)): foreach($images as $key=>$vo): ?><img style="max-width: 48%;margin-right: 5px;" src="<?php echo sp_get_image_preview_url($vo);?>"><?php endforeach; endif; ?>
					</p>
				</li>
				<li style="text-align: center;">
					<?php if($user['banned'] == 1): ?><a class="label label-inverse" onclick="banned('<?php echo ($user["id"]); ?>')">禁言</a>
						<?php else: ?>
						<a class="label label-warning" onclick="cancel_banned('<?php echo ($user["id"]); ?>')">取消禁言</a><?php endif; ?>
				</li>
			</ul>
			<div style="position: relative;">
				<p><span style="color: #92C33c;">| </span>大家热议</p>
			</div>
			<?php if(is_array($comment)): foreach($comment as $key=>$vc): ?><ul class="bottom" style="margin-bottom: 20px;">
				<li style="width:33%;">
					<div>
						<img style="width:30%;border-radius: 50px;" src="<?php echo sp_get_image_preview_url('admin/20180815/5b73e89c85980.jpg');?>">
						<!--<img style="width:30%;border-radius: 50px;" src="<?php echo sp_get_image_preview_url($vc['avatar']);?>">-->
						<div style="width:60%;float: right;">
							<label><b><?php echo ($vc['username']); ?></b></label>
							<label><?php echo ($vc['create_time']); ?></label>
						</div>
					</div>
				</li>
				<li style="width:66%;">
					<ul>
						<li style="width:50%;font-size: 16px;"><?php echo ($vc["comment"]); ?></li>
						<li style="width:49%;text-align: center;">
							<a class="label label-inverse" href="javascript:delete_comment('<?php echo ($vc["id"]); ?>');">删除</a>
							<?php if($vc['banned'] == 1): ?><a class="label label-inverse " onclick="banned('<?php echo ($vc["user_id"]); ?>')">禁言</a>
								<?php else: ?>
								<a class="label label-warning" onclick="cancel_banned('<?php echo ($vc["user_id"]); ?>')">取消禁言</a><?php endif; ?>
						</li>
						<?php if(is_array($vc['reply'])): foreach($vc['reply'] as $kr=>$vr): ?><ul class="<?php if($kr == 0): ?>kr-mar<?php endif; ?>">
								<li style="width:50%;font-size: 14px;">
									<span style="color: #92C33c;"><?php echo ($vr["username"]); ?>：</span><?php echo ($vr["reply"]); ?>
								</li>
								<li style="width:49%;text-align: center;">
									<a onclick="delete_reply('<?php echo ($vr["id"]); ?>')" style="color: #00a0e9;">删除</a>
								</li>
							</ul><?php endforeach; endif; ?>
					</ul>
				</li>
			</ul><?php endforeach; endif; ?>
		</div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
    //禁言
    function banned(id) {
        $.dialog({
            id: 'popup',
            lock: true,
            icon:"question",
            content: "禁言后该员工3天内不能在发帖、回帖，是否禁言？",
            cancel: true,
            ok: function(){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo U("Admin/Bbs/banned");?>',
                    data: {id:id},
                    dataType: 'json',
                    success: function (res) {
                        if(res.rs == 0){
                            $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            setInterval(function(){
                                location.reload();
                            },2000)
                        }else{
                            $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                        }
                    }
                })
            }
        })
    }

    //取消禁言
    function cancel_banned(id) {
        $.dialog({
            id: 'popup',
            lock: true,
            icon:"question",
            content: "是否取消禁言？",
            cancel: true,
            ok: function(){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo U("Admin/Bbs/cancel_banned");?>',
                    data: {id:id},
                    dataType: 'json',
                    success: function (res) {
                        if(res.rs == 0){
                            $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            setInterval(function(){
                                location.reload();
                            },2000)
                        }else{
                            $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                        }
                    }
                })
            }
        })
    }

    //删除评论
    function delete_comment(id) {
        $.dialog({
            id: 'popup',
            lock: true,
            icon:"question",
            content: "会一并删除该条评论的回复，是否删除？",
            cancel: true,
            ok: function(){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo U("Admin/Bbs/comment_delete");?>',
                    data: {id:id},
                    dataType: 'json',
                    success: function (res) {
                        if(res.rs == 0){
                            $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            setInterval(function(){
                                location.reload();
                            },2000)
                        }else{
                            $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                        }
                    }
                })
            }
        })
    }

    //删除回复
    function delete_reply(id) {
        $.dialog({
            id: 'popup',
            lock: true,
            icon:"question",
            content: "是否删除？",
            cancel: true,
            ok: function(){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo U("Admin/Bbs/reply_delete");?>',
                    data: {id:id},
                    dataType: 'json',
                    success: function (res) {
                        if(res.rs == 0){
                            $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            setInterval(function(){
                                location.reload();
                            },2000)
                        }else{
                            $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                        }
                    }
                })
            }
        })
    }
</script>
</body>
</html>