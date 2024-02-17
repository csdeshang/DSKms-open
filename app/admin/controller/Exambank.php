<?php

namespace app\admin\controller;
use think\facade\View;
use think\facade\Lang;

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
 * 控制器
 */
class Exambank extends AdminControl {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'admin/lang/' . config('lang.default_lang') . '/exambank.lang.php');
    }

    public function index() {
        $exambank_model = model('exambank');
        $condition = array();
        $condition[] = array('store_id','=',0);
        $exambank_question = input('param.exambank_question');
        if (!empty($exambank_question)) {
            $condition[] = array('exambank_question','like', "%" . $exambank_question . "%");
        }
        $exambank_level = input('param.exambank_level');
        if ($exambank_level != '') {
            $condition[] = array('exambank_level','=',$exambank_level);
        }
        $examtype_id = intval(input('param.examtype_id'));
        if ($examtype_id > 0) {
            $condition[] = array('examtype_id','=',$examtype_id);
        }
        $examclass_id = intval(input('param.examclass_id'));
        if ($examclass_id > 0) {
            $condition[] = array('examclass_id','=',$examclass_id);
        }


        $exambank_list = $exambank_model->getExambankList($condition, 10);
        View::assign('exambank_list', $exambank_list);
        View::assign('show_page', $exambank_model->page_info->render());
        View::assign('exambank_level_list', $exambank_model->getLevelList());
        View::assign('examtype_list', $exambank_model->getExamtypeList());
        View::assign('examclass_list', model('examclass')->getTreeClassList(2, 0));
        $this->setAdminCurItem('index');
        return View::fetch('index');
    }

    public function list_json() {
        $exambank_model = model('exambank');
        $examtype_id = intval(input('param.examtype_id'));
        $condition = array();
        $condition[] = array('store_id','=',0);
        $exambank_question = input('param.exambank_question');
        if (!empty($exambank_question)) {
            $condition[] = array('exambank_question','like', "%" . $exambank_question . "%");
        }
        $exambank_level = input('param.exambank_level');
        if ($exambank_level != '') {
            $condition[] = array('exambank_level','=',$exambank_level);
        }
        if ($examtype_id > 0) {
            $condition[] = array('examtype_id','=',$examtype_id);
        }
        $examclass_id = intval(input('param.examclass_id'));
        if ($examclass_id > 0) {
            $condition[] = array('examclass_id','=',$examclass_id);
        }
        $exambank_model->getExambankList($condition, 5);
        $result = $exambank_model->page_info->toArray();
        foreach ($result['data'] as $key => $exambank) {
            //异步调用为显示标题，去除html 标签
            $result['data'][$key]['exambank_question'] = strip_tags(htmlspecialchars_decode($exambank['exambank_question']));
        }
        ds_json_encode(10000, '', $result);
    }

    function mobile_page($page_info) {
        //输出是否有下一页
        $extend_data = array();
        if ($page_info == '') {
            $extend_data['page_total'] = 1;
            $extend_data['hasmore'] = false;
        } else {
            $current_page = $page_info->currentPage();
            if ($current_page <= 0) {
                $current_page = 1;
            }
            if ($current_page >= $page_info->lastPage()) {
                $extend_data['hasmore'] = false;
            } else {
                $extend_data['hasmore'] = true;
            }
            $extend_data['page_total'] = $page_info->lastPage();
        }
        return $extend_data;
    }

    /**
     * 题库添加
     */
    public function add() {
        $exambank_model = model('exambank');
        if (!request()->isPost()) {
            $exambank = array(
                'examtype_id' => 0,
                'exambank_level' => 0,
                'exambank_answer' => 'A',
                'exambank_question' => '',
                'examclass_id' => 0,
            );
            View::assign('exambank', $exambank);
            View::assign('exambank_level_list', $exambank_model->getLevelList());
            View::assign('examtype_list', $exambank_model->getExamtypeList());
            View::assign('examclass_list', model('examclass')->getTreeClassList(2, 0));
            $this->setAdminCurItem('add');
            return View::fetch('form');
        } else {
            //根据类型获取选项
            $data = $this->_get_post_data();
            $data['store_id'] = 0;
            $result = $exambank_model->addExambank($data);
            if ($result) {
                $this->log(lang('ds_add') . lang('limit_exambank') . '[' . input('post.exambank_name') . ']', 1);
                $this->success(lang('ds_common_save_succ'));
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }

    public function edit() {
        $exambank_id = intval(input('param.exambank_id'));
        $exambank_model = model('exambank');
        $exambank = $exambank_model->getOneExambank(array('exambank_id' => $exambank_id));
        if (empty($exambank)) {
            $this->error(lang('param_error'));
        }
        if (!request()->isPost()) {
            $exambank['exambank_question'] = htmlspecialchars_decode($exambank['exambank_question']);
            $exambank['exambank_describe'] = htmlspecialchars_decode($exambank['exambank_describe']);
            View::assign('exambank', $exambank);
            View::assign('exambank_level_list', $exambank_model->getLevelList());
            View::assign('examtype_list', $exambank_model->getExamtypeList());
            View::assign('examclass_list', model('examclass')->getTreeClassList(2, 0));
            $this->setAdminCurItem('edit');
            return View::fetch('form');
        } else {
            //根据类型获取选项
            $data = $this->_get_post_data();
            $condition = array(
                'store_id' => 0,
                'exambank_id' => $exambank_id,
            );
            $result = $exambank_model->editExambank($condition, $data);
            if ($result) {
                $this->log(lang('ds_edit') . lang('limit_exambank') . '[ID:' . $exambank_id . ']', 1);
                $this->success(lang('ds_common_save_succ'));
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }
    
     public function suggest() {
        $exambanksuggest_model = model('exambanksuggest');
        $condition = array();
        $condition[]=array('es.store_id','=',0);
        
       
        
        $exambanksuggest_membername = input('param.exambanksuggest_membername');
        if ($exambanksuggest_membername != '') {
            $condition[]=array('es.exambanksuggest_membername','=',$exambanksuggest_membername);
        }
        $exambanksuggest_list = $exambanksuggest_model->getExambanksuggestList($condition,'*',10);
        View::assign('exambanksuggest_list', $exambanksuggest_list);
        View::assign('show_page', $exambanksuggest_model->page_info->render());
        $this->setAdminCurItem('suggest');
        return View::fetch('suggest');
    }
    
    public function suggestedit() {
        $exambanksuggest_model = model('exambanksuggest');
        $condition = array();
        if (request()->isPost()) {
            $exambanksuggest_id = input('param.exambanksuggest_id');
            if($exambanksuggest_id != ''){
                $condition[]=array('exambanksuggest_id','=',$exambanksuggest_id);
            }else{
                $this->error(lang('param_error'));
            }
            $exambanksuggest_feedback = input('param.exambanksuggest_feedback');
            $update = $exambanksuggest_model->editExambanksuggest($condition,array('exambanksuggest_feedback' => $exambanksuggest_feedback));
            if($update){
             $this->log(lang('exambanksuggest_handle') . $exambanksuggest_id, 1);
             dsLayerOpenSuccess(lang('ds_common_op_succ'));
            }else{
                $this->error(lang('ds_common_op_fail'));
            }
        }else{
            return View::fetch('suggestedit'); 
        }
       
    }

    private function _get_post_data() {

        $examtype_id = intval(input('param.examtype_id'));
        $exambank_select = array();
        $exambank_answer = '';
        $exambank_selectnum = 0;
        if ($examtype_id == 1 || $examtype_id == 2) {
            // 单选/多选
            $args = input('param.args/a');
            if (isset($args['option'])) {
                //题目选项
                foreach ($args['option'] as $key => $value) {
                    $exambank_select[$args['alisa'][$key]] = $value;
                }
                //答案
                foreach ($args['key'] as $key => $value) {
                    $exambank_answer .= $value;
                }
            }
        } elseif ($examtype_id == 3 || $examtype_id == 5) {
            // 判断 问答
            $exambank_answer = input('param.exambank_answer');
        } elseif ($examtype_id == 4) {
            //填空
            $args = input('param.args/a');
            $exambank_selectnum = count($args['key']);
            $exambank_answer = implode('+', $args['key']);
        }
        
        $data['examclass_id'] = input('post.examclass_id');
        $data['examtype_id'] = $examtype_id;
        $data['exambank_question'] = input('post.exambank_question');
        $data['exambank_answer'] = $exambank_answer; //填空题答案为数组
        $data['exambank_select'] = serialize($exambank_select); //只有单选和多选才有值
        $data['exambank_selectnum'] = $exambank_selectnum; // 填空数量
        $data['exambank_describe'] = input('post.exambank_describe');
        $data['exambank_level'] = input('post.exambank_level');
        $data['exambank_addtime'] = TIMESTAMP;

        return $data;
    }

    public function detail() {
        $exambank_id = intval(input('param.exambank_id'));
        $exambank_model = model('exambank');
        $exambank = $exambank_model->getOneExambank(array('exambank_id' => $exambank_id));
        if (empty($exambank)) {
            $this->error(lang('param_error'));
        }
        View::assign('exambank', $exambank);
        View::assign('exambank_level_list', $exambank_model->getLevelList());
        View::assign('examtype_list', $exambank_model->getExamtypeList());
        return View::fetch();
    }

    public function drop() {
        $exambank_id = intval(input('param.exambank_id'));
        if (!empty($exambank_id)) {
            $exambank_mod = model('exambank');
            $store_id = 0;
            $condition = array(
                'exambank_id' => $exambank_id,
                'store_id' => $store_id
            );
            $exambank_mod->delExambank($condition);
            $this->log(lang('ds_del') . lang('limit_exambank') . '[ID:' . $exambank_id . ']', 1);
            ds_json_encode(10000, lang('ds_common_del_succ'));
        } else {
            ds_json_encode(10001, lang('ds_common_del_fail'));
        }
    }

    /**
     * 获取卖家栏目列表,针对控制器下的栏目
     */
    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index',
                'text' => lang('exambank_index'),
                'url' => url('exambank/index')
            ),
            array(
                'name' => 'add',
                'text' => lang('exambank_add'),
                'url' => url('exambank/add')
            ),
            array(
                'name' => 'suggest',
                'text' => lang('exambank_suggest'),
                'url' => url('exambank/suggest')
            ),
        );

        return $menu_array;
    }

}

?>
