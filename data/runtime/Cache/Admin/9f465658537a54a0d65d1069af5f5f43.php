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
<link href="/public/js/jedate/skin/jedate.css" rel="stylesheet">
<style>
	span{
		margin-right: 20px;
	}

	.laydate-icon {
		background: url('/public/js/jedate/skin/jedate.png') no-repeat right;
	}

	.time {
		width: 249px;
		height: 15px;
		line-height: 15px;
		padding: 6px 0 6px 10px;
		border: 1px solid #C1C1C1;
	}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('Employee/behavior');?>">员工行为数据</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Employee/behavior');?>">
			<span>部门</span>
			<select style="margin-right: 10px;width: 170px;">
				<option>请选择</option>
			</select>
			<select style="margin-right: 10px;width: 170px;">
				<option>请选择</option>
			</select>
			<span style="margin-left: 60px;">员工</span>
			<input type="text" name="user_name" style="width: 200px;" value="<?php echo ($user_name); ?>" placeholder="请输入员工姓名">
			<span style="margin-left: 60px;">操作类型</span>
			<select name="type" id="type" style="margin-right: 10px;width: 150px;">
				<option value="0">全部</option>
				<option value="1">登录</option>
				<option value="2">学习</option>
				<option value="3">测评</option>
				<option value="4">答题PK</option>
				<option value="5">发帖</option>
			</select>
			<div style="margin-top: 20px;"></div>
			<span>时间段</span>
			<input class="time laydate-icon" type="text" name="start_time" autocomplete="off" id="start_time" style="cursor: pointer;width: 165px;margin-right: 20px" placeholder="请选择日期" value="<?php echo ($start_time); ?>">
			<input class="time laydate-icon" type="text" name="end_time" autocomplete="off" id="end_time" style="cursor: pointer;width: 165px;" placeholder="请选择日期" value="<?php echo ($end_time); ?>">&nbsp;
			<input type="submit" class="btn btn-primary" value="查询" style="margin-right: 30px" />
			<a href="<?php echo U('Employee/export');?>&type=<?php echo ($type); ?>&user_name=<?php echo ($user_name); ?>&start_time=<?php echo ($start_time); ?>&end_time=<?php echo ($end_time); ?>" class="btn btn-info">导出EXCEL</a>
		</form>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th width="30">ID</th>
						<th align="left">操作时间</th>
						<th align="left">姓名</th>
						<th align="left">邮箱</th>
						<th align="left">部门</th>
						<th align="left">区域</th>
						<th align="left">职务</th>
						<th align="left">操作类型</th>
						<th align="left">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
							<td><?php echo ($vo["id"]); ?></td>
							<td><?= date('Y-m-d H:i:s',$vo['create_time'])?></td>
							<td><?php echo ($vo["user_name"]); ?></td>
							<td><?php echo ($vo["email"]); ?></td>
							<td><?php echo ($vo["department"]); ?></td>
							<td><?php echo ($vo["area"]); ?></td>
							<td><?php echo ($vo["duty"]); ?></td>
							<?php if($vo["type"] == 1): ?><td>登录</td><?php endif; ?>
							<?php if($vo["type"] == 2): ?><td>学习</td><?php endif; ?>
							<?php if($vo["type"] == 3): ?><td>测评</td><?php endif; ?>
							<?php if($vo["type"] == 4): ?><td>答题PK</td><?php endif; ?>
							<?php if($vo["type"] == 5): ?><td>发帖</td><?php endif; ?>
							<td>
								<a class="btn btn-success" onclick="detail('<?php echo ($vo["email"]); ?>')">查看详情</a>
							</td>
						</tr><?php endforeach; endif; ?>
					<?php if(count($data) == 0): ?><tr><td colspan="9">暂无数据</td></tr><?php endif; ?>
				</tbody>
			</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script src="/public/js/jedate/jedate.js"></script>
	<script type="text/javascript">
		$('#type').val('<?php echo ($type); ?>');

        $(function () {
            jeDate("#start_time",{
                theme:{bgcolor:"#00A680",pnColor:"#00DDAA"},
                format: "YYYY-MM-DD hh:mm:ss",
            });
            jeDate("#end_time",{
                theme:{bgcolor:"#00A680",pnColor:"#00DDAA"},
                format: "YYYY-MM-DD hh:mm:ss",
            });
        });

        function detail(email) {
            var type = '<?php echo ($type); ?>';
            var start_time = '<?php echo ($start_time); ?>';
            var end_time = '<?php echo ($end_time); ?>';
            console.log(type);
            console.log(email);
            console.log(start_time);

            location.href = '<?php echo U("personal_behavior");?>&type='+type+'&start_time='+start_time+'&end_time='+end_time+'&email='+email;
        }
	</script>
</body>
</html>