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
 * 店铺花费控制器
 */
class  Sellercost extends MobileSeller {

    public function initialize() {
        parent::initialize();
    }

    /**
     * @api {POST} api/Sellercost/cost_list 店铺花费列表
     * @apiVersion 1.0.0
     * @apiGroup Sellercost
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {String} storecost_remark 日志内容
     * @apiParam {String} add_time_from 开始时间 YYYY-MM-DD
     * @apiParam {String} add_time_to 结束时间 YYYY-MM-DD
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.cost_list  花费列表 （返回字段参考storecost表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function cost_list() {
        $storecost_model = model('storecost');
        $condition = array();
        $condition[] = array('storecost_store_id','=',$this->store_info['store_id']);
        $storecost_remark = input('param.storecost_remark');
        if (!empty($storecost_remark)) {
            $condition[] = array('storecost_remark','like', '%' . $storecost_remark . '%');
        }
        $add_time_from = input('param.add_time_from');
        $add_time_to = input('param.add_time_to');
        if (!empty($add_time_from) && !empty($add_time_to)) {
            $condition[] = array('storecost_time','between', array(strtotime($add_time_from), strtotime($add_time_to)));
        }
        $cost_list = $storecost_model->getStorecostList($condition, 10, 'storecost_id desc');

        $result = array_merge(array('cost_list' => $cost_list), mobile_page($storecost_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }
    
}
