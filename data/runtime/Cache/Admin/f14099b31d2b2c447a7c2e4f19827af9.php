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
			<li class="active"><a href="<?php echo U('rbac/index');?>"><?php echo L('ADMIN_RBAC_INDEX');?></a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('rbac/index');?>">
			角色名:
			<input type="text" name="role_name" style="width: 100px;" value="<?php echo I('request.role_name/s','');?>" placeholder="请输入角色名">
			<input type="submit" class="btn btn-primary" value="查询" />
			<a href="<?php echo U('rbac/roleadd');?>" class="btn btn-info">新增</a>
		</form>
		<form method="post">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th width="30">ID</th>
						<th align="left"><?php echo L('ROLE_NAME');?></th>
						<th align="left"><?php echo L('ROLE_DESCRIPTION');?></th>
						<th width="240"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><tr>
						<td><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo["name"]); ?></td>
						<td><?php echo ($vo["remark"]); ?></td>
						<td>
							<?php if($vo['id'] == 1): ?><font color="#cccccc"><?php echo L('ROLE_SETTING');?></font>
								<font color="#cccccc"><?php echo L('DELETE');?></font>
								<font color="#cccccc"><?php echo L('EDIT');?></font>
							<?php else: ?>
								<a class="label label-inverse" href="<?php echo U('Rbac/authorize',array('id'=>$vo['id']));?>"><?php echo L('ROLE_SETTING');?></a>

								<a class="label label-inverse" onclick="delete_role('<?php echo ($vo["id"]); ?>')"><?php echo L('DELETE');?></a>
								<a class="label label-inverse" href="<?php echo U('Rbac/roleedit',array('id'=>$vo['id']));?>"><?php echo L('EDIT');?></a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //删除角色
        function delete_role(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否删除？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Rbac/roledelete");?>',
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