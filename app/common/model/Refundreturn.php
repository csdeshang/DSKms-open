<?php

namespace app\common\model;

use think\facade\Db;

class Refundreturn extends BaseModel {
    
    
    //退款
    public function refundAmount($order, $refund_amount,$refund_type='order') {
        $order_model = model('order');
        //生成out_request_no 支付宝部分退款必传唯一的标识一次退款请求号
        $order['out_request_no'] = $order['order_sn'];

        //原路退回金额
        $trade_refund_amount = 0;
        //充值卡退回金额
        $rcb_refund_amount = 0;
        //预存款退回金额
        $pd_refund_amount = 0;


        $order_amount = $order['order_amount']; //订单金额
        $rcb_amount = $order['rcb_amount']; //充值卡支付金额
        $pd_amount = $order['pd_amount']; //预存款支付金额

        $predeposit_amount = $order_amount - $order['refund_amount'] - $rcb_amount; //可退预存款金额（预存款+在线支付金额） 在线支付可能原路返还

        $predeposit_model = model('predeposit');

        $not_trade_refund = TRUE; //在线支付 不原路返还
        $alipay_payment_list = array('alipay', 'alipay_app', 'alipay_h5');
        $wxpay_payment_list = array('wxpay_app', 'wxpay_h5', 'wxpay_jsapi', 'wxpay_minipro', 'wxpay_native');

        //未使用预存款支付 以及  充值卡支付的订单 才支持订单原路返还。
        if ($predeposit_amount > 0 && (in_array($order['payment_code'], $alipay_payment_list) || in_array($order['payment_code'], $wxpay_payment_list)) && $rcb_amount == 0 && $pd_amount == 0) {
            if (in_array($order['payment_code'], $alipay_payment_list)) {
                $payment_code = 'alipay';
            }
            if (in_array($order['payment_code'], $wxpay_payment_list)) {
                $payment_code = 'wxpay_native';
            }
            //调用支付接口处理原路退款
            $logic_payment = model('payment', 'logic');
            $result = $logic_payment->getPaymentInfo($payment_code);
            if (!$result['code']) {
                throw new \think\Exception($payment_code . '支付方法未开启', 10006);
            }
            $main_payment_info = $payment_info = $result['data'];

            if($payment_code == 'alipay' && !isset($payment_info['payment_config']['alipay_trade_refund_state'])){
                throw new \think\Exception($payment_code . '请配置支付宝支付', 10006);
            }
            if($payment_code == 'wxpay_native' && !isset($payment_info['payment_config']['wx_trade_refund_state'])){
                throw new \think\Exception($payment_code . '请配置微信支付', 10006);
            }
            //支付宝/微信 未开启原路返回
            if (($payment_code == 'alipay' && $payment_info['payment_config']['alipay_trade_refund_state'] == 1) || ($payment_code == 'wxpay_native' && $payment_info['payment_config']['wx_trade_refund_state'] == 1)) {
                $result = $logic_payment->getPaymentInfo($order['payment_code']);
                if (!$result['code']) {
                    throw new \think\Exception($order['payment_code'] . '支付方法未开启', 10006);
                }
                $payment_info = $result['data'];
                //原路返还金额
                $trade_refund_amount = $refund_amount; //退预存款金额
                if ($refund_amount > $predeposit_amount) {
                    $trade_refund_amount = $predeposit_amount;
                }
                $payment_info['payment_config']=array_merge($main_payment_info['payment_config'],$payment_info['payment_config']);
                $payment_api = new $payment_code($payment_info);
                $result = $payment_api->trade_refund($order, $trade_refund_amount);
                if (!$result['code']) {
                    throw new \think\Exception($result['msg'], 10006);
                }
                $not_trade_refund = FALSE;
            }
        }



        if (($rcb_amount > 0) && ($refund_amount > $predeposit_amount) && $not_trade_refund) {//退充值卡
            $log_array = array();
            $log_array['member_id'] = $order['buyer_id'];
            $log_array['member_name'] = $order['buyer_name'];
            $log_array['order_sn'] = $order['order_sn'];
            //充值卡退会金额
            $rcb_refund_amount = $refund_amount;
            if ($predeposit_amount > 0) {
                $rcb_refund_amount = $refund_amount - $predeposit_amount;
            }
            $log_array['amount'] = $rcb_refund_amount;
            $state = $predeposit_model->changeRcb('refund', $log_array); //增加买家可用充值卡金额
            if (!$state) {
                throw new \think\Exception('充值卡退回失败', 10006);
            }
        }


        //全部退回预存款
        if ($predeposit_amount > 0 && $not_trade_refund) {
            $log_array = array();
            $log_array['member_id'] = $order['buyer_id'];
            $log_array['member_name'] = $order['buyer_name'];
            $log_array['order_sn'] = $order['order_sn'];
            //预存款退回金额
            $pd_refund_amount = $refund_amount;
            if ($refund_amount > $predeposit_amount) {
                $pd_refund_amount = $predeposit_amount;
            }
            $log_array['amount'] = $pd_refund_amount; //退预存款金额
            $state = $predeposit_model->changePd('refund', $log_array); //增加买家可用预存款金额
            if (!$state) {
                throw new \think\Exception('预存款退回失败', 10006);
            }
        }
        
        
        //实物订单记录退款日志
        if ($refund_type == 'order') {
            $data = array();
            $data['order_id'] = $order['order_id'];
            $data['log_role'] = 'system';
            $data['log_user'] = '';
            $log_msg = '用户退款';
            if($trade_refund_amount > 0){
                $log_msg.= '，原路退回：'.$trade_refund_amount.'元';
            }
            if($rcb_refund_amount > 0){
                $log_msg.= '，充值卡退回：'.$rcb_refund_amount.'元';
            }
            if($pd_refund_amount > 0){
                $log_msg.= '，预存款退回：'.$pd_refund_amount.'元';
            }
            $data['log_msg'] = $log_msg;
            $order_model->addOrderlog($data);
        }elseif ($refund_type == 'vrorder') {
            $data = array();
            $data['order_id'] = $order['order_id'];
            $data['log_role'] = 'system';
            $data['log_user'] = '';
            $log_msg = '用户退款';
            if($trade_refund_amount > 0){
                $log_msg.= '，原路退回：'.$trade_refund_amount.'元';
            }
            if($rcb_refund_amount > 0){
                $log_msg.= '，充值卡退回：'.$rcb_refund_amount.'元';
            }
            if($pd_refund_amount > 0){
                $log_msg.= '，预存款退回：'.$pd_refund_amount.'元';
            }
            $data['log_msg'] = $log_msg;
            
            model('orderlog')->addVrOrderlog($data);
        }
        
    }
    
    
    
}