<?php

namespace app\common\model;

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
 * 数据层模型
 */
class  Vrrefund extends BaseModel {
    public $page_info;
   
    /**
     * 增加退款
     * @access public
     * @author csdeshang
     * @param type $refund_array 退款数组数据
     * @param type $order 排序
     * @return boolean
     */
    public function addVrrefund($refund_array, $order = array()) {
        if (!empty($order) && is_array($order)) {
            $refund_array['order_id'] = $order['order_id'];
            $refund_array['order_sn'] = $order['order_sn'];
            $refund_array['store_id'] = $order['store_id'];
            $refund_array['store_name'] = $order['store_name'];
            $refund_array['buyer_id'] = $order['buyer_id'];
            $refund_array['buyer_name'] = $order['buyer_name'];
            $refund_array['goods_id'] = $order['goods_id'];
            $refund_array['goods_name'] = $order['goods_name'];
            $refund_array['goods_image'] = $order['goods_image'];
            $refund_array['commis_rate'] = $order['commis_rate'];
        }
        $refund_array['refund_sn'] = $this->getVrrefundSn($refund_array['store_id']);

        Db::startTrans();
        try {
            $refund_id = Db::name('vrrefund')->insertGetId($refund_array);
            Db::commit();
            return $refund_id;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 平台退款处理
     * @access public
     * @author csdeshang
     * @param type $refund 退款
     * @return boolean
     */
    public function editVrorderRefund($refund) {

        $refund_id = $refund['refund_id'];
        $vrorder_model = model('vrorder');

        $order_id = $refund['order_id']; //订单编号
        $order = $vrorder_model->getVrorderInfo(array('order_id' => $order_id));

        //生成out_request_no 支付宝部分退款必传唯一的标识一次退款请求号
        $order['out_request_no'] = $order['order_sn'];

        //订单总金额，单次支付包含多个店铺订单的总金额（针对于微信退款原路返回需要传订单总金额）
        $order['total_order_amount'] = 0;
        $order_array = $vrorder_model->getVrOrderList(array('trade_no' => $order['trade_no']));
        foreach ($order_array as $key => $value) {
            $order['total_order_amount'] += $vrorder_model->getVrorderInfo(array('order_id' => $value['order_id']), 'order_amount')['order_amount'];
        }

        Db::startTrans();
        try {

            //对店铺资金进行扣款
            $logic_order = model('vrorder', 'logic');
            $logic_order->balanceVrOrderStateRefund($order, $refund);

            $refundreturn_model = model('refundreturn');
            $refundreturn_model->refundAmount($order, $order['order_amount'], 'vrorder');

            $order_array = array();
            $order_amount = $order['order_amount']; //订单金额
            $refund_amount = $order['refund_amount'] + $refund['refund_amount']; //退款金额
            $order_array['refund_state'] = ($order_amount - $refund_amount) > 0 ? 1 : 2;
            $order_array['refund_amount'] = ds_price_format($refund_amount);

            $state = $vrorder_model->editVrorder($order_array, array('order_id' => $order_id)); //更新订单退款

            Db::commit();
            return $state;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 修改退款
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $data 数据
     * @return boolean
     */
    public function editVrrefund($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $result = Db::name('vrrefund')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 退款编号
     * @access public
     * @author csdeshang
     * @param type $store_id 机构ID
     * @return string
     */
    public function getVrrefundSn($store_id) {
        $result = mt_rand(100, 999) . substr(500 + $store_id, -3) . date('ymdHis');
        return $result;
    }

    /**
     * 退款记录
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $pagesize 分页
     * @param type $limit 限制
     * @param type $fields 字段
     * @return type
     */

        public function getVrrefundList($condition = array(), $pagesize = '',  $field = '*', $order = 'refund_id desc', $limit = 0) {
        if($pagesize){
            $result = Db::name('vrrefund')->field($field)->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            $result = $result->items();
        }else{
            $result = Db::name('vrrefund')->field($field)->where($condition)->order($order)->limit($limit)->select()->toArray();
        }
        
        foreach ($result as $key => $vrrefund) {
            $result[$key]['admin_state_desc'] = get_vrrefund_admin_state($vrrefund['admin_state']);
        }
        
        return $result;
    }

    /**
     * 取得退款记录的数量
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getVrrefundCount($condition) {
        $result = Db::name('vrrefund')->where($condition)->count();
        return $result;
    }

    /**
     * 详细页右侧订单信息
     * @access public
     * @author csdeshang
     * @param type $order_condition 条件
     * @return type
     */
    public function getRightVrorderList($order_condition,$order_id) {
        $vrorder_model = model('vrorder');
        $order_info = $vrorder_model->getVrorderInfo($order_condition);

        $store_model = model('store');
        $store = $store_model->getStoreInfo(array('store_id' => $order_info['store_id']));

        //显示退款
        $vrrefund_model=model('vrrefund');
        $condition=array();
        $condition[]=array('order_id','=',$order_info['order_id']);
        $condition[]=array('admin_state','<>',3);
        $vrrefund_info=$vrrefund_model->getOneVrrefund($condition);
        $order_info['refund'] = $vrrefund_info?'0':'1';
        $order_info['if_refund'] = $vrorder_model->getVrorderOperateState('refund', $order_info);
        return array('order_info'=>$order_info,'store'=>$store);
    }

    /**
     * 获得退款的机构列表
     * @access public
     * @author csdeshang
     * @param type $list 列表
     * @return type
     */
    public function getVrrefundStoreList($list) {
        $store_ids = array();
        if (!empty($list) && is_array($list)) {
            foreach ($list as $key => $value) {
                $store_ids[] = $value['store_id']; //机构编号
            }
        }
        $field = 'store_id,store_name,member_id,member_name,seller_name,store_company_name,store_qq,store_ww,store_phone';
        return model('store')->getStoreMemberIDList($store_ids, $field);
    }
 
    /**
     * 获取一条退款记录
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getOneVrrefund($condition){
        $refund = Db::name('vrrefund')->where($condition)->find();
        if(!empty($refund)){
            $refund['admin_state_desc'] = get_vrrefund_admin_state($refund['admin_state']);
        }
        return $refund;
    }


}
