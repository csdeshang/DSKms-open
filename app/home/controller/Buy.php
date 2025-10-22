<?php

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
class  Buy extends BaseMember {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/'.config('lang.default_lang').'/buy.lang.php');
        //验证该会员是否禁止购买
        if(!session('is_buy')){
            $this->error(lang('cart_buy_noallow'));
        }
        if(config('ds_config.member_auth') && $this->member_info['member_auth_state']!=3){
            $this->error(lang('cart_buy_noauth'),url('MemberAuth/index'));
        }
    }

    public function buy_step1() {
        if(empty(input('post.'))){
            $this->error(lang('param_error'));
        }
        //虚拟商品购买分流
        $this->_buy_branch(input('post.'));
    }

    /**
     * 得到所购买的id和数量
     *
     */
    private function _parseItems($cart_id) {
        //存放所购商品ID和数量组成的键值对
        $buy_items = array();
        if (is_array($cart_id)) {
            foreach ($cart_id as $value) {
                if (preg_match_all('/^(\d{1,10})\|(\d{1,6})$/', $value, $match)) {
                    $buy_items[$match[1][0]] = $match[2][0];
                }
            }
        }
        return $buy_items;
    }
    /**
     * 购买分流
     */
    private function _buy_branch($post) {
        if (!isset($post['ifcart'])) {
            //取得购买商品信息
            $buy_items = $this->_parseItems($post['cart_id']);
            $goods_id = key($buy_items);
            $quantity = current($buy_items);

            $goods_info = model('goods')->getGoodsOnlineInfoAndPromotionById($goods_id);
                $this->redirect('Buyvirtual/buy_step1',['goods_id'=>$goods_id,'quantity'=>$quantity]);
        }
    }
    
    /**
	 * 机构入驻支付页面
	 */
	public function sj_pay(){
		$storejoinin_model = model('storejoinin');
		$joinin_detail = $storejoinin_model->getOneStorejoinin(array('member_id' => session('member_id')));
		if(!$joinin_detail){
			$this->error('机构入驻不存在');
		}
		if($joinin_detail['joinin_state']!=STORE_JOIN_STATE_VERIFY_SUCCESS){
			$this->error('机构入驻状态不是待支付状态');
		}
		
		$pay_amount_online=round($joinin_detail['paying_amount']-$joinin_detail['rcb_amount']-$joinin_detail['pd_amount'],2);
		View::assign('pay_amount_online', $pay_amount_online);
		View::assign('store_name', $joinin_detail['store_name']);
		//显示支付接口列表
		if ($pay_amount_online > 0) {
		//显示支付接口列表
		$payment_model = model('payment');
		$condition = array();
		$condition[] = array('payment_code','not in',array('predeposit'));
		$condition[] = array('payment_state','=',1);
		$condition[] = array('payment_platform','=','pc');
		$payment_list = $payment_model->getPaymentList($condition);
		if (empty($payment_list)) {
			$this->error(lang('appropriate_payment_method'));
		}
		View::assign('payment_list', $payment_list);
		}
		//显示预存款、支付密码、充值卡
		$member_model=model('member');
		$buyer_info=$member_model->getMemberInfoByID($this->member_info['member_id']);
		View::assign('available_pd_amount', ($buyer_info['available_predeposit']>0)?$buyer_info['available_predeposit']:'');
		View::assign('member_paypwd', $buyer_info['member_paypwd']);
		View::assign('available_rcb_amount', ($buyer_info['available_rc_balance']>0)?$buyer_info['available_rc_balance']:'');
		//标识 购买流程执行第几步
		View::assign('buy_step', 'step3');
		return View::fetch($this->template_dir.'storejoinin_pay');
		
	}
	
    /**
     * 预存款充值下单时支付页面
     */
    public function pd_pay() {
        $pay_sn = input('param.pay_sn');
        if (!preg_match('/^\d{20}$/', $pay_sn)) {
            $this->error(lang('param_error'), url('Predeposit/index'));
        }

        //查询支付单信息
        $predeposit_model = model('predeposit');
        $pd_info = $predeposit_model->getPdRechargeInfo(array('pdr_sn' => $pay_sn, 'pdr_member_id' => session('member_id')));
        if (empty($pd_info)) {
            $this->error(lang('param_error'));
        }
        if (intval($pd_info['pdr_payment_state'])) {
            $this->error(lang('not_repeat_payment'), url('Predeposit/index'));
        }
        View::assign('pdr_info', $pd_info);

        //显示支付接口列表
        $payment_model = model('payment');
        $condition = array();
        $condition[] = array('payment_code','not in',array('predeposit'));
        $condition[] = array('payment_state','=',1);
        $condition[] = array('payment_platform','=','pc');
        $payment_list = $payment_model->getPaymentList($condition);
        if (empty($payment_list)) {
            $this->error(lang('appropriate_payment_method'), url('Predeposit/index'));
        }
        View::assign('payment_list', $payment_list);

        //标识 购买流程执行第几步
        View::assign('buy_step', 'step3');
        return View::fetch($this->template_dir.'predeposit_pay');
    }
    
    /**
     * AJAX验证支付密码
     */
    public function check_pd_pwd() {
        $password = input('param.password');
        if (empty($password))
            exit('0');
        $buyer_info = model('member')->getMemberInfoByID(session('member_id'));
        echo ($buyer_info['member_paypwd'] != '' && $buyer_info['member_paypwd'] === md5($password)) ? '1' : '0';
    }
    /**
     * F码验证
     */
    public function check_fcode() {
        $result = model('buy','logic')->checkFcode(input('goods_commonid'), input('fcode'));
        echo $result['code'] ? '1' : '0';
        exit;
    }

}