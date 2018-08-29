<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 16:18
 */

namespace Common\Model;


class SystemConfigModel extends CommonModel
{
    //根据条件返回数据
    public function getData($where)
    {
        return $this->where($where)->find();
    }

    //新增系统设置
    public function addData($param)
    {
        $param['create_time'] = time();
        return $this->add($param);
    }

    //修改系统设置
    public function updateData($where,$param)
    {
        return $this->where($where)->save($param);
    }
}