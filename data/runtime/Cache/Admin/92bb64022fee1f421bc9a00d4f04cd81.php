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
			<li class="active"><a href="<?php echo U('Knowledge/index');?>">知识文库</a></li>
			<li><a href="<?php echo U('Knowledge/category');?>">知识类型</a></li>
			<li><a href="<?php echo U('Knowledge/banner');?>">页面轮播图</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Knowledge/index');?>">
            知识:
            <input type="text" name="title" style="width: 100px;" value="<?php echo I('request.title/s','');?>" placeholder="输入知识标题">
			知识类型:
			<select name="cate_id">
				<option value="0">全部</option>
				<?php if(is_array($cate)): foreach($cate as $key=>$vc): ?><option value="<?php echo ($vc["id"]); ?>" <?php if($vc["id"] == I('request.cate_id/s','')): ?>selected<?php endif; ?>><?php echo ($vc["name"]); ?></option><?php endforeach; endif; ?>
			</select>
            <input type="submit" class="btn btn-primary" value="查询" />
            <a class="btn btn-info" href="<?php echo U('Knowledge/add');?>">新增知识</a>
			<a class="btn btn-inverse" id="exportExcel">导出Excel</a>
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th width="50">ID</th>
					<th>创建时间</th>
					<th>标题</th>
					<th>知识类型</th>
					<th>学习时间(分钟)</th>
					<th>排序</th>
					<th>可查看对象</th>
					<th>状态</th>
					<th>阅读量</th>
					<th>点赞数</th>
					<th>分享数</th>
					<th>收藏数</th>
					<th width="240"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $train_status=array("0"=>"隐藏","1"=>"显示"); ?>
				<?php if(is_array($paper)): foreach($paper as $key=>$vo): ?><tr>
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["create_time"]); ?></td>
					<td><?php echo ($vo["title"]); ?></td>
					<td>
						<?php if(is_array($cate)): foreach($cate as $key=>$vc): if($vc['id'] == $vo['cate_id']): echo ($vc["name"]); ?>
								<?php else: endif; endforeach; endif; ?>
					</td>
					<td><?php echo ($vo["exam_time"]); ?></td>
					<td><?php echo ($vo["listorder"]); ?></td>
					<td>所有人</td>
					<td><?php echo ($train_status[$vo['status']]); ?></td>
					<td><?php echo ($vo["number"]); ?></td>
					<td><?php echo ($vo["number"]); ?></td>
					<td><?php echo ($vo["number"]); ?></td>
					<td><?php echo ($vo["number"]); ?></td>
					<td>
						<a class="label label-inverse" onclick="delete_paper('<?php echo ($vo["id"]); ?>')"><?php echo L('DELETE');?></a>
						<a class="label label-inverse" href='<?php echo U("Exam/edit",array("id"=>$vo["id"]));?>'><?php echo L('EDIT');?></a>
						<?php if($vo['status'] == 1): ?><a href="javascript:hidden_paper('<?php echo ($vo["id"]); ?>');" class="label label-inverse">隐藏</a>
							<?php else: ?>
							<a href="javascript:show_paper('<?php echo ($vo["id"]); ?>')" class="label label-warning">显示</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //删除试卷
        function delete_paper(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否删除？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/delete");?>',
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
        function hidden_paper(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定隐藏？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/hidden");?>',
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
        function show_paper(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "确定显示？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/show");?>',
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
        
        //导出excel
		$("#exportExcel").on('click',function () {
		    var title = $("input[name=title]").val().trim();
		    var cate_id = $("select[name=cate_id]").val();
            //location.href = '<?php echo U("Admin/Exam/excel");?>&title='+title+'&cate_id='+cate_id;
        })
	</script>
</body>
</html>