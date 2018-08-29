<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 
// +----------------------------------------------------------------------
// | Author: Dean <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use GatewayWorker\Lib\Gateway;
use Think\Controller;
require APP_PATH.'/GatewayWorker/vendor/autoload.php';

class IndexController extends Controller {
    // 服务器绑定当前创建房间
    public function bind()
    {
        file_put_contents('aa.txt', var_export($_GET,true));
        $client_id = I('get.client_id');
        Gateway::$registerAddress = '127.0.0.1:1238';

        // 假设用户已经登录，用户uid和群组id在session中
        $uid =I('get.uid');
        // 被邀请者id
        $guid =I('get.guid');        
        $isroom =I('isroom');
        if($isroom==1)
        {
            $group_id = $uid;
            // client_id与uid绑定
            Gateway::bindUid($client_id, $uid);
        }else
        {
            $group_id  = I('get.room');
            // client_id与uid绑定
            Gateway::bindUid($client_id, $guid);
        }
        
        // 加入某个群组（可调用多次加入多个群组）
        Gateway::joinGroup($client_id, $group_id);
        if($isroom ==1)
        {
            $username =M('member')->where("id='$uid'")->getField('email');
            $data['title']  = $username.'向你发起了挑战，去迎战吧！';
            $data['roomid'] = $group_id;
            $data['fromuserid'] =$uid;
            $data['touserid'] =$guid;
            $data['createtime'] =time();
            M('message')->add($data);
        }        
        $_SESSION['roomid'] =$group_id;
        $this->ajaxReturn(array('status'=>0,'room'=>$group_id));
    }
    // 被邀请者加入成功通知
    public function send(){
        $request = I('post.');
        Gateway::$registerAddress = '127.0.0.1:1238';
        // 获得数据
        $userGuid=$request['uid'];
        $roomId=$request['roomid'];
        $message=trim($request['message']);      
        // 发送信息应当发送json数据,同时应该返回发送的用户的guid,用于客户端进行判断使用
        $dataArr=json_encode(array(
            'type'=>'join',
            'message' =>$userGuid.'加入成功',
            'user'=>$userGuid
        ));
    
        // 向roomId的分组发送数据
        Gateway::sendToGroup($roomId,$dataArr);
        $this->ajaxReturn(array('status'=>0));
    }
     public function send2(){
        $request = I('post.');
        Gateway::$registerAddress = '127.0.0.1:1238';
        // 获得数据
        $userGuid=$request['uid'];
        $roomId=$request['roomid'];     
        // 发送信息应当发送json数据,同时应该返回发送的用户的guid,用于客户端进行判断使用
        $dataArr=json_encode(array(
            'type'=>'start',
            'message' =>$userGuid.'开始答题',
            'user'=>$userGuid
        ));
    
        // 向roomId的分组发送数据
        Gateway::sendToGroup($roomId,$dataArr);
        $this->ajaxReturn(array('status'=>0));
    }
     // 答题通知
    public function sendanswer(){
        $request = I('post.');
        Gateway::$registerAddress = '127.0.0.1:1238';
        // 获得数据
        $userGuid = $request['uid'];
        $roomId   = $request['roomid'];
        $isright  = $request['isright'];// 1答对   0答错    
        // 发送信息应当发送json数据,同时应该返回发送的用户的guid,用于客户端进行判断使用
        $dataArr=json_encode(array(
            'type'=>'answer',
            'message' =>$userGuid.'答题成功',
            'user'=>$userGuid,
            'isright'=>$isright
        ));
        
        // 向roomId的分组发送数据
        Gateway::sendToGroup($roomId,$dataArr);
        $this->ajaxReturn(array('status'=>0));
    }
    // 获取房间两个用户信息
    public function getroommember()
    {
        $uid =I('uid');
        $roomid =I('roomid');
        $guid =I('guid');       
        $info=M('member')->field('id,email,avatar')->where("id='$uid'")->find();        
        if($info['avatar'])
        {
            $info['avatar'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$info['avatar'];
        }
        $ginfo =M('member')->field('id,email,avatar')->where("id='$guid'")->find();
        if($ginfo)
        {
            if($ginfo['avatar'])
            {
                $ginfo['avatar'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$ginfo['avatar'];
            }
        }
        $data['fromuser'] =$info;
        $data['touser'] =$ginfo;
        $this->ajaxReturn(array('status'=>200,'msg'=>'返回成功','data'=>$data));
    }

    // 消息列表
    public function getmessage()
    {
        $uid =I('uid');
        $list =M('message')->where("touserid='$uid'")->order('createtime desc')->select();
        if(count($list) > 0)
        {
            foreach ($list as $key => $value) {
                $value['avatar'] =M('member')->where("id='".$value['fromuserid']."'")->getField('avatar');
                if($value['avatar'])
                {
                    $list[$key]['avatar'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$value['avatar'];
                }
            }
            $this->ajaxReturn(array('status'=>200,'msg'=>'返回成功','data'=>$list));
        }else
        {
            $this->ajaxReturn(array('status'=>200,'msg'=>'返回成功','data'=>null));
        }
    }

    //  抽取试题
    public function getexamcourse()
    {
        $page=I('page','1','intval');  // 一次返回一条试题
        $count =3;
        $list=M('pkcourse')->where("status=1")->page($page.',1')->order('createtime desc')->find();
        
        $questionstr = unserialize($list['items']);
        if($v['img_items'])
        {
             $imgstr = unserialize($list['img_items']);
              foreach ($imgstr as $key => $value) {
                if($value == '')
                {
                    unset($imgstr[$key]);
                }
            }
            $list['img_question'] =$imgstr;
        }           
        foreach ($questionstr as $key => $value) {
            if($value == '')
            {
                unset($questionstr[$key]);
            }
        }           
        $list['question'] =$questionstr;            
        unset($list['items']);
        
        $this->ajaxReturn(array('status'=>200,'msg'=>'正常返回','data'=>$list,'totalpage'=>$count));
    }

    // 写入pk记录
    public function addpklog()
    {
        $data =I('post.');
        
        if(empty($data['roomid']) || empty($data['client_id']) || empty($data['uid']) || empty($data['guid']))
        {
            $this->ajaxReturn(array('status'=>201,'msg'=>'参数未传','data'=>null));
        }else
        {           
            $parray['client_id'] =$data['client_id'];
            $parray['roomid']=$data['roomid'];            
            if($data['istop'] ==1)
            {
                 $parray['uid']=$data['uid'];
                 $parray['istop']=1;
                 M('pklog')->add($parray);
            }else{
                 $parray['uid']=$data['guid'];
                 $parray['istop']=1;
                 M('pklog')->add($parray);
            }
            Gateway::$registerAddress = '127.0.0.1:1238';
            if($parray['istop'] ==1)
            {
               $message = $parray['uid'].'答题胜利';
               $code = 'success';
            }else
            {
                $message = $parray['uid'].'答题失败';
                $code ='fail';
            }
            $numberstr =rand(1,15);
            $datastr   =M('system_config')->where("type=1")->getField('data');
            $datastr2  =json_decode($datastr,true);
            $baoxiang  =  $numberstr <= $datastr2['treasure_box_probability']*100 ? 1:0;
            $integral=0;
            if($baoxiang==1)
            {
                $integral =rand($datastr2['tb_lowest_integral'],$datastr2['tb_highest_integral']);              
            }
            // 发送信息应当发送json数据,同时应该返回发送的用户的guid,用于客户端进行判断使用
            $dataArr=json_encode(array(
                'type'=>'finish',
                'message' =>$message,
                'user'=>$parray['uid'],
                'roomid'=>$parray['roomid'],
                'code'=>$code,
                'integral'=>$integral
            ));
        
            // 向roomId的分组发送数据
            Gateway::sendToGroup($parray['roomid'],$dataArr);    
            $this->ajaxReturn(array('status'=>200,'msg'=>'正常返回','data'=>null));
        }
    }
    // 获取pk结果
}


