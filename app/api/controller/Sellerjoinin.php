<?php

namespace app\api\controller;

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
class Sellerjoinin extends MobileMember {

    private $joinin_detail = NULL;
    
    public function initialize() {

        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellerjoinin.lang.php');
        
        $storejoinin_model = model('storejoinin');
        $this->joinin_detail = $storejoinin_model->getOneStorejoinin(array('member_id' => $this->member_info['member_id']));
        
        $store_joinin_open=config('ds_config.store_joinin_open');

        if(!$this->joinin_detail || !$this->joinin_detail['joinin_state']){//已经填写过入驻资料的则不跳转
            if($store_joinin_open==0){
                ds_json_encode(10001,lang('store_joinin_close'));
            }
        }
    }

    /**
     * @api {POST} api/Sellerjoinin/get_info 获取入驻信息
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.store_joinin  入驻信息（返回数据参考storejoinin表）
     */
    public function get_info() {
        $storejoinin_model = model('storejoinin');
        $joinin_detail=$this->joinin_detail;
        if ($joinin_detail) {
            $store_class_names = unserialize($joinin_detail['store_class_names']);
            $store_class_ids = unserialize($joinin_detail['store_class_ids']);
            $joinin_detail['store_class_names'] = $store_class_names ? $store_class_names : array();
            $joinin_detail['store_class_ids'] = $store_class_ids ? $store_class_ids : array();
            $joinin_detail['store_class_commis_rates'] = explode(',', $joinin_detail['store_class_commis_rates']);
            if ($joinin_detail['business_licence_number_electronic']) {
                $joinin_detail['business_licence_number_electronic_url'] = get_store_joinin_imageurl($joinin_detail['business_licence_number_electronic']);
            }
            if (!empty($joinin_detail['sg_info'])) {
                $store_grade_info = model('storegrade')->getOneStoregrade($joinin_detail['storegrade_id']);
                $joinin_detail['storegrade_price'] = $store_grade_info['storegrade_price'];
            } else {
                $joinin_detail['sg_info'] = @unserialize($joinin_detail['sg_info']);
                if (is_array($joinin_detail['sg_info'])) {
                    $joinin_detail['storegrade_price'] = $joinin_detail['sg_info']['storegrade_price'];
                }
            }
			 if($joinin_detail['joinin_state'] == STORE_JOIN_STATE_VERIFY_SUCCESS){
				$joinin_detail['member_available_pd'] = $this->member_info['available_predeposit'];
				$joinin_detail['member_available_rcb'] = $this->member_info['available_rc_balance'];

				$joinin_detail['member_paypwd'] = true;
				if (empty($this->member_info['member_paypwd'])) {
					$joinin_detail['member_paypwd'] = false;
				}
				$joinin_detail['pay_amount'] = round($joinin_detail['paying_amount'] - $joinin_detail['pd_amount'] - $joinin_detail['rcb_amount'],2);
			}
			
        }
        ds_json_encode(10000, '', array('store_joinin' => $joinin_detail));
    }

    /**
     * @api {POST} api/Sellerjoinin/step2 商家入驻第二步
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} store_type 入驻类型（0企业1个人）
     * @apiParam {String} company_name 公司名
     * @apiParam {String} store_longitude 店铺地址纬度
     * @apiParam {String} store_latitude 店铺地址经度
     * @apiParam {Int} company_province_id 地区id
     * @apiParam {String} company_address 公司地区名
     * @apiParam {String} company_address_detail 公司地址
     * @apiParam {String} company_registered_capital 注册资金
     * @apiParam {String} contacts_name 联系人姓名
     * @apiParam {String} contacts_phone 联系人电话
     * @apiParam {String} contacts_email 联系人邮箱
     * @apiParam {String} business_licence_number 营业执照号
     * @apiParam {String} business_licence_address 营业执照地区
     * @apiParam {String} business_licence_start 营业执照有效期起始
     * @apiParam {String} business_licence_end 营业执照有效期结束
     * @apiParam {String} business_sphere 营业范围
     * @apiParam {String} business_licence_number_electronic 营业执照图片
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function step2() {
        $param = array();
        $param['member_name'] = $this->member_info['member_name'];
        $param['store_type'] = input('post.store_type');
        $param['company_name'] = input('post.company_name');
        $param['store_longitude'] = input('post.store_longitude');
        $param['store_latitude'] = input('post.store_latitude');
        $param['company_province_id'] = intval(input('post.company_province_id'));
        $param['company_address'] = input('post.company_address');
        $param['company_address_detail'] = input('post.company_address_detail');
        $param['company_registered_capital'] = intval(input('post.company_registered_capital'));
        $param['contacts_name'] = input('post.contacts_name');
        $param['contacts_phone'] = input('post.contacts_phone');
        $param['contacts_email'] = input('post.contacts_email');
        $param['business_licence_number'] = input('post.business_licence_number');
        $param['business_licence_address'] = input('post.business_licence_address');
        $param['business_licence_start'] = input('post.business_licence_start');
        $param['business_licence_end'] = input('post.business_licence_end');
        $param['business_sphere'] = input('post.business_sphere');
        $param['business_licence_number_electronic'] = input('post.business_licence_number_electronic');


        $this->step2_save_valid($param);

        $storejoinin_model = model('storejoinin');
        if (empty($this->joinin_detail)) {
            $param['member_id'] = $this->member_info['member_id'];
            $storejoinin_model->addStorejoinin($param);
        } else {
            $storejoinin_model->editStorejoinin($param, array('member_id' => $this->member_info['member_id']));
        }
        ds_json_encode(10000);
    }

    private function step2_save_valid($param) {
        $sellerjoinin_validate = ds_validate('sellerjoinin');
        if (!$sellerjoinin_validate->scene(input('post.store_type') ? 'step2_save_valid2' : 'step2_save_valid')->check($param)) {
            ds_json_encode(10001, $sellerjoinin_validate->getError());
        }
    }

    /**
     * @api {POST} api/Sellerjoinin/step3 商家入驻第三步
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} store_type 入驻类型（0企业1个人）
     * @apiParam {String} settlement_bank_account_name 结算银行账户名
     * @apiParam {Int} settlement_bank_account_number 结算银行账号
     * @apiParam {String} bank_account_name 银行账户名
     * @apiParam {String} bank_account_number 银行账号
     * @apiParam {String} bank_name 银行名
     * @apiParam {String} bank_address 银行地区名
     * @apiParam {String} is_settlement_account 是否结算银行同号（0否1是）
     * @apiParam {String} settlement_bank_name 结算银行名
     * @apiParam {String} settlement_bank_address 结算银行地区名
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function step3() {
        $param = array();
        if (input('post.store_type')) {
            $param['settlement_bank_account_name'] = input('post.settlement_bank_account_name');
            $param['settlement_bank_account_number'] = input('post.settlement_bank_account_number');
        } else {
            $param['bank_account_name'] = input('post.bank_account_name');
            $param['bank_account_number'] = input('post.bank_account_number');
            $param['bank_name'] = input('post.bank_name');
            $param['bank_address'] = input('post.bank_address');
            $is_settlement_account = input('post.is_settlement_account');
            if (!empty($is_settlement_account)) {
                $param['is_settlement_account'] = 1;
                $param['settlement_bank_account_name'] = input('post.bank_account_name');
                $param['settlement_bank_account_number'] = input('post.bank_account_number');
                $param['settlement_bank_name'] = input('post.bank_name');
                $param['settlement_bank_address'] = input('post.bank_address');
            } else {
                $param['is_settlement_account'] = 2;
                $param['settlement_bank_account_name'] = input('post.settlement_bank_account_name');
                $param['settlement_bank_account_number'] = input('post.settlement_bank_account_number');
                $param['settlement_bank_name'] = input('post.settlement_bank_name');
                $param['settlement_bank_address'] = input('post.settlement_bank_address');
            }
        }

        $this->step3_save_valid($param);

        $storejoinin_model = model('storejoinin');
        $storejoinin_model->editStorejoinin($param, array('member_id' => $this->member_info['member_id']));
        ds_json_encode(10000);
    }

    private function step3_save_valid($param) {
        $sellerjoinin_validate = ds_validate('sellerjoinin');
        if (!$sellerjoinin_validate->scene(input('post.store_type') ? 'step3_save_valid3' : 'step3_save_valid')->check($param)) {
            ds_json_encode(10001, $sellerjoinin_validate->getError());
        }
    }

    /**
     * @api {POST} api/Sellerjoinin/step4 商家入驻第四步
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} seller_name 账号名
     * @apiParam {Int[]} store_class_ids 经营分类id
     * @apiParam {String[]} store_class_names 经营分类名
     * @apiParam {String} store_name 店铺名
     * @apiParam {String} store_type 店铺类型
     * @apiParam {Int} joinin_year 入驻年限
     * @apiParam {String} storegrade_id 店铺等级
     * @apiParam {Int} storeclass_id 店铺分类id
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function step4() {
        $store_class_ids = array();
        $store_class_names = array();
        $store_class_ids_array = input('post.store_class_ids/a'); #获取数组
        if (!empty($store_class_ids_array)) {
            foreach ($store_class_ids_array as $value) {
                $store_class_ids[] = $value;
            }
        }

        $store_class_names_array = input('post.store_class_names/a'); #获取数组
        if (!empty($store_class_names_array)) {
            foreach ($store_class_names_array as $value) {
                if (empty($value)) {
                    continue;
                } else {
                    $store_class_names[] = $value;
                }
            }
        }
        //取最小级分类最新分佣比例
        $sc_ids = array();
        foreach ($store_class_ids as $v) {
            $v = explode(',', trim($v, ','));
            if (!empty($v) && is_array($v)) {
                $sc_ids[] = end($v);
            }
        }
        $store_class_commis_rates = array();
        if (!empty($sc_ids)) {
            $goods_class_list = model('goodsclass')->getGoodsclassListByIds($sc_ids);
            if (!empty($goods_class_list) && is_array($goods_class_list)) {
                $sc_ids = array();
                foreach ($goods_class_list as $v) {
                    $store_class_commis_rates[] = $v['commis_rate'];
                }
            }
        }
        $param = array();
        $param['store_type'] = input('post.store_type');
        $param['seller_name'] = $this->member_info['member_name'];
        $param['store_name'] = trim(input('post.store_name'));
        $param['store_class_ids'] = serialize($store_class_ids);
        $param['store_class_names'] = serialize($store_class_names);
        $param['joinin_year'] = intval(input('post.joinin_year'));
        $param['joinin_state'] = STORE_JOIN_STATE_NEW;
        $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);

        //取店铺等级信息
        $grade_list = rkcache('storegrade', true);
        $storegrade_id = intval(input('post.storegrade_id'));
        if ($storegrade_id <= 0) {
            ds_json_encode(10001, lang('select_store_level'));
        }
        if (!empty($grade_list[$storegrade_id])) {
            $param['storegrade_id'] = $storegrade_id;
            $param['storegrade_name'] = $grade_list[$storegrade_id]['storegrade_name'];
            $param['sg_info'] = serialize(array('storegrade_price' => $grade_list[$storegrade_id]['storegrade_price']));
        }

        //取最新店铺分类信息
        $store_class_info = model('storeclass')->getStoreclassInfo(array('storeclass_id' => intval(input('post.storeclass_id'))));
        if ($store_class_info) {
            $param['storeclass_id'] = $store_class_info['storeclass_id'];
            $param['storeclass_name'] = $store_class_info['storeclass_name'];
            $param['storeclass_bail'] = $store_class_info['storeclass_bail'];
        }

        //店铺应付款
        $param['paying_amount'] = floatval($grade_list[$storegrade_id]['storegrade_price']) * $param['joinin_year'] + floatval($param['storeclass_bail']);
        $this->step4_save_valid($param);

        $storejoinin_model = model('storejoinin');
        $storejoinin_model->editStorejoinin($param, array('member_id' => $this->member_info['member_id']));

        ds_json_encode(10000);
    }

    private function step4_save_valid($param) {
        $sellerjoinin_validate = ds_validate('sellerjoinin');
        if (!$sellerjoinin_validate->scene(input('post.store_type') ? 'step4_save_valid4' : 'step4_save_valid')->check($param)) {
            ds_json_encode(10001, $sellerjoinin_validate->getError());
        }
    }

    /**
     * @api {POST} api/Sellerjoinin/pay_save 保存付款凭证
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} paying_money_certificate 付款截图
     * @apiParam {String} paying_money_certificate_explain 付款备注
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function pay_save() {
        $param = array();
        $param['paying_money_certificate'] = input('post.paying_money_certificate');
        $param['paying_money_certificate_explain'] = input('post.paying_money_certificate_explain');
        $param['joinin_state'] = STORE_JOIN_STATE_PAY;
        if (empty($param['paying_money_certificate'])) {
            ds_json_encode(10001, lang('paying_money_certificate_empty'));
        }
        $storejoinin_model = model('storejoinin');
        $storejoinin_model->editStorejoinin($param, array('member_id' => $this->member_info['member_id']));
        ds_json_encode(10000);
    }

    /**
     * @api {POST} api/Sellerjoinin/upload_image 上传图片
     * @apiVersion 1.0.0
     * @apiGroup Sellerjoinin
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {File} file 图片
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * 
     */
    public function upload_image() {
        $file = 'file';
        //上传文件保存路径
        $pic_name = '';
        if (!empty($_FILES[$file]['name'])) {
            //设置特殊图片名称
            $file_name = $this->member_info['member_id'] . '_' . date('YmdHis') . rand(10000, 99999) . '.png';
            $res = ds_upload_pic('home' . DIRECTORY_SEPARATOR . 'store_joinin', $file, $file_name);
            if ($res['code']) {
                $pic_name = $res['data']['file_name'];
            } else {
                ds_json_encode(10001, $res['msg']);
            }
        }
        ds_json_encode(10000, '', array('path' => $pic_name, 'url' => get_store_joinin_imageurl($pic_name)));
    }

}

?>
