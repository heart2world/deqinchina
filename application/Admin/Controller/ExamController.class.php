<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ExamController extends AdminbaseController{

	protected $category_model,$question_model,$option_model,$paper_model,
        $paper_question_model,$paper_user_model,$answer_model,$banner_model;

	public function _initialize() {
		parent::_initialize();
        $this->category_model = D("Common/Category");
        $this->paper_model = D("Common/ExamPaper");
        $this->paper_question_model = D("Common/ExamPaperQuestion");
        $this->question_model = D("Common/ExamQuestion");
        $this->option_model = D("Common/ExamQuestionOption");
        $this->paper_user_model = D("Common/ExamPaperUser");
        $this->answer_model = D("Common/ExamPaperAnswer");
        $this->banner_model = D("Common/Banner");
	}

	// 在线考试试卷列表
	public function index(){
		$where['id'] = array('gt',0);
		/**搜索条件**/
		$title = I('request.title');
		$cate_id = trim(I('request.cate_id'));
		if($title){
			$where['title'] = array('like',"%$title%");
		}
		if($cate_id){
			$where['cate_id'] = array('eq',$cate_id);
		}
		
		$count=$this->paper_model->where($where)->count();
		$page = $this->page($count, 20);
        $paper = $this->paper_model
            ->where($where)
            ->order("listorder DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        $cate = $this->category_model->where(array('type'=>1))->order('listorder DESC')->select();
        $this->assign('cate',$cate);

		$this->assign("page", $page->show('Admin'));
		$this->assign("paper",$paper);
		$this->display();
	}

	// 考试试卷添加
	public function add(){
        $cate = $this->category_model->where(array('type'=>1))->order('listorder DESC')->select();
        $this->assign('cate',$cate);
		$this->display();
	}

	// 考试试卷添加提交
	public function add_post(){
        if (IS_POST) {
            if(empty($_POST['title'])){
                $this->error("测评名称不能为空！");
            }
            if(empty($_POST['cate_id'])){
                $this->error("测评类型不能为空！");
            }
            if(!empty($_POST['exam_time'])){
                if(!is_numeric($_POST['exam_time'])){
                    $this->error('学习时间只能是数字！');
                }
                if($_POST['exam_time'] < 0){
                    $this->error('学习时间不能小于0！');
                }
            }else{
                $_POST['exam_time'] = 0;
            }
            if(!in_array($_POST['visible_version'],array('0','1','2'))){
                $this->error("可见版本数据有误！");
            }

            //题数及分数
            $single = $_POST['single'];
            $single_score = $_POST['single_score'];
            $multiple = $_POST['multiple'];
            $multiple_score = $_POST['multiple_score'];
            $judgment = $_POST['judgment'];
            $judgment_score = $_POST['judgment_score'];
            $subject = $_POST['subject'];
            $subject_score = $_POST['subject_score'];
            $score = $single*$single_score+$multiple*$multiple_score+$judgment*$judgment_score+$subject*$subject_score;

            $paper = [
                'title' => $_POST['title'],
                'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                'cate_id' => $_POST['cate_id'],
                'listorder' => intval($_POST['listorder']),
                'remark' => $_POST['remark'],
                'exam_time' => $_POST['exam_time'],
                'single' => $single,
                'single_score' => $single_score,
                'multiple' => $multiple,
                'multiple_score' => $multiple_score,
                'judgment' => $judgment,
                'judgment_score' => $judgment_score,
                'subject' => $subject,
                'subject_score' => $subject_score,
                'score' => $score,
                'create_time' => date('Y-m-d H:i:s'),
                'visible_beau' => $_POST['visible_beau'],
                'visible_version' => $_POST['visible_version']
            ];
            $paperId = $this->paper_model->add($paper);
            if ($paperId !== false) {
                $this->success("考试测评添加成功！", U("Exam/paper",array('id'=>$paperId)));
            } else {
                $this->error('添加失败！');
            }
        }
	}

	// 考试试卷试题添加
    public function paper(){
        $id = I('get.id',0,'intval');
        $paper=$this->paper_model->where(array("id"=>$id))->find();
        if(!$paper){
            $this->error('不存在该考试测评！');
        }
        $cate = $this->category_model->where(array("id"=>$paper['cate_id']))->find();//考试类型

        $single = array();//单选题
        $multiple = array();//多选题
        $judgment = array();//判断题
        $subject = array();//主观题
        $relation = $this->paper_question_model->where(array('paper_id'=>$id))->order("listorder ASC")->select();//考试试题
        foreach ($relation as $k=>$v){
            $ques = $this->question_model->where(array('id'=>$v['question_id']))->find();
            if($ques){
                $v['topic'] = $ques['topic'];
                if($ques['type'] == 1){
                    $single[] = $v;
                }elseif ($ques['type'] == 2){
                    $multiple[] = $v;
                }elseif ($ques['type'] == 3){
                    $judgment[] =$v;
                }else{
                    $subject[] = $v;
                }
            }
        }
        $this->assign($paper);
        $this->assign("cate",$cate);
        $this->assign("singles",$single);
        $this->assign("multiples",$multiple);
        $this->assign("judgments",$judgment);
        $this->assign("subjects",$subject);
	    $this->display();
    }

    // 考试试卷试题选择
    public function library(){
        $id = I('get.id',0,'intval');
        $paper = $this->paper_model->where(array("id"=>$id))->find();
        if(!$paper){
            $this->error('不存在该考试测评！');
        }
        $type = I('get.type',0,'intval');
        if(!in_array($type,array('1','2','3','4'))){
            $this->error('试题类型错误！');
        }
        /**搜索条件**/
        $title = I('request.title');
        if($title){
            $where['topic'] = array('like',"%$title%");
        }
        $where['type'] = array("eq",$type);

        //获取该考试试卷已经选择的该类型的试题,不再显示在列表中
        $relation = $this->paper_question_model->where(array("paper_id"=>$id))->select();
        $relArray = array();
        foreach ($relation as $k=>$v){
            $relArray[] = $v['question_id'];
        }
        if(count($relArray) > 0){
            $quesIds = implode(',',$relArray);
            $where['id'] = array('not in',$quesIds);
        }

        $count=$this->question_model->where($where)->count();
        $page = $this->page($count, 20);
        $question = $this->question_model->where($where)->limit($page->firstRow, $page->listRows)->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("question",$question);
        $this->assign("type",$type);
        $this->assign("id",$id);
        $this->display();
    }

    // 保存选中的试题
    public function library_choose(){
        if(IS_POST){
            $id = $_POST['id'];
            $paper = $this->paper_model->where(array("id"=>$id))->find();
            if(!$paper){
                $this->error('不存在该考试测评！');
            }
            //该试卷是否已经开考
            $userCount = $this->paper_user_model->where(array("paper_id"=>$paper['id']))->count();
            if($userCount > 0){
                $this->error('该考试测评已产生考试信息，不能再添加！');
            }

            $type = $_POST['type'];
            if(!in_array($type,array('1','2','3','4'))){
                $this->error('试题类型错误！');
            }

            //试题类型的限制数量
            if($type == 1){
                $totalNum = $paper['single'];
            }elseif ($type == 2){
                $totalNum = $paper['multiple'];
            }elseif ($type == 3){
                $totalNum = $paper['judgment'];
            }else{
                $totalNum = $paper['subject'];
            }
            $library = $_POST['library'];
            if(count($library) <= 0){
                $this->error('请选择试题！');
            }
            //获取已经添加了的该类型试题数量
            $count = 0;
            $haveRelation = $this->paper_question_model->where(array('paper_id'=>$id))->select();
            foreach ($haveRelation as $k=>$v){
                $ques  = $this->question_model->where(array('id'=>$v['question_id']))->find();
                if($ques && $ques['type'] == $type){
                    $count++;
                }
            }
            if($count+count($library) > $totalNum){
                $this->error('题目数量超过限制，请重新选择！');
            }

            foreach ($library as $v){
                $question = $this->question_model->where(array('id'=>$v,'type'=>$type))->find();
                if(!$question){
                    $this->error('你选择的试题信息有误，请正确操作！');
                }
            }
            $relation = array();
            foreach ($library as $v){
                $relation[] = ['paper_id' => $id, 'question_id' => $v];
            }
            $res = $this->paper_question_model->addAll($relation);
            if($res == false){
                $this->error('添加失败！');
            }
            $this->success("添加试题成功！", U("Exam/index"));
        }
    }

    //删除选择的试题
    public function library_delete(){
        $id = I('post.id',0,'intval');
        $paper_id = I('post.paper_id',0,'intval');
        $relation = $this->paper_question_model->where(array("id"=>$id,"paper_id"=>$paper_id))->find();
        if(!$relation){
            $this->error("不存在该数据！");
        }
        //该试卷是否已经开考
        $userCount = $this->paper_user_model->where(array("paper_id"=>$paper_id))->count();
        if($userCount > 0){
            $this->error('该考试测评已产生考试信息，不能再删除相关数据！');
        }
        if ($this->paper_question_model->delete($id)!==false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    // 提交试卷选择完试题后保存
    public function paper_post(){
        if(IS_POST){
            $id = $_POST['id'];
            $paper = $this->paper_model->where(array('id'=>$id))->find();
            if(!$paper){
                $this->error("该考试测评信息不存在！");
            }
            //该试卷是否已经开考
            $userCount = $this->paper_user_model->where(array("paper_id"=>$paper['id']))->count();
            if($userCount > 0){
                $this->error('该考试测评已产生考试信息，不能再修改相关信息！');
            }
            //获取各题型数量
            $singleNum = 0;
            $multipleNum = 0;
            $judgmentNum = 0;
            $subjectNum = 0;
            $relation = $this->paper_question_model->where(array('paper_id'=>$id))->select();
            foreach ($relation as $k=>$v){
                $ques = $this->question_model->where(array('id'=>$v['question_id']))->find();
                if($ques){
                    if($ques['type'] == 1){
                        $singleNum++;
                    }elseif ($ques['type'] ==2){
                        $multipleNum++;
                    }elseif ($ques['type'] == 3){
                        $judgmentNum++;
                    }else{
                        $subjectNum++;
                    }
                }
            }

            if($singleNum != $paper['single']){
                $this->error("已选单选题数量不符合试卷要求,保存失败！");
            }
            if($multipleNum != $paper['multiple']){
                $this->error("已选多选题数量不符合试卷要求,保存失败！");
            }
            if($judgmentNum != $paper['judgment']){
                $this->error("已选判断题数量不符合试卷要求,保存失败！");
            }
            if($subjectNum != $paper['subject']){
                $this->error("已选主观题数量不符合试卷要求,保存失败！");
            }

            $data = ['id' => $id, 'status' => 1];
            if($this->paper_model->save($data) !== false){
                $this->success("保存成功！");
            }else{
                $this->error("保存失败！");
            }
        }
    }

	// 考试试卷编辑
	public function edit(){
	    $id = I('get.id',0,'intval');
		$paper = $this->paper_model->where(array("id"=>$id))->find();
		if(!$paper){
            $this->error('不存在该考试测评信息！');
        }
        $cate = $this->category_model->where(array('type'=>1))->order('listorder DESC')->select();
        $this->assign('cate',$cate);
		$this->assign($paper);
		$this->display();
	}

	// 考试试卷编辑提交
	public function edit_post(){
        if (IS_POST) {
            $id = $_POST['id'];
            $paper = $this->paper_model->where(array("id"=>$id))->find();
            if(!$paper){
                $this->error('不存在该考试测评信息！');
            }
            //如果该试卷下已经产生有考试信息，则不能再修改
            $userCount = $this->paper_user_model->where(array('paper_id'=>$id))->count();
            if($userCount > 0){
                $this->error("该考试测评下已经存在考试信息，不能再编辑了！");
            }
            if(empty($_POST['title'])){
                $this->error("测评名称不能为空！");
            }
            if(empty($_POST['cate_id'])){
                $this->error("测评类型不能为空！");
            }
            if(!empty($_POST['exam_time'])){
                if(!is_numeric($_POST['exam_time'])){
                    $this->error('学习时间只能是数字！');
                }
                if($_POST['exam_time'] < 0){
                    $this->error('学习时间不能小于0！');
                }
            }else{
                $_POST['exam_time'] = 0;
            }
            if(!in_array($_POST['visible_version'],array('0','1','2'))){
                $this->error("可见版本数据有误！");
            }

            //题数及分数
            $single = $_POST['single'];
            $single_score = $_POST['single_score'];
            $multiple = $_POST['multiple'];
            $multiple_score = $_POST['multiple_score'];
            $judgment = $_POST['judgment'];
            $judgment_score = $_POST['judgment_score'];
            $subject = $_POST['subject'];
            $subject_score = $_POST['subject_score'];
            $score = $single*$single_score+$multiple*$multiple_score+$judgment*$judgment_score+$subject*$subject_score;

            //状态
            if($single != $paper['single']){
                $status = 0;
            }elseif ($multiple != $paper['multiple']){
                $status = 0;
            }elseif ($judgment != $paper['judgment']){
                $status = 0;
            }elseif ($subject != $paper['subject']){
                $status = 0;
            }else{
                $status = $paper['status'];
            }

            $paperInfo = [
                'id' => $id,
                'title' => $_POST['title'],
                'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                'cate_id' => $_POST['cate_id'],
                'listorder' => intval($_POST['listorder']),
                'remark' => $_POST['remark'],
                'exam_time' => $_POST['exam_time'],
                'single' => $single,
                'single_score' => $single_score,
                'multiple' => $multiple,
                'multiple_score' => $multiple_score,
                'judgment' => $judgment,
                'judgment_score' => $judgment_score,
                'subject' => $subject,
                'subject_score' => $subject_score,
                'score' => $score,
                'status' => $status,
                'visible_beau' => $_POST['visible_beau'],
                'visible_version' => $_POST['visible_version']
            ];
            if ($this->paper_model->save($paperInfo) !== false) {
                $this->success("测评信息编辑成功！", U("Exam/paper",array('id'=>$id)));
            } else {
                $this->error('编辑失败！');
            }
        }
	}

	// 考试试卷删除
	public function delete(){
	    $id = I('post.id',0,'intval');
	    //先要判断该试卷下是否已产生考试信息，若已产生，则不能删除
        $userCount = $this->paper_user_model->where(array('paper_id'=>$id))->count();
        if($userCount > 0){
            $this->error("该考试测评下已经存在考试信息，不能删除！");
        }
		if ($this->paper_model->delete($id)!==false) {//删除试卷
		    $this->paper_question_model->where(array("paper_id"=>$id))->delete();//删除试卷试题
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	// 隐藏
    public function hidden(){
        $id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = $this->paper_model->where(array("id"=>$id))->setField('status','0');
    		if ($result!==false) {
    			$this->success("测评信息隐藏成功！", U("Exam/index"));
    		} else {
    			$this->error('测评信息隐藏失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    // 显示
    public function show(){
    	$id = I('post.id',0,'intval');
    	if (!empty($id)) {
            $paper = $this->paper_model->where(array('id'=>$id))->find();
            if(!$paper){
                $this->error("该考试测评信息不存在！");
            }
            //获取各题型数量
            $singleNum = 0;
            $multipleNum = 0;
            $judgmentNum = 0;
            $subjectNum = 0;
            $relation = $this->paper_question_model->where(array('paper_id'=>$id))->select();
            foreach ($relation as $k=>$v){
                $ques = $this->question_model->where(array('id'=>$v['question_id']))->find();
                if($ques){
                    if($ques['type'] == 1){
                        $singleNum++;
                    }elseif ($ques['type'] ==2){
                        $multipleNum++;
                    }elseif ($ques['type'] == 3){
                        $judgmentNum++;
                    }else{
                        $subjectNum++;
                    }
                }
            }

            if($singleNum != $paper['single']){
                $this->error("该考试测评还未编辑完成,显示失败！");
            }
            if($multipleNum != $paper['multiple']){
                $this->error("该考试测评还未编辑完成,显示失败！");
            }
            if($judgmentNum != $paper['judgment']){
                $this->error("该考试测评还未编辑完成,显示失败！");
            }
            if($subjectNum != $paper['subject']){
                $this->error("该考试测评还未编辑完成,显示失败！");
            }

    		$result = $this->paper_model->where(array("id"=>$id))->setField('status','1');
    		if ($result!==false) {
    			$this->success("测评信息显示成功！", U("Exam/index"));
    		} else {
    			$this->error('测评信息显示失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    // 考试类型
    public function category(){
        $where['type'] = array('eq',1);
        $count = $this->category_model->where($where)->count();
        $page = $this->page($count, 20);
        $cate = $this->category_model->where($where)->order('id ASC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign('cate',$cate);
        $this->display();
    }

    // 新增考试类型
    public function cateadd(){
        $this->display();
    }

    // 新增考试类型提交
    public function cateadd_post(){
        if (IS_POST) {
            $_POST['type'] = 1;
            if ($this->category_model->create()!==false) {//创建数据对象
                if ($this->category_model->add()!==false) {
                    $this->success("添加测评类型成功！", U("Exam/category"));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->category_model->getError());
            }
        }
    }

    // 编辑考试类型
    public function catedit(){
        $id = I("get.id",0,'intval');
        $data = $this->category_model->where(array("id" => $id,'type'=>1))->find();
        if (!$data) {
            $this->error("该测评类型不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑考试类型提交
    public function catedit_post(){
        if (IS_POST) {
            if ($this->category_model->create()!==false) {
                if ($this->category_model->save()!==false) {
                    $this->success("测评类型修改成功！", U("Exam/category"));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->category_model->getError());
            }
        }
    }

    // 考试类型删除
    public function cate_delete(){
        $id = I('post.id',0,'intval');
        //查询该类型下是否有考试试卷
        $paperCount = $this->paper_model->where(array("cate_id"=>$id))->count();
        if($paperCount > 0){
            $this->error('该测评类型下存在考试测评，不能删除！');
        }
        if ($this->category_model->where(array('type'=>0))->delete($id) !== false) {
            $this->success("删除成功！");
        } else {
            $this->error('删除失败！');
        }
    }

    // 试题库
    public function question(){
        $where['id'] = array('gt',0);
        /**搜索条件**/
        $title = I('request.title');
        $type = trim(I('request.type'));
        if($title){
            $where['topic'] = array('like',"%$title%");
        }
        if($type){
            $where['type'] = array('eq',$type);
        }

        $count=$this->question_model->where($where)->count();
        $page = $this->page($count, 20);
        $question = $this->question_model
            ->where($where)
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("question",$question);
        $this->assign("type",$type);
        $this->display();
    }

    // 添加试题
    public function quesadd(){
        $id = I('get.id',0,'intval');
        $type = I('get.type',0,'intval');
        $this->assign("id",$id);
        $this->assign("type",$type);
        $this->display();
    }

    // 添加试题提交
    public function quesadd_post(){
        if (IS_POST) {
            $type = $_POST['type'];
            if(!in_array($type,array('1','2','3','4'))){
                $this->error('题型参数错误！');
            }
            $topic = $_POST['topic'];
            if($topic == ''){
                $this->error('题目不能为空！');
            }
            $paperId = $_POST['id'];
            $paper = '';
            if(!empty($paperId)){
                $paper = $this->paper_model->where(array("id"=>$paperId))->find();
                if(!$paper){
                    $this->error('你提交绑定的考试测评信息不存在！');
                }
            }

            if($type == 1 || $type == 2) {
                $options = $_POST['options'];
                if(!is_array($options)){
                    $this->error('题目选项参数错误！');
                }
                $question = [
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $quesId = $this->question_model->add($question);
                if($quesId == false){
                    $this->error('添加失败！');
                }
                $numberArray = array('A','B','C','D','E','F','G');//选项编号
                $optionArray = array();//选项数组
                foreach ($options as $k=>$v){
                    $optionArray[] =array(
                        'question_id' => $quesId,
                        'number' => $numberArray[$k],
                        'is_right' => $v['check'],
                        'answer' => $v['answer'],
                        'listorder' => $k,
                        'imgurl' => sp_asset_relative_url($v['img'])
                    );
                }
                $return = $this->option_model->addAll($optionArray);
                if($return == false){
                    $this->question_model->delete($quesId);//添加失败，删除试题
                    $this->error('添加失败！');
                }
            }elseif ($type == 3){
                if(!in_array($_POST['judgment'],array('1','0'))){
                    $this->error('题目选项参数错误！');
                }
                $question = [
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'judgment' => $_POST['judgment'],
                    'answer' => $_POST['explain'],
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $quesId = $this->question_model->add($question);
                if($quesId == false){
                    $this->error('添加失败！');
                }
            }else{
                $question = [
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'answer' => $_POST['answer'],
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $quesId = $this->question_model->add($question);
                if($quesId == false){
                    $this->error('添加失败！');
                }
            }

            if($paper){
                //检查该试卷是否已经产生考试信息
                $userCount = $this->paper_user_model->where(array("paper_id"=>$paperId))->count();
                if($userCount > 0){
                    $this->success("试题添加成功，但不能绑定该已经开考的考试测评！",U("Exam/paper",array('id'=>$paperId)));
                }

                //试题类型的限制数量
                if($type == 1){
                    $totalNum = $paper['single'];
                }elseif ($type == 2){
                    $totalNum = $paper['multiple'];
                }elseif ($type == 3){
                    $totalNum = $paper['judgment'];
                }else{
                    $totalNum = $paper['subject'];
                }
                //获取已经添加了的该类型试题数量
                $count = 0;
                $haveRelation = $this->paper_question_model->where(array('paper_id'=>$paperId))->select();
                foreach ($haveRelation as $k=>$v){
                    $ques  = $this->question_model->where(array('id'=>$v['question_id']))->find();
                    if($ques && $ques['type'] == $type){
                        $count++;
                    }
                }
                if($count+1 > $totalNum){
                    $this->success("试题添加成功,但题目数量超过限制,绑定该考试测评失败！",U("Exam/paper",array('id'=>$paperId)));
                }

                //没有开考，则添加该试题进入该试卷
                $relation = ['paper_id'=>$paperId,'question_id'=>$quesId];
                if($this->paper_question_model->add($relation) !== false){
                    //添加成功，将试卷状态修改为隐藏
                    $paperInfo = ['id'=>$paperId,'status'=>0];
                    $this->paper_model->save($paperInfo);
                    $this->success("试题添加及绑定该考试测评信息成功！",U("Exam/paper",array('id'=>$paperId)));
                }else{
                    $this->success("试题添加成功,但绑定该考试测评失败！",U("Exam/paper",array('id'=>$paperId)));
                }
            }
            $this->success("添加成功！",U("Exam/question"));
        }
    }

    //删除试题库试题
    public function ques_delete(){
        $id = I('post.id',0,'intval');
        //先要判断该试题是否已经被加入某试卷，如果加入，则不能删除
        $count = $this->paper_question_model->where(array('question_id'=>$id))->count();
        if($count > 0){
            $this->error("该试题已被选入考试测评，不能删除！");
        }
        if ($this->question_model->delete($id)!==false) {
            $this->option_model->where(array('question_id'=>$id))->delete();
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    // 编辑试题
    public function quesedit(){
        $id = I('get.id',0,'intval');
        $question=$this->question_model->where(array("id"=>$id))->find();
        if(!$question){
            $this->error('不存在该试题！');
        }
        //获取单选或多选试题选项
        if(in_array($question['type'],array('1','2'))){
            $options = $this->option_model->where(array("question_id"=>$id))->select();
            $length = count($options);
            $number = array('A','B','C','D','E','F','G');
            $this->assign("option",$options);
            $this->assign("length",$length);
            $this->assign("number",$number);
        }
        $this->assign($question);
        $this->display();
    }

    // 编辑试题提交
    public function quesedit_post(){
        if (IS_POST) {
            $id = $_POST['id'];
            $quesInfo=$this->question_model->where(array("id"=>$id))->find();
            if(!$quesInfo){
                $this->error('不存在该试题！');
            }
            $type = $_POST['type'];
            if(!in_array($type,array('1','2','3','4'))){
                $this->error('题型参数错误！');
            }

            //begin--先要判断该试题是否被选入试卷且该试卷是否已经有人参考，若有，则不能再修改
            //获取选择该试题的试卷
            $paperRelation = $this->paper_question_model->where(array("question_id"=>$id))->group('paper_id')->select();
            if(count($paperRelation) > 0){
                //判断其中是否有试卷已有考试信息
                foreach ($paperRelation as $k=>$v){
                    $userCount = $this->paper_user_model->where(array("paper_id"=>$v['paper_id']))->count();
                    if($userCount > 0){
                        $this->error('该试题已经被考试过,不能再修改了！');
                    }
                }
            }
            //--end

            $topic = $_POST['topic'];
            if($topic == ''){
                $this->error('题目不能为空！');
            }
            if($type == 1 || $type == 2) {
                $options = $_POST['options'];
                if(!is_array($options)){
                    $this->error('题目选项参数错误！');
                }
                //获取原来的选项
                $originalOption = $this->option_model->where(array('question_id'=>$id))->select();
                //修改
                $question = [
                    'id' => $id,
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'answer' => NULL
                ];
                $quesRes = $this->question_model->save($question);
                if($quesRes == false){
                    $this->error('修改失败！');
                }
                $numberArray = array('A','B','C','D','E','F','G');//选项编号
                $optionArray = array();//选项数组
                foreach ($options as $k=>$v){
                    $optionArray[] =array(
                        'question_id' => $id,
                        'number' => $numberArray[$k],
                        'is_right' => $v['check'],
                        'answer' => $v['answer'],
                        'listorder' => $k,
                        'imgurl' => sp_asset_relative_url($v['img'])
                    );
                }
                $return = $this->option_model->addAll($optionArray);
                if($return == false){
                    //将试题还原
                    $reduction = [
                        'id' => $id,
                        'topic' => $quesInfo['topic'],
                        'type' => $quesInfo['type'],
                        'imgurl' => $quesInfo['imgurl'],
                        'answer' => $quesInfo['answer']
                    ];
                    $this->question_model->save($reduction);
                    $this->error('修改失败！');
                }
                //删除原来的选项
                foreach ($originalOption as $origin){
                    $this->option_model->delete($origin['id']);
                }
            }elseif ($type == 3){
                if(!in_array($_POST['judgment'],array('1','0'))){
                    $this->error('题目选项参数错误！');
                }
                $question = [
                    'id' => $id,
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'judgment' => $_POST['judgment'],
                    'answer' => $_POST['explain']
                ];
                $quesRes = $this->question_model->save($question);
                if($quesRes == false){
                    $this->error('修改失败！');
                }
                //如果试题原本类型是单选或多选，删除选项
                if(in_array($quesInfo['type'],array('1','2'))){
                    $this->option_model->where(array('question_id'=>$id))->delete();
                }
            }else{
                $question = [
                    'id' => $id,
                    'topic' => $topic,
                    'type' => $type,
                    'imgurl' => sp_asset_relative_url($_POST['imgurl']),
                    'answer' => $_POST['answer']
                ];
                $quesRes = $this->question_model->save($question);
                if($quesRes == false){
                    $this->error('修改失败！');
                }
                //如果试题原本类型是单选或多选，删除选项
                if(in_array($quesInfo['type'],array('1','2'))){
                    $this->option_model->where(array('question_id'=>$id))->delete();
                }
            }

            //如果修改了该题原本的类型，则选择该题的试卷状态变为隐藏
            if(count($paperRelation) > 0 && $type != $quesInfo['type']){
                foreach ($paperRelation as $k=>$v){
                    $paperInfo = ['id'=>$v['paper_id'],'status'=>0];
                    $this->paper_model->save($paperInfo);
                }
            }
            $this->success("修改成功！");
        }
    }

    //查看考试
    public function examinfo(){
        $id = I('get.id',0,'intval');
        $paper=$this->paper_model->where(array("id"=>$id))->find();
        if(!$paper){
            $this->error('不存在该考试测评！');
        }

        //获取考试列表
        $where['paper_id'] = array('eq',$id);
        /**搜索条件**/
        $title = I('request.title');
        $status = trim(I('request.status'));
        if($title){
            $where['user_id'] = array('like',"%$title%");//添加用户表后再修改，待完成
        }
        if($status){
            $where['status'] = array('eq',$status);
        }

        $count=$this->paper_user_model->where($where)->count();
        $page = $this->page($count, 20);
        $list = $this->paper_user_model
            ->where($where)
            ->order("time ASC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("list",$list);
        $this->display();
    }

    // 测评详情
    public function info(){
        $id = I('get.id',0,'intval');
        $exam = $this->paper_user_model->where(array("id"=>$id))->find();
        if(!$exam){
            $this->error('不存在该测评信息！');
        }
        //试卷
        $paper = $this->paper_model->where(array("id"=>$exam['paper_id']))->find();
        //用户$exam['user_id']
        $user = array("name"=>'小明');

        //获取试题
        $singles = array();//单选题
        $multiples = array();//多选题
        $judgments = array();//判断题
        $subjects = array();//主观题
        $relation = $this->paper_question_model->where(array("paper_id"=>$paper['id']))->order("listorder ASC")->select();
        foreach ($relation as $k=>$v){
            $question = $this->question_model->where(array("id"=>$v['question_id']))->find();
            if($question){
                //获取答题详情
                $answer = $this->answer_model->where(array("exam_id"=>$exam['id'],"question_id"=>$question['id']))->find();
                if($answer){
                    //判断单选、多选、判断题是否正确
                    if(in_array($question['type'],array('1','2','3'))){
                        ($answer['true_false'] == 0) ? $question['correct'] = 0 : $question['correct'] = 1;
                    }
                }

                //获取试题选项
                $option = $this->option_model->where(array("question_id"=>$question['id']))->select();
                //添加单选、多选试题选项选择状态
                if(in_array($question['type'],array('1','2'))){
                    foreach ($option as $m=>$n){
                        if($question['type'] == 1){
                            ($n['id'] == $answer['option_id']) ? $option[$m]['check'] = "checked" : $option[$m]['check'] = '';
                        }else{
                            (in_array($n['id'],explode(',',$answer['option_id']))) ? $option[$m]['check'] = "checked" : $option[$m]['check'] = '';
                        }
                    }
                }
                if($question['type'] == 3){
                    $question['check'] = $answer['judgment'];
                }
                if($question['type'] == 4){
                    $question['reply'] = $answer['answer'];
                    $question['score'] = $answer['score'];
                }
                $question['option'] = $option;

                //加入题型数组
                if($question['type'] == 1){
                    $singles[] = $question;
                }elseif ($question['type'] == 2){
                    $multiples[] = $question;
                }elseif ($question['type'] == 3){
                    $judgments[] = $question;
                }else{
                    $subjects[] = $question;
                }
            }
        }

        $this->assign("exam",$exam);
        $this->assign("paper",$paper);
        $this->assign("user",$user);
        $this->assign("singles",$singles);
        $this->assign("multiples",$multiples);
        $this->assign("judgments",$judgments);
        $this->assign("subjects",$subjects);
        $this->display();
    }

    // 阅卷
    public function reading(){
        if(IS_POST){
            $examId = $_POST['exam_id'];
            $exam = $this->paper_user_model->where(array("id"=>$examId))->find();
            if(!$exam){
                $this->error('不存在该测评信息！');
            }
            if($exam['status'] != 1){
                $this->error('该测评不是待审阅状态，提交失败！');
            }
            //获取试卷信息
            $paper = $this->paper_model->where(array("id"=>$exam['paper_id']))->find();
            if(!$paper){
                $this->error('该测评信息所属考试数据异常，提交失败！');
            }
            $scoresInfo = $_POST['scores'];
            if(!is_array($scoresInfo) || count($scoresInfo) <= 0){
                $this->error('请提交主观题审阅分数信息！');
            }
            foreach ($scoresInfo as $k=>$v){
                if($v['score'] > $paper['subject_score']){
                    $this->error('你提交的分数不能大于此题的最大分值！');
                }
                $answer = $this->answer_model->where(array("exam_id"=>$examId,"question_id"=>$v['question_id']))->find();
                if(!$answer){
                    $this->error('你提交的主观题不存在答题信息，提交失败！');
                }
            }

            //保存审阅信息
            foreach ($scoresInfo as $m=>$n){
                $answer = $this->answer_model->where(array("exam_id"=>$examId,"question_id"=>$n['question_id']))->find();
                $answerInfo = ['id'=>$answer['id'],'score'=>$n['score']];
                $this->answer_model->save($answerInfo);
            }

            //获取该测评所有解答分数
            $sumScore = $this->answer_model->where(array("exam_id"=>$examId))->sum('score');
            $newExam = ['id'=>$examId,'score'=>$sumScore,'status'=>2];
            if($this->paper_user_model->save($newExam) !== false){
                $this->success("审阅提交成功！",U("Exam/examinfo",array('id'=>$paper['id'])));
            }else{
                $this->error('提交失败！');
            }
        }
    }

    //试题排序
    public function list_order(){
        if(IS_POST){
            $id = $_POST['id'];
            //获取试卷信息
            $paper = $this->paper_model->where(array("id"=>$id))->find();
            if(!$paper){
                $this->error('该试题所属考试测评数据异常，提交失败！');
            }
            $lists = $_POST['lists'];
            foreach ($lists as $k=>$v){
                if(!is_numeric($v['value'])){
                    $this->error('提交失败！');
                }
                $relation = $this->paper_question_model->where(array("id"=>$v['id'],"paper_id"=>$id))->find();
                if(!$relation){
                    $this->error('数据传入失败！');
                }
            }
            //排序
            foreach ($lists as $m=>$n){
                $news = ['id'=>$n['id'],'listorder'=>intval($n['value'])];
                $this->paper_question_model->save($news);
            }
            $this->success('提交成功！');
        }
    }

    // 页面轮播图
    public function banner(){
        $where['cattype'] = 2;//在线测评轮播图类型
        /**搜索条件**/
        $name = I('request.name');
        if($name) {
            $where['name'] = array('like', "%$name%");
        }
        $count = $this->banner_model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $this->banner_model
            ->where($where)
            ->order(array("listorder" => "ASC", "id" => "asc"))
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("banner", $data);
        $this->display();
    }

    // 新增轮播图
    public function banneradd(){
        $this->display();
    }

    // 新增轮播图提交
    public function banneradd_post(){
        if(IS_POST){
            $_POST['cattype'] = 2;
            if ($this->banner_model->create()!==false) {
                if ($this->banner_model->add()!==false) {
                    $res = ['rs'=>0,'msg'=>'添加成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'删除失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->banner_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 编辑轮播图
    public function banneredit(){
        $id = I("get.id",0,'intval');
        $data = $this->banner_model->where(array("id" => $id,"cattype" => 2))->find();
        if (!$data) {
            $this->error("该轮播图不存在！");
        }
        $this->assign($data);
        $this->display();
    }

    // 编辑轮播图提交
    public function banneredit_post(){
        if(IS_POST){
            if ($this->banner_model->create()!==false) {
                if ($this->banner_model->save()!==false) {
                    $res = ['rs'=>0,'msg'=>'保存成功！'];
                } else {
                    $res = ['rs'=>-1,'msg'=>'保存失败！'];
                }
            } else {
                $res = ['rs'=>-2,'msg'=>$this->banner_model->getError()];
            }
            $this->ajaxReturn($res);
        }
    }

    // 轮播图删除
    public function banner_delete(){
        $id = I('post.id',0,'intval');
        if ($this->banner_model->delete($id)!==false) {
            $res = ['rs'=>0,'msg'=>'删除成功！'];
        } else {
            $res = ['rs'=>-1,'msg'=>'删除失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 轮播图下架
    public function obtained(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->banner_model->where(array("id"=>$id))->setField('status','0');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'轮播图下架成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'轮播图下架失败！'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 轮播图上架
    public function shelf(){
        $id = I('post.id',0,'intval');
        if (!empty($id)) {
            $result = $this->banner_model->where(array("id"=>$id))->setField('status','1');
            if ($result!==false) {
                $res = ['rs'=>0,'msg'=>'轮播图上架成功！'];
            } else {
                $res = ['rs'=>-1,'msg'=>'轮播图上架失败！'];
            }
        } else {
            $res = ['rs'=>-2,'msg'=>'数据传入失败！'];
        }
        $this->ajaxReturn($res);
    }

    // 导出excel
    public function excel(){
        $where['id'] = array('gt',0);
        /**搜索条件**/
        $title = I('get.title');
        $cate_id = trim(I('get.cate_id'));
        if($title){
            $where['title'] = array('like',"%$title%");
        }
        if($cate_id){
            $where['cate_id'] = array('eq',$cate_id);
        }

        $paper = $this->paper_model->where($where)->select();
        if(count($paper) > 0){
            $data = array();
            $cate = $this->category_model->where(array('type'=>1))->select();
            $statusInfo = array("隐藏","显示");
            foreach ($paper as $k=>$v){
                $data[$k]['id'] = $v['id'];
                $data[$k]['create_time'] = $v['create_time'];
                $data[$k]['title'] = $v['title'];
                $data[$k]['cate_name'] = '';
                foreach ($cate as $m=>$n){
                    if($n['id'] == $v['cate_id']){
                        $data[$k]['cate_name'] = $n['name'];
                    }
                }
                $data[$k]['exam_time'] = $v['exam_time'];
                $data[$k]['listorder'] = $v['listorder'];
                $data[$k]['status'] = $statusInfo[$v['status']];
                $data[$k]['number'] = $v['number'];
            }
            $fileheader = array(
                array('id', 'ID'),
                array('create_time', '创建时间'),
                array('title', '测评标题'),
                array('cate_name', '测评类型'),
                array('exam_time', '测评时间(分钟)'),
                array('listorder', '排序'),
                array('status', '状态'),
                array('number', '参与测评(人)')
            );
            $this->exportExcel(time(),$fileheader,$data);
        }else{
            $this->error("当前条件下不存在测评信息！");
        }
    }

}