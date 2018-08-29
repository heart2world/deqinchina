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
			<li class="active"><a href="<?php echo U('SystemConfig/integration_rule');?>">积分规则</a></li>
			<li><a href="<?php echo U('SystemConfig/sensitive_word');?>">敏感词汇</a></li>
			<li><a href="<?php echo U('SystemConfig/protocol');?>">使用协议</a></li>
			<li><a href="<?php echo U('SystemConfig/app');?>">APP名称</a></li>
			<li><a href="<?php echo U('SystemConfig/game');?>">游戏PK设置</a></li>
		</ul>
		<div style="margin-top: 30px;margin-left: 30px;">
			<div style="float: left">
				<span>签到积分</span>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">每次签到获得</span>
					<span><input style="width: 100px;margin-right: 10px" type="number" min="0.0" step="0.1" id="single_sign_integral" value="<?php echo ($data["single_sign_integral"]); ?>">积分</span>
				</div>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">连续签到</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" id="days" value="<?php echo ($data["days"]); ?>">天,可获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="continuous_sign_integral" value="<?php echo ($data["continuous_sign_integral"]); ?>">积分</span>
				</div>
			</div>
			<div style="float: left;margin-left: 300px">
				<span>分享积分</span>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">每次分享一次知识文库,可获得</span>
					<span><input style="width: 100px;margin-right: 10px" type="number" min="0.0" step="0.1" id="share_integral" value="<?php echo ($data["share_integral"]); ?>">积分</span>
				</div>
			</div>
			<div style="clear: both;margin-bottom: 50px"></div>
			<div style="float: left">
				<span>评测积分</span>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">首次得分达到</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="first_exam_score" value="<?php echo ($data["first_exam_score"]); ?>">分,可获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="first_exam_integral" value="<?php echo ($data["first_exam_integral"]); ?>">积分</span>
				</div>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">首次得分</span>
					<span><input style="width: 60px" type="number" min="0.0" step="0.1" id="lowest_score" value="<?php echo ($data["lowest_score"]); ?>"> - <input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="highest_score" value="<?php echo ($data["highest_score"]); ?>">之间,可获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="first_exam_integral1" value="<?php echo ($data["first_exam_integral1"]); ?>">积分</span>
				</div>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">首次得分</span>
					<span><input style="width: 60px" type="number" min="0.0" step="0.1" id="lowest_score1" value="<?php echo ($data["lowest_score1"]); ?>"> - <input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="highest_score1" value="<?php echo ($data["highest_score1"]); ?>">之间,可获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="first_exam_integral2" value="<?php echo ($data["first_exam_integral2"]); ?>">积分</span>
				</div>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">补考得分达到</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="mk_exam_score" value="<?php echo ($data["mk_exam_score"]); ?>">分,可获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="mk_exam_integral" value="<?php echo ($data["mk_exam_integral"]); ?>">积分</span>
				</div>
			</div>
			<div style="float: left;margin-left: 200px">
				<span>答题PK积分</span>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">PK获胜后获得宝盒几率为</span>
					<span><input style="width: 100px;margin-right: 10px" type="number" min="0.00" step="0.01" id="treasure_box_probability" value="<?php echo ($data["treasure_box_probability"]); ?>">%</span>
				</div>
				<div style="margin-top: 20px;margin-bottom: 50px">
					<span style="margin-right: 10px">打开宝盒获得</span>
					<span><input style="width: 60px;" type="number" min="0.0" step="0.1" id="tb_lowest_integral" value="<?php echo ($data["tb_lowest_integral"]); ?>"> - <input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="tb_highest_integral" value="<?php echo ($data["tb_highest_integral"]); ?>"> 积分</span>
				</div>
				<span>热门帖子奖励</span>
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">发布帖子被设为热门,可获得</span>
					<span><input style="width: 100px;margin-right: 10px" type="number" min="0.0" step="0.1" id="hot_topics_integral" value="<?php echo ($data["hot_topics_integral"]); ?>"> 积分</span>
				</div>
			</div>
			<div style="clear: both;margin-bottom: 50px"></div>
			<div style="float: left">
				<div style="margin-top: 20px">
					<span style="margin-right: 10px">每人每天最多获得</span>
					<span><input style="width: 60px;margin-right: 10px" type="number" min="0.0" step="0.1" id="upper_limit" value="<?php echo ($data["upper_limit"]); ?>">积分</span>
				</div>
			</div>
			<div style="float: left;margin-left: 650px;margin-top: 20px">
				<a class="btn btn-success" onclick="save()"> 保存</a>
			</div>
		</div>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
		function save() {
			var single_sign_integral = $('#single_sign_integral').val();//每次签到获得积分
			var days = $('#days').val();//连续签到天数
			var continuous_sign_integral = $('#continuous_sign_integral').val();//持续签到获得积分
			var share_integral = $('#share_integral').val();//分享一次文库获得积分
			var first_exam_score = $('#first_exam_score').val();//首次达到分数 第一等级
			var first_exam_integral = $('#first_exam_integral').val();//首次达到分数获得积分  第一等级
			var lowest_score = $('#lowest_score').val();//首次考试达到最低....分（第二等级）
			var highest_score = $('#highest_score').val();//首次考试达到最高....分 （第二等级）
			var first_exam_integral1 = $('#first_exam_integral1').val();//达到分数获得积分 （第二等级）
			var lowest_score1 = $('#lowest_score1').val();//首次考试达到最低....分  （第三等级）
			var highest_score1 = $('#highest_score1').val();//首次考试达到最高....分 （第三等级）
			var first_exam_integral2 = $('#first_exam_integral2').val();//达到分数获得积分 （第三等级）
			var mk_exam_score = $('#mk_exam_score').val();  //补考达到分数
			var mk_exam_integral = $('#mk_exam_integral').val(); //补考获得积分
			var treasure_box_probability = $('#treasure_box_probability').val(); //宝箱概率
			var tb_lowest_integral = $('#tb_lowest_integral').val(); //宝箱最低可得积分
			var tb_highest_integral = $('#tb_highest_integral').val(); //宝箱最高可得积分
			var hot_topics_integral = $('#hot_topics_integral').val(); //热门帖子积分
			var upper_limit = $('#upper_limit').val(); //最多可得积分（没人每天）

            $.ajax({
                type:'POST',
                url:'<?php echo U("set_rule");?>',
                data:{
                    single_sign_integral:single_sign_integral,
					days:days,
					continuous_sign_integral:continuous_sign_integral,
					share_integral:share_integral,
					first_exam_score:first_exam_score,
					first_exam_integral:first_exam_integral,
                    lowest_score:lowest_score,
                    highest_score:highest_score,
                    first_exam_integral1:first_exam_integral1,
                    lowest_score1:lowest_score1,
                    highest_score1:highest_score1,
                    first_exam_integral2:first_exam_integral2,
                    mk_exam_score:mk_exam_score,
                    mk_exam_integral:mk_exam_integral,
                    treasure_box_probability:treasure_box_probability,
                    tb_lowest_integral:tb_lowest_integral,
                    tb_highest_integral:tb_highest_integral,
                    hot_topics_integral:hot_topics_integral,
                    upper_limit:upper_limit
				},
                dataType:'json',
                success:function (res) {
                    if(res.code == 0){
                        $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                        setTimeout(function(){
                            location.href='<?php echo U("integration_rule");?>';
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