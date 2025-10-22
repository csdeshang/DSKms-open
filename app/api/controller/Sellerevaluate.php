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
 * 店铺评论控制器
 */
class  Sellerevaluate extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/'.config('lang.default_lang').'/memberevaluate.lang.php');
    }

    /**
     * @api {POST} api/Sellerevaluate/evaluate_list 评价列表
     * @apiVersion 1.0.0
     * @apiGroup Sellerevaluate
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {String} goods_name 商品名称
     * @apiParam {String} member_name 会员名称
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goodsevallist  评论列表 （返回字段参考evaluategoods表）
     * @apiSuccess {String} result.goodsevallist.geval_goodsimage_url  商品图片完整路径
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function evaluate_list() {
        $evaluategoods_model = model('evaluategoods');

        $condition = array();
        
        $goods_name = input('param.goods_name');
        if (!empty($goods_name)) {
            $condition[] = array('geval_goodsname','like', '%' . $goods_name . '%');
        }
        $member_name = input('param.member_name');
        if (!empty($member_name)) {
            $condition[] = array('geval_frommembername','like', '%' . $member_name . '%');
        }
        $condition[] = array('geval_storeid','=',$this->store_info['store_id']);
        $goodsevallist = $evaluategoods_model->getEvaluategoodsList($condition, 5, 'geval_id desc');
        foreach($goodsevallist as $key => $val){
            $goodsevallist[$key]['geval_goodsimage_url']= goods_cthumb($val['geval_goodsimage']);
        }
    
        $result = array_merge(array('goodsevallist' => $goodsevallist), mobile_page($evaluategoods_model->page_info));
        ds_json_encode(10000, '', $result);
    }


    /**
     * @api {POST} api/Sellerevaluate/explain_save 解释来自买家的评价
     * @apiVersion 1.0.0
     * @apiGroup Sellerevaluate
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} geval_id 评论ID
     * @apiParam {String} geval_explain 解释
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function explain_save() {
        $geval_id = intval(input('post.geval_id'));
        $geval_explain = trim(input('post.geval_explain'));
        //验证表单
        if (!$geval_explain) {
            ds_json_encode(10001, '解释内容不能为空');
        }
        $data = array();
        $data['result'] = true;

        $evaluategoods_model = model('evaluategoods');

        $evaluate_info = $evaluategoods_model->getEvaluategoodsInfoByID($geval_id, $this->store_info['store_id']);
        if (empty($evaluate_info)) {
            ds_json_encode(10001, lang('param_error'));
        }

        $update = array('geval_explain' => $geval_explain);
        $condition = array('geval_id' => $geval_id);
        $result = $evaluategoods_model->editEvaluategoods($update, $condition);

        if ($result) {
            ds_json_encode(10000, '解释成功');
        } else {
            ds_json_encode(10001, '解释保存失败');
        }
    }


}

?>
