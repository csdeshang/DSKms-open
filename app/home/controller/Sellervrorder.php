<?php

/*
 * 虚拟订单
 */

namespace app\home\controller;
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
class  Sellervrorder extends BaseSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/'.config('lang.default_lang').'/sellervrorder.lang.php');
    }

    /**
     * 虚拟订单列表
     *
     */
    public function index() {
        $vrorder_model = model('vrorder');

        $condition = array();
        $condition[] = array('store_id','=',session('store_id'));

        $order_sn = input('get.order_sn');
        if ($order_sn != '') {
            $condition[] = array('order_sn','=',$order_sn);
        }
        $buyer_name = input('get.buyer_name');
        if ($buyer_name != '') {
            $condition[] = array('buyer_name','=',$buyer_name);
        }
        $allow_state_array = array('state_new', 'state_noeval', 'state_success', 'state_cancel');
        $state_type = input('param.state_type');
        if (in_array($state_type, $allow_state_array)) {
            switch($state_type){
                case 'state_new':
                    $condition[] = array('order_state','=',ORDER_STATE_NEW);
                    break;
                case 'state_success':
                    $condition[] = array('order_state','=',ORDER_STATE_PAY);
                    break;
                case 'state_noeval':
                    $condition[] = array('order_state','=',ORDER_STATE_SUCCESS);
                    $condition[] = array('evaluation_state','=',0);
                    break;
                case 'state_cancel':
                    $condition[] = array('order_state','=',ORDER_STATE_CANCEL);
                    break;
            }
        } else {
            $state_type = 'store_order';
        }
        $query_start_date = input('get.query_start_date');
        $query_end_date = input('get.query_end_date');
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_end_date);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_start_date);
        $start_unixtime = $if_start_date ? strtotime($query_end_date) : null;
        $end_unixtime = $if_end_date ? (strtotime($query_start_date)+86399) : null;
        if ($start_unixtime) {
            $condition[] = array('add_time', '>=', $start_unixtime);
        }
        if ($end_unixtime) {
            $condition[] = array('add_time', '<=', $end_unixtime);
        }

        $skip_off = input('get.skip_off');
        if ($skip_off == 1) {
            $condition[] = array('order_state','<>',ORDER_STATE_CANCEL);
        }

        $order_list = $vrorder_model->getVrorderList($condition, 20, '*', 'order_id desc');
        View::assign('show_page', $vrorder_model->page_info->render());
        
        foreach ($order_list as $key => $order) {
            //显示取消订单
            $order_list[$key]['if_cancel'] = $vrorder_model->getVrorderOperateState('buyer_cancel', $order);

            //追加返回买家信息
            $order_list[$key]['extend_member'] = model('member')->getMemberInfoByID($order['buyer_id']);
            
            $order_list[$key]['goods_type_cn'] = get_order_goodstype($order['goods_type']);
        }

        View::assign('order_list', $order_list);



        /* 设置卖家当前菜单 */
        $this->setSellerCurMenu('sellervrorder');
        /* 设置卖家当前栏目 */
        $this->setSellerCurItem($state_type);
        return View::fetch($this->template_dir.'index');
    }

    /**
     * 卖家订单详情
     *
     */
    public function show_order() {
        $order_id = intval(input('param.order_id'));
        if ($order_id <= 0) {
            $this->error(lang('param_error'));
        }
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('order_id','=',$order_id);
        $condition[] = array('store_id','=',session('store_id'));
        $order_info = $vrorder_model->getVrorderInfo($condition);
        if (empty($order_info)) {
            $this->error(lang('store_order_none_exist'));
        }

        //显示取消订单
        $order_info['if_cancel'] = $vrorder_model->getVrorderOperateState('buyer_cancel', $order_info);

        //显示订单进行步骤
        $order_info['step_list'] = $vrorder_model->getVrorderStep($order_info);

        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + config('ds_config.order_auto_cancel_day') * 24 * 3600;
        }
        
        $order_info['goods_type_cn'] = get_order_goodstype($order_info['goods_type']);

        View::assign('order_info', $order_info);
        $this->setSellerCurMenu('sellervrorder');
        $this->setSellerCurItem('store_order');

        return View::fetch($this->template_dir.'show_order');
    }

    /**
     * 卖家订单状态操作
     *
     */
    public function change_state() {
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('order_id','=',intval(input('param.order_id')));
        $condition[] = array('store_id','=',session('store_id'));
        $order_info = $vrorder_model->getVrorderInfo($condition);
        $state_type = input('param.state_type');
        if ($state_type == 'order_cancel') {
            $result = $this->_order_cancel($order_info, input('post.'));
        }
        if (!isset($result['code'])) {
            ds_json_encode(10001,lang('error'));
        } else {
            ds_json_encode(10000,$result['msg']);
        }
    }

    /**
     * 取消订单
     * @param arrty $order_info
     * @param arrty $post
     * @throws Exception
     */
    private function _order_cancel($order_info, $post) {
        if (!request()->isPost()) {
            View::assign('order_id', $order_info['order_id']);
            View::assign('order_info', $order_info);
            echo View::fetch($this->template_dir.'cancel');
            exit();
        } else {
            $vrorder_model = model('vrorder');
            $logic_vrorder = model('vrorder','logic');
            $if_allow = $vrorder_model->getVrorderOperateState('store_cancel', $order_info);
            if (!$if_allow) {
                return ds_callback(false,lang('have_right_operate'));
            }
            $msg = $post['state_info1'] != '' ? $post['state_info1'] : $post['state_info'];
            return $logic_vrorder->changeOrderStateCancel($order_info, 'seller', $msg);
        }
    }


    /**
     *    栏目菜单
     */
    function getSellerItemList() {
        $menu_array = array(
            array(
                'name' => 'store_order',
                'text' => lang('ds_member_path_all_order'),
                'url' => url('Sellervrorder/index'),
            ),
            array(
                'name' => 'state_new',
                'text' => lang('ds_member_path_wait_pay'),
                'url' => url('Sellervrorder/index', ['state_type' => 'state_new']),
            ),

            array(
                'name' => 'state_success',
                'text' => lang('store_paid_payment'),
                'url' => url('Sellervrorder/index', ['state_type' => 'state_success']),
            ),
            array(
                'name' => 'state_noeval',
                'text' => lang('store_pending_evaluation'),
                'url' => url('Sellervrorder/index', ['state_type' => 'state_noeval']),
            ),
            array(
                'name' => 'state_cancel',
                'text' => lang('ds_member_path_canceled'),
                'url' => url('Sellervrorder/index', ['state_type' => 'state_cancel']),
            ),
        );
        return $menu_array;
    }


}
