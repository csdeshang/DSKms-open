<?php

/**
 * 卖家分销管理
 */

namespace app\api\controller;
use think\facade\View;
use think\facade\Db;
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
 * 卖家分销控制器
 */
class  Sellerinviter extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellerinviter.lang.php');
        if (intval(config('ds_config.inviter_open')) !== 1) {
            ds_json_encode(10001, lang('promotion_unavailable'));
        }
    }
    /**
     * @api {POST} api/Sellerinviter/order_list 分销业绩
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} orderinviter_order_sn 订单号
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.order_list  分销订单列表 （返回字段参考orderinviter表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function order_list() {
        $condition = array(array('orderinviter_store_id' ,'=', $this->store_info['store_id']));
        if (input('param.orderinviter_order_sn')) {
            $condition[] = array('orderinviter_order_sn','like', '%' . input('param.orderinviter_order_sn') . '%');
        }
        $orderinviter_list = Db::name('orderinviter')->where($condition)->order('orderinviter_id desc')->paginate(10);
        
        $order_list=$orderinviter_list->items();
        foreach($order_list as $key => $val){
            $order_list[$key]['orderinviter_valid_text']=lang('orderinviter_valid_array')[$val['orderinviter_valid']];
        }
        $result = array_merge(array('order_list' => $order_list), mobile_page($orderinviter_list));
        ds_json_encode(10000, '操作成功！', $result);
    }
    
    /**
     * @api {POST} api/Sellerinviter/goods_list 分销商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} goods_name 商品名
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_list  分销商品列表 （返回字段参考goods表）
     * @apiSuccess {Object} result.storage_array  库存列表，键为商品公共ID
     * @apiSuccess {Int} result.storage_array.sum  库存
     * @apiSuccess {Int} result.storage_array.goods_id  商品ID
     */
    public function goods_list() {
        $goods_model = model('goods');


        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $condition[] = array('inviter_open','=',1);
        if ((input('param.goods_name'))) {
            $condition[] = array('goods_name','like', '%' . input('param.goods_name') . '%');
        }

        $goods_list = $goods_model->getGoodsList($condition, '*',$this->pagesize);
		foreach($goods_list as $key => $val){
			$goods_list[$key]['goods_image'] = goods_cthumb($val['goods_image']);
		}

        $result=array('goods_list'=>$goods_list);
        $result = array_merge($result, mobile_page(is_object($goods_model->page_info) ? $goods_model->page_info : ''));
        ds_json_encode(10000,'',$result);
    }

    /**
     * @api {POST} api/Sellerinviter/goods_add 添加分销活动
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} inviter_goods_id 商品公共ID
     * @apiParam {Float} inviter_ratio_1 一级分销佣金比例
     * @apiParam {Float} inviter_ratio_2 二级分销佣金比例
     * @apiParam {Float} inviter_ratio_3 三级分销佣金比例
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_add() {
        $goods_model = model('goods');
        //验证输入
        $inviter_goods_id = intval(input('post.inviter_goods_id'));
        $inviter_ratio = floatval(input('post.inviter_ratio'));

        if (!($inviter_goods_id)) {
            ds_json_encode(10001, lang('inviter_goods_id_required'));
        }
        $goods_info = $goods_model->getGoodsInfo('goods_id=' . $inviter_goods_id . ' AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        if ($inviter_ratio <= 0) {
                ds_json_encode(10001, lang('inviter_ratio_min') . 0.01 . '%');
            }
        if ($inviter_ratio > 100) {
            ds_json_encode(10001, lang('inviter_ratio_max') . 100 . '%');
        }
        $result = $goods_model->editGoodsById(array(
            'inviter_open' => 1,
            'inviter_ratio' => $inviter_ratio,
                ), array($inviter_goods_id));
        if ($result) {
            $this->recordSellerlog('添加分销商品，商品编号：' . $inviter_goods_id);
            ds_json_encode(10000, lang('goods_add_success'));
        } else {
            ds_json_encode(10001, lang('goods_add_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/goods_info 获取分销商品信息
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} goods_commonid 商品公共ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.info  商品信息 （返回字段参考goods表）
     */
    public function goods_info() {
        $goods_model = model('goods');
        $goods_id = intval(input('param.goods_id'));
        $goods_info = $goods_model->getGoodsInfo('goods_id=' . $goods_id . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001,lang('sellerinviter_goods_empty'));
        }
        ds_json_encode(10000,'',array('info'=>$goods_info));
    }

    /**
     * @api {POST} api/Sellerinviter/goods_edit 编辑分销活动
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} inviter_goods_id 商品公共ID
     * @apiParam {Float} inviter_ratio_1 一级分销佣金比例
     * @apiParam {Float} inviter_ratio_2 二级分销佣金比例
     * @apiParam {Float} inviter_ratio_3 三级分销佣金比例
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_edit() {
        $goods_model = model('goods');
        //验证输入
        $inviter_goods_id = intval(input('post.inviter_goods_id'));
        $inviter_ratio = floatval(input('post.inviter_ratio'));

        if (!($inviter_goods_id)) {
            ds_json_encode(10001, lang('inviter_goods_id_required'));
        }
        $goods_info = $goods_model->getGoodsInfo('goods_id=' . $inviter_goods_id . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        if ($inviter_ratio <= 0) {
                ds_json_encode(10001, lang('inviter_ratio_min') . 0.01 . '%');
            }
        if ($inviter_ratio > 100) {
            ds_json_encode(10001, lang('inviter_ratio_max') . 100 . '%');
        }
        $result = $goods_model->editGoodsById(array(
            'inviter_ratio' => $inviter_ratio,
                ), array($inviter_goods_id));
        if ($result) {
            $this->recordSellerlog('编辑分销商品，商品编号：' . $inviter_goods_id);
            ds_json_encode(10000, lang('goods_edit_success'));
        } else {
            ds_json_encode(10001, lang('goods_edit_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/goods_del 删除分销商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} goods_id 商品公共ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_del() {
        $goods_model = model('goods');
        $goods_id = intval(input('param.goods_id'));
        $goods_info = $goods_model->getGoodsInfo('goods_id=' . $goods_id . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        $result = $goods_model->editGoodsById(array(
            'inviter_open' => 0,
                ), array($goods_id));
        if ($result) {
            $this->recordSellerlog('删除分销商品，商品编号：' . $goods_id);
            ds_json_encode(10000, lang('goods_del_success'));
        } else {
            ds_json_encode(10001, lang('goods_del_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/search_goods 选择活动商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} goods_name 商品名称
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function search_goods() {
        $goods_model = model('goods');
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $condition[] = array('inviter_open','=',0);
        $condition[] = array('goods_name','like','%' . input('param.goods_name') . '%');
        $goods_list = $goods_model->getGoodsList($condition, 'goods_id,goods_name,goods_price,goods_image,inviter_open',$this->pagesize);
        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['goods_image'] = goods_cthumb($value['goods_image'], '270');
        }
        $result=array('goods_list'=>$goods_list);
        $result = array_merge($result, mobile_page(is_object($goods_model->page_info) ? $goods_model->page_info : ''));
        ds_json_encode(10000,'',$result);
    }

    public function inviter_goods_info() {
        $goods_id = intval(input('param.goods_id'));

        $data = array();
        $data['result'] = true;


        //获取商品具体信息用于显示
        $goods_model = model('goods');
        $condition = array();
        $condition[] = array('goods_id','=',$goods_id);
        $goods_list = $goods_model->getGoodsOnlineList($condition);

        if (empty($goods_list)) {
            $data['result'] = false;
            $data['message'] = lang('param_error');
            echo json_encode($data);
            die;
        }


        $goods_info = $goods_list[0];
        $data['goods_id'] = $goods_info['goods_id'];
        $data['goods_name'] = $goods_info['goods_name'];
        $data['goods_price'] = $goods_info['goods_price'];
        $data['goods_image'] = goods_thumb($goods_info, 270);

        echo json_encode($data);
        die;
    }

}
