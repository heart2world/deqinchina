<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class UserController extends AdminbaseController{

	protected $users_model,$role_model;

	public function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->role_model = D("Common/Role");
	}

	// 管理员列表
	public function index(){
		$where = array("user_type"=>1);
		/**搜索条件**/
		$user_login = I('request.user_login');
		$user_email = trim(I('request.user_email'));
		if($user_login){
			$where['user_login'] = array('like',"%$user_login%");
		}
		
		if($user_email){
			$where['user_email'] = array('like',"%$user_email%");;
		}
		
		$count=$this->users_model->where($where)->count();
		$page = $this->page($count, 20);
        $users = $this->users_model
            ->where($where)
            ->order("create_time ASC")
            ->limit($page->firstRow, $page->listRows)
            ->select();
        //获取角色关联
        $role_user_model=M("RoleUser");
        foreach ($users as $k=>$v){
            $role_user = $role_user_model->where(array("user_id" => $v['id']))->find();
            if($role_user){
                $roleInfo = $this->role_model->where(array("id" => $role_user['role_id']))->find();
                if($roleInfo){
                    $users[$k]['belong_role'] = $roleInfo['name'];
                }
            }else{
                $users[$k]['belong_role'] = '超级管理员';
            }
        }
		$this->assign("page", $page->show('Admin'));
		$this->assign("users",$users);
		$this->display();
	}

	// 管理员添加
	public function add(){
		$roles=$this->role_model->where(array('status' => 1))->order("id DESC")->select();
		$this->assign("roles",$roles);
		$this->display();
	}

	// 管理员添加提交
	public function add_post(){
		if(IS_POST){
			if(!empty($_POST['role_id'])){
				$role_id=$_POST['role_id'];
				$roleInfo = $this->role_model->where(array("id" => $role_id))->find();
                if (!$roleInfo) {
                    $res = ['rs'=>-2,'msg'=>'你选择的角色不存在！'];
                }else {
                    unset($_POST['role_id']);
                    if ($this->users_model->create() !== false) {
                        $result = $this->users_model->add();
                        if ($result !== false) {
                            $role_user_model = M("RoleUser");
                            if (sp_get_current_admin_id() != 1 && $role_id == 1) {
                                $res = ['rs' => -3, 'msg' => '为了网站的安全，非网站创建者不可创建超级管理员！'];
                            } else {
                                $role_user_model->add(array("role_id" => $role_id, "user_id" => $result));
                                $res = ['rs' => 0, 'msg' => '添加成功！'];
                            }
                        } else {
                            $res = ['rs' => -1, 'msg' => '添加失败！'];
                        }
                    } else {
                        $res = ['rs' => -4, 'msg' => $this->users_model->getError()];
                    }
                }
			}else{
                $res = ['rs'=>-5,'msg'=>'请为此用户指定角色！'];
			}
            $this->ajaxReturn($res);
		}
	}

	// 管理员编辑
	public function edit(){
	    $id = I('get.id',0,'intval');
		$roles=$this->role_model->where(array('status' => 1))->order("id DESC")->select();
		$this->assign("roles",$roles);
		$role_user_model=M("RoleUser");
		$role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
		$this->assign("role_ids",$role_ids);

		$user=$this->users_model->where(array("id"=>$id))->find();
		$this->assign($user);
		$this->display();
	}

	// 管理员编辑提交
	public function edit_post(){
		if (IS_POST) {
			if(!empty($_POST['role_id'])){
				if(empty($_POST['user_pass'])){
					unset($_POST['user_pass']);
				}
                $role_id = I('post.role_id');
                $roleInfo = $this->role_model->where(array("id" => $role_id))->find();
                if (!$roleInfo) {
                    $res = ['rs'=>-2,'msg'=>'你选择的角色不存在！'];
                }else {
                    unset($_POST['role_id']);
                    if ($this->users_model->create() !== false) {
                        $result = $this->users_model->save();
                        if ($result !== false) {
                            $uid = I('post.id', 0, 'intval');
                            $role_user_model = M("RoleUser");
                            $role_user_model->where(array("user_id" => $uid))->delete();
                            if (sp_get_current_admin_id() != 1 && $role_id == 1) {
                                $res = ['rs' => -3, 'msg' => '为了网站的安全，非网站创建者不可创建超级管理员！'];
                            }
                            $role_user_model->add(array("role_id" => $role_id, "user_id" => $uid));
                            $res = ['rs' => 0, 'msg' => '保存成功！'];
                        } else {
                            $res = ['rs' => -1, 'msg' => '保存失败！'];
                        }
                    } else {
                        $res = ['rs' => -4, 'msg' => $this->users_model->getError()];
                    }
                }
			}else{
                $res = ['rs'=>-5,'msg'=>'请为此用户指定角色！'];
			}
            $this->ajaxReturn($res);
		}
	}

	// 管理员删除
	public function delete(){
	    $id = I('post.id',0,'intval');
		if($id==1){
            $res = ['rs'=>-2,'msg'=>'最高管理员不能删除！'];
		}else {
            if ($this->users_model->delete($id) !== false) {
                M("RoleUser")->where(array("user_id" => $id))->delete();
                $res = ['rs'=>0,'msg'=>'删除成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'删除失败！'];
            }
        }
        $this->ajaxReturn($res);
	}

	// 冻结管理员
    public function ban(){
        $id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','0');
    		if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'管理员停用成功！'];
    		} else {
                $res = ['rs'=>-1,'msg'=>'管理员停用失败！'];
    		}
    	} else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
    	}
        $this->ajaxReturn($res);
    }

    // 解冻管理员
    public function cancelban(){
    	$id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','1');
    		if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'管理员启用成功！'];
    		} else {
                $res = ['rs'=>-1,'msg'=>'管理员启用失败！'];
    		}
    	} else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
    	}
        $this->ajaxReturn($res);
    }

    // 管理员详情
    public function info(){
        $id = I('get.id',0,'intval');
        $user=$this->users_model->where(array("id"=>$id))->find();
        //获取角色关联
        $role_user_model=M("RoleUser");
        $role_user = $role_user_model->where(array("user_id" => $id))->find();
        if($role_user){
            $roleInfo = $this->role_model->where(array("id" => $role_user['role_id']))->find();
            if($roleInfo){
                $user['belong_role'] = $roleInfo['name'];
            }
        }else{
            $user['belong_role'] = '超级管理员';
        }

        $this->assign($user);
        $this->display();
    }


}