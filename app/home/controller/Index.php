<?php

namespace app\home\controller;
use think\facade\View;
use think\facade\Lang;
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
 * 控制器
 */
class  Index extends BaseMall {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/'.config('lang.default_lang').'/index.lang.php');
    }

    public function index() {
        View::assign('index_sign', 'index');

        $this->getIndexData();
        //楼层广告
        $result=false;
        $condition=array();
        $condition[]=['ap_id','=',1];
        $condition[]=['adv_enabled','=',1];
        $condition[]=['adv_startdate','<',strtotime(date('Y-m-d H:00:00'))];
        $condition[]=['adv_enddate','>',strtotime(date('Y-m-d H:00:00'))];
        $adv_list=model('adv')->getAdvList($condition,'',10,'adv_sort asc,adv_id asc');
        if(!empty($adv_list)){
            $result=$adv_list;
        }
        View::assign('adv_index_1', $result);
        $this->getFloorList();
        //显示订单信息
        if (session('is_login')) {
            //交易提醒 - 显示数量
            $vrorder_model = model('vrorder');
            $favorites_model = model('favorites');
            $index_member_info['favoritesgoods_count'] = $favorites_model->getGoodsFavoritesCountByMemberId(session('member_id'));#搜藏的商品数
            $condition = array();
            $condition[] = array('buyer_id','=',session('member_id'));
            $condition[] = array('order_state','in', array( ORDER_STATE_PAY,ORDER_STATE_SUCCESS));
            $index_member_info['order_noreceipt_count'] = $vrorder_model->getVrorderCount($condition);#获取已付款,或已完成的商品
            View::assign('index_member_info', $index_member_info);
        }
        //SEO 设置
        $seo = model('seo')->type('index')->show();
        $this->_assign_seo($seo);
        return View::fetch($this->template_dir . 'index');
    }
    
    private function getIndexData()
    {
        $index_data = rcache("index_data");
        if (empty($index_data)) {
            $index_data = array();
            //友情链接
            $index_data['link_list'] = model('link')->getLinkList();
            //获取第一文章分类的前三篇文章
            $index_data['index_articles'] = Db::name('article')->where('ac.ac_code', 'notice')->where('a.article_show', 1)->alias('a')->field('a.article_id,a.article_url,a.article_title')->order('a.article_sort asc,a.article_time desc')->limit(3)->join('articleclass ac', 'a.ac_id=ac.ac_id')->select()->toArray();
            wcache('index_data',$index_data);
        }
        View::assign('link_list', $index_data['link_list']);
        View::assign('index_articles', $index_data['index_articles']);
    }
    

    private function getFloorList() {
        $prefix = 'home-index-floor';
        $floor_block = rcache($prefix);
        if (empty($floor_block)) {
            $goodsclass_list = Db::name('goodsclass')->where('gc_parent_id', 0)->where('gc_show', 1)->order('gc_sort asc')->select()->toArray();
            $key = 0;
            foreach ($goodsclass_list as $goodsclass) {
                $condition=model('goods')->_getRecursiveClass(array(),$goodsclass['gc_id']);
                $floor_list = model('goods')->getGoodsOnlineList($condition, '*', 0, 'mall_goods_commend desc,mall_goods_sort asc', 10, 'goods_id');
                if (!empty($floor_list)) {
                    $floor_block[$key]['gc_list'] = $floor_list;
                    $floor_block[$key]['gc_name'] = $goodsclass['gc_name'];
                    $key++;
                }
            }
            wcache($prefix, $floor_block,'', 3600);
        }
        //楼层数据
        View::assign('floor_block', $floor_block);
    }

    //json输出商品分类
    public function josn_class() {
        /**
         * 实例化商品分类模型
         */
        $goodsclass_model = model('goodsclass');
        $goods_class = $goodsclass_model->getGoodsclassListByParentId(intval(input('get.gc_id')));
        $array = array();
        if (is_array($goods_class) and count($goods_class) > 0) {
            foreach ($goods_class as $val) {
                $array[$val['gc_id']] = array(
                    'gc_id' => $val['gc_id'], 'gc_name' => htmlspecialchars($val['gc_name']),
                    'gc_parent_id' => $val['gc_parent_id'], 'commis_rate' => $val['commis_rate'],
                    'gc_sort' => $val['gc_sort']
                );
            }
        }

        echo input('param.callback') . '(' . json_encode($array) . ')';
    }

    //判断是否登录
    public function login() {
        echo (session('is_login') == '1') ? '1' : '0';
    }

    /**
     * 查询每月的周数组
     */
    public function getweekofmonth() {
        include_once root_path(). 'extend/mall/datehelper.php';
        $year = input('get.y');
        $month = input('get.m');
        $week_arr = getMonthWeekArr($year, $month);
        echo json_encode($week_arr);
        die;
    }

    /**
     * 头部最近浏览的商品
     */
    public function viewed_info() {
        $info = array();
        if (session('is_login') == '1') {
            $member_id = session('member_id');
            $info['m_id'] = $member_id;
            if (config('ds_config.voucher_allow') == 1) {
                $time_to = TIMESTAMP; //当前日期
                $condition = array();
                $condition[] = array('voucher_owner_id','=',$member_id);
                $condition[] = array('voucher_state','=',1);
                $condition[] = array('voucher_startdate','<=',$time_to);
                $condition[] = array('voucher_enddate','>=',$time_to);
                $info['voucher'] = Db::name('voucher')->where($condition)->count();
            }
            $time_to = strtotime(date('Y-m-d')); //当前日期
            $time_from = date('Y-m-d', ($time_to - 60 * 60 * 24 * 7)); //7天前
            $consult_mod=model('consult');
            $condition = array();
            $condition[] = array('member_id','=',$member_id);
            $condition[] = array('consult_replytime','>',strtotime($time_from));
            $condition[] = array('consult_replytime','<',$time_to + 60 * 60 * 24);
            $info['consult'] = $consult_mod->getConsultCount($condition);
        }
        $goods_list = model('goodsbrowse')->getViewedGoodsList(session('member_id'), 5);
        if (is_array($goods_list) && !empty($goods_list)) {
            $viewed_goods = array();
            foreach ($goods_list as $key => $val) {
                $goods_id = $val['goods_id'];
                $val['url'] = (string)url('Goods/index', ['goods_id' => $goods_id]);
                $val['goods_image'] = goods_thumb($val, 270);
                $viewed_goods[$goods_id] = $val;
            }
            $info['viewed_goods'] = $viewed_goods;
        }
        echo json_encode($info);
    }
    /**
     * json输出地址数组 public/static/plugins/area_datas.js
     */
    public function json_area() {
        echo input('param.callback') . '(' . json_encode(model('area')->getAreaArrayForJson()) . ')';
    }
}
