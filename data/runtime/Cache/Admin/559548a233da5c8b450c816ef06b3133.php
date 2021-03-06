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
			<li><a href="<?php echo U('Exam/index');?>">在线测评</a></li>
			<li><a href="<?php echo U('Exam/category');?>">测评类型</a></li>
			<li><a href="<?php echo U('Exam/question');?>">试题库</a></li>
			<li class="active"><a href="<?php echo U('Exam/banner');?>">页面轮播图</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Exam/banner');?>">
             标题:
            <input type="text" name="name" style="width: 100px;" value="<?php echo I('request.name/s','');?>">
            <input type="submit" class="btn btn-primary" value="查询" />
			<a class="btn btn-info" href="<?php echo U('Exam/banneradd');?>">新增轮播图</a>
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th width="50">ID</th>
					<th>标题</th>
					<th>排序</th>
					<th>状态</th>
					<th width="240"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $car_statuses=array("0"=>"下架","1"=>"上架"); ?>
				<?php if(is_array($banner)): foreach($banner as $key=>$vo): ?><tr>
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["name"]); ?></td>
					<td><?php echo ($vo["listorder"]); ?></td>
					<td><?php echo ($car_statuses[$vo['status']]); ?></td>
					<td>
						<a class="label label-inverse" onclick="delete_banner('<?php echo ($vo["id"]); ?>')"><?php echo L('DELETE');?></a>
						<a class="label label-inverse" href='<?php echo U("Exam/banneredit",array("id"=>$vo["id"]));?>'><?php echo L('EDIT');?></a>
						<?php if($vo['status'] == 1): ?><a onclick="obtained('<?php echo ($vo["id"]); ?>')" class="label label-inverse">下架</a>
							<?php else: ?>
							<a onclick="shelf('<?php echo ($vo["id"]); ?>')" class="label label-warning">上架</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //删除轮播图
        function delete_banner(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否删除？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/banner_delete");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.rs == 0){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout(function(){
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

        //上架
        function shelf(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定上架？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/shelf");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.rs == 0){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout(function(){
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

        // 下架
        function obtained(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定下架？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/obtained");?>',
                        data: {id:id},
                        dataType: 'json',
                        success: function (res) {
                            if(res.rs == 0){
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout(function(){
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