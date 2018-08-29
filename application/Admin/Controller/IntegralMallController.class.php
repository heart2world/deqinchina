<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 10:44
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\AddressModel;
use Common\Model\GiftModel;
use Common\Model\OrderModel;
use Common\Model\ReceiveAddressModel;

class IntegralMallController extends AdminbaseController
{
    //礼品管理
    public function mg_gift()
    {
        $gift_name = I('post.gift_name');
        if ($gift_name) {
            $where['gift_name'] = array('like','%'.$gift_name.'%');
            $this->assign('gift_name',$gift_name);
        }

        $giftModel = new GiftModel();
        $count = $giftModel->getCount($where);
        $page = $this->page($count, 20);
        $data = $giftModel->getData($where,$page);
        if ($data) {
            foreach ($data as &$v)
            {
                $start_time = date('Y-m-d',$v['start_time']);
                $end_time = date('Y-m-d',$v['end_time']);
                $v['exchange_time'] = $start_time.'至'.$end_time;
            }
        }

        $this->assign('data',$data);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //增加礼品
    public function add_gift()
    {
        $this->display();
    }

    //保存添加的数据
    public function add_post()
    {
        $post = I('post.');
        $post['start_time'] = strtotime($post['start_time']);
        $post['end_time'] = strtotime($post['end_time']);
        $post['detail'] = htmlspecialchars_decode($post['detail']);
        $giftModel = new GiftModel();
        $rst = $giftModel->add_gift($post);

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'添加成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'添加失败']);
        }
    }

    //编辑礼品
    public function edit_gift()
    {
        $id = I('get.id');

        $giftModel = new GiftModel();
        $where['id'] = $id;
        $data = $giftModel->get_gift($where);

        $data['start_time'] = date('Y-m-d H:i:s',$data['start_time']);
        $data['end_time'] = date('Y-m-d H:i:s',$data['end_time']);

        $this->assign('url',$this->get_url());
        $this->assign('data',$data);
        $this->display();
    }

    //编辑数据提交
    public function edit_post()
    {
        $post = I('post.');
        $post['start_time'] = strtotime($post['start_time']);
        $post['end_time'] = strtotime($post['end_time']);
        $post['detail'] = htmlspecialchars_decode($post['detail']);

        $giftModel = new GiftModel();
        $where['id'] = $post['id'];
        $rst = $giftModel->save_gift($where,$post);

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //更改礼品状态
    public function change_status()
    {
        $post = I('post.');

        $giftModel = new GiftModel();
        $where['id'] = $post['id'];
        $rst = $giftModel->save_gift($where,$post);

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //删除礼品
    public function delete_gift()
    {
        $id = I('post.id');
        if ($id) {
            $giftModel = new GiftModel();
            $rst = $giftModel->delete($id);

            if ($rst) {
                $this->ajaxReturn(['code'=>0,'msg'=>'删除成功']);
            }else{
                $this->ajaxReturn(['code'=>-1,'msg'=>'删除失败']);
            }
        }
    }

    //兑换订单
    public function exchange_order()
    {
        $name = I('post.nick_name');
        $where = [];
        if ($name) {
            $where['nick_name'] = array('like','%'.$name.'%');
            $this->assign('nick_name',$name);
        }

        $orderModel = new OrderModel();
        $count = $orderModel->getCount($where);
        $page = $this->page($count,20);
        $data = $orderModel->getData($where,$page);

        $this->assign('data',$data);
        $this->display();
    }

    //改变订单状态
    public function change_order()
    {
        $post = I('post.');

        $orderModel = new OrderModel();
        $rst = $orderModel->save($post);

        if ($rst) {
            $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
        }else{
            $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
        }
    }

    //收货地址
    public function address()
    {
        $addressModel = new ReceiveAddressModel();
        $count = $addressModel->getCount('');
        $page = $this->page($count,20);
        $data = $addressModel->getData('',$page);

        $this->assign('page',$page->show('Admin'));
        $this->assign('data',$data);
        $this->display();
    }

    //删除地址
    public function delete_address()
    {
        $id = I('post.id');
        if ($id) {
            $addressModel = new ReceiveAddressModel();
            $rst = $addressModel->delete($id);

            if ($rst) {
                $this->ajaxReturn(['code'=>0,'msg'=>'删除成功']);
            }else{
                $this->ajaxReturn(['code'=>-1,'msg'=>'删除失败']);
            }
        }
    }

    //新增地址
    public function add_address()
    {
        $this->display();
    }

    //添加地址提交
    public function add_address_post()
    {
        $post = I('post.');
        if ($post) {
            $addressModel = new ReceiveAddressModel();

            $post['create_time'] = time();
            $rst = $addressModel->add($post);

            if ($rst) {
                $this->ajaxReturn(['code'=>0,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['code'=>-1,'msg'=>'添加失败']);
            }
        }
    }

    //编辑地址
    public function edit_address()
    {
        $id = I('get.id');
        $addressModel = new ReceiveAddressModel();
        $data = $addressModel->find($id);

        $this->assign('data',$data);
        $this->display();
    }

    //编辑地址提交
    public function edit_address_post()
    {
        $post = I('post.');
        if ($post) {
            $addressModel = new ReceiveAddressModel();
            $rst = $addressModel->save($post);

            if ($rst) {
                $this->ajaxReturn(['code'=>0,'msg'=>'编辑成功']);
            }else{
                $this->ajaxReturn(['code'=>-1,'msg'=>'编辑失败']);
            }
        }
    }
}