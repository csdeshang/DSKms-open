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
class  Search extends BaseMall {

    //每页显示商品数
    const PAGESIZE = 12;

    //模型对象
    private $_model_search;

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/search.lang.php');
    }

    public function index() {

        $this->_model_search = model('search');
        //显示左侧分类
        //默认分类，从而显示相应的属性和品牌
        $cate_id = $default_classid = intval(input('param.cate_id'));
        $keyword = input('param.keyword');
        $goods_class_array = array();
        if ($default_classid > 0) {
            $goods_class_array = $this->_model_search->getLeftCategory(array($default_classid));
        } elseif ($keyword != '') {
            //从TAG中查找分类
            $goods_class_array = $this->_model_search->getTagCategory($keyword);
            //取出第一个分类作为默认分类，从而显示相应的属性和品牌
            $default_classid = isset($goods_class_array[0]) ? $goods_class_array[0] : "";
            $goods_class_array = $this->_model_search->getLeftCategory($goods_class_array, 1);
        }

        View::assign('goods_class_array', $goods_class_array);
        View::assign('default_classid', $default_classid);


        //处理排序
        $order = 'mall_goods_commend desc,mall_goods_sort asc';
        $key = input('param.key');
        $sequence = input('param.order');
        if (in_array($key, array('1', '2', '3'))) {
            $sequence = $sequence == '1' ? 'asc' : 'desc';
            $order = str_replace(array('1', '2', '3'), array('goods_salenum', 'goods_click', 'goods_price'), $key);
            $order .= ' ' . $sequence;
        }
        $goods_model = model('goods');
        // 字段
        $fields = "goods_id,goods_name,goods_advword,gc_id,store_id,store_name,goods_price,goods_image,goods_salenum,evaluation_good_star,evaluation_count";

        
        $class_array = model('goodsclass')->getGoodsclassForCacheModel();
        $goods_param['class'] = !empty(input('param.cate_id'))&&isset($class_array[input('param.cate_id')]) ? $class_array[input('param.cate_id')]:array();
        
        $condition = array();
        //执行正常搜索
        if (isset($goods_param['class']['depth'])) {
            $condition[] = array('gc_id_'.$goods_param['class']['depth'],'=',$goods_param['class']['gc_id']);
        }
        if ($keyword != '') {
            $condition[] = array('goods_name|goods_advword','like','%' . $keyword . '%');
        }

        $priceMin = intval(input('param.priceMin'));
        if ($priceMin > 0) {
            $condition[] = array('goods_price','>=',$priceMin);
        }
        $priceMax = intval(input('param.priceMax'));
        if ($priceMax > 0) {
            $condition[] = array('goods_price','<=',$priceMax);
        }

        if ($priceMin > 0 && $priceMax > 0) {
            $condition[] = array('goods_price','between',array($priceMin, $priceMax));
        }
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fields, $order, self::PAGESIZE);
        View::assign('show_page', is_object($goods_model->page_info) ? $goods_model->page_info->render() : "");

        // 商品多图
        if (!empty($goods_list)) {
            $storeid_array = array();       // 机构id数组
            foreach ($goods_list as $value) {
                $storeid_array[] = $value['store_id'];
            }
            $storeid_array = array_unique($storeid_array);

            // 机构
            $store_list = model('store')->getStoreMemberIDList($storeid_array);
            //搜索的关键字
            $search_keyword = $keyword;
            foreach ($goods_list as $key => $value) {
                // 机构的开店会员编号
                $store_id = $value['store_id'];
                $goods_list[$key]['member_id'] = $store_list[$store_id]['member_id'];
                //将关键字置红
                if ($search_keyword) {
                    $goods_list[$key]['goods_name_highlight'] = str_replace($search_keyword, '<font style="color:#f00;">' . $search_keyword . '</font>', $value['goods_name']);
                } else {
                    $goods_list[$key]['goods_name_highlight'] = $value['goods_name'];
                }
            }
        }
        View::assign('goods_list', $goods_list);
        if ($keyword != '') {
            View::assign('show_keyword', $keyword);
        } else {
            View::assign('show_keyword', isset($goods_param['class']['gc_name']) ? $goods_param['class']['gc_name'] : '');
        }

        $goodsclass_model = model('goodsclass');

        // SEO
        if ($keyword == '') {
            $seo_class_name = isset($goods_param['class']['gc_name']) ? $goods_param['class']['gc_name'] : '';
            if (is_numeric($cate_id) && empty($keyword)) {
                $seo_info = $goodsclass_model->getKeyWords($cate_id);
                if (empty($seo_info[1])) {
                    $seo_info[1] = config('ds_config.site_name') . ' - ' . $seo_class_name;
                }
                $seo = model('seo')->type($seo_info)->param(array('name' => $seo_class_name))->show();
                $this->_assign_seo($seo);
            }
        } elseif ($keyword != '') {
            $keyword=urldecode($keyword);
            View::assign('html_title', (empty($keyword) ? '' : $keyword . ' - ') . config('ds_config.site_name') . lang('ds_common_search'));
        }

        // 当前位置导航
        $nav_link_list = $goodsclass_model->getGoodsclassnav($cate_id);
        View::assign('nav_link_list', $nav_link_list);
        
        // 得到自定义导航信息
        $nav_id = intval(input('param.nav_id'));
        View::assign('index_sign', $nav_id);


        /* 引用搜索相关函数 */
        require_once(base_path() . '/home/common_search.php');

        // 浏览过的商品
        $viewed_goods = model('goodsbrowse')->getViewedGoodsList(session('member_id'), 20);
        View::assign('viewed_goods', $viewed_goods);

        return View::fetch($this->template_dir . 'search');
    }

    /**
     * 获得推荐商品
     */
    public function get_hot_goods() {
        $gc_id = input('param.cate_id');
        if ($gc_id <= 0) {
            exit;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = model('goodsclass')->getGoodsclassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            exit;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);

        $goodsid_array = array();

        $fieldstr = "goods_id,goods_name,goods_advword,store_id,store_name,goods_price,goods_image,goods_salenum,evaluation_count";
        $goods_list = model('goods')->getGoodsOnlineList(array(array('goods_id','in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            exit;
        }

        View::assign('goods_list', $goods_list);
        echo View::fetch($this->template_dir . 'goods_hot');
    }

    /**
     * 获得同类商品排行
     */
    public function get_listhot_goods() {
        $gc_id = input('param.cate_id');
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = model('goodsclass')->getGoodsclassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);

        $goodsid_array = array();

        $fieldstr = "goods_id,goods_name,goods_advword,store_id,store_name,goods_price,goods_image,goods_salenum,evaluation_count";
        $goods_list = model('goods')->getGoodsOnlineList(array(array('goods_id','in', $goodsid_array)), $fieldstr, 5, 'goods_salenum desc');
        if (empty($goods_list)) {
            return false;
        }

        View::assign('goods_list', $goods_list);
    }

    /**
     * 获得猜你喜欢
     */
    public function get_guesslike() {
        $goodslist = model('goodsbrowse')->getGuessLikeGoods(session('member_id'), 20);
        if (!empty($goodslist)) {
            View::assign('goodslist', $goodslist);
            echo View::fetch($this->template_dir . 'goods_guesslike');
        }
    }

}

?>
