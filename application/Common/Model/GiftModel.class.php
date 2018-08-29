<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 14:26
 */

namespace Common\Model;


class GiftModel extends CommonModel
{
    //通过条件获取礼品数据
    public function getData($where,$page)
    {
        return $this->where($where)->limit($page->firstRow, $page->listRows)->order('id desc')->select();
    }

    //获取数据总条数
    public function getCount($where)
    {
        return $this->where($where)->order('id desc')->count();
    }

    //获取一条礼品数据
    public function get_gift($where)
    {
        return $this->where($where)->find();
    }

    //添加数据
    public function add_gift($param)
    {
       $param['create_time'] = time();
       return $this->add($param);
    }

    //保存数据
    public function save_gift($where,$param)
    {
        return $this->where($where)->save($param);
    }
}