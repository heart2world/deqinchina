<?php
namespace Common\Model;
use Common\Model\CommonModel;
class HelpModel extends  CommonModel{
	
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('title', 'require', '标题未填写，请填写后再进行保存~', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('listorder', 'number', '排序只能是0或正整数！', 2 , 'regex', self::MODEL_BOTH),
        array('image', 'require', '图片不能为空！', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('description', 'require', '简介未填写，请填写后再进行保存~', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '内容未填写，请填写后再进行保存~', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	
	protected $_auto = array (
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
	    array ('image', 'sp_asset_relative_url', self::MODEL_BOTH, 'function'),
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);

        if(!empty($data['content'])){
            $data['content']=htmlspecialchars_decode($data['content']);
        }
	}

    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return date('Y-m-d H:i:s');
    }
}