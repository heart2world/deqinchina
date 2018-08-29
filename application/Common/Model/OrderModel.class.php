<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 17:43
 */

namespace Common\Model;


class OrderModel extends CommonModel
{
    //获取订单数据
    public function getData($where,$page)
    {
        return $this->where($where)->limit($page->firstRow,$page->lastRow)->select();
    }

    //获取总数量
    public function getCount($where)
    {
        return $this->where($where)->count();
    }
}