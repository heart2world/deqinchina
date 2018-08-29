<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ExamQuestionModel extends CommonModel{
	
	//自动验证
	protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('topic', 'require', '试题题目不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH),
	);

    protected $_auto = array(
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
    );

    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return date('Y-m-d H:i:s');
    }
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
}