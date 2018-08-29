<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class HelpController extends AdminbaseController{

	protected $users_model,$role_model,$help_model,$feedback_model;

	public function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->role_model = D("Common/Role");
        $this->help_model = D("Common/Help");
        $this->feedback_model = D("Common/Feedback");
	}

	// 用户帮助
	public function index(){
        $where['id'] = array("gt",0);
		/**搜索条件**/
		$title = I('request.title');
		if($title){
			$where['title'] = array('like',"%$title%");
		}
		
		$count=$this->help_model->where($where)->count();
		$page = $this->page($count, 20);
        $help = $this->help_model
            ->where($where)
            ->order("listorder ASC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
		$this->assign("page", $page->show('Admin'));
		$this->assign("help",$help);
		$this->display();
	}

	// 用户帮助添加
	public function add(){
		$this->display();
	}

	// 用户帮助添加提交
	public function add_post(){
        if(IS_POST){
            if ($this->help_model->create()!==false) {
                if ($this->help_model->add()!==false) {
                    $res = ['rs'=>0,'msg'=>'添加成功~'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'添加失败~'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->help_model->getError()];
            }
            $this->ajaxReturn($res);
        }
	}

	// 用户帮助编辑
	public function edit(){
        $id = I("get.id",0,'intval');
        $data = $this->help_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该用户帮助不存在！");
        }
        $this->assign($data);
        $this->display();
	}

	// 用户帮助编辑提交
	public function edit_post(){
        if(IS_POST){
            if ($this->help_model->create()!==false) {
                if ($this->help_model->save()!==false) {
                    $res = ['rs'=>0,'msg'=>'保存成功~'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'保存失败~'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->help_model->getError()];
            }
            $this->ajaxReturn($res);
        }
	}

	// 用户帮助删除
	public function delete(){
        $id = I('post.id',0,'intval');
        if ($this->help_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功！'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
        }
        $this->ajaxReturn($res);
	}

    // 用户反馈
    public function feedback(){
        $where['id'] = array("gt",0);
        /**搜索条件**/
        $content = I('request.content');
        if($content){
            $where['content'] = array('like',"%$content%");
        }

        $count=$this->feedback_model->where($where)->count();
        $page = $this->page($count, 20);
        $feedback = $this->feedback_model
            ->where($where)
            ->order("id DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
        foreach ($feedback as $k=>$v){
            $user = $this->users_model->where(array("id"=>$v['user_id']))->find();
            $feedback[$k]['username'] = $user['user_nicename'];
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("feedback",$feedback);
        $this->display();
    }

    // 用户反馈删除
    public function fb_delete(){
        $id = I('post.id',0,'intval');
        if ($this->feedback_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功！'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 用户反馈详情
    public function info(){
        $id = I('get.id',0,'intval');
        $feedback=$this->feedback_model->where(array("id"=>$id))->find();
        if(!$feedback){
            $this->error("不存在该用户反馈！");
        }
        $this->assign($feedback);
        $this->display();
    }
}