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
 * 卖家子账号控制器
 */
class  Selleraccount extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/selleraccount.lang.php');
    }


    /**
     * @api {POST} api/Selleraccount/account_list 获取子账户列表
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.seller_list  子账号列表 （返回字段参考seller表）
     */
    public function account_list() {
        $seller_model = model('seller');
        $condition = array(
            'seller.store_id' => $this->store_info['store_id'],
            'seller.is_admin' => 0,
        );
        $seller_list = $seller_model->getSellerList($condition);
		foreach ($seller_list as $key => $val){
			if($seller_list[$key]['last_logintime']>0){
				$seller_list[$key]['last_logintime'] = date('Y-m-d H:i:s',$val['last_logintime']);
			}
		}
        $result = array(
            'seller_list' => $seller_list
        );
        ds_json_encode(10000, '操作成功！', $result);
    }


    /**
     * @api {POST} api/Selleraccount/group_list 获取店铺账户组
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.sellergroup_list  账号组列表 （返回字段参考sellergroup表）
     */
    public function group_list() {
        $sellergroup_model = model('sellergroup');
        $seller_group_list = $sellergroup_model->getSellergroupList(array('store_id' => $this->store_info['store_id']));
        if (empty($seller_group_list)) {
            ds_json_encode(10001, lang('please_set_account_group_first'));
        }
        $result = array(
            'sellergroup_list' => $seller_group_list,
        );
        ds_json_encode(10000, '操作成功！', $result);
    }
    /**
     * @api {POST} api/Selleraccount/account_add 新增机构子账户
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {String} member_name 用户名
     * @apiParam {String} password 密码
     * @apiParam {String} seller_name 机构账号名
     * @apiParam {Int} group_id 账户组ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function account_add() {
        $member_name = input('post.member_name');
        $password = input('post.password');
        $member_info = $this->_check_seller_member($member_name, $password);
        if (!$member_info) {
            ds_json_encode(10001, lang('user_authentication_failed'));
        }

        $seller_name = $member_name;

        $group_id = intval(input('post.group_id'));



        $seller_info = array(
            'seller_name' => $seller_name,
            'member_id' => $member_info['member_id'],
            'sellergroup_id' => $group_id,
            'store_id' => $this->store_info['store_id'],
            'is_admin' => 0
        );
        $seller_model = model('seller');
        $result = $seller_model->addSeller($seller_info);

        if ($result) {
            $this->recordSellerlog(lang('add_account_successfully') . $result);
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            $this->recordSellerlog(lang('failed_add_account'));
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
    }
    /**
     * @api {POST} api/Selleraccount/account_info 获取店铺单个子账户信息
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} seller_id 子账户ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} seller_info  卖家信息 （返回字段参考seller表）
     * @apiSuccess {Object} seller_info.sellergroup_name 账号组名称
     */
    public function account_info() {
        $seller_id = intval(input('param.seller_id'));
        if ($seller_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $seller_model = model('seller');
        $seller_info = $seller_model->getSellerInfo(array('seller_id' => $seller_id));
        if (empty($seller_info) || intval($seller_info['store_id']) !== intval($this->store_info['store_id'])) {
            ds_json_encode(10001, lang('account_not_exist'));
        }
        //获取当前用户选择的账号组
        $sellergroup_model = model('sellergroup');
        $seller_group = $sellergroup_model->getSellergroupInfo(array('sellergroup_id' => $seller_info['sellergroup_id']));
        $seller_info['sellergroup_name'] = $seller_group['sellergroup_name'];
        $result = array(
            'seller_info' => $seller_info
        );
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/Selleraccount/account_edit 编辑机构子账户
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} seller_id 子账户ID
     * @apiParam {Int} group_id 账户组ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function account_edit() {
        $param = array('sellergroup_id' => intval(input('post.group_id')));
        $condition = array(
            'seller_id' => intval(input('post.seller_id')),
            'store_id' => $this->store_info['store_id']
        );
        $seller_model = model('seller');
        $result = $seller_model->editSeller($param, $condition);
        if ($result) {
            $this->recordSellerlog(lang('edit_account_successfully') . input('post.seller_id'));
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            $this->recordSellerlog(lang('edit_account_failed') . input('post.seller_id'), 0);
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
    }

    /**
     * @api {POST} api/Selleraccount/account_del 删除机构子账户
     * @apiVersion 1.0.0
     * @apiGroup Selleraccount
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} seller_id 子账户ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function account_del() {
        $seller_id = intval(input('post.seller_id'));
        if ($seller_id > 0) {
            $condition = array();
            $condition[] = array('seller_id','=',$seller_id);
            $condition[] = array('store_id','=',$this->store_info['store_id']);
            $seller_model = model('seller');
            $result = $seller_model->delSeller($condition);
            if ($result) {
                $this->recordSellerlog(lang('delete_account_successfully') . $seller_id);
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                $this->recordSellerlog(lang('deletion_account_failed') . $seller_id);
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
        } else {
            ds_json_encode(10001, lang('param_error'));
        }
    }

    public function check_seller_name_exist() {
        $seller_name = input('param.seller_name');
        $result = $this->_is_seller_name_exist($seller_name);
        if ($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    private function _is_seller_name_exist($seller_name) {
        $condition = array();
        $condition[] = array('seller_name','=',$seller_name);
        $seller_model = model('seller');
        return $seller_model->isSellerExist($condition);
    }

    public function check_seller_member() {
        $member_name = input('param.member_name');
        $password = input('param.password');
        $result = $this->_check_seller_member($member_name, $password);
        if ($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    private function _check_seller_member($member_name, $password) {
        $member_info = $this->_check_member_password($member_name, $password);
        if ($member_info && !$this->_is_seller_member_exist($member_info['member_id'])) {
            return $member_info;
        } else {
            return false;
        }
    }

    private function _check_member_password($member_name, $password) {
        $condition = array();
        $condition[] = array('member_name','=',$member_name);
        $condition[] = array('member_password','=',md5($password));
        $member_model = model('member');
        $member_info = $member_model->getMemberInfo($condition);
        return $member_info;
    }

    private function _is_seller_member_exist($member_id) {
        $condition = array();
        $condition[] = array('member_id','=',$member_id);
        $seller_model = model('seller');
        return $seller_model->isSellerExist($condition);
    }


}
