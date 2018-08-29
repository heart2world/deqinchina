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
			<li><a href="<?php echo U('IntegralMall/mg_gift');?>">礼品管理</a></li>
			<li class="active"><a href="<?php echo U('IntegralMall/exchange_order');?>">兑换订单</a></li>
			<li><a href="<?php echo U('IntegralMall/address');?>">收货地址</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('IntegralMall/exchange_order');?>">
			兑换者
			<input type="text" name="nick_name" style="width: 140px;margin-right: 30px" value="<?php echo ($nick_name); ?>" placeholder="请输入兑换者昵称">
			<input type="submit" class="btn btn-primary" value="查询" style="margin-right: 30px" />
		</form>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th width="30">ID</th>
						<th align="left">兑换时间</th>
						<th align="left">昵称</th>
						<th align="left">兑换礼品</th>
						<th align="left">数量</th>
						<th align="left">联系电话</th>
						<th align="left">收货地址</th>
						<th align="left">状态</th>
						<th width="240">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
							<td><?php echo ($vo["id"]); ?></td>
							<td><?= date('Y-m-d H:i:s',$vo['create_time'])?></td>
							<td><?php echo ($vo["nick_name"]); ?></td>
							<td><?php echo ($vo["gift_name"]); ?></td>
							<td><?php echo ($vo["number"]); ?></td>
							<td><?php echo ($vo["mobile"]); ?></td>
							<td><?php echo ($vo["address"]); ?></td>
							<?php if($vo["status"] == 1): ?><td>兑换中</td><?php endif; ?>
							<?php if($vo["status"] == 2): ?><td>派送中</td><?php endif; ?>
							<?php if($vo["status"] == 3): ?><td>已完成</td><?php endif; ?>
							<td>
								<?php if($vo["status"] == 1): ?><a class="btn btn-success" onclick="change_status('<?php echo ($vo["id"]); ?>',2)">确认发货</a><?php endif; ?>
								<?php if($vo["status"] == 2): ?><a class="btn btn-success" onclick="change_status('<?php echo ($vo["id"]); ?>',3)">已完成</a><?php endif; ?>
							</td>
						</tr><?php endforeach; endif; ?>
					<?php if(count($data) == 0): ?><tr><td colspan="9">暂无数据</td></tr><?php endif; ?>
				</tbody>
			</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //更改状态
        function change_status(id,status) {
            $.ajax({
                type: 'POST',
                url: '<?php echo U("change_order");?>',
                data: {id:id,status:status},
                dataType: 'json',
                success: function (res) {
                    if(res.code == 0){
                        setInterval(function(){
                            location.reload();
                        },1000)
                    }else{
                        $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                    }
                }
            })
        }
	</script>
</body>
</html>