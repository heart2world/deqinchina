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
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('Course/index');?>">在线课程</a></li>
			<li><a href="<?php echo U('Course/category');?>">课程类型</a></li>
			<li><a href="<?php echo U('Course/banner');?>">页面轮播图</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Course/index');?>">
            课程:
            <input type="text" name="title" style="width: 100px;" value="<?php echo I('request.title/s','');?>" placeholder="输入课程名称">
			课程类型:
			<select name="cate_id">
				<option value="0">全部</option>
				<?php if(is_array($cate)): foreach($cate as $key=>$vc): ?><option value="<?php echo ($vc["id"]); ?>" <?php if($vc["id"] == I('request.cate_id/s','')): ?>selected<?php endif; ?>><?php echo ($vc["name"]); ?></option><?php endforeach; endif; ?>
			</select>
            <input type="submit" class="btn btn-primary" value="查询" />
            <a class="btn btn-info" href="<?php echo U('Course/add');?>">新增课程</a>
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th width="50">ID</th>
					<th>创建时间</th>
					<th>课程名称</th>
					<th>课程类型</th>
					<th>报名时间</th>
					<th>排序</th>
					<th>状态</th>
					<th width="240"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $course_status=array("0"=>"隐藏","1"=>"显示"); ?>
				<?php if(is_array($course)): foreach($course as $key=>$vo): ?><tr>
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["create_time"]); ?></td>
					<td><?php echo ($vo["title"]); ?></td>
					<td>
						<?php if(is_array($cate)): foreach($cate as $key=>$vc): if($vc['id'] == $vo['cate_id']): echo ($vc["name"]); ?>
								<?php else: endif; endforeach; endif; ?>
					</td>
					<td><?php echo date('Y-m-d H:i:s',$vo['begin_time']); ?>--<?php echo date('Y-m-d H:i:s',$vo['end_time']); ?></td>
					<td><?php echo ($vo["listorder"]); ?></td>
					<td><?php echo ($course_status[$vo['status']]); ?></td>
					<td>
						<a class="label label-inverse" onclick="delete_course('<?php echo ($vo["id"]); ?>')"><?php echo L('DELETE');?></a>
						<a class="label label-inverse" href='<?php echo U("Course/edit",array("id"=>$vo["id"]));?>'><?php echo L('EDIT');?></a>
						<?php if($vo['status'] == 1): ?><a href="javascript:ban_course('<?php echo ($vo["id"]); ?>');" class="label label-inverse">隐藏</a>
							<?php else: ?>
							<a href="javascript:cancel_ban('<?php echo ($vo["id"]); ?>')" class="label label-warning">显示</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //删除在线课程
        function delete_course(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否删除？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Course/delete");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.status === 1){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.info, time: 2});
                                setTimeout(function(){
                                    location.reload();
                                },2000)
                            }else{
                                $.dialog({id: 'popup', lock: true,icon:"warning", content: res.info, time: 2});
                            }
                        }
                    })
                }
            })
        }

        //隐藏
        function ban_course(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定隐藏？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Course/ban");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.status === 1){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.info, time: 2});
                                setTimeout(function(){
                                    location.reload();
                                },2000)
                            }else{
                                $.dialog({id: 'popup', lock: true,icon:"warning", content: res.info, time: 2});
                            }
                        }
                    })
                }
            })
        }

        //显示
        function cancel_ban(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定显示？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Course/cancelban");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.status === 1){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.info, time: 2});
                                setTimeout(function(){
                                    location.reload();
                                },2000)
                            }else{
                                $.dialog({id: 'popup', lock: true,icon:"warning", content: res.info, time: 2});
                            }
                        }
                    })
                }
            })
        }
	</script>
</body>
</html>