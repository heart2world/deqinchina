<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 15:42
 */

namespace Common\Model;


class EmployeeBehaviorModel extends CommonModel
{
    public function getData($where,$page)
    {
        return $this->where($where)->limit($page->firstRow, $page->listRows)->order('id desc')->select();
    }

    //获取数据总条数
    public function getCount($where)
    {
        return $this->where($where)->order('id desc')->count();
    }

}