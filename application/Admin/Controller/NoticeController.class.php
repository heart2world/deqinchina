<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class NoticeController extends AdminbaseController{

	protected $notice_model,$banner_model;

	public function _initialize() {
		parent::_initialize();
		$this->notice_model = D("Common/Notice");
        $this->banner_model = D("Common/Banner");
	}

	// 公告
	public function index(){
        $where['id'] = array("gt",0);
		/**搜索条件**/
		$title = I('request.title');
		if($title){
			$where['title'] = array('like',"%$title%");
		}

		$count=$this->notice_model->where($where)->count();
		$page = $this->page($count, 20);
        $notice = $this->notice_model
            ->where($where)
            ->order("listorder DESC,id DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
		$this->assign("page", $page->show('Admin'));
		$this->assign("notice",$notice);
		$this->display();
	}

	// 公告添加
	public function add(){
		$this->display();
	}

	// 公告添加提交
	public function add_post(){
        if(IS_POST){
            if ($this->notice_model->create()!==false) {
                if ($this->notice_model->add()!==false) {
                    $res = ['rs'=>0,'msg'=>'添加成功~'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'添加失败~'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->notice_model->getError()];
            }
            $this->ajaxReturn($res);
        }
	}

	// 公告编辑
	public function edit(){
        $id = I("get.id",0,'intval');
        $data = $this->notice_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该公告不存在！");
        }
        $this->assign($data);
        $this->display();
	}

	// 公告编辑提交
	public function edit_post(){
        if(IS_POST){
            if ($this->notice_model->create()!==false) {
                if ($this->notice_model->save()!==false) {
                    $res = ['rs'=>0,'msg'=>'保存成功~'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'保存失败~'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->notice_model->getError()];
            }
            $this->ajaxReturn($res);
        }
	}

	// 公告删除
	public function delete(){
        $id = I('post.id',0,'intval');
        if ($this->help_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功！'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
        }
        $this->ajaxReturn($res);
	}

    // 首页轮播图
    public function banner(){
        $where['cattype'] = 0;
        /**搜索条件**/
        $name = I('request.name');
        if($name) {
            $where['name'] = array('like', "%$name%");
        }
        $count = $this->banner_model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $this->banner_model
            ->where($where)
            ->order(array("listorder" => "ASC", "id" => "asc"))
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("banner", $data);
        $this->display();
    }

    // 新增轮播图
    public function banneradd(){
        $this->display();
    }

    // 新增轮播图提交
    public function banneradd_post(){
        if(IS_POST){
            $_POST['cattype'] = 0;
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
    public function banneredit(){
        $id = I("get.id",0,'intval');
        $data = $this->banner_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该轮播图不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑轮播图提交
    public function banneredit_post(){
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
    public function banner_delete(){
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