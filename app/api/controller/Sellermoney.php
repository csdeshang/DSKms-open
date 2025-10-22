<?php

namespace app\api\controller;

use think\facade\Lang;
use think\facade\Db;
use app\common\model\Storemoneylog;
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
 * 卖家资金控制器
 */
class  Sellermoney extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellermoney.lang.php');
    }

    /**
     * @api {POST} api/Sellermoney/index 店铺资金日志
     * @apiVersion 1.0.0
     * @apiGroup Sellermoney
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.log_list  资金记录列表 （返回字段参考storemoneylog表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function index() {
        $condition = array('store_id' => $this->store_info['store_id']);

        $query_start_date = input('param.query_start_date');
        $query_end_date = input('param.query_end_date');
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date) : null;
        $end_unixtime = $if_end_date ? (strtotime($query_end_date) + 86399) : null;
        if ($start_unixtime || $end_unixtime) {
            $condition[] = array('storemoneylog_add_time','between',array($start_unixtime, $end_unixtime));
        }

        $storemoneylog_desc = input('param.storemoneylog_desc');
        if ($storemoneylog_desc) {
            $condition[] = array('storemoneylog_desc','like','%' . $storemoneylog_desc . '%');
        }
        $storemoneylog_model = model('storemoneylog');
        $log_list = $storemoneylog_model->getStoremoneylogList($condition, 10, '*', 'storemoneylog_id desc');

        $result = array_merge(array('log_list' => $log_list), mobile_page($storemoneylog_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }
    

    /**
     * @api {POST} api/Sellermoney/withdraw_list 提现列表
     * @apiVersion 1.0.0
     * @apiGroup Sellermoney
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.log_list  资金记录列表 （返回字段参考storemoneylog表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function withdraw_list() {
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $condition[] = array('storemoneylog_type','=',Storemoneylog::TYPE_WITHDRAW);
        $paystate_search = input('param.paystate_search');
        if (isset($paystate_search) && $paystate_search !== '') {
            $condition[] = array('storemoneylog_state','=',intval($paystate_search));
        }
        

        $storemoneylog_model = model('storemoneylog');
        $log_list = $storemoneylog_model->getStoremoneylogList($condition, 10, '*', 'storemoneylog_id desc');

        $result = array_merge(array('log_list' => $log_list), mobile_page($storemoneylog_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }

    /**
     * @api {POST} api/Sellermoney/withdraw_add 申请提现
     * @apiVersion 1.0.0
     * @apiGroup Sellermoney
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Float} pdc_amount 提现金额
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function withdraw_add() {
        $data = [
            'pdc_amount' => floatval(input('post.pdc_amount')),
        ];
        $sellermoney_validate = ds_validate('sellermoney');
        if (!$sellermoney_validate->scene('withdraw_add')->check($data)) {
            ds_json_encode(10001, $sellermoney_validate->getError());
        }

        $pdc_amount = $data['pdc_amount'];
        $storemoneylog_model = model('storemoneylog');
        //是否超过提现周期
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $condition[] = array('storemoneylog_state','in',[Storemoneylog::STATE_WAIT, Storemoneylog::STATE_AGREE]);
        $condition[] = array('storemoneylog_type','=',Storemoneylog::TYPE_WITHDRAW);
        $condition[] = array('storemoneylog_add_time','>',TIMESTAMP - intval(config('ds_config.store_withdraw_cycle')) * 86400);
        $last_withdraw = $storemoneylog_model->getStoremoneylogInfo($condition, 'storemoneylog_add_time');
        if ($last_withdraw) {
            ds_json_encode(10001, lang('sellermoney_last_withdraw_time_error') . date('Y-m-d', $last_withdraw['storemoneylog_add_time']));
        }
        //是否不小于最低提现金额
        if ($pdc_amount < floatval(config('ds_config.store_withdraw_min'))) {
            ds_json_encode(10001, lang('sellermoney_withdraw_min') . config('ds_config.store_withdraw_min') . lang('currency_zh'));
        }
        //是否不超过最高提现金额
        if ($pdc_amount > floatval(config('ds_config.store_withdraw_max'))) {
            ds_json_encode(10001, lang('sellermoney_withdraw_max') . config('ds_config.store_withdraw_max') . lang('currency_zh'));
        }
        $data = array(
            'store_id' => $this->store_info['store_id'],
            'store_name' => $this->store_info['store_name'],
            'storemoneylog_type' => Storemoneylog::TYPE_WITHDRAW,
            'storemoneylog_state' => Storemoneylog::STATE_WAIT,
            'storemoneylog_add_time' => TIMESTAMP,
        );
        $data['storemoneylog_avaliable_money'] = -$pdc_amount;
        $data['storemoneylog_freeze_money'] = $pdc_amount;

        $storejoinin_info = Db::name('storejoinin')->where(array('member_id' => $this->store_info['member_id']))->field('settlement_bank_account_name,settlement_bank_account_number,settlement_bank_name,settlement_bank_address')->find();

        $joinin_detail = model('storejoinin')->getOneStorejoinin(array('member_id' => $this->store_info['member_id']));
        if ($joinin_detail['business_licence_address'] != '') {
            $sml_desc = lang('sellermoney_bank_user') . '：' . $storejoinin_info['settlement_bank_account_name'] . '，' . lang('sellermoney_bank_number') . '：' . $storejoinin_info['settlement_bank_account_number'] . '，' . lang('sellermoney_bank_sub_name') . '：' . $storejoinin_info['settlement_bank_name'] . '，' . lang('sellermoney_bank_name') . '：' . $storejoinin_info['settlement_bank_address'];
        } else {
            $sml_desc = lang('sellermoney_alipay_name') . '：' . $storejoinin_info['settlement_bank_account_name'] . '，' . lang('sellermoney_alipay_number') . '：' . $storejoinin_info['settlement_bank_account_number'];
        }

        $data['storemoneylog_desc'] = $sml_desc;
        
        Db::startTrans();
        try {
            $storemoneylog_model->changeStoremoney($data);
            Db::commit();
            $this->recordSellerlog(lang('sellermoney_apply_withdraw'));
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } catch (\Exception $e) {
            Db::rollback();
            ds_json_encode(10001, $e->getMessage());
        }
    }



}
