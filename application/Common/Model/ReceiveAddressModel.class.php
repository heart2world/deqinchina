<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 15:18
 */

namespace Common\Model;


class ReceiveAddressModel extends CommonModel
{
    //通过条件获取所有地址信息
    public function getData($where,$page)
    {
        return $this->where($where)->limit($page->firstRow,$page->listRows)->order('id desc')->select();
    }

    //获取数据总条数
    public function getCount($where)
    {
        return $this->where($where)->order('id desc')->count();
    }
}