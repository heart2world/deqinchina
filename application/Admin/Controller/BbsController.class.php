<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class BbsController extends AdminbaseController{

	protected $users_model,$category_model,$banner_model,$bbs_model,$banned_model,$comment_model,$reply_model;

	public function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->category_model = D("Common/BbsCat");
        $this->banner_model = D("Common/Banner");
        $this->bbs_model = D("Common/Bbs");
        $this->banned_model = D("Common/BbsBanned");
        $this->comment_model = D("Common/BbsComment");
        $this->reply_model = D("Common/BbsReply");
	}

	// 帖子列表
	public function index(){
		$where['id'] = array("gt",0);
		/**搜索条件**/
        $title = I('request.title');
        $cat = trim(I('request.cat'));
        if($title){
            $where['title'] = array('like',"%$title%");
        }
        if($cat){
            $where['category_id'] = array('eq',$cat);;
        }
		
		$count=$this->bbs_model->where($where)->count();
		$page = $this->page($count, 20);
        $bbs = $this->bbs_model
            ->where($where)
            ->order("create_time ASC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
        foreach ($bbs as $k=>$v){
            $user=$this->users_model->where(array("id"=>$v['user_id']))->find();
            if($user){
                $bbs[$k]['username'] = $user['user_nicename'];
            }
            $cate = $this->category_model->where(array("id"=>$v['category_id']))->find();
            if($cate){
                $bbs[$k]['catename'] = $cate['name'];
            }
        }
        //所有类型
        $category = $this->category_model->select();

		$this->assign("page", $page->show('Admin'));
        $this->assign("category",$category);
		$this->assign("bbs",$bbs);
		$this->display();
	}

    // 帖子取消热门
    public function cancel_popular(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->bbs_model->where(array("id"=>$id))->setField('status','0');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'帖子取消热门成功!'];
            } else {
                $res = ['rs'=>-1,'msg'=>'帖子取消热门失败!'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败!'];
        }
        $this->ajaxReturn($res);
    }

    // 帖子设为热门
    public function popular(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->bbs_model->where(array("id"=>$id))->setField('status','1');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'帖子设为热门成功!'];
            } else {
                $res = ['rs'=>-1,'msg'=>'帖子设为热门失败!'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败!'];
        }
        $this->ajaxReturn($res);
    }

	// 删除帖子
	public function delete(){
	    $id = I('post.id',0,'intval');
		if ($this->bbs_model->delete($id)!==false) {
            $this->comment_model->where(array("bbs_id"=>$id))->delete();
            $this->reply_model->where(array("bbs_id"=>$id))->delete();
			$res = ['rs'=>0,'msg'=>'删除成功！'];
		} else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
		}
        $this->ajaxReturn($res);
	}

    // 帖子详情
    public function info(){
        $id = I('get.id',0,'intval');
        $info = $this->bbs_model->where(array("id"=>$id))->find();
        if(!$info){
            $this->error("该帖子不存在！");
        }
        //获取发帖人信息
        $user = $this->users_model->where(array("id"=>$info['user_id']))->field('id,avatar,user_nicename')->find();
        //查看该发帖人是否被禁言
        $user['banned'] = 1;
        $banned = $this->banned_model->where(array("user_id"=>$user['id']))->find();
        if($banned){
            $finish_time = strtotime($banned['finish_time']);
            if($finish_time >= time()){
                $user['banned'] = 0;//被禁言中
            }
        }
        //获取评论列表
        $comment = $this->comment_model->where(array("bbs_id"=>$id))->order("id DESC")->select();
        foreach ($comment as $k=>$v){
            $comUser = $this->users_model->where(array("id"=>$v['user_id']))->field('id,avatar,user_nicename')->find();
            $comment[$k]['username'] = $comUser['user_nicename'];
            $comment[$k]['avatar'] = $comUser['avatar'];
            //查看该用户是否被禁言
            $comment[$k]['banned'] = 1;
            $comBanned = $this->banned_model->where(array("user_id"=>$comUser['id']))->find();
            if($comBanned){
                $com_finish_time = strtotime($comBanned['finish_time']);
                if($com_finish_time >= time()){
                    $comment[$k]['banned'] = 0;//被禁言中
                }
            }
            //获取评论回复列表
            $reply = $this->reply_model->where(array("bbs_id"=>$id,"comment_id"=>$v['id']))->select();
            foreach ($reply as $m=>$n){
                $repUser = $this->users_model->where(array("id"=>$n['user_id']))->field('id,user_nicename')->find();
                $reply[$m]['username'] = $repUser['user_nicename'];
            }
            $comment[$k]['reply'] = $reply;
        }

        $this->assign("info",$info);
        $this->assign("user",$user);
        $this->assign("comment",$comment);
        $this->display();
    }

    // 禁言
    public function banned(){
        $id = I('post.id',0,'intval');
        $user=$this->users_model->where(array("id"=>$id))->find();
        if(!$user){
            $res = ['rs'=>-2,'msg'=>'该用户不存在!'];
        }else {
            $this->banned_model->where(array("user_id" => $id))->delete();

            $endTime = time() + 3600 * 24 * 3;
            $banned = [
                'user_id' => $id,
                'create_time' => date('Y-m-d H:i:s'),
                'finish_time' => date('Y-m-d H:i:s', $endTime)
            ];
            $result = $this->banned_model->add($banned);
            if ($result) {
                $res = ['rs'=>0,'msg'=>'禁言成功!'];
            } else {
                $res = ['rs'=>-1,'msg'=>'禁言失败!'];
            }
        }
        $this->ajaxReturn($res);
    }

    // 取消禁言
    public function cancel_banned(){
        $id = I('post.id',0,'intval');
        $user=$this->users_model->where(array("id"=>$id))->find();
        if(!$user){
            $res = ['rs'=>-2,'msg'=>'该用户不存在!'];
        }else {
            if ($this->banned_model->where(array("user_id" => $id))->delete() !== false) {
                $res = ['rs'=>0,'msg'=>'取消禁言成功!'];
            } else {
                $res = ['rs'=>0,'msg'=>'取消禁言失败!'];
            }
        }
        $this->ajaxReturn($res);
    }

    // 删除帖子评论
    public function comment_delete(){
        $id = I('post.id',0,'intval');
        if ($this->comment_model->delete($id)!==false) {
            $this->reply_model->where(array("comment_id"=>$id))->delete();
            $res = ['rs'=>0,'msg'=>'删除成功!'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败!'];
        }
        $this->ajaxReturn($res);
    }

    // 删除帖子评论回复
    public function reply_delete(){
        $id = I('post.id',0,'intval');
        if ($this->reply_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功!'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败!'];
        }
        $this->ajaxReturn($res);
    }

    // 帖子类型
    public function category(){
        $count=$this->category_model->count();
        $page = $this->page($count, 20);
        $category = $this->category_model
            ->order("create_time ASC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("category",$category);
        $this->display();
    }

    // 新增帖子类型
    public function cateadd(){
        $this->display();
    }

    // 新增帖子类型提交
    public function cateadd_post(){
        if (IS_POST) {
            if ($this->category_model->create()!==false) {//创建数据对象
                if ($this->category_model->add()!==false) {
                    $res = ['rs'=>0,'msg'=>'添加帖子类型成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'添加失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->category_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 编辑帖子类型
    public function catedit(){
        $id = I("get.id",0,'intval');
        $data = $this->category_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该帖子类型不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑帖子类型提交
    public function catedit_post(){
        if (IS_POST) {
            if ($this->category_model->create()!==false) {
                if ($this->category_model->save()!==false) {
                    $res = ['rs'=>0,'msg'=>'帖子类型修改成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'修改失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->category_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 帖子类型删除
    public function cate_delete(){
        $id = I('post.id',0,'intval');
        //查询该类型下是否还有帖子
        $bbsCount = $this->bbs_model->where(array("category_id"=>$id))->count();
        if($bbsCount > 0){
            $res = ['rs'=>-2,'msg'=>'该帖子类型下存在帖子，不能删除！'];
        }else {
            if ($this->category_model->delete($id) !== false) {
                $res = ['rs'=>0,'msg'=>'删除成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'删除失败！'];
            }
        }
        $this->ajaxReturn($res);
    }

    // 页面轮播图
    public function carousel(){
        $where['cattype'] = 5;
        /**搜索条件**/
        $name = I('request.name');
        if($name){
            $where['name'] = array('like',"%$name%");
        }
        $count = $this->banner_model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $this->banner_model
            ->where($where)
            ->order(array("listorder" => "ASC", "id" => "asc"))
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("carousel", $data);
        $this->display();
    }

    // 新增轮播图
    public function carouseladd(){
        $this->display();
    }

    // 新增轮播图提交
    public function carouseladd_post(){
        if(IS_POST){
            $_POST['cattype'] = 5;
            if ($this->banner_model->create()!==false) {
                if ($this->banner_model->add()!==false) {
                    $res = ['rs'=>0,'msg'=>'添加成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'删除失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->banner_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 编辑轮播图
    public function carouseledit(){
        $id = I("get.id",0,'intval');
        $data = $this->banner_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该轮播图不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑轮播图提交
    public function carouseledit_post(){
        if(IS_POST){
            if ($this->banner_model->create()!==false) {
                if ($this->banner_model->save()!==false) {
                    $res = ['rs'=>0,'msg'=>'保存成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'保存失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->banner_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 轮播图删除
    public function carousel_delete(){
        $id = I('post.id',0,'intval');
        if ($this->banner_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功！'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 轮播图下架
    public function obtained(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->banner_model->where(array("id"=>$id))->setField('status','0');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'轮播图下架成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'轮播图下架失败！'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 轮播图上架
    public function shelf(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->banner_model->where(array("id"=>$id))->setField('status','1');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'轮播图上架成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'轮播图上架失败！'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
        }
        $this->ajaxReturn($res);
    }
}