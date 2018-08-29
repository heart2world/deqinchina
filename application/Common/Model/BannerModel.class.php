<?php
namespace Common\Model;
use Common\Model\CommonModel;
class BannerModel extends CommonModel{

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '名称不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('listorder', 'number', '排序只能是0或正整数！', 2 , 'regex', self::MODEL_BOTH),
        array('imgurl', 'require', '图片不能为空！', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('imgurl', 'sp_asset_relative_url', self::MODEL_BOTH, 'function'),
    );

    protected function _before_write(&$data) {
        parent::_before_write($data);

        if(!empty($data['content'])){
            $data['content']=htmlspecialchars_decode($data['content']);
        }
    }
}