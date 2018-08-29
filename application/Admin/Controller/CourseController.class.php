<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class CourseController extends AdminbaseController{

	protected $category_model,$banner_model,$course_model;

	public function _initialize() {
		parent::_initialize();
        $this->category_model = D("Common/Category");
        $this->banner_model = D("Common/Banner");
        $this->course_model = D("Common/Course");
	}

	// 在线课程列表
	public function index(){
		$where['id'] = array('gt',0);
		/**搜索条件**/
		$title = I('request.title');
		$cate_id = trim(I('request.cate_id'));
		if($title){
			$where['title'] = array('like',"%$title%");
		}
		if($cate_id){
			$where['cate_id'] = array('eq',$cate_id);
		}
		
		$count=$this->course_model->where($where)->count();
		$page = $this->page($count, 20);
        $course = $this->course_model
            ->where($where)
            ->order("listorder DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        $cate = $this->category_model->where(array('type'=>0))->order('listorder DESC')->select();
        $this->assign('cate',$cate);

		$this->assign("page", $page->show('Admin'));
		$this->assign("course",$course);
		$this->display();
	}

	// 在线课程添加
	public function add(){
        $cate = $this->category_model->where(array('type'=>0))->order('listorder DESC')->select();
        $this->assign('cate',$cate);
		$this->display();
	}

	// 在线课程添加提交
	public function add_post(){
        if (IS_POST) {
            if(empty($_POST['title'])){
                $this->error("课程名称不能为空！");
            }
            if(empty($_POST['cate_id'])){
                $this->error("课程类型不能为空！");
            }
            if(empty($_POST['begin_time'])){
                $this->error("报名开始时间不能为空！");
            }
            if(empty($_POST['end_time'])){
                $this->error("报名截止时间不能为空！");
            }
            $beginTime = strtotime($_POST['begin_time']);
            $endTime = strtotime($_POST['end_time']);
            if($beginTime < time()){
                $this->error("报名开始时间不能小于当前时间！");
            }
            if($endTime <= $beginTime){
                $this->error("报名截止时间需大于开始时间！");
            }
            if(empty($_POST['address'])){
                $this->error("报名地址不能为空！");
            }

            $course = [
                'title' => $_POST['title'],
                'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                'cate_id' => $_POST['cate_id'],
                'listorder' => intval($_POST['listorder']),
                'remark' => $_POST['remark'],
                'create_time' => date('Y-m-d H:i:s'),
                'visible_beau' => $_POST['visible_beau'],
                'begin_time' => $beginTime,
                'end_time' => $endTime,
                'address' => $_POST['address']
            ];
            $courseId = $this->course_model->add($course);

            if ($courseId !== false) {
                $this->success("添加在线课程信息成功！", U("Course/index"));
            } else {
                $this->error('添加失败！');
            }
        }
	}

	// 在线课程编辑
	public function edit(){
	    $id = I('get.id',0,'intval');
		$course=$this->course_model->where(array("id"=>$id))->find();
		if(!$course){
            $this->error('不存在该课程信息！');
        }
        $course['begin_time'] = date('Y-m-d H:i:s',$course['begin_time']);
		$course['end_time'] = date('Y-m-d H:i:s',$course['end_time']);

        $cate = $this->category_model->where(array('type'=>0))->order('listorder DESC')->select();
        $this->assign('cate',$cate);

		$this->assign($course);
		$this->display();
	}

	// 课程编辑提交
	public function edit_post(){
		if (IS_POST) {
            if(empty($_POST['title'])){
                $this->error("课程名称不能为空！");
            }
            if(empty($_POST['cate_id'])){
                $this->error("课程类型不能为空！");
            }
            if(empty($_POST['begin_time'])){
                $this->error("报名开始时间不能为空！");
            }
            if(empty($_POST['end_time'])){
                $this->error("报名截止时间不能为空！");
            }
            $beginTime = strtotime($_POST['begin_time']);
            $endTime = strtotime($_POST['end_time']);
            if($beginTime < time()){
                $this->error("报名开始时间不能小于当前时间！");
            }
            if($endTime <= $beginTime){
                $this->error("报名截止时间需大于开始时间！");
            }
            if(empty($_POST['address'])){
                $this->error("报名地址不能为空！");
            }

            $course = [
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                'cate_id' => $_POST['cate_id'],
                'listorder' => intval($_POST['listorder']),
                'remark' => $_POST['remark'],
                'visible_beau' => $_POST['visible_beau'],
                'begin_time' => $beginTime,
                'end_time' => $endTime,
                'address' => $_POST['address']
            ];

            if ($this->course_model->save($course) !== false) {
                $this->success("修改课程信息成功！", U("Course/index"));
            } else {
                $this->error('修改失败！');
            }
		}
	}

	// 课程信息删除
	public function delete(){
	    $id = I('post.id',0,'intval');
		if ($this->course_model->delete($id)!==false) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	// 隐藏
    public function ban(){
        $id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = $this->course_model->where(array("id"=>$id))->setField('status','0');
    		if ($result!==false) {
    			$this->success("在线课程隐藏成功！", U("Course/index"));
    		} else {
    			$this->error('在线课程隐藏失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    // 显示
    public function cancelban(){
    	$id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = $this->course_model->where(array("id"=>$id))->setField('status','1');
    		if ($result!==false) {
    			$this->success("在线课程显示成功！", U("Course/index"));
    		} else {
    			$this->error('在线课程显示失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    // 课程类型
    public function category(){
        $where['type'] = 0;
        $count = $this->category_model->where($where)->count();
        $page = $this->page($count, 20);
        $cate = $this->category_model
            ->where($where)
            ->order('listorder DESC')
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign('cate',$cate);
        $this->display();
    }

    // 新增课程类型
    public function cateadd(){
        $this->display();
    }

    // 新增课程类型提交
    public function cateadd_post(){
        if (IS_POST) {
            $_POST['type'] = 0;
            if ($this->category_model->create()!==false) {//创建数据对象
                if ($this->category_model->add()!==false) {
                    $this->success("添加课程类型成功！", U("Course/category"));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->category_model->getError());
            }
        }
    }

    // 编辑课程类型
    public function catedit(){
        $id = I("get.id",0,'intval');
        $data = $this->category_model->where(array("id" => $id,'type'=>0))->find();
        if (!$data) {
            $this->error("该课程类型不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑课程类型提交
    public function catedit_post(){
        if (IS_POST) {
            if ($this->category_model->create()!==false) {
                if ($this->category_model->save()!==false) {
                    $this->success("课程类型修改成功！", U("Course/category"));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->category_model->getError());
            }
        }
    }

    // 课程类型删除
    public function cate_delete(){
        $id = I('post.id',0,'intval');
        //查询该类型下是否有课程内容
        $courseCount = $this->course_model->where(array("cate_id"=>$id))->count();
        if($courseCount > 0){
            $this->error('该课程类型下存在在线课程，不能删除！');
        }
        if ($this->category_model->where(array('type'=>0))->delete($id) !== false) {
            $this->success("删除成功！");
        } else {
            $this->error('删除失败！');
        }
    }

    // 页面轮播图
    public function banner(){
        $where['cattype'] = 3;
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
            $_POST['cattype'] = 3;
            if ($this->banner_model->create()!==false) {
                if ($this->banner_model->add()!==false) {
                    $this->success("添加成功！");
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->banner_model->getError());
            }
        }
    }

    // 编辑轮播图
    public function banneredit(){
        $id = I("get.id",0,'intval');
        $data = $this->banner_model->where(array("id" => $id,'cattype'=>3))->find();
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
                    $this->success("保存成功！");
                } else {
                    $this->error('保存失败！');
                }
            } else {
                $this->error($this->banner_model->getError());
            }
        }
    }

    // 轮播图删除
    public function banner_delete(){
        $id = I('post.id',0,'intval');
        if ($this->banner_model->where(array('cattype'=>3))->delete($id)!==false) {
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