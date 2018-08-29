<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 11:33
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\EmployeeBehaviorModel;

class EmployeeController extends AdminbaseController
{
    //员工行为数据
    public function behavior()
    {
        $username = I('post.user_name');
        $type = I('post.type');
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');
        if ($username) {
            $where['user_name'] = array('like','%'.$username.'%');
            $this->assign('user_name',$username);
        }

        if ($type) {
            $where['type'] = $type;
            $this->assign('type',$type);
        }

        if ($start_time || $end_time) {
            if (empty($start_time)) {
                $start_time = time();
            }

            if (empty($end_time)) {
                $end_time = time();
            }

            $where['create_time'] = array('between',[strtotime($start_time),strtotime($end_time)]);
            $this->assign('start_time',$start_time);
            $this->assign('end_time',$end_time);
        }

        $employeeModel = new EmployeeBehaviorModel();

        $count = $employeeModel->getCount($where);
        $page = $this->page($count,20);
        $data = $employeeModel->getData($where,$page);

        $this->assign('data',$data);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //个人行为数据
    public function personal_behavior()
    {
        $email = I('get.email');
        $where['email'] = $email;

        $type = I('post.type');
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');

        if ($type) {
            $where['type'] = $type;
            $this->assign('type',$type);
        }

        if ($start_time || $end_time) {
            if (empty($start_time)) {
                $start_time = time();
            }

            if (empty($end_time)) {
                $end_time = time();
            }

            $where['create_time'] = array('between',[strtotime($start_time),strtotime($end_time)]);
            $this->assign('start_time',$start_time);
            $this->assign('end_time',$end_time);
        }

        $employeeModel = new EmployeeBehaviorModel();

        $count = $employeeModel->getCount($where);
        $page = $this->page($count,20);
        $data = $employeeModel->getData($where,$page);

        $this->assign('email',$email);
        $this->assign('data',$data);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //导出Excel
    public function export()
    {
        $get = I('get.');
        //数据库中的数据表
        $xlsName = "Stamp";
        $xlsCell = array(
            array('id', 'ID'),
            array('create_time', '操作时间'),
            array('user_name', '姓名'),
            array('email', '邮箱'),
            array('department', '部门'),
            array('area', '区域'),
            array('duty', '职务'),
            array('type', '操作类型'),
        );

        //充值时间
        if ($get['start_time'] || $get['end_time']) {
            $where['create_time'] = array('between',[strtotime($get['start_time']),strtotime($get['end_time'])]);
        }

        //类型
        if ($get['type']) {
            $where['type'] = $get['type'];
        }

        //用户名称
        if ($get['user_name']) {
            $where['user_name'] = array('like','%'.$get['user_name'].'%');
        }

        if ($get['email']) {
            $where['email'] = $get['email'];
        }

        $xlsModel = new EmployeeBehaviorModel();
        //导出所有的内容
        $xlsData = $xlsModel->getData($where,'');

        foreach ($xlsData as &$v)
        {
            $v['type'] = get_behavior_type($v['type']);
        }

        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }
}