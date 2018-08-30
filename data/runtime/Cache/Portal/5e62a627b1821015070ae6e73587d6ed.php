<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <meta charset="utf-8">
    <title>chatroom</title>
</head>
<body onload="link();">
<div style="background: red;width: 200px;height: 5px;"></div>

<div style="background: #564562;width: 200px;height: 5px;margin-top: 20px;"></div>
<div>
    <input name="uid" id="uid" value="">
    <input name="msg" id="msg" value="" size="50">
    <input type="hidden" id="room" value="" />
    <button type="button" id="send" onclick="sendsmg()">发送</button>
</div>
<script type="text/javascript" src="http://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"/>
<script src="/public/js/common.js"></script>
<script>
  function link()
  {
       ws = new WebSocket("ws://127.0.0.1:8282");
       ws.sendsmg =sendsmg;
       ws.onmessage = onmessage;       
  }
  function  sendsmg()
  {
      $.ajax({
        url:"<?php echo U('index/send');?>",
        data:{room:$("#room").val(),message:$("#msg").val(),uid:$("#uid").val()},
        type:'POST',
        success:function(data)
        {

        }
      })
  }
    // 服务端发来消息时
    function onmessage(e)
    {        
        var data =eval('('+e.data+')');
        switch(data['type']){
            case 'init':
                console.log(data.clientId);
                $.ajax({
                    url:"<?php echo U('Index/bind');?>",
                    data:{client_id:data.clientId},
                    type:'POST',
                    success:function(data)
                    {
                        $("#room").val(data.room);
                    }
                })
                break;
            case 'say':
                console.log(data.message);
                break;
            default:
              break;
        }
    }
</script>
</body>
</html>