<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class knowledgeController extends AdminbaseController{

	protected $category_model,$banner_model;

	public function _initialize() {
		parent::_initialize();
		$this->category_model = D("Common/Category");
		$this->banner_model = D("Common/Banner");
	}

	// 知识文库列表
    public function index(){
	    $category = $this->category_model->where(array("type"=>2))->order('listorder ASC')->select();
	    $this->assign("cate",$category);
	    $this->display();
    }

    // 知识类型
    public function category(){
        $where['type'] = array('eq',2);
        $count = $this->category_model->where($where)->count();
        $page = $this->page($count, 20);
        $category = $this->category_model->where($where)->order('listorder ASC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign('cate',$category);
        $this->display();
    }

    // 知识类型添加
    public function cateadd(){
        $this->display();
    }

    // 知识类型添加提交
    public function cateadd_post(){
        if (IS_POST) {
            if(empty($_POST['name'])){
                $this->error('你还没有输入知识类型名称，请输入！');
            }
            if(empty($_POST['imgurl'])){
                $this->error('你还没上传形象图，请上传！');
            }
            $category = [
                'name' => $_POST['name'],
                'listorder' => intval($_POST['listorder']),
                'create_time' => date('Y-m-d H:i:s'),
                'type' => 2,
                'imgurl' => sp_asset_relative_url($_POST['imgurl'])
            ];
            if ($this->category_model->add($category)!==false) {
                $this->success("知识类型添加成功！", U("Knowledge/category"));
            } else {
                $this->error('添加失败！');
            }
        }
    }

    // 知识类型编辑
    public function catedit(){
        $id = I("get.id",0,'intval');
        $data = $this->category_model->where(array("id" => $id,'type'=>2))->find();
        if (!$data) {
            $this->error("该知识类型不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 知识类型编辑提交
    public function catedit_post(){
        if (IS_POST) {
            if(empty($_POST['name'])){
                $this->error('你还没有输入知识类型名称，请输入！');
            }
            if(empty($_POST['imgurl'])){
                $this->error('你还没上传形象图，请上传！');
            }
            $category = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'listorder' => intval($_POST['listorder']),
                'imgurl' => sp_asset_relative_url($_POST['imgurl'])
            ];
            if ($this->category_model->save($category)!==false) {
                $this->success("修改知识类型成功！", U("Knowledge/category"));
            } else {
                $this->error('修改失败！');
            }
        }
    }

    // 知识类型删除
    public function cate_delete(){
        $id = I('post.id',0,'intval');
        //查询该知识类型下是否有知识文库--待完成
//        $courseCount = $this->course_model->where(array("cate_id"=>$id))->count();
//        if($courseCount > 0){
//            $this->error('该知识类型下存在知识文库，不能删除！');
//        }
        if ($this->category_model->where(array('type'=>2))->delete($id) !== false) {
            $this->success("删除成功！");
        } else {
            $this->error('删除失败！');
        }
    }

    // 页面轮播图
    public function banner(){
        $where['cattype'] = 1;
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
            $_POST['cattype'] = 1;
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
        $data = $this->banner_model->where(array("id" => $id,'cattype'=>1))->find();
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
        if ($this->banner_model->where(array('cattype'=>1))->delete($id)!==false) {
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