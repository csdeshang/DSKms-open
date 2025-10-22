<?php

namespace app\api\controller;
use think\facade\View;
use think\facade\Db;

/**
 * ============================================================================
 * DSKMS多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 文章控制器
 */
class Memberexampaper extends MobileMember {

    public function initialize() {
        parent::initialize();
    }

    
    //平台试卷列表
    public function exampaper_list() {
        $exampaper_model = model('exampaper');
		$goods_model = model('goods');
        $condition = array();
        $exampaper_list = $exampaper_model->getExampaperList($condition,10);
		foreach($exampaper_list as $key => $val){
			if($val['goods_id'] > 0){
				$condition  = array();
				$condition[]= array('goods_id','=',$val['goods_id']);
				$goods_info  = $goods_model->getGoodsInfo($condition,'goods_name,goods_image,goods_price');
				$exampaper_list[$key]['goods_name'] = $goods_info['goods_name'];
				$exampaper_list[$key]['goods_price'] = $goods_info['goods_price'];
				$exampaper_list[$key]['goods_image'] = goods_cthumb($goods_info['goods_image']);
			}
		}
        $result= array_merge(array('exampaper_list' => $exampaper_list), mobile_page($exampaper_model->page_info));
        ds_json_encode(10000, '',$result);
    }
    
    
    /**
     * @api {POST} api/Exampaper/detail 获取单个题目详情
     * @apiVersion 1.0.0
     * @apiGroup Article
     *
     * @apiParam {Int} article_id 文章ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.ac_id  分类ID
     * @apiSuccess {String} result.article_content  文章内容
     * @apiSuccess {Int} result.article_id  文章ID
     * @apiSuccess {String} result.article_pic  文章图片
     * @apiSuccess {Int} result.article_show  是否显示 0否1是
     * @apiSuccess {Int} result.article_sort  排序
     * @apiSuccess {Int} result.article_time  添加时间（Unix时间戳）
     * @apiSuccess {String} result.article_title  文章标题
     * @apiSuccess {String} result.article_url  文章跳转链接
     */
    public function detail() {
        $exampaper_id = intval(input('param.exampaper_id'));
        $exampaper_model = model('exampaper');
        $exampaper = $exampaper_model->getOneExampaper(array('exampaper_id' => $exampaper_id));
        if (empty($exampaper)) {
            ds_json_encode(10001, '缺少参数:文章编号');
        }

        if ($exampaper['exampaper_type'] == 0) {
            //普通测试题
            $exampaper['section_list'] = unserialize($exampaper['exampaper_setting']);
        } else {
            //随机题  根据配置自动生成题目
            $exampaper_setting = unserialize($exampaper['exampaper_setting']);
            $section_list = array();
            $exambank_model = model('exambank');
            foreach ($exampaper_setting as $value) {
                //检索   根据条件 生成题目
                $condition = array();
                if (in_array($value['section_type'], array(1, 2, 3, 4, 5))) {
                    $condition[] = array('examtype_id','=',$value['section_type']);
                }
                if (in_array($value['section_level'], array(0, 1, 2, 3, 4))) {
                    $condition[] = array('exambank_level','=',$value['section_level']);
                }
                $limit = $value['section_nums']; #题目数量
                $exambank_list = Db::name('exambank')->where($condition)->limit($limit)->select()->toArray();

                $section_score = $value['section_score']; #每题得分
                //题目
                $items = array();
                $i = 0;
                foreach ($exambank_list as $key => $exambank) {
                    $items[$i]['exambank_score'] = $section_score;
                    $items[$i]['examtype_id'] = $exambank['examtype_id'];
                    $items[$i]['exambank_id'] = $exambank['exambank_id'];
                    $items[$i]['exambank_question'] = $exambank['exambank_question'];
                    $items[$i]['exambank_answer'] = $exambank['exambank_answer'];
                    $items[$i]['exambank_select'] = $exambank['exambank_select'];
                    $items[$i]['exambank_describe'] = $exambank['exambank_describe'];
                    $items[$i]['exambank_level'] = $exambank['exambank_level'];
                }

                $section_list[] = array(
                    'section_name' => $value['section_name'],
                    'section_remark' => $value['section_remark'],
                    'items' => $items,
                );
            }
            $exampaper['section_list'] = $section_list;
            $exampaper['section_list_serialize'] = serialize($section_list);
        }
//        halt($exampaper);
        $order_id = 0;
        foreach ($exampaper['section_list'] as $section_key => $section) {
            foreach ($section['items'] as $item_key => $item) {
                $exampaper['section_list'][$section_key]['items'][$item_key]['exambank_describe'] = htmlspecialchars_decode($item['exambank_describe']);
                $exampaper['section_list'][$section_key]['items'][$item_key]['exambank_question'] = htmlspecialchars_decode($item['exambank_question']);
                $exambank_select = unserialize($item['exambank_select']);
                $exampaper['section_list'][$section_key]['items'][$item_key]['exambank_select'] = $exambank_select;
                //适合vue选项
                $exambank_select_options = array();
                foreach ($exambank_select as $select_key => $select) {
                    $options = array(
                        'label' => '[' . $select_key . ']' . $select,
//                         'label' =>  $select,
                        'value' => $select_key,
                    );
                    $exambank_select_options[] = $options;
                }
                $exampaper['section_list'][$section_key]['items'][$item_key]['exambank_select_options'] = $exambank_select_options;
                $order_id++;
                $exampaper['section_list'][$section_key]['items'][$item_key]['exambank_order_id'] = $order_id;
            }
        }
        $exampaper['items_total'] = $order_id; #题目总数
        ds_json_encode(10000, '', $exampaper);
    }

    function add() {
        $exampaper_id = intval(input('param.exampaper_id'));
        $exampaper_model = model('exampaper');
        $exampaper = $exampaper_model->getOneExampaper(array('exampaper_id' => $exampaper_id));
        if (empty($exampaper)) {
            ds_json_encode(10001, '缺少参数:文章编号');
        }

        
        $exampaperlog_model = model('exampaperlog');
        // 用户提交的答案
        $member_answer = input('param.member_answer/a');
        
        
        if ($exampaper['exampaper_type'] == 0) {
            //普通测试题  答案和数据整合一起
            $section_list = unserialize($exampaper['exampaper_setting']);
        } else {
            // 随机题 获取生成的数据 整合一起 录入到试卷
            $section_list = unserialize($_POST['section_list_serialize']);
        }
        $exampaperlog_score = 0; #考试批改后得分
        $total_count=0;
        $score_count=0;
        foreach ($section_list as $section_key => $section_value) {
            foreach ($section_value['items'] as $item_key => $item_value) {
                //$section_list 新增答案  存放至数组
                $section_list[$section_key]['items'][$item_key]['member_answer'] = $this->get_member_answer($item_value['examtype_id'], isset($member_answer[$item_value['exambank_id']]) ? $member_answer[$item_value['exambank_id']] : '');
                $total_count++;
                if($item_value['examtype_id']==1 || $item_value['examtype_id']==3){
                    $score_count++;
                    $check_state='N';
                    $check_score=0;
                    if($item_value['exambank_answer']==$section_list[$section_key]['items'][$item_key]['member_answer']){
                        $exampaperlog_score += $item_value['exambank_score'];
                        $check_state='Y';
                        $check_score=$item_value['exambank_score'];
                    }
                    $section_list[$section_key]['items'][$item_key]['check_state']=$check_state;
                    $section_list[$section_key]['items'][$item_key]['check_score']=$check_score;
                }
            }
        }
        $data = array(
            'member_id' => $this->member_info['member_id'],
            'member_name' => $this->member_info['member_name'],
            'exampaper_id' => $exampaper_id,
            'store_id' => $exampaper['store_id'],
            'exampaperlog_data' => serialize($section_list),
            'exampaperlog_state' => 1,
            'exampaperlog_ip' => request()->ip(),
            'exampaperlog_begintime' => input('param.exampaperlog_begintime'),
            'exampaperlog_endtime' => TIMESTAMP,
        );
        if($total_count==$score_count){
            $data['exampaperlog_state']=2;
            $data['exampaperlog_score']=$exampaperlog_score;
        }
        $result = $exampaperlog_model->addExampaperlog($data);
        ds_json_encode(10000, '提交成功');
    }
    
    private function get_member_answer($examtype_id, $member_answer) {
        if (empty($member_answer)) {
            return '';
        }
        if (in_array($examtype_id, array(1, 3, 5))) {
            //单选  判断  问答
            return $member_answer;
        } elseif ($examtype_id == 4) {
            //填空
            return implode('+', $member_answer);
        }elseif ($examtype_id == 2) {
            //问答
            return implode('+', $member_answer);
        }
    }
    
    /*
     * 考试记录
     */
    public function log()
    {
        $exampaperlog_model = model('exampaperlog');
        $condition = array();
        $condition[] = array('member_id','=',$this->member_info['member_id']);
        $exampaperlog_list = $exampaperlog_model->getExampaperlogList($condition, 10);
        
        $result= array_merge(array('exampaperlog_list' => $exampaperlog_list), mobile_page($exampaperlog_model->page_info));
        ds_json_encode(10000, '',$result);
    }
    
    /**
     * 查看考试详情
     */
    public function check()
    {
        //获取试卷答案
        $exampaperlog_id = intval(input('param.exampaperlog_id'));
        $exampaperlog_model = model('exampaperlog');
        $condition = array();
        $condition[] = array('exampaperlog_id','=',$exampaperlog_id);
        $condition[] = array('member_id','=',$this->member_info['member_id']);
        $exampaperlog = $exampaperlog_model->getOneExampaperlog($condition);
        $section_list = unserialize($exampaperlog['exampaperlog_data']);
        $exampaperlog['section_list'] = $section_list;
        View::assign('exampaperlog', $exampaperlog);
        
        $order_id = 0;
        foreach ($exampaperlog['section_list'] as $section_key => $section) {
            foreach ($section['items'] as $item_key => $item) {
                $exampaperlog['section_list'][$section_key]['items'][$item_key]['exambank_describe'] = htmlspecialchars_decode($item['exambank_describe']);
                $exampaperlog['section_list'][$section_key]['items'][$item_key]['exambank_question'] = htmlspecialchars_decode($item['exambank_question']);
                $exambank_select = unserialize($item['exambank_select']);
                $exampaperlog['section_list'][$section_key]['items'][$item_key]['exambank_select'] = $exambank_select;
                //适合vue选项
                $exambank_select_options = array();
                foreach ($exambank_select as $select_key => $select) {
                    $options = array(
                        'label' => '[' . $select_key . ']' . $select,
//                         'label' =>  $select,
                        'value' => $select_key,
                        'disabled'=>true
                    );
                    $exambank_select_options[] = $options;
                }
                $exampaperlog['section_list'][$section_key]['items'][$item_key]['exambank_select_options'] = $exambank_select_options;
                $order_id++;
                $exampaperlog['section_list'][$section_key]['items'][$item_key]['exambank_order_id'] = $order_id;
            }
        }
        $exampaperlog['items_total'] = $order_id; #题目总数
        
        ds_json_encode(10000, '',$exampaperlog);
    }
    
}
