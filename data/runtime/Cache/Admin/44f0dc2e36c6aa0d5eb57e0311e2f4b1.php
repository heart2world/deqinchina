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
			<li class="active"><a href="<?php echo U('Exam/question');?>">试题库</a></li>
			<li><a href="<?php echo U('Exam/banner');?>">页面轮播图</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Exam/question');?>">
            试题:
            <input type="text" name="title" style="width: 100px;" value="<?php echo I('request.title/s','');?>" placeholder="输入试题标题">
			题型:
			<select name="type">
				<option value="0">全部</option>
				<option value="1" <?php if($type == 1): ?>selected<?php endif; ?>>单选</option>
				<option value="2" <?php if($type == 2): ?>selected<?php endif; ?>>多选</option>
				<option value="3" <?php if($type == 3): ?>selected<?php endif; ?>>判断</option>
				<option value="4" <?php if($type == 4): ?>selected<?php endif; ?>>主观题</option>
			</select>
            <input type="submit" class="btn btn-primary" value="查询" />
            <a class="btn btn-info" href="<?php echo U('Exam/quesadd');?>">新增试题</a>
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th width="50">ID</th>
					<th>试题</th>
					<th width="200">试题类型</th>
					<th width="240"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $ques_types=array("1"=>"单选","2"=>"多选","3"=>"判断","4"=>"主观题"); ?>
				<?php if(is_array($question)): foreach($question as $key=>$vo): ?><tr>
					<td><?php echo ($vo["id"]); ?></td>
					<td><?php echo ($vo["topic"]); ?></td>
					<td><?php echo ($ques_types[$vo['type']]); ?></td>
					<td>
						<a class="label label-inverse" onclick="delete_question('<?php echo ($vo["id"]); ?>')"><?php echo L('DELETE');?></a>
						<a class="label label-inverse" href='<?php echo U("Exam/quesedit",array("id"=>$vo["id"]));?>'><?php echo L('EDIT');?></a>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        //删除培训内容
        function delete_question(id) {
            $.dialog({
                id: 'popup',
                lock: true,
                icon:"question",
                content: "是否删除？",
                cancel: true,
                ok: function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo U("Admin/Exam/ques_delete");?>',
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