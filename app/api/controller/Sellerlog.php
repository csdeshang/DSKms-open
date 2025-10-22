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
 * 卖家日志控制器
 */
class  Sellerlog extends MobileSeller {

    public function initialize() {
        parent::initialize();
    }

    /**
     * @api {POST} api/Sellerlog/log_list 机构日志列表
     * @apiVersion 1.0.0
     * @apiGroup Sellerlog
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} cate_id 分类ID
     * @apiParam {String} seller_name 卖家账号
     * @apiParam {String} log_content 日志内容
     * @apiParam {String} add_time_from 开始时间 YYYY-MM-DD
     * @apiParam {String} add_time_to 结束时间 YYYY-MM-DD
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.log_list  日志列表 （返回字段参考sellerlog表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function log_list() {
        $sellerlog_model = model('sellerlog');
        $condition = array();
        $condition[] = array('sellerlog_store_id', '=', $this->store_info['store_id']);
        $seller_name = input('param.seller_name');
        $log_content = input('param.log_content');
        $add_time_from = input('param.add_time_from');
        $add_time_to = input('param.add_time_to');
        if (!empty($seller_name)) {
            $condition[] = array('sellerlog_seller_name', 'like', '%' . input('param.seller_name') . '%');
        }
        if (!empty($log_content)) {
            $condition[] = array('sellerlog_content', 'like', '%' . $log_content . '%');
        }
        if (!empty($add_time_from) || $add_time_to) {
            $condition[] = array('sellerlog_time', 'between', [strtotime($add_time_from), strtotime($add_time_to)]);
        }
        $log_list = $sellerlog_model->getSellerlogList($condition, 10, 'sellerlog_id desc');
        $result = array_merge(array('log_list' => $log_list), mobile_page($sellerlog_model->page_info));
        ds_json_encode(10000, '操作成功！', $result);
    }

}
