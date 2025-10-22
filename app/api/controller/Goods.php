<?php

namespace app\api\controller;

use think\facade\View;
use think\facade\Lang;
use \Firebase\JWT\JWT;
use AlibabaCloud\Client\AlibabaCloud;

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
 * 商品控制器
 */
class Goods extends MobileMall {

    private $PI = 3.14159265358979324;
    private $x_pi = 0;

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/goods.lang.php');
        $this->x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    }

    /**
     * @api {POST} api/Goods/goods_list 商品列表
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} gc_id 分类ID
     * @apiParam {String} keyword 关键词
     * @apiParam {String} b_id 品牌id
     * @apiParam {Float} price_from 价格从
     * @apiParam {Float} price_to 价格到
     * @apiParam {Int} sort_key 排序键 goods_salenum销量 goods_click浏览量 goods_price价格
     * @apiParam {Int} sort_order 排序值 1升序 2降序
     * @apiParam {Int} gift 是否有赠品 1有
     * @apiParam {Int} own_shop 自营 1是
     * @apiParam {Int} area_id 地区id
     * @apiParam {Int} xianshi 是否秒杀 1是
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_list  商品列表
     * @apiSuccess {Int} result.goods_list.evaluation_count  评论数
     * @apiSuccess {Float} result.goods_list.evaluation_good_star  评分
     * @apiSuccess {String} result.goods_list.goods_advword  广告词
     * @apiSuccess {Int} result.goods_list.goods_id  商品ID
     * @apiSuccess {String} result.goods_list.goods_image  商品图片名称
     * @apiSuccess {String} result.goods_list.goods_image_url  商品图片完整路径
     * @apiSuccess {Float} result.goods_list.goods_marketprice  商品市场价
     * @apiSuccess {String} result.goods_list.goods_name  商品名称
     * @apiSuccess {Float} result.goods_list.goods_price  商品价格
     * @apiSuccess {Float} result.goods_list.goods_promotion_price  商品促销价
     * @apiSuccess {String} result.goods_list.goods_promotion_type  促销类型
     * @apiSuccess {Int} result.goods_list.goods_salenum  商品销售量
     * @apiSuccess {Int} result.goods_list.store_id  店铺ID
     * @apiSuccess {String} result.goods_list.store_name  店铺名称
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function goods_list() {
        $goods_model = model('goods');
        $search_model = model('search');

        //查询条件
        $condition = array();
        $gc_id = intval(input('param.gc_id'));
        $keyword = input('param.keyword');
        $b_id = intval(input('param.b_id'));
        if ($gc_id > 0) {
//            $condition[] = array('gc_id','=',$gc_id);
            $condition = model('goods')->_getRecursiveClass($condition, $gc_id);
        } elseif (!empty($keyword)) {
            $condition[] = array('goods_name|goods_advword', 'like', '%' . $keyword . '%');
            if (cookie('hisSearch') == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', cookie('hisSearch'));
            }
            if (strlen($keyword) <= 20 && !in_array($keyword, $his_sh_list)) {
                if (array_unshift($his_sh_list, $keyword) > 8) {
                    array_pop($his_sh_list);
                }
            }
            cookie('hisSearch', implode('~', $his_sh_list), 2592000);
        } elseif ($b_id > 0) {
            $condition[] = array('brand_id', '=', $b_id);
        }
        $price_from = input('param.price_from');
        $price_to = input('param.price_to');
        $price_from = preg_match('/^[\d.]{1,20}$/', $price_from) ? $price_from : null;
        $price_to = preg_match('/^[\d.]{1,20}$/', $price_to) ? $price_to : null;

        //所需字段
        $fieldstr = "goods_id,store_id,goods_name,goods_advword,goods_price,goods_image,goods_salenum,evaluation_good_star,evaluation_count";

        $fieldstr .= ',goods_advword,store_id,store_name';

        //排序方式
        $order = $this->_goods_list_order(input('param.sort_key'), input('param.sort_order'));


        if ($price_from && $price_to) {
            $condition[] = array('goods_price', 'between', "{$price_from},{$price_to}");
        } elseif ($price_from) {
            $condition[] = array('goods_price', '>=', $price_from);
        } elseif ($price_to) {
            $condition[] = array('goods_price', '<=', $price_to);
        }
        if (input('param.own_shop') == 1) {
            $condition[] = array('store_id', '=', 1);
        }
        if (intval(input('param.area_id')) > 0) {
            $condition[] = array('areaid_1', '=', intval(input('param.area_id')));
        }

        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fieldstr, $order, $this->pagesize);
        foreach ($goods_list as $key => $value) {
            //商品图片url
            $goods_list[$key]['goods_image_url'] = goods_cthumb($value['goods_image'], 270, $value['store_id']);
        }
        $result = array_merge(array('goods_list' => $goods_list), mobile_page(is_object($goods_model->page_info) ? $goods_model->page_info : ''));
        ds_json_encode(10000, '', $result);
    }
    
    
    /**
         * @api {POST} api/Goods/exampaper_list 课程试卷列表
         * @apiVersion 1.0.0
         * @apiGroup Goods
         *
         * @apiParam {Int} goods_id 课程ID
         * @apiParam {Int} page 页码
         * @apiParam {Int} per_page 每页显示数量
         *
         * @apiSuccess {String} code 返回码,10000为成功
         * @apiSuccess {String} message  返回消息
         * @apiSuccess {Object} result  返回数据
         * @apiSuccess {Int} result.page_total  总页数
         * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
         */
        public function exampaper_list() {
            $goods_id = intval(input('param.goods_id'));
            
            //课程关联试卷
            $exampaper_model = model('exampaper');
            $condition  = array();
            $condition[]= array('goods_id','=',$goods_id);
            $exampaper_list = $exampaper_model->getExampaperList($condition);
    
           
            $result = array_merge(array('exampaper_list' => $exampaper_list), mobile_page(is_object($exampaper_model->page_info) ? $goods_model->page_info : ''));
            ds_json_encode(10000, '', $result);
        }

    /**
     * 商品列表排序方式
     */
    private function _goods_list_order($sort_key, $sort_order) {
        $result = 'mall_goods_commend desc,mall_goods_sort asc';
        if (!empty($sort_key)) {

            $sequence = 'desc';
            if ($sort_order == 'asc') {
                $sequence = 'asc';
            }

            switch ($sort_key) {
                //销量
                case 'goods_salenum' :
                    $result = 'goods_salenum' . ' ' . $sequence;
                    break;
                //浏览量
                case 'goods_click' :
                    $result = 'goods_click' . ' ' . $sequence;
                    break;
                //价格
                case 'goods_price' :
                    $result = 'goods_price' . ' ' . $sequence;
                    break;
                //新品
                case 'add_time' :
                    $result = 'add_time' . ' ' . $sequence;
                    break;
            }
        }
        return $result;
    }

    /**
     * @api {POST} api/Goods/goods_detail 商品详细页
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.consult_type 咨询类型列表，键为咨询类型ID
     * @apiSuccess {Int} result.consult_type.consulttype_id 咨询类型ID
     * @apiSuccess {Object} result.consult_type.consulttype_introduce 咨询介绍
     * @apiSuccess {Object} result.consult_type.consulttype_name 咨询类型标题
     * @apiSuccess {Int} result.consult_type.consulttype_sort 咨询类型排序
     * @apiSuccess {Object[]} result.gift_array 赠品列表
     * @apiSuccess {Int} result.gift_array.gift_id 赠品ID
     * @apiSuccess {Int} result.gift_array.gift_goodsid 赠品商品ID
     * @apiSuccess {Object} result.gift_array.gift_goodsname 主商品名称
     * @apiSuccess {Object} result.gift_array.gift_goodsimage 主商品图片名称
     * @apiSuccess {Object} result.gift_array.gift_goodsimage_url  主商品图片完整路径
     * @apiSuccess {Object} result.gift_array.gift_amount 赠品数量
     * @apiSuccess {Int} result.gift_array.goods_id 主商品ID
     * @apiSuccess {Int} result.gift_array.goods_commonid 主商品公共ID
     * @apiSuccess {Object[]} result.goods_commend_list 推荐商品列表
     * @apiSuccess {Int} result.goods_commend_list.goods_id 商品ID
     * @apiSuccess {Object} result.goods_commend_list.goods_image_url 商品图片
     * @apiSuccess {Object} result.goods_commend_list.goods_name 商品名称
     * @apiSuccess {Float} result.goods_commend_list.goods_price 商品价格
     * @apiSuccess {Float} result.goods_commend_list.goods_promotion_price 商品促销价
     * @apiSuccess {Object[]} result.goods_eval_list 商品评论列表（返回字段参考evaluategoods）
     * @apiSuccess {Object} result.goods_evaluate_info 商品评论综合信息
     * @apiSuccess {Int} result.goods_evaluate_info.all 商品评论总数
     * @apiSuccess {Int} result.goods_evaluate_info.bad 差评数
     * @apiSuccess {Int} result.goods_evaluate_info.bad_percent 差评率
     * @apiSuccess {Int} result.goods_evaluate_info.good 好评数
     * @apiSuccess {Int} result.goods_evaluate_info.good_percent 好评率
     * @apiSuccess {Int} result.goods_evaluate_info.good_star 好评评分
     * @apiSuccess {Int} result.goods_evaluate_info.normal 中评数
     * @apiSuccess {Int} result.goods_evaluate_info.normal_percent 中评率
     * @apiSuccess {Int} result.goods_evaluate_info.star_average 平均评分
     * @apiSuccess {String} result.goods_image 商品图片，用逗号分隔
     * @apiSuccess {Object} result.goods_info 商品信息（返回字段参考goods）
     * @apiSuccess {Object[]} result.mb_body 商品详情列表
     * @apiSuccess {String} result.mb_body.type 详情类型 text文字image图片
     * @apiSuccess {String} result.mb_body.value 详情值
     * @apiSuccess {Float} result.inviter_amount 分销佣金
     * @apiSuccess {Boolean} result.is_favorate 是否已收藏 true是false否
     * @apiSuccess {Object} result.spec_image 规格图片列表，键为规格ID，值为规格图片完整路径
     * @apiSuccess {Object} result.spec_list 规格商品ID列表，键为规格ID，值为商品ID
     * @apiSuccess {Object} result.store_info 店铺信息
     * @apiSuccess {Int} result.store_info.goods_count 商品数
     * @apiSuccess {Int} result.store_info.member_id 用户ID
     * @apiSuccess {String} result.store_info.member_name 用户名称
     * @apiSuccess {String} result.store_info.store_address 店铺地址
     * @apiSuccess {Object} result.store_info.store_credit 店铺信用信息
     * @apiSuccess {Object} result.store_info.store_credit.store_deliverycredit 发货信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_deliverycredit.credit 发货信用值
     * @apiSuccess {String} result.store_info.store_credit.store_deliverycredit.text 发货信用描述
     * @apiSuccess {Object} result.store_info.store_credit.store_desccredit 描述相符信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_desccredit.credit 描述相符信用值
     * @apiSuccess {String} result.store_info.store_credit.store_desccredit.text 描述相符信用描述
     * @apiSuccess {Object} result.store_info.store_credit.store_servicecredit 服务态度信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_servicecredit.credit 服务态度信用值
     * @apiSuccess {String} result.store_info.store_credit.store_servicecredit.text 服务态度信用描述
     * @apiSuccess {Int} result.store_info.store_id 店铺ID
     * @apiSuccess {String} result.store_info.store_logo 店铺logo
     * @apiSuccess {String} result.store_info.store_name 店铺名称
     * @apiSuccess {Object[]} result.voucher 店铺优惠券列表
     * @apiSuccess {String} result.voucher.vouchertemplate_enddate 优惠券过期时间描述
     * @apiSuccess {Int} result.voucher.vouchertemplate_id 优惠券模板ID
     * @apiSuccess {Float} result.voucher.vouchertemplate_limit 优惠券最低消费金额
     * @apiSuccess {Int} result.voucher.vouchertemplate_price 优惠金额
     */
    public function goods_detail() {
        $goods_id = intval(input('param.goods_id'));
        $area_id = intval(input('param.area_id'));
        // 商品详细信息
        $goods_model = model('goods');
        $goods_detail = $goods_model->getGoodsDetail($goods_id);
        if (empty($goods_detail)) {
            ds_json_encode(10001, '商品不存在');
        }
        $store_info = model('store')->getStoreInfoByID($goods_detail['goods_info']['store_id']);
        $goods_detail['store_info']['store_avatar'] = get_store_logo($store_info['store_avatar'], 'store_avatar');
        $goods_detail['store_info']['store_logo'] = get_store_logo($store_info['store_logo'], 'store_logo');
		$goods_detail['store_info']['mb_title_img'] = !empty($store_info['mb_title_img']) ? ds_get_pic( ATTACH_STORE , $store_info['mb_title_img']) : '';
        $goods_detail['store_info']['store_id'] = $store_info['store_id'];
        $goods_detail['store_info']['store_name'] = $store_info['store_name'];
        $goods_detail['store_info']['member_id'] = $store_info['member_id'];
        $goods_detail['store_info']['member_name'] = $store_info['member_name'];
        $goods_detail['store_info']['store_address'] = $store_info['area_info'] . $store_info['store_address'];
        $goods_detail['store_info']['goods_count'] = $store_info['goods_count'];

            $storeCredit = array();
            $percentClassTextMap = array(
                'equal' => '平',
                'high' => '高',
                'low' => '低',
            );
            foreach ((array) $store_info['store_credit'] as $k => $v) {
                isset($v['percent_class']) && $v['percent_text'] = $percentClassTextMap[$v['percent_class']];
                $storeCredit[$k] = $v;
            }
            $goods_detail['store_info']['store_credit'] = $storeCredit;

        //商品详细信息处理
        $goods_detail = $this->_goods_detail_extend($goods_detail);

        $goods_common_info = $goods_model->getGoodsInfoByID($goods_detail['goods_info']['goods_id']);
        $goods_detail['mb_body'] = array();
        if ($goods_common_info['mobile_body'] != '') {
            $goods_detail['mb_body'] = unserialize($goods_common_info['mobile_body']);
        }
        // 如果已登录 判断该商品是否已被收藏&&添加浏览记录
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int) model('favorites')->getGoodsFavoritesCountByGoodsId($goods_id, $member_id);
            $goods_detail['is_favorate'] = $c > 0;
            $seller_model = model('seller');
            $seller_info = $seller_model->getSellerInfo(array('member_id' => $member_id));
            //判断是否是店主本人
            if (empty($seller_info) || $store_info['store_id'] != $seller_info['store_id']) {
                model('goodsbrowse')->addViewedGoods($goods_id, $member_id);
            }
        }

        // 机构优惠券
        $condition = array();
		$condition[] = array('vouchertemplate_if_private', '=', 0);
        $condition[] = array('vouchertemplate_state', '=', 1);
        $condition[] = array('vouchertemplate_enddate', '>', TIMESTAMP);
        $condition[] = array('vouchertemplate_store_id', '=', $store_info['store_id']);
        $voucher_template = model('voucher')->getVouchertemplateList($condition);
        if (!empty($voucher_template)) {
            foreach ($voucher_template as $val) {
                $param = array();
                $param['vouchertemplate_id'] = $val['vouchertemplate_id'];
                $param['vouchertemplate_price'] = $val['vouchertemplate_price'];
                $param['vouchertemplate_points'] = $val['vouchertemplate_points'];
                $param['vouchertemplate_limit'] = $val['vouchertemplate_limit'];
                $param['vouchertemplate_enddate'] = date('Y年m月d日', $val['vouchertemplate_enddate']);
                $goods_detail['voucher'][] = $param;
            }
        }

        // 评价列表
        $goods_eval_list = model('evaluategoods')->getEvaluategoodsList(array('geval_goodsid' => $goods_id), '3');
        //$goods_eval_list = model('memberevaluate','logic')->evaluateListDity($goods_eval_list);
        $goods_detail['goods_eval_list'] = $goods_eval_list;

        //判断当前用户是否已购买此商品
        $if_have_buy = $this->_check_buy_goods($goods_id);
        $goods_detail['goods_info']['if_have_buy'] = $if_have_buy;
        //获取当前商品下的章节
        $goods_detail['goodscourses_group'] = $this->_getGoodscoursesList($goods_detail['goods_info'], $if_have_buy);

        //评价信息
        $goods_evaluate_info = model('evaluategoods')->getEvaluategoodsInfoByGoodsID($goods_id);
        $goods_detail['goods_evaluate_info'] = $goods_evaluate_info;

        $inviter_model = model('inviter');
        $goods_detail['inviter_money'] = 0;
        if (config('ds_config.inviter_show') && config('ds_config.inviter_open') && $goods_detail['goods_info']['inviter_open'] && $member_id && $inviter_model->getInviterInfo('i.inviter_id=' . $member_id . ' AND i.inviter_state=1')) {
            $inviter_money = round($goods_detail['goods_info']['inviter_ratio'] / 100 * $goods_detail['goods_info']['goods_price'] * floatval(config('ds_config.inviter_ratio_1')) / 100, 2);
            if ($inviter_money > 0) {
                $goods_detail['goods_info']['inviter_money'] = $inviter_money;
            }
        }
        // 咨询类型
        $consult_type = rkcache('consulttype', true);

        $goods_detail['consult_type'] = $consult_type;
        $storeplate_model = model('storeplate');
        // 顶部关联版式
        if ($goods_detail['goods_info']['plateid_top'] > 0) {
            $plate_top = $storeplate_model->getStoreplateInfoByID($goods_detail['goods_info']['plateid_top']);
            if ($plate_top) {
                $plate_top['storeplate_content'] = htmlspecialchars_decode($plate_top['storeplate_content']);
            }
            $goods_detail['goods_info']['plate_top'] = $plate_top;
        }
        // 底部关联版式
        if ($goods_detail['goods_info']['plateid_bottom'] > 0) {
            $plate_bottom = $storeplate_model->getStoreplateInfoByID($goods_detail['goods_info']['plateid_bottom']);
            if ($plate_bottom) {
                $plate_bottom['storeplate_content'] = htmlspecialchars_decode($plate_bottom['storeplate_content']);
            }
            $goods_detail['goods_info']['plate_bottom'] = $plate_bottom;
        }

        //组合商品
        $goodscombo_list = model('goodscombo')->getGoodscomboCacheByGoodsId($goods_id);
        $goods_detail['goods_info']['goodscombo_list'] = unserialize($goodscombo_list['gcombo_list']);
        $total_price = 0;
        foreach ($goods_detail['goods_info']['goodscombo_list'] as $key => $val) {
            $total_price += $val['goods_price'];
            $goods_detail['goods_info']['goodscombo_list'][$key]['goods_image_url'] = goods_cthumb($val['goods_image'], 270, $val['store_id']);
        }
        $goods_detail['goods_info']['goodscombo_price'] = round($total_price, 2);
        ds_json_encode(10000, '', $goods_detail);
    }

    /**
     * 获取当前商品下的视频
     * @param type $goods_info
     * @return type
     */
    private function _getGoodscoursesList($goods_info, $if_have_buy) {
        $goodscourses_model = model('goodscourses');
        $goodscourses_list = $goodscourses_model->getGoodscoursesList(array('goods_id' => $goods_info['goods_id']));
        //查看商品是否免费
        $if_goods_free = FALSE;
        if ($goods_info['goods_price'] == 0.00) {
            $if_goods_free = TRUE;
        }
        $goodscourses_group = array();
        if (!empty($goodscourses_list)) {
            $goodscourses_class_model = model('goodscourses_class');

            $sort = array();
            $id = array();
            $videoupload_model = model('videoupload');
            foreach ($goodscourses_list as $key => $goodscourses) {
                $goodscourses_list[$key]['goodscourses_url'] = '';
                if ($goodscourses['goodscourses_type']==0) {
                    $videoupload_info = $videoupload_model->getOneVideoupload(array(array('videoupload_id', '=', $goodscourses['goodscourses_type_id'])));
                    if ($videoupload_info && $videoupload_info['videoupload_state'] == 1) {
                        $goodscourses_list[$key]['goodscourses_url'] = $videoupload_info['videoupload_url'];
                    } else {
                        unset($goodscourses_list[$key]);
                        continue;
                    }
                } else {
                    unset($goodscourses_list[$key]);
                    continue;
                }

                if (!isset($goodscourses_group[$goodscourses['goodscourses_class_id']])) {
                    $goodscourses_group[$goodscourses['goodscourses_class_id']] = array(
                        'goodscourses_class_id' => 0,
                        'goodscourses_class_sort' => 0,
                        'goodscourses_class_name' => '',
                        'list' => array()
                    );
                }
                if ($goodscourses['goodscourses_class_id']) {
                    $goodscourses_class_info = $goodscourses_class_model->getGoodscoursesClassInfo(array(array('goodscourses_class_id', '=', $goodscourses['goodscourses_class_id'])));
                    if ($goodscourses_class_info) {
                        $goodscourses_group[$goodscourses['goodscourses_class_id']]['goodscourses_class_name'] = $goodscourses_class_info['goodscourses_class_name'];
                        $goodscourses_group[$goodscourses['goodscourses_class_id']]['goodscourses_class_id'] = $goodscourses_class_info['goodscourses_class_id'];
                        $goodscourses_group[$goodscourses['goodscourses_class_id']]['goodscourses_class_sort'] = $goodscourses_class_info['goodscourses_class_sort'];
                    }
                }
                if (!isset($sort[$goodscourses['goodscourses_class_id']])) {
                    $sort[$goodscourses['goodscourses_class_id']][] = $goodscourses_group[$goodscourses['goodscourses_class_id']]['goodscourses_class_sort'];
                    $id[$goodscourses['goodscourses_class_id']][] = $goodscourses_group[$goodscourses['goodscourses_class_id']]['goodscourses_class_id'];
                }
                $goodscourses_list[$key]['file_id'] = '';
                $goodscourses_list[$key]['video_type'] = '';
                $goodscourses_list[$key]['psign'] = '';
                $goodscourses_list[$key]['can_view'] = 1;
                //根据课程是否免费以及是否购买判断
                if ($goodscourses['goodscourses_free'] || $goodscourses['goodscourses_exper'] || $if_have_buy || $if_goods_free) {
                    if ($goodscourses['goodscourses_type']==0) {
                        $videoupload_info = $videoupload_model->getOneVideoupload(array(array('videoupload_id', '=', $goodscourses['goodscourses_type_id'])));
                        if ($videoupload_info && $videoupload_info['videoupload_state'] == 1) {
                            $goodscourses_list[$key]['goodscourses_url'] = $videoupload_info['videoupload_url'];
                            $goodscourses_list[$key]['video_type'] = $videoupload_info['video_type'];
                            if ($videoupload_info['video_type'] == 'tencent') {
                                $appId = config('ds_config.vod_tencent_appid'); // 用户 appid
                                $fileId = $videoupload_info['videoupload_fileid']; // 目标 FileId
                                $currentTime = TIMESTAMP;
                                $psignExpire = $currentTime + 3600; // 可任意设置过期时间，示例1h
                                $urlTimeExpire = dechex($psignExpire); // 可任意设置过期时间，16进制字符串形式，示例1h
                                $the_key = config('ds_config.vod_tencent_play_key');

                                $payload = array(
                                    "appId" => intval($appId),
                                    "fileId" => $fileId,
                                    "currentTimeStamp" => $currentTime,
                                    "expireTimeStamp" => $psignExpire,
                                    "urlAccessInfo" => array(
                                        "t" => $urlTimeExpire
                                    )
                                );
                                if (!$if_have_buy && $goodscourses['goodscourses_exper']) {
                                    $payload['urlAccessInfo']['exper'] = $goodscourses['goodscourses_exper'];
                                }
                                $jwt = JWT::encode($payload, $the_key, 'HS256');
                                $temp = parse_url($videoupload_info['videoupload_url']);
                                $goodscourses_list[$key]['t'] = $urlTimeExpire;
                                $goodscourses_list[$key]['sign'] = md5($the_key . dirname($temp['path']) . '/' . $urlTimeExpire);
                                $goodscourses_list[$key]['file_id'] = $fileId;
                                $goodscourses_list[$key]['psign'] = $jwt;
                            } else if ($videoupload_info['video_type'] == 'aliyun') {
                                $goodscourses_list[$key]['file_id'] = $videoupload_info['videoupload_fileid'];
                                $exper = 0;
                                if (!$if_have_buy && $goodscourses['goodscourses_exper'] && !$goodscourses['goodscourses_free']) {
                                    $exper = $goodscourses['goodscourses_exper'];
                                }
                                $url = $videoupload_model->getVideoExpire($videoupload_info, $exper);
                                if ($url) {
                                    $goodscourses_list[$key]['goodscourses_url'] = $url;
                                }
                                $regionId = 'cn-shanghai';
                                AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                                        ->regionId($regionId)
                                        ->asDefaultClient();
                                try {
                                    $result = AlibabaCloud::rpc()
                                            ->product('vod')
                                            // ->scheme('https') // https | http
                                            ->version('2017-03-21')
                                            ->action('GetVideoPlayAuth')
                                            ->method('POST')
                                            ->host('vod.' . $regionId . '.aliyuncs.com')
                                            ->options([
                                                'query' => [
                                                    'VideoId' => $videoupload_info['videoupload_fileid'],
                                                ],
                                            ])
                                            ->request();
                                    $result=$result->toArray();
                                    $goodscourses_list[$key]['psign'] = $result['PlayAuth'];
                                } catch (\Exception $e) {
                                    ds_json_encode(10001, $e->getMessage());
                                }
                            } else {
                                if (!$if_have_buy && $goodscourses['goodscourses_exper'] && !$goodscourses['goodscourses_free']) {
                                    $goodscourses_list[$key]['goodscourses_url'] = ''; //如果不匹配则不能试看
                                }else{
                                    $url = $videoupload_model->getVideoExpire($videoupload_info);
                                    if ($url) {
                                        $goodscourses_list[$key]['goodscourses_url'] = $url;
                                    }
                                }
                            }
                        }else{
                            $goodscourses_list[$key]['can_view'] = 0;
                            if(!$videoupload_info){
                                $goodscourses_list[$key]['can_view_text'] = '视频已被删除';
                            }else{
                                $goodscourses_list[$key]['can_view_text'] = '视频未审核';
                            }
                        }
                    }else if($goodscourses['goodscourses_type']==1){
                        $live_apply_model=model('live_apply');
                        $live_apply_info=$live_apply_model->getLiveApplyInfo(array(array('live_apply_id','=',$goodscourses['goodscourses_type_id'])));
                        if($live_apply_info){
                            if($live_apply_info['live_apply_state']!=1){
                                $goodscourses_list[$key]['can_view'] = 0;
                                $goodscourses_list[$key]['can_view_text'] = '直播未审核';
                            }else{
                                $goodscourses_list[$key]['live_apply_id'] = $live_apply_info['live_apply_id'];
                            }
                        }else{
                            $goodscourses_list[$key]['can_view'] = 0;
                            $goodscourses_list[$key]['can_view_text'] = '直播已被删除';
                        }
                    }else if($goodscourses['goodscourses_type']==2){
                        $minipro_live_model=model('minipro_live');
                        $minipro_live_info=$minipro_live_model->getMiniproLiveInfo(array(array('minipro_live_id','=',$goodscourses['goodscourses_type_id'])));
                        if($minipro_live_info){
                            $goodscourses_list[$key]['minipro_live_room_id'] = $minipro_live_info['minipro_live_room_id'];
                        }else{
                            $goodscourses_list[$key]['can_view'] = 0;
                            $goodscourses_list[$key]['can_view_text'] = '直播已被删除';
                        }
                    }else{
                        unset($goodscourses_list[$key]);continue;
                    }


                } else {
                    //购买后才可以观看
                    unset($goodscourses_list[$key]['goodscourses_url']);
                }
                if ($if_have_buy || $if_goods_free) {
                    $goodscourses_list[$key]['goodscourses_text'] = '开始学习';
                } elseif ($goodscourses['goodscourses_free']) {
                    $goodscourses_list[$key]['goodscourses_text'] = '免费试看';
                } else {
                    $goodscourses_list[$key]['goodscourses_text'] = '需先购买';
                }
                //课程附件
                $goodscourses_list[$key]['baidu_pan_fsids'] = json_decode($goodscourses['baidu_pan_fsids'], true);
                $goodscourses_group[$goodscourses['goodscourses_class_id']]['list'][] = $goodscourses_list[$key];
            }
            array_multisort($sort, $id, $goodscourses_group);
        }

        return $goodscourses_group;
    }
	
	/**
	 * 显示当前产品的课程列表
	 */
	public function goodscourses_info() {
	    $goodscourses_id = intval(input('param.goodscourses_id'));
	
	    $condition = array();
	    $condition[] = array('goodscourses_id', '=', $goodscourses_id);
	    $goodscourses_model = model('goodscourses');
	    $goodscourses = $goodscourses_model->getOneGoodscourses($condition);
	    if(!$goodscourses){
	      ds_json_encode(10001,'课程不存在');
	    }
	    $goods_id=$goodscourses['goods_id'];
	    $if_have_buy = $this->_check_buy_goods($goods_id);
	    $goodscourses['goodscourses_url'] = '';
	    //判断此商品是否被购买
	    if ($goodscourses['goodscourses_free'] || $goodscourses['goodscourses_exper'] || $if_have_buy) {
	        // 商品详细信息
	        $goods_model = model('goods');
	        $goods_detail = $goods_model->getGoodsDetail($goods_id);
	        $goods_info = $goods_detail['goods_info'];
	        if (empty($goods_info) || !$goods_info['goods_state'] || !$goods_info['goods_verify']) {
	            ds_json_encode(10001,lang('goods_index_no_goods'));
	        }
	
	        //通过链接获取腾讯fileId
	        $goodscourses['file_id'] = '';
	        $goodscourses['video_type'] = '';
	        $goodscourses['psign'] = '';
	        if ($goodscourses['goodscourses_type']==0) {
	            $videoupload_model = model('videoupload');
	            $videoupload_info = $videoupload_model->getOneVideoupload(array(array('videoupload_id', '=', $goodscourses['goodscourses_type_id'])));
	            if ($videoupload_info && $videoupload_info['videoupload_state'] == 1) {
	                $goodscourses['goodscourses_url'] = $videoupload_info['videoupload_url'];
	                if ($videoupload_info['video_type'] == 'tencent') {
	                    $appId = config('ds_config.vod_tencent_appid'); // 用户 appid
	                    $fileId = $videoupload_info['videoupload_fileid']; // 目标 FileId
	                    $currentTime = TIMESTAMP;
	                    $psignExpire = $currentTime + 3600; // 可任意设置过期时间，示例1h
	                    $urlTimeExpire = dechex($psignExpire); // 可任意设置过期时间，16进制字符串形式，示例1h
	                    $key = config('ds_config.vod_tencent_play_key');
	
	                    $payload = array(
	                        "appId" => intval($appId),
	                        "fileId" => $fileId,
	                        "currentTimeStamp" => $currentTime,
	                        "expireTimeStamp" => $psignExpire,
	                        "urlAccessInfo" => array(
	                            "t" => $urlTimeExpire,
	                        )
	                    );
	                    if (!$if_have_buy && $goodscourses['goodscourses_exper'] && !$goodscourses['goodscourses_free']) {
	                        $payload['urlAccessInfo']['exper'] = $goodscourses['goodscourses_exper'];
	                    }
	                    $jwt = JWT::encode($payload, $key, 'HS256');
	
	                    $goodscourses['video_type'] = 'tencent';
	                    $goodscourses['file_id'] = $fileId;
	                    $goodscourses['psign'] = $jwt;
	                } else if ($videoupload_info['video_type'] == 'aliyun') {
	                    $goodscourses['video_type'] = 'aliyun';
	                    $goodscourses['file_id'] = $videoupload_info['videoupload_fileid'];
	                    $exper = 0;
	                    if (!$if_have_buy && $goodscourses['goodscourses_exper'] && !$goodscourses['goodscourses_free']) {
	                        $exper = $goodscourses['goodscourses_exper'];
	                    }
	                    $url = $videoupload_model->getVideoExpire($videoupload_info, $exper);
	                    if ($url) {
	                        $goodscourses['goodscourses_url'] = $url;
	                    }
	                            $regionId = 'cn-shanghai';
	                            AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
	                                    ->regionId($regionId)
	                                    ->asDefaultClient();
	                            try {
	                                $result = AlibabaCloud::rpc()
	                                        ->product('vod')
	                                        // ->scheme('https') // https | http
	                                        ->version('2017-03-21')
	                                        ->action('GetVideoPlayAuth')
	                                        ->method('POST')
	                                        ->host('vod.' . $regionId . '.aliyuncs.com')
	                                        ->options([
	                                            'query' => [
	                                                'VideoId' => $videoupload_info['videoupload_fileid'],
	                                            ],
	                                        ])
	                                        ->request();
	                                $result=$result->toArray();
	                                $goodscourses['psign'] = $result['PlayAuth'];
	                            } catch (\Exception $e) {
	                                ds_json_encode(10001, $e->getMessage());
	                            }
	                } else {
	                    if (!$if_have_buy && $goodscourses['goodscourses_exper'] && !$goodscourses['goodscourses_free']) {
	                        $goodscourses['goodscourses_url'] = ''; //如果不匹配则不能试看
	                    } else {
	                        $url = $videoupload_model->getVideoExpire($videoupload_info);
	                        if ($url) {
	                            $goodscourses['goodscourses_url'] = $url;
	                        }
	                    }
	                }
	            } else {
	                if (!$videoupload_info) {
	                    ds_json_encode(10001,'视频已被删除');
	                } else {
	                    ds_json_encode(10001,'视频未审核');
	                }
	            }
	        }
	      
	        //课程附件
	        $goodscourses['baidu_pan_fsids'] = json_decode($goodscourses['baidu_pan_fsids'], true);
	
	        ds_json_encode(10000,'',array('goodscourses_info'=>$goodscourses));
	    } else {
	        ds_json_encode(10001,'您没有观看权限');
	    }
	}

    /**
     * 检测当前用户是否购买此商品
     */
    private function _check_buy_goods($goods_id) {
        $if_have_buy = 0;
        $member_id = $this->getMemberIdIfExists();
        if ($member_id) {
            $condition = array();
            $condition[] = array('buyer_id', '=', $member_id);
            $condition[] = array('goods_id', '=', $goods_id);
            $condition[] = array('order_state', 'in', array(ORDER_STATE_PAY, ORDER_STATE_SUCCESS));
            $condition[] = array('refund_state', '=', 0);
            $vrorder = model('vrorder')->getVrorderInfo($condition);
            if (!empty($vrorder)) {
                $if_have_buy = 1;
            }
        }
        return $if_have_buy;
    }

    /**
     * @api {POST} api/Goods/consulting_list 产品咨询列表
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.consult_list  产品咨询列表
     * @apiSuccess {Int} result.consult_list.consult_id  产品咨询ID
     * @apiSuccess {Int} result.consult_list.goods_id  商品ID
     * @apiSuccess {String} result.consult_list.goods_name  商品名称
     * @apiSuccess {Int} result.consult_list.member_id  用户ID
     * @apiSuccess {String} result.consult_list.member_name  用户名称
     * @apiSuccess {Int} result.consult_list.store_id  店铺ID
     * @apiSuccess {String} result.consult_list.store_name  店铺名称
     * @apiSuccess {Int} result.consult_list.consulttype_id  咨询类型ID
     * @apiSuccess {String} result.consult_list.consult_content  咨询内容
     * @apiSuccess {Int} result.consult_list.consult_addtime  咨询时间，Unix时间戳
     * @apiSuccess {String} result.consult_list.consult_reply  回复内容
     * @apiSuccess {Int} result.consult_list.consult_replytime  回复时间，Unix时间戳
     * @apiSuccess {Int} result.consult_list.consult_isanonymous  是否匿名
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function consulting_list() {

        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }

        //得到商品咨询信息
        $consult_model = model('consult');
        $condition = array();
        $condition[] = array('goods_id', '=', $goods_id);

        if (intval(input('param.ctid')) > 0) {
            $condition[] = array('consulttype_id', '=', intval(input('param.ctid')));
        }
        $consult_list = $consult_model->getConsultList($condition, '*');

        $result = array_merge(array('consult_list' => $consult_list), mobile_page($consult_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/Goods/save_consult 商品咨询添加
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {String} goods_content 咨询内容
     * @apiParam {Int} consult_type_id 咨询类型ID
     * @apiParam {String} key 用户授权token
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function save_consult() {
        $member_id = $this->getMemberIdIfExists();
        //检查是否可以评论
        if (!config('ds_config.guest_comment') && !$member_id) {
            ds_json_encode(10001, lang('goods_index_goods_noallow'));
        }
        $goods_id = intval(input('post.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        //咨询内容的非空验证
        if (trim(input('post.goods_content')) == "") {
            ds_json_encode(10001, lang('goods_index_input_consult'));
        }
        //表单验证
        $data = [
            'goods_content' => input('post.goods_content')
        ];
        $res=word_filter($data['goods_content']);
        if(!$res['code']){
            ds_json_encode(10001,$res['msg']);
        }
        $data['goods_content']=$res['data']['text'];
        $goods_validate = ds_validate('goods');
        if (!$goods_validate->scene('save_consult')->check($data)) {
            ds_json_encode(10001, $goods_validate->getError());
        }


        //判断商品编号的存在性和合法性
        $goods = model('goods');
        $goods_info = $goods->getGoodsInfoByID($goods_id);
        if (empty($goods_info)) {
            ds_json_encode(10001, lang('goods_index_goods_not_exists'));
        }

        if ($member_id) {
            //查询会员信息
            $member_model = model('member');
            $member_info = $member_model->getMemberInfo(array('member_id' => $member_id));
            if (empty($member_info) || $member_info['is_allowtalk'] == 0) {
                ds_json_encode(10001, lang('goods_index_goods_noallow'));
            }
            $seller_model = model('seller');
            $seller_info = $seller_model->getSellerInfo(array('member_id' => $member_id));
            //判断是否是店主本人
            if ($seller_info && $goods_info['store_id'] == $seller_info['store_id']) {
                ds_json_encode(10001, lang('goods_index_consult_store_error'));
            }
        }

        //检查机构状态
        $store_model = model('store');
        $store_info = $store_model->getStoreInfoByID($goods_info['store_id']);
        if ($store_info['store_state'] == '0' || intval($store_info['store_state']) == '2' || (intval($store_info['store_endtime']) != 0 && $store_info['store_endtime'] <= TIMESTAMP)) {
            ds_json_encode(10001, lang('goods_index_goods_store_closed'));
        }
        //接收数据并保存
        $input = array();
        $input['goods_id'] = $goods_id;
        $input['goods_name'] = $goods_info['goods_name'];
        $input['member_id'] = intval($member_id) > 0 ? $member_id : 0;
        $input['member_name'] = isset($member_info) ? $member_info['member_name'] : '';
        $input['store_id'] = $store_info['store_id'];
        $input['store_name'] = $store_info['store_name'];
        $input['consulttype_id'] = intval(input('post.consult_type_id', 1));
        $input['consult_addtime'] = TIMESTAMP;
        $input['consult_content'] = $data['goods_content'];
        $input['consult_isanonymous'] = input('post.hide_name') == 'hide' ? 1 : 0;
        $consult_model = model('consult');
        if ($consult_model->addConsult($input)) {
            ds_json_encode(10000, lang('goods_index_consult_success'));
        } else {
            ds_json_encode(10001, lang('goods_index_consult_fail'));
        }
    }

    /**
     * 记录浏览历史
     */
    public function addbrowse() {
        $goods_id = intval(input('param.gid'));
        model('goodsbrowse')->addViewedGoods($goods_id, $this->member_info['member_id'], $this->store_info['store_id']);
        exit();
    }

    /**
     * 商品详细信息处理
     */
    private function _goods_detail_extend($goods_detail) {

        //商品链接
        $goods_detail['goods_info']['goods_image'] = goods_cthumb($goods_detail['goods_info']['goods_image'], 1260, $goods_detail['goods_info']['store_id']);


        //整理数据
        unset($goods_detail['goods_info']['gc_id']);
        unset($goods_detail['goods_info']['gc_name']);
        unset($goods_detail['goods_info']['store_id']);
        unset($goods_detail['goods_info']['store_name']);
        unset($goods_detail['goods_info']['brand_id']);
        unset($goods_detail['goods_info']['brand_name']);
        unset($goods_detail['goods_info']['type_id']);
        unset($goods_detail['goods_info']['goods_body']);
//        unset($goods_detail['goods_info']['goods_state']);
        unset($goods_detail['goods_info']['goods_stateremark']);
//        unset($goods_detail['goods_info']['goods_verify']);
        unset($goods_detail['goods_info']['goods_verifyremark']);
        unset($goods_detail['goods_info']['goods_lock']);
        unset($goods_detail['goods_info']['goods_addtime']);
        unset($goods_detail['goods_info']['goods_edittime']);
        unset($goods_detail['goods_info']['goods_shelftime']);
        unset($goods_detail['goods_info']['goods_show']);
        unset($goods_detail['goods_info']['goods_commend']);
        unset($goods_detail['goods_info']['explain']);
        unset($goods_detail['goods_info']['buynow_text']);

        return $goods_detail;
    }

    /**
     * @api {POST} api/Goods/goods_evaluate 商品评论
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {Int} type 类型 1好评 2中评 3差评
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_eval_list  评论列表 （返回字段参考evaluategoods）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function goods_evaluate() {
        $goods_id = intval(input('param.goods_id'));
        $type = intval(input('param.type'));

        $condition = array();
        $condition[] = array('geval_goodsid', '=', $goods_id);
        switch ($type) {
            case '1':
                $condition[] = array('geval_scores', 'in', '5,4');
                break;
            case '2':
                $condition[] = array('geval_scores', 'in', '3,2');
                break;
            case '3':
                $condition[] = array('geval_scores', 'in', '1');
                break;
            case '4':
                //$condition[] = array('geval_image|geval_image_again','<>', '');  //追加评价带后续处理
                $condition[] = array('geval_image', '<>', '');
                break;
            case '5':
                $condition[] = array('geval_content_again', '<>', '');
                break;
        }

        //查询商品评分信息
        $evaluategoods_model = model('evaluategoods');
        $goods_eval_list = $evaluategoods_model->getEvaluategoodsList($condition, $this->pagesize);
        foreach ($goods_eval_list as $k => $val) {
            if ($val['geval_isanonymous']) {
                $goods_eval_list[$k]['member_avatar'] = get_member_avatar_for_id(0);
                $goods_eval_list[$k]['geval_frommembername'] = str_cut($val['geval_frommembername'], 2) . '***';
            }
            if (!empty($goods_eval_list[$k]['geval_image'])) {
                $goods_eval_list[$k]['geval_image'] = explode(',', $goods_eval_list[$k]['geval_image']);
                foreach ($goods_eval_list[$k]['geval_image'] as $kk => $vv) {
                    $store_id = substr($vv, 0, strpos($vv, '_'));
                    $goods_eval_list[$k]['geval_image'][$kk] = ds_get_pic(ATTACH_MALBUM . '/' . $store_id , $vv);
                }
            }
        }
        $goods_eval_list = model('memberevaluate', 'logic')->evaluateListDity($goods_eval_list);
        $result = array_merge(array('goods_eval_list' => $goods_eval_list), mobile_page(is_object($evaluategoods_model->page_info) ? $evaluategoods_model->page_info : 0));
        ds_json_encode(10000, '', $result);
    }

    /*
     * 下载课件
     */

    public function download() {
        $file_id = input('param.file_id');
        $file_name = input('param.file_name');
        $goodscourses_id = intval(input('param.goodscourses_id'));
        $goods_id = intval(input('param.goods_id'));
        if (!$file_id || !$goodscourses_id || !$goods_id || !$file_name) {
            $this->error(lang('param_error'));
        }
        $condition = array();
        $condition[] = array('goodscourses_id', '=', $goodscourses_id);
        $condition[] = array('goods_id', '=', $goods_id);
        $goodscourses_model = model('goodscourses');
        $goodscourses = $goodscourses_model->getOneGoodscourses($condition);
        if (!$goodscourses) {
            $this->error('课程不存在');
        }
        $goodscourses['baidu_pan_fsids'] = json_decode($goodscourses['baidu_pan_fsids'], true);
        if (empty($goodscourses['baidu_pan_fsids'])) {
            $this->error('课件不存在');
        }
        $store_model = model('store');
        $store_info = $store_model->getStoreInfoByID($goodscourses['store_id']);
        if (!$store_info) {
            $this->error('店铺不存在');
        }
        $if_have_buy = $this->_check_buy_goods($goods_id);

        if ($goodscourses['goodscourses_free'] || $if_have_buy) {

            $access_token = $store_info['baidu_pan_access_token'];

            $prefix = 'baidu_pan_file-';
            $result = rcache($file_id, $prefix);

            if (empty($result)) {

                $res = http_request('https://pan.baidu.com/rest/2.0/xpan/multimedia?method=filemetas&access_token=' . $access_token . '&dlink=1&fsids=' . json_encode(array_keys($goodscourses['baidu_pan_fsids'])));
                $res = json_decode($res, true);
                if (isset($res['errno']) && $res['errno']) {
                    $this->error('查询文件信息出错，错误码' . $res['errno']);
                }
                foreach ($res['list'] as $val) {
                    if ($val['fs_id'] == $file_id) {
                        $result=$val;
                        wcache($val['fs_id'], $val, $prefix, 3600 * 7); //dlink有效期为8小时
                        $dlink = $val['dlink'];
                    }
                }
                if (!isset($dlink)) {
                    $this->error('文件不存在');
                }
            } else {
                $dlink = $result['dlink'];
            }

            $data = http_request($dlink . '&access_token=' . $access_token, "GET", null, array("User-Agent:pan.baidu.com"));
            header('HTTP/1.1 200 OK');
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header("Content-Length: " . $result['size']);
            echo $data;
            exit;
        } else {
            $this->error('您没有下载权限');
        }
    }

}
