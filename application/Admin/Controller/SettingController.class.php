<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SettingController extends AdminbaseController{
	
	protected $options_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->options_model = D("Common/Options");
	}
	
	// 密码修改
	public function password(){
		$this->display();
	}
	
	// 密码修改提交
	public function password_post(){
		if (IS_POST) {
			if(empty($_POST['old_password'])){
			    $res = ['rs'=>-2,'msg'=>'原始密码不能为空！'];
			}elseif (empty($_POST['password'])){
                $res = ['rs'=>-3,'msg'=>'新密码不能为空！'];
			}else {
                $user_obj = D("Common/Users");
                $uid = sp_get_current_admin_id();
                $admin = $user_obj->where(array("id" => $uid))->find();
                $old_password = I('post.old_password');
                $password = I('post.password');
                if (sp_compare_password($old_password, $admin['user_pass'])) {
                    if ($password == I('post.repassword')) {
                        if (sp_compare_password($password, $admin['user_pass'])) {
                            $res = ['rs' => -3, 'msg' => '新密码不能和原始密码相同！'];
                        } else {
                            $data['user_pass'] = sp_password($password);
                            $data['id'] = $uid;
                            $r = $user_obj->save($data);
                            if ($r !== false) {
                                $res = ['rs' => 0, 'msg' => '修改成功！'];
                            } else {
                                $res = ['rs' => -1, 'msg' => '修改失败！'];
                            }
                        }
                    } else {
                        $res = ['rs' => -4, 'msg' => '密码输入不一致！'];
                    }
                } else {
                    $res = ['rs' => -5, 'msg' => '原始密码不正确！'];
                }
            }
            $this->ajaxReturn($res);
		}
	}
	
	// 清除缓存
	public function clearcache(){
		sp_clear_cache();
		$this->display();
	}
	
	
}