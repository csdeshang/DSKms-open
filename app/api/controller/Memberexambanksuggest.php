<?php

namespace app\api\controller;/**
 * ============================================================================
 * DSKMS多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 用户反馈控制器
 */

class Memberexambanksuggest extends MobileMember {

    public function initialize() {
        parent::initialize();
    }

    /**
     * 反馈列表
     */
    public function memberexambanksuggest_list() {
        $exambanksuggest_model = model('exambanksuggest');

        $exambanksuggest_list = $exambanksuggest_model->getExambanksuggestList(array('exambanksuggest_memberid' => session('member_id')), '*', 10);
        foreach ($exambanksuggest_list as $key => $value) {
            $exambanksuggest_list[$key]['exambank_question'] = strip_tags(htmlspecialchars_decode($value['exambank_question']));
        }
        $result = array_merge(array('exambanksuggest_list' => $exambanksuggest_list), mobile_page($exambanksuggest_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * 反馈列表
     */
    public function get_onememberexambank() {
        $exambank_id = input('post.exambank_id');
        $exambank_model = model('exambank');
        if ($exambank_id > 0) {
            //获取题目信息
            $exambank = $exambank_model->getOneExambank(array('exambank_id' => $exambank_id));
            if (empty($exambank)) {
                ds_json_encode(10001, lang('exambank_id_no'));
            } else {
                $exambank['exambank_question'] = strip_tags(htmlspecialchars_decode($exambank['exambank_question']));
                $result = array_merge(array('exambank' => $exambank));
            }
        } else {
            ds_json_encode(10001, lang('param_error'));
        }
        ds_json_encode(10000, '', $result);
    }

    /**
     * 添加反馈
     */
    public function memberexambanksuggest_add() {
        $exambanksuggest_model = model('exambanksuggest');
        $exambank_model = model('exambank');

        $exambank_id = input('post.exambank_id');

        if ($exambank_id > 0) {
            //获取题目信息
            $exambank = $exambank_model->getOneExambank(array('exambank_id' => $exambank_id));
            if (empty($exambank)) {
                ds_json_encode(10001, lang('exambank_id_no'));
            } else {
                $param = array();
                $param['exambanksuggest_describe'] = input('post.exambanksuggest_describe');
                $param['exambank_id'] = input('post.exambank_id');
                $param['exambanksuggest_addtime'] = TIMESTAMP;
                $param['exambanksuggest_memberid'] = $this->member_info['member_id'];
                $param['exambanksuggest_membername'] = $this->member_info['member_name'];

                $result = $exambanksuggest_model->addExambanksuggest($param);
                if ($result) {
                    ds_json_encode(10000, lang('ds_common_op_succ'));
                } else {
                    ds_json_encode(10001, lang('ds_common_op_fail'));
                }
            }
        } else {
            ds_json_encode(10001, lang('param_error'));
        }
    }

}
