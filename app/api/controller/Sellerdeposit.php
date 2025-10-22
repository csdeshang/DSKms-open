<?php

/**
 * 预存款管理
 */

namespace app\api\controller;
use think\facade\Db;
use think\facade\Lang;
use app\common\model\Storedepositlog;
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
 * 店铺保证金控制器
 */
class  Sellerdeposit extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellerdeposit.lang.php');
    }


    /**
     * @api {POST} api/Sellerdeposit/index 保证金变更日志
     * @apiVersion 1.0.0
     * @apiGroup Sellerdeposit
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {String} query_start_date 开始时间 YYYY-MM-DD
     * @apiParam {String} query_end_date 结束时间  YYYY-MM-DD
     * @apiParam {String} storedepositlog_desc 日志详情
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.log_list  保证金列表 （返回字段参考storedepositlog表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function index() {
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);

        $query_start_date = input('param.query_start_date');
        $query_end_date = input('param.query_end_date');
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date) : null;
        $end_unixtime = $if_end_date ? (strtotime($query_end_date) + 86399) : null;
        if ($start_unixtime || $end_unixtime) {
            $condition[] = array('storedepositlog_add_time','between', array($start_unixtime, $end_unixtime));
        }

        $storedepositlog_desc = input('param.storedepositlog_desc');
        if ($storedepositlog_desc) {
            $condition[] = array('storedepositlog_desc','like', '%' . $storedepositlog_desc . '%');
        }
        $storedepositlog_model = model('storedepositlog');
        $log_list = $storedepositlog_model->getStoredepositlogList($condition, 10, '*', 'storedepositlog_id desc');

        $result = array_merge(array('log_list' => $log_list), mobile_page($storedepositlog_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }


    /**
     * @api {POST} api/Sellerdeposit/withdraw_list 保证金提现列表
     * @apiVersion 1.0.0
     * @apiGroup Sellerdeposit
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} paystate_search 提现状态 0无效1有效2待审核3已同意4已拒绝5已缴纳6已取消
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.log_list  保证金列表 （返回字段参考storedepositlog表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function withdraw_list() {
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $condition[] = array('storedepositlog_type','in', [Storedepositlog::TYPE_WITHDRAW, Storedepositlog::TYPE_RECHARGE]);

        $paystate_search = input('param.paystate_search');
        if (isset($paystate_search) && $paystate_search !== '') {
            $condition[] = array('storedepositlog_state','=',intval($paystate_search));
        }

        $storedepositlog_model = model('storedepositlog');
        $log_list = $storedepositlog_model->getStoredepositlogList($condition, 10, '*', 'storedepositlog_id desc');

        $result = array_merge(array('log_list' => $log_list), mobile_page($storedepositlog_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }

    /**
     * @api {POST} api/Sellerdeposit/recharge_add 补缴机构保证金
     * @apiVersion 1.0.0
     * @apiGroup Sellerdeposit
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Float} pdc_amount 补缴金额
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function recharge_add() {
        $storedepositlog_model = model('storedepositlog');
        $money = abs(floatval(input('post.pdc_amount')));
        if (!$money) {
            ds_json_encode(10001, lang('param_error'));
        }

        Db::startTrans();
        try {

            $data = array(
                'store_id' => $this->store_info['store_id'],
                'store_name' => $this->store_info['store_name'],
                'storedepositlog_type' => Storedepositlog::TYPE_PAY,
                'storedepositlog_state' => Storedepositlog::STATE_VALID,
                'storedepositlog_add_time' => TIMESTAMP,
            );
            $data['storedepositlog_avaliable_deposit'] = $money;

            $data['storedepositlog_desc'] = lang('sellerdeposit_recharge_deposit');

            $storedepositlog_model->changeStoredeposit($data);
            //从机构资金中扣除
            $storemoneylog_model = model('storemoneylog');
            $data2 = array(
                'store_id' => $this->store_info['store_id'],
                'store_name' => $this->store_info['store_name'],
                'storemoneylog_type' => Storemoneylog::TYPE_DEPOSIT_IN,
                'storemoneylog_state' => Storemoneylog::STATE_VALID,
                'storemoneylog_add_time' => TIMESTAMP,
                'storemoneylog_avaliable_money' => -$money,
                'storemoneylog_desc' => $data['storedepositlog_desc'],
            );
            $storemoneylog_model->changeStoremoney($data2);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            ds_json_encode(10001, $e->getMessage());
        }
        $this->recordSellerlog(lang('sellerdeposit_recharge_deposit'));
        ds_json_encode(10000, lang('ds_common_op_succ'));
    }

    /**
     * @api {POST} api/Sellerdeposit/withdraw_add 申请保证金提现
     * @apiVersion 1.0.0
     * @apiGroup Sellerdeposit
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
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
        $sellerdeposit_validate = ds_validate('sellerdeposit');
        if (!$sellerdeposit_validate->scene('withdraw_add')->check($data)) {
            ds_json_encode(10001, $sellerdeposit_validate->getError());
        }

        $pdc_amount = $data['pdc_amount'];
        $storedepositlog_model = model('storedepositlog');

        $data = array(
            'store_id' => $this->store_info['store_id'],
            'store_name' => $this->store_info['store_name'],
            'storedepositlog_type' => Storedepositlog::TYPE_WITHDRAW,
            'storedepositlog_state' => Storedepositlog::STATE_WAIT,
            'storedepositlog_add_time' => TIMESTAMP,
        );
        $data['storedepositlog_avaliable_deposit'] = -$pdc_amount;
        $data['storedepositlog_freeze_deposit'] = $pdc_amount;


        $data['storedepositlog_desc'] = lang('sellerdeposit_apply_withdraw') . lang('sellerdeposit_avaliable_money');
        
        Db::startTrans();
        try {
            $storedepositlog_model->changeStoredeposit($data);
            Db::commit();
            $this->recordSellerlog(lang('sellerdeposit_apply_withdraw'));
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } catch (\Exception $e) {
            Db::rollback();
            ds_json_encode(10001, $e->getMessage());
        }
    }

}
