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
 * 卖家账户组控制器
 */
class  Selleraccountgroup extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/selleraccount.lang.php');
    }
    /**
     * @api {POST} api/Selleraccountgroup/group_list 账户组列表
     * @apiVersion 1.0.0
     * @apiGroup Selleraccountgroup
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.seller_group_list  卖家账户组列表 （返回字段参考sellergroup表）
     */
    public function group_list() {
        $sellergroup_model = model('sellergroup');
        $seller_group_list = $sellergroup_model->getSellergroupList(array('store_id' => $this->store_info['store_id']));
        ds_json_encode(10000, '', array('seller_group_list' => $seller_group_list));
    }

    /**
     * @api {POST} api/Selleraccountgroup/get_seller_menu 
     * @apiVersion 1.0.0
     * @apiGroup Selleraccountgroup
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.smt_list  店铺消息模板列表
     * @apiSuccess {String} result.smt_list.storemt_code  店铺消息模板代码
     * @apiSuccess {String} result.smt_list.storemt_name  店铺消息模板名称
     * @apiSuccess {Object[]} result.seller_menu  卖家功能菜单列表 （返回字段参考\app\home\controller\BaseSeller::getSellerMenuList）
     */
    public function get_seller_menu() {
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/baseseller.lang.php');
        // 机构消息模板列表
        $smt_list = model('storemsgtpl')->getStoremsgtplList(array(), 'storemt_code,storemt_name');
        $seller_menu = \app\home\controller\BaseSeller::getSellerMenuList($this->store_info['store_id']);
        ds_json_encode(10000, '', array('smt_list' => $smt_list, 'seller_menu' => $seller_menu));
    }

    /**
     * @api {POST} api/Selleraccountgroup/group_edit 获取账户组数据
     * @apiVersion 1.0.0
     * @apiGroup Selleraccountgroup
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} group_id 账户组ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.group_name  帐号组名称
     * @apiSuccess {String[]} result.group_limits  卖家按钮列表
     * @apiSuccess {String[]} result.smt_limits  店铺消息模板列表
     */
    public function group_edit() {
        $group_id = intval(input('param.group_id'));
        if ($group_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $sellergroup_model = model('sellergroup');
        $seller_group_info = $sellergroup_model->getSellergroupInfo(array('sellergroup_id' => $group_id, 'store_id' => $this->store_info['store_id']));
        if (empty($seller_group_info)) {
            ds_json_encode(10001, lang('there_no_group'));
        }
        

        ds_json_encode(10000, '', array('group_name' => $seller_group_info['sellergroup_name'], 'group_limits' => explode(',', $seller_group_info['sellergroup_limits']), 'smt_limits' => explode(',', $seller_group_info['smt_limits'])));
    }

    /**
     * @api {POST} api/Selleraccountgroup/group_save 保存账户组数据
     * @apiVersion 1.0.0
     * @apiGroup Selleraccountgroup
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} group_id 账户组ID 0新增
     * @apiParam {String} seller_group_name 账户组名称
     * @apiParam {Array} limits 操作权限
     * @apiParam {Array} smt_limits 消息接收权限
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function group_save() {
        $seller_info = array();
        $group_id = intval(input('param.group_id'));

        $seller_info['sellergroup_name'] = input('post.seller_group_name');
        $seller_info['sellergroup_limits'] = implode(',', input('post.limits/a'));
        $seller_info['smt_limits'] = empty(input('post.smt_limits/a')) ? '' : implode(',', input('post.smt_limits/a'));
        $seller_info['store_id'] = $this->store_info['store_id'];


        $sellergroup_model = model('sellergroup');

        if (empty($group_id)) {
            $result = $sellergroup_model->addSellergroup($seller_info);
            $this->recordSellerlog(lang('add_group_successfully') . $result);
            if ($result) {
                ds_json_encode(10000, lang('add_success'));
            } else {
                ds_json_encode(10001, lang('add_failure'));
            }
        } else {
            $condition = array();
            $condition[] = array('sellergroup_id','=',$group_id);
            $condition[] = array('store_id','=',$this->store_info['store_id']);
            $result = $sellergroup_model->editSellergroup($seller_info, $condition);
            $this->recordSellerlog(lang('editorial_team_succeeds') . $group_id);
            if ($result) {
                ds_json_encode(10000, lang('edit_success'));
            } else {
                ds_json_encode(10001, lang('edit_failure'));
            }
        }
    }

    /**
     * @api {POST} api/Selleraccountgroup/group_del 删除账户组
     * @apiVersion 1.0.0
     * @apiGroup Selleraccountgroup
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} group_id 账户组ID 0新增
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function group_del() {
        $group_id = intval(input('param.group_id'));
        if ($group_id > 0) {
            //判断当前用户组下是否有用户
            $condition = array(
                'seller.store_id' => $this->store_info['store_id'],
                'seller.sellergroup_id' => $group_id
            );
            $seller_list = model('seller')->getSellerList($condition);
            if (!empty($seller_list)) {
                ds_json_encode(10001, '此帐号组下有设置的会员,请先设置会员为其他账户组再删除');
            }

            $condition = array();
            $condition[] = array('sellergroup_id','=',$group_id);
            $condition[] = array('store_id','=',$this->store_info['store_id']);
            $sellergroup_model = model('sellergroup');
            $result = $sellergroup_model->delSellergroup($condition);
            if ($result) {
                $this->recordSellerlog(lang('group_deleted_successfully') . $group_id);
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                $this->recordSellerlog(lang('deletion_group_failed') . $group_id);
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
        } else {
            ds_json_encode(10001, lang('param_error'));
        }
    }

}
