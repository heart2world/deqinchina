<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 9:26
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\SystemConfigModel;

class SystemConfigController extends AdminbaseController
{
    //积分规则
    public function integration_rule()
    {
        $configModel = new SystemConfigModel();
        $where['type'] = 1;
        $data = $configModel->getData($where);
        if ($data) {
            $data = json_decode($data['data'],true);
            $data['treasure_box_probability'] = $data['treasure_box_probability'] * 100;
        }

        $this->assign('data',$data);
        $this->display();
    }

    //设置积分规则
    public function set_rule()
    {
        $post = I('post.');
        if ($post['treasure_box_probability']) {
            $post['treasure_box_probability'] = $post['treasure_box_probability'] / 100;
        }
        $param['data'] = json_encode($post,true);
        $configModel = new SystemConfigModel();
        $condition['type'] = 1;
        $data = $configModel->getData($condition);

        if (empty($data)) {
            $param['type'] = 1;
            $param['format'] = 2;
            $rst = $configModel->addData($param);
        }else{
            $where['id'] = $data['id'];
            $rst = $configModel->updateData($where,$param);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //敏感词汇
    public function sensitive_word()
    {
        $configModel = new SystemConfigModel();
        $where['type'] = 2;
        $data = $configModel->getData($where);

        $this->assign('data',$data);
        $this->display();
    }

    //设置敏感词汇
    public function set_word()
    {
        $param['data'] = I('post.word');
        $configModel = new SystemConfigModel();
        $condition['type'] = 2;
        $data = $configModel->getData($condition);

        if (empty($data)) {
            $param['type'] = 2;
            $param['format'] = 1;
            $rst = $configModel->addData($param);
        }else{
            $where['id'] = $data['id'];
            $rst = $configModel->updateData($where,$param);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //使用协议
    public function protocol()
    {
        $configModel = new SystemConfigModel();
        $where['type'] = 3;
        $data = $configModel->getData($where);

        $this->assign('data',$data);
        $this->display();
    }

    //设置使用协议
    public function set_protocol()
    {
        $param['data'] = I('post.protocol');
        $configModel = new SystemConfigModel();
        $condition['type'] = 3;
        $data = $configModel->getData($condition);

        if (empty($data)) {
            $param['type'] = 3;
            $param['format'] = 1;
            $rst = $configModel->addData($param);
        }else{
            $where['id'] = $data['id'];
            $rst = $configModel->updateData($where,$param);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //app名称
    public function app()
    {
        $configModel = new SystemConfigModel();
        $where['type'] = 4;
        $data = $configModel->getData($where);

        $this->assign('data',$data['data']);
        $this->display();
    }

    //设置APP名称
    public function set_app()
    {
        $param['data'] = I('post.name');
        $configModel = new SystemConfigModel();
        $condition['type'] = 4;
        $data = $configModel->getData($condition);

        if (empty($data)) {
            $param['type'] = 4;
            $param['format'] = 1;
            $rst = $configModel->addData($param);
        }else{
            $where['id'] = $data['id'];
            $rst = $configModel->updateData($where,$param);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //游戏PK设置
    public function game()
    {
        $configModel = new SystemConfigModel();
        $where['type'] = 5;
        $data = $configModel->getData($where);
        if ($data) {
            $data = json_decode($data['data'],true);
        }

        $this->assign('data',$data);
        $this->display();
    }

    //设置游戏配置
    public function set_game()
    {
        $post = I('post.');
        $param['data'] = json_encode($post,true);
        $configModel = new SystemConfigModel();
        $condition['type'] = 5;
        $data = $configModel->getData($condition);

        if (empty($data)) {
            $param['type'] = 5;
            $param['format'] = 2;
            $rst = $configModel->addData($param);
        }else{
            $where['id'] = $data['id'];
            $rst = $configModel->updateData($where,$param);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }
}