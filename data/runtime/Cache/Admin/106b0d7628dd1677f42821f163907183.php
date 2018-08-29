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
			<li class="active"><a href="<?php echo U('Bbs/index');?>">BBS管理</a></li>
			<li><a href="<?php echo U('Bbs/category');?>">帖子类型</a></li>
			<li><a href="<?php echo U('Bbs/carousel');?>">页面轮播图</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Bbs/index');?>">
             帖子标题:
            <input type="text" name="title" style="width: 100px;" value="<?php echo I('request.title/s','');?>" placeholder="输入帖子标题">
            类型:
			<select name="cat">
				<option value="0">全部</option>
				<?php if(is_array($category)): foreach($category as $key=>$vc): ?><option value="<?php echo ($vc["id"]); ?>" <?php if($vc["id"] == I('request.cat/s','')): ?>selected<?php endif; ?>><?php echo ($vc["name"]); ?></option><?php endforeach; endif; ?>
			</select>
            <input type="submit" class="btn btn-primary" value="查询" />
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th width="50">ID</th>
					<th>发布时间</th>
					<th>标题</th>
					<th>发帖人</th>
					<th>类型</th>
					<th>阅读量</th>
					<th>回复量</th>
					<th>是否热门</th>
					<th width="240"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $bbs_statuses=array("0"=>"否","1"=>"是"); ?>
				<?php if(is_array($bbs)): foreach($bbs as $key=>$vo): ?><tr>
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["create_time"]); ?></td>
					<td><?php echo ($vo["title"]); ?></td>
					<td><?php echo ($vo["username"]); ?></td>
					<td><?php echo ($vo["catename"]); ?></td>
					<td><?php echo ($vo["read"]); ?></td>
					<td><?php echo ($vo["reply"]); ?></td>
					<td><?php echo ($bbs_statuses[$vo['status']]); ?></td>
					<td>
						<a class="label label-inverse" href='<?php echo U("Bbs/info",array("id"=>$vo["id"]));?>'>详情</a>
						<a class="label label-inverse" onclick="delete_bbs('<?php echo ($vo["id"]); ?>')">删贴</a>
						<?php if($vo['status'] == 1): ?><a onclick="cancel_popular('<?php echo ($vo["id"]); ?>')" class="label label-inverse">取消热门</a>
							<?php else: ?>
							<a onclick="as_popular('<?php echo ($vo["id"]); ?>')" class="label label-warning">设为热门</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
		//删除帖子
        function delete_bbs(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否确认删除该帖子？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Bbs/delete");?>',
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

        //设为热门
		function as_popular(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定设为热门？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Bbs/popular");?>',
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

        //取消热门
        function cancel_popular(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定取消热门？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Bbs/cancel_popular");?>',
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