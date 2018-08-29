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
			<li><a href="<?php echo U('SystemConfig/integration_rule');?>">积分规则</a></li>
			<li><a href="<?php echo U('SystemConfig/sensitive_word');?>">敏感词汇</a></li>
			<li><a href="<?php echo U('SystemConfig/protocol');?>">使用协议</a></li>
			<li><a href="<?php echo U('SystemConfig/app');?>">APP名称</a></li>
			<li class="active"><a href="<?php echo U('SystemConfig/game');?>">游戏PK设置</a></li>
		</ul>
		<div style="margin-top: 30px;margin-left: 30px;">
			<span>游戏PK设置</span>
			<div style="margin-top: 25px">
				<span>每次抽取</span>
				<input type="number" id="number" value="<?php echo ($data["number"]); ?>" style="width: 60px;margin-left: 50px;margin-right: 30px">
				<span>道题进行PK</span>
			</div>
			<div style="margin-top: 20px">
				<span>每道题答题时限</span>
				<input type="number" id="time" value="<?php echo ($data["time"]); ?>" style="width: 60px;margin-left: 10px;margin-right: 30px">
				<span>秒</span>
			</div>
			<div style="float: left;margin-left: 300px;margin-top: 20px">
				<a class="btn btn-success" onclick="save()"> 保存</a>
			</div>
		</div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
		function save() {
		    var number = $('#number').val();
		    var time = $('#time').val();
            $.ajax({
                type:'POST',
                url:'<?php echo U("set_game");?>',
                data:{number:number,time:time},
                dataType:'json',
                success:function (res) {
                    if(res.code == 0){
                        $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                        setTimeout(function(){
                            location.href='<?php echo U("game");?>';
                        },1000);
                    } else {
                        $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                    }
                }

            })
        }
	</script>
</body>
</html>