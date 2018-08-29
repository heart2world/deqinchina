<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class RbacController extends AdminbaseController {

    protected $role_model, $auth_access_model;

    public function _initialize() {
        parent::_initialize();
        $this->role_model = D("Common/Role");
    }

    // 角色管理列表
    public function index() {
        $where['id'] = array('gt',0);
        /**搜索条件**/
        $role_name = I('request.role_name');
        if($role_name){
            $where['name'] = array('like',"%$role_name%");
        }
        $count = $this->role_model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $this->role_model
            ->where($where)
            ->order(array("listorder" => "ASC", "id" => "asc"))
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("roles", $data);
        $this->display();
    }

    // 添加角色
    public function roleadd() {
        $this->display();
    }
    
    // 添加角色提交
    public function roleadd_post() {
    	if (IS_POST) {
    		if ($this->role_model->create()!==false) {//创建数据对象
    			if ($this->role_model->add()!==false) {
    			    $res = ['rs'=>0,'msg'=>'添加角色成功！'];
    			} else {
                    $res = ['rs'=>-1,'msg'=>'添加角色失败！'];
    			}
    		} else {
                $res = ['rs'=>-2,'msg'=>$this->role_model->getError()];
    		}
            $this->ajaxReturn($res);
    	}
    }

    // 删除角色
    public function roledelete() {
        $id = I("post.id",0,'intval');
        if ($id == 1) {
            $res = ['rs'=>-3,'msg'=>'超级管理员角色不能被删除！'];
        }else {
            $role_user_model = M("RoleUser");
            $count = $role_user_model->where(array('role_id' => $id))->count();
            if ($count > 0) {
                $res = ['rs'=>-2,'msg'=>'该角色已经有用户不能被删除！'];
            } else {
                $status = $this->role_model->delete($id);
                if ($status !== false) {
                    $res = ['rs'=>0,'msg'=>'删除成功！'];
                } else {
                    $res = ['rs'=>0,'msg'=>'删除失败！'];
                }
            }
        }
        $this->ajaxReturn($res);
    }

    // 编辑角色
    public function roleedit() {
        $id = I("get.id",0,'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        $data = $this->role_model->where(array("id" => $id))->find();
        if (!$data) {
        	$this->error("该角色不存在！");
        }
        $this->assign("data", $data);
        $this->display();
    }
    
    // 编辑角色提交
    public function roleedit_post() {
        if (IS_POST) {
            $id = I("post.id", 0, 'intval');
            if ($id == 1) {
                $res = ['rs' => -3, 'msg' => '超级管理员角色不能被修改！'];
            } else {
                if ($this->role_model->create() !== false) {
                    if ($this->role_model->save() !== false) {
                        $res = ['rs' => 0, 'msg' => '修改成功！'];
                    } else {
                        $res = ['rs' => -1, 'msg' => '修改失败！'];
                    }
                } else {
                    $res = ['rs' => -2, 'msg' => $this->role_model->getError()];
                }
            }
            $this->ajaxReturn($res);
        }
    }

    // 角色授权
    public function authorize() {
        $this->auth_access_model = D("Common/AuthAccess");
       //角色ID
        $roleid = I("get.id",0,'intval');
        if (empty($roleid)) {
        	$this->error("参数错误！");
        }
        //获取角色信息
        $role = $this->role_model->where(array("id" => $roleid))->field('id,name')->find();
        if (!$role) {
            $this->error("该角色不存在！");
        }
        import("Tree");
        $menu = new \Tree();
        $menu->icon = array('│ ', '├─ ', '└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = $this->initMenu();
        $newmenus=array();
        $priv_data=$this->auth_access_model->where(array("role_id"=>$roleid))->getField("rule_name",true);//获取权限表数据
        foreach ($result as $m){
        	$newmenus[$m['id']]=$m;
        }
        
        foreach ($result as $n => $t) {
        	$result[$n]['checked'] = ($this->_is_checked($t, $roleid, $priv_data)) ? ' checked' : '';
        	$result[$n]['level'] = $this->_get_level($t['id'], $newmenus);
        	$result[$n]['style'] = empty($t['parentid']) ? '' : 'display:none;';
        	$result[$n]['parentid_node'] = ($t['parentid']) ? ' class="child-of-node-' . $t['parentid'] . '"' : '';
        }
        $str = "<tr id='node-\$id' \$parentid_node  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
    			</tr>";
        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);
        
        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        $this->assign("role",$role);
        $this->display();
    }
    
    // 角色授权提交
    public function authorize_post() {
    	$this->auth_access_model = D("Common/AuthAccess");
    	if (IS_POST) {
    		$roleid = I("post.roleid",0,'intval');
    		if(!$roleid){
    			$this->error("需要授权的角色不存在！");
    		}
    		if (is_array($_POST['menuid']) && count($_POST['menuid'])>0) {
    			
    			$menu_model=M("Menu");
    			$auth_rule_model=M("AuthRule");
    			$this->auth_access_model->where(array("role_id"=>$roleid,'type'=>'admin_url'))->delete();
    			foreach ($_POST['menuid'] as $menuid) {
    				$menu=$menu_model->where(array("id"=>$menuid))->field("app,model,action")->find();
    				if($menu){
    					$app=$menu['app'];
    					$model=$menu['model'];
    					$action=$menu['action'];
    					$name=strtolower("$app/$model/$action");
    					$this->auth_access_model->add(array("role_id"=>$roleid,"rule_name"=>$name,'type'=>'admin_url'));
    				}
    			}
    
    			$this->success("授权成功！", U("Rbac/index"));
    		}else{
    			//当没有数据时，清除当前角色授权
    			$this->auth_access_model->where(array("role_id" => $roleid))->delete();
    			$this->error("没有接收到数据，执行清除授权成功！");
    		}
    	}
    }
    
    /**
     *  检查指定菜单是否有权限
     * @param array $menu menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function _is_checked($menu, $roleid, $priv_data) {
    	
    	$app=$menu['app'];
    	$model=$menu['model'];
    	$action=$menu['action'];
    	$name=strtolower("$app/$model/$action");
    	if($priv_data){
	    	if (in_array($name, $priv_data)) {
	    		return true;
	    	} else {
	    		return false;
	    	}
    	}else{
    		return false;
    	}
    	
    }

    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
        
        	if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
        		return  $i;
        	}else{
        		$i++;
        		return $this->_get_level($array[$id]['parentid'],$array,$i);
        	}
        		
    }
    
    //角色成员管理
    public function member(){
    	//TODO 添加角色成员管理
    	
    }

}

