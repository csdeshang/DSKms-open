<?php

namespace app\common\logic;
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
 * 逻辑层模型
 */
class  Payment
{

    /**
     * 取得虚拟订单所需支付金额等信息
     * @param int $order_sn
     * @param int $member_id
     * @return array
     */
    public function getVrOrderInfo($order_sn, $member_id = null)
    {

        //验证订单信息
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('order_sn','=',$order_sn);
        if (!empty($member_id)) {
            $condition[] = array('buyer_id','=',$member_id);
        }
        $order_info = $vrorder_model->getVrorderInfo($condition);
        if (empty($order_info)) {
            return ds_callback(false, '该订单不存在');
        }

        $order_info['subject'] = '虚拟订单_' . $order_sn;
        $order_info['order_type'] = 'vr_order';
        $order_info['pay_sn'] = $order_sn;

       
        //修复 第三方支付时 充值卡没算在内BUG
        $pay_amount = ds_price_format(floatval($order_info['order_amount']) - floatval($order_info['pd_amount']) - floatval($order_info['rcb_amount']));

        $order_info['api_pay_amount'] = $pay_amount;

        return ds_callback(true, '', $order_info);
    }

	 /**
	 * 取得机构入驻所需支付金额等信息
	 * @param int $order_sn
	 * @param int $member_id
	 * @return array
	 */
	public function getSjOrderInfo($order_sn)
	{
		if(empty($order_sn)){
			return ds_callback(false, '该机构入驻不存在');
		}
		//验证订单信息
		$storejoinin_model = model('storejoinin');
		$order_info = $storejoinin_model->getOneStorejoinin(array('pay_sn' => $order_sn));
		if (empty($order_info)) {
			return ds_callback(false, '该机构入驻不存在');
		}

		$order_info['subject'] = '机构入驻_' . $order_sn;
		$order_info['order_type'] = 'sj_order';
		$order_info['pay_sn'] = $order_sn;

	   
		//修复 第三方支付时 充值卡没算在内BUG
		$pay_amount = ds_price_format(floatval($order_info['paying_amount']) - floatval($order_info['rcb_amount']) - floatval($order_info['pd_amount']));

		$order_info['api_pay_amount'] = $pay_amount;

		return ds_callback(true, '', $order_info);
	}
	
    /**
     * 取得充值单所需支付金额等信息
     * @param int $pdr_sn
     * @param int $member_id
     * @return array
     */
    public function getPdOrderInfo($pdr_sn, $member_id = null)
    {

        $predeposit_model = model('predeposit');
        $condition = array();
        $condition[] = array('pdr_sn','=',$pdr_sn);
        if (!empty($member_id)) {
            $condition[] = array('pdr_member_id','=',$member_id);
        }

        $order_info = $predeposit_model->getPdRechargeInfo($condition);
        if (empty($order_info)) {
            return ds_callback(false, '该订单不存在');
        }

        $order_info['subject'] = '预存款充值_' . $order_info['pdr_sn'];
        $order_info['order_type'] = 'pd_order';
        $order_info['pay_sn'] = $order_info['pdr_sn'];
        $order_info['api_pay_amount'] = $order_info['pdr_amount'];
        return ds_callback(true, '', $order_info);
    }

    /**
     * 取得所使用支付方式信息
     * @param unknown $payment_code
     */
    public function getPaymentInfo($payment_code)
    {
        if (in_array($payment_code, array('offline', 'predeposit')) || empty($payment_code)) {
            return ds_callback(false, '系统不支持选定的支付方式');
        }
        $payment_model = model('payment');
        $condition = array();
        $condition[] = array('payment_code','=',$payment_code);
        $payment_info = $payment_model->getPaymentOpenInfo($condition);
        if (empty($payment_info)) {
            return ds_callback(false, '系统不支持选定的支付方式');
        }
        $inc_file = PLUGINS_PATH . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . $payment_info['payment_code'] . DIRECTORY_SEPARATOR . $payment_info['payment_code'] . '.php';
        if (!file_exists($inc_file)) {
            return ds_callback(false, '系统不支持选定的支付方式');
        }
        require_once  $inc_file;
        $payment_info['payment_config'] = unserialize($payment_info['payment_config']);

        return ds_callback(true, '', $payment_info);
    }


    /**
     * 支付成功后修改虚拟订单状态
     */
    public function updateVrOrder($out_trade_no, $payment_code, $order_info, $trade_no)
    {
        $post['payment_code'] = $payment_code;
        $post['trade_no'] = $trade_no;
        return model('vrorder','logic')->changeOrderStatePay($order_info, 'system', $post);
    }
	
	/**
	 * 支付成功后修改店铺入驻状态
	 */
	public function updateSjOrder($out_trade_no, $payment_code, $order_info, $trade_no)
	{
		return model('store')->setStoreOpen($order_info,array('joinin_state'=>STORE_JOIN_STATE_FINAL,'payment_code'=>$payment_code,'trade_sn'=>$trade_no));
	}

    /**
     * 支付成功后修改充值订单状态
     * @param unknown $out_trade_no
     * @param unknown $trade_no
     * @param unknown $payment_code
     * @throws Exception
     * @return multitype:unknown
     */
    public function updatePdOrder($out_trade_no, $payment_code, $recharge_info, $trade_no)
    {

        $condition = array();
        $condition[] = array('pdr_sn','=',$recharge_info['pdr_sn']);
        $condition[] = array('pdr_payment_state','=',0);
        $update = array();
        $update['pdr_payment_state'] = 1;
        $update['pdr_paymenttime'] = TIMESTAMP;
        $update['pdr_payment_code'] = $payment_code;
        $update['pdr_trade_sn'] = $trade_no;

        $predeposit_model = model('predeposit');
        try {
            Db::startTrans();
            $pdnum = $predeposit_model->getPdRechargeCount(array(
                                                       'pdr_sn' => $recharge_info['pdr_sn'], 'pdr_payment_state' => 1
                                                   ));
            if (intval($pdnum) > 0) {
                throw new \think\Exception('订单已经处理', 10006);
            }
            //更改充值状态
            $state = $predeposit_model->editPdRecharge($update, $condition);
            if (!$state) {
                throw new \think\Exception('更新充值状态失败', 10006);
            }
            //变更会员预存款
            $data = array();
            $data['member_id'] = $recharge_info['pdr_member_id'];
            $data['member_name'] = $recharge_info['pdr_member_name'];
            $data['amount'] = $recharge_info['pdr_amount'];
            $data['pdr_sn'] = $recharge_info['pdr_sn'];
            $predeposit_model->changePd('recharge', $data);
            Db::commit();
            return ds_callback(true);

        } catch (\Exception $e) {
            Db::rollback();
            return ds_callback(false, $e->getMessage());
        }
    }
    
    
    /**
     * 
     * @param type $out_trade_no  #商城内部订单号
     * @param type $trade_no  #支付交易流水号
     * @param type $order_type  #订单ID
     * @param type $payment_code  #支付方式代号
     */
    public function updateOrder($out_trade_no,$trade_no,$order_type,$payment_code){
        $out_trade_no = current(explode('_', $out_trade_no));
        if($order_type == 'vr_order') {
            $order = $this->getVrOrderInfo($out_trade_no);
            if ($order['data']['order_state'] != ORDER_STATE_NEW) {
                //订单已支付
                return true;
            }
            $result = $this->updateVrOrder($out_trade_no, $payment_code, $order['data'], $trade_no);
		}elseif($order_type == 'sj_order') {
			$order = $this->getSjOrderInfo($out_trade_no);
			if ($order['data']['joinin_state'] != STORE_JOIN_STATE_VERIFY_SUCCESS) {
				//订单已支付
				return true;
			}
			$result = $this->updateSjOrder($out_trade_no, $payment_code, $order['data'], $trade_no);
        }elseif($order_type == 'pd_order') {
            $order = $this->getPdOrderInfo($out_trade_no);
            if ($order['data']['pdr_payment_state'] == 1) {
                //订单已支付
                return true;
            }
            $result = $this->updatePdOrder($out_trade_no, $payment_code, $order['data'], $trade_no);
        }
        return $result['code'] ? TRUE : FALSE;
    }
    
    
}