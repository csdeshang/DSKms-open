<?php

namespace app\api\controller;
use think\facade\View;
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
 * 店铺控制器
 */
class  Store extends MobileMall {

    public function initialize() {
        parent::initialize();
    }


    /**
     * @api {POST} api/Store/store_list 店铺列表
     * @apiVersion 1.0.0
     * @apiGroup Store
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {Int} cate_id 分类ID
     * @apiParam {String} keyword 关键词
     * @apiParam {String} user_name 用户名
     * @apiParam {String} area_info 地区
     * @apiParam {String} order 排序 desc降序 asc升序
     * @apiParam {String} sort_key 排序字段 store_credit店铺信用 store_sales销量
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.store_list  店铺列表 （返回字段参考store）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function store_list() {

        //机构类目快速搜索

        $class_list = rkcache('storeclass', true, 'file');

        $cate_id = intval(input('param.cate_id'));


        if (!key_exists($cate_id, $class_list))
            $cate_id = 0;

        View::assign('class_list', $class_list);

        //机构搜索
        $condition = array();
        $keyword = trim(input('param.keyword'));

            if ($keyword != '') {
                $condition[] = array('store_name|store_mainbusiness','like','%' . $keyword . '%');
            }
            $user_name = trim(input('param.user_name'));
            if ($user_name != '') {
                $condition[] = array('member_name','=',$user_name);
            }
   
        $area_info = trim(input('param.area_info'));
        if (!empty($area_info)) {
            //修复机构按地区搜索
            $tabs = preg_split("#\s+#", $area_info, -1, PREG_SPLIT_NO_EMPTY);
            $len = count($tabs);
            $area_name = $tabs[$len - 1];
            if ($area_name) {
                $area_name = trim($area_name);
                $condition[] = array('area_info','like','%' . $area_name . '%');
            }
        }
        if ($cate_id > 0) {
            $condition[] = array('storeclass_id','=',$cate_id);
        }

        $condition[] = array('store_state','=',1);

        $order = trim(input('param.order'));
        if (!in_array($order, array('desc', 'asc'))) {
            unset($order);
        }


        $order_sort = 'store_sort asc';
        //信用度排序
        $key = trim(input('param.sort_key'));
        switch($key){
            case 'store_sales':
                $order_sort = 'store_sales desc';
                break;
            case 'store_credit':
                $order_sort = 'store_credit desc';
                break;
        }
        $store_model = model('store');
        $store_list = $store_model->getStoreList($condition,10,$order_sort);
        //获取机构商品数，推荐商品列表等信息
        $store_list = $store_model->getStoreSearchList($store_list);
        

        $memberId = $this->getMemberIdIfExists();
        foreach($store_list as $key => $val){
            // 如果已登录 判断该店铺是否已被收藏
            if ($memberId) {
                $c = (int) model('favorites')->getStoreFavoritesCountByStoreId($val['store_id'], $memberId);
                $store_list[$key]['is_favorate'] = $c > 0;
            } else {
                $store_list[$key]['is_favorate'] = false;
            }
            $store_list[$key]['goods_list']=model('goods')->getGoodsListByColorDistinct(array(array('store_id','=',$val['store_id']),array('goods_commend','=',1)), 'goods_image,goods_id,goods_price', 'goods_id desc', 0, 4);
            foreach($store_list[$key]['goods_list'] as $k => $v){
                $store_list[$key]['goods_list'][$k]['goods_image_url']=goods_cthumb($v['goods_image'], 480, $val['store_id']);
            }
            $store_list[$key]['store_avatar'] = get_store_logo($store_list[$key]['store_avatar']);
        }
     
        $result = array_merge(array('store_list' => $store_list), mobile_page($store_model->page_info));
        ds_json_encode(10000, '', $result);
    }
    
    /**
     * @api {POST} api/Store/store_info 店铺信息
     * @apiVersion 1.0.0
     * @apiGroup Store
     *
     * @apiParam {Int} store_id 店铺ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.rec_goods_list  推荐商品列表
     * @apiSuccess {Int} result.rec_goods_list.evaluation_count  评论数
     * @apiSuccess {Int} result.rec_goods_list.evaluation_good_star  评分
     * @apiSuccess {Int} result.rec_goods_list.goods_addtime  添加时间
     * @apiSuccess {Int} result.rec_goods_list.goods_id  商品ID
     * @apiSuccess {String} result.rec_goods_list.goods_image  商品图片名称
     * @apiSuccess {String} result.rec_goods_list.goods_image_url  商品图片完整路径
     * @apiSuccess {Float} result.rec_goods_list.goods_marketprice  市场价
     * @apiSuccess {String} result.rec_goods_list.goods_name  商品名称
     * @apiSuccess {Float} result.rec_goods_list.goods_price  商品价格
     * @apiSuccess {Int} result.rec_goods_list.goods_salenum  销量
     * @apiSuccess {Boolean} result.rec_goods_list.group_flag  是否抢购 false否true是
     * @apiSuccess {Int} result.rec_goods_list.is_goodsfcode  是否F码商品 0否1是
     * @apiSuccess {Int} result.rec_goods_list.is_have_gift  是否含赠品 0否1是
     * @apiSuccess {Int} result.rec_goods_list.is_presell  是否预售 0否1是
     * @apiSuccess {Int} result.rec_goods_list.is_virtual  是否虚拟商品 0否1是
     * @apiSuccess {Int} result.rec_goods_list.store_id  店铺ID
     * @apiSuccess {String} result.rec_goods_list.store_name  店铺名称
     * @apiSuccess {Boolean} result.rec_goods_list.xianshi_flag  是否显示 false否true是
     * @apiSuccess {Int} result.rec_goods_list_count  推荐商品数
     * @apiSuccess {Object} result.store_info  店铺信息
     * @apiSuccess {Int} result.store_info.goods_count  商品数
     * @apiSuccess {Boolean} result.store_info.is_favorate  已收藏 false否true是
     * @apiSuccess {String[]} result.store_info.mb_sliders  轮播图
     * @apiSuccess {String} result.store_info.mb_title_img  背景图
     * @apiSuccess {Int} result.store_info.member_id  用户ID
     * @apiSuccess {String} result.store_info.store_avatar  店铺图像
     * @apiSuccess {Int} result.store_info.store_collect  店铺收藏数
     * @apiSuccess {String} result.store_info.store_credit_text  信用描述
     * @apiSuccess {Int} result.store_info.store_id  店铺ID
     * @apiSuccess {String} result.store_info.store_name 店铺名称
     * @apiSuccess {Object[]} result.store_info.store_credit  店铺评分
     * @apiSuccess {String} result.store_info.store_company_name  店铺公司名称
     * @apiSuccess {String} result.store_info.area_info  店铺地址
     * @apiSuccess {String} result.store_info.store_phone  店铺商家电话
     * @apiSuccess {String} result.store_info.store_mainbusiness  店铺主营商品
     * @apiSuccess {String} result.store_info.seller_name  店铺店主用户名
     * @apiSuccess {String} result.store_info.store_qq  店铺QQ
     * @apiSuccess {String} result.store_info.store_ww  店铺旺旺
     */
    public function store_info() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001,'参数错误');
        }
        $store_online_info = model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            ds_json_encode(10001,'机构不存在或未开启');
        }

        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        $store_info['member_id'] = $store_online_info['member_id'];
        $store_info['store_credit'] = $store_online_info['store_credit'];
        $store_info['store_company_name'] = $store_online_info['store_company_name'];
        $store_info['area_info'] = $store_online_info['area_info'];
        $store_info['store_phone'] = $store_online_info['store_phone'];
        $store_info['store_mainbusiness'] = $store_online_info['store_mainbusiness'];
        $store_info['seller_name'] = $store_online_info['seller_name'];
        $store_info['store_qq'] = $store_online_info['store_qq'];
        $store_info['store_ww'] = $store_online_info['store_ww'];
        $storejoinin_model=model('storejoinin');
            $storejoinin_info=$storejoinin_model->getOneStorejoinin(array('member_id'=>$store_info['member_id']));
            //营业执照
            if($storejoinin_info){
                $store_info['business_licence_number_electronic']=$storejoinin_info['business_licence_number_electronic']?get_store_joinin_imageurl($storejoinin_info['business_licence_number_electronic']):'';
            }  
        

        // 机构头像
        $store_info['store_avatar'] = get_store_logo($store_online_info['store_avatar']);
        // 商品数
        $store_info['goods_count'] = (int) $store_online_info['goods_count'];

        // 机构被收藏次数
        $store_info['store_collect'] = (int) $store_online_info['store_collect'];

        // 如果已登录 判断该机构是否已被收藏
        if ($memberId = $this->getMemberIdIfExists()) {
            $c = (int) model('favorites')->getStoreFavoritesCountByStoreId($store_id, $memberId);
            $store_info['is_favorate'] = $c > 0;
        } else {
            $store_info['is_favorate'] = false;
        }


        // 动态评分
            $store_info['store_credit_text'] = sprintf(
                    '描述: %0.1f, 服务: %0.1f, 物流: %0.1f', $store_online_info['store_credit']['store_desccredit']['credit'], $store_online_info['store_credit']['store_servicecredit']['credit'], $store_online_info['store_credit']['store_deliverycredit']['credit']
            );

        // 页头背景图
        $store_info['mb_title_img'] = !empty($store_online_info['mb_title_img']) ? ds_get_pic( ATTACH_STORE , $store_online_info['mb_title_img']) : '';

        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array) $mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = ds_get_pic( ATTACH_STORE . DIRECTORY_SEPARATOR . 'mobileslide' , $s['img']);
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }

        //店主推荐
        $condition = array();
        $condition[] = array('store_id','=',$store_id);
        $condition[] = array('goods_commend','=',1);
        $goods_model = model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $goods_fields, 'goods_id desc', 0, 20);
        
        foreach ($goods_list as $key => $value) {
            //商品图片url
            $goods_list[$key]['goods_image_url'] = goods_cthumb($value['goods_image'], 270, $value['store_id']);
        }
        
        $result = array(
            'store_info' => $store_info,
            'rec_goods_list_count' => count($goods_list),
            'rec_goods_list' => $goods_list,
        );
        ds_json_encode(10000, '',$result);
    }

    /**
     * @api {POST} api/Store/store_goods_class 机构商品分类
     * @apiVersion 1.0.0
     * @apiGroup Store
     *
     * @apiParam {Int} store_id 机构ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function store_goods_class() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001,'参数错误');
        }
        $store_online_info = model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            ds_json_encode(10001,'机构不存在或未开启');
        }

        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];

        $store_goods_class = model('storegoodsclass')->getStoregoodsclassList(array('store_id'=>$store_id));
        if($store_goods_class){
            $tree = new \mall\Tree();
        $tree->setTree($store_goods_class, 'storegc_id', 'storegc_parent_id', 'storegc_name');
        $store_goods_class = $tree->getArrayList();
        }
        $result = array(
            'store_info' => $store_info,
            'store_goods_class' => $store_goods_class
        );
        ds_json_encode(10000, '',$result);
    }

    /**
     * @api {POST} api/Store/store_goods_class 机构商品
     * @apiVersion 1.0.0
     * @apiGroup Store
     *
     * @apiParam {Int} store_id 机构ID
     * @apiParam {Int} storegc_id 机构分类ID
     * @apiParam {String} keyword 关键词
     * @apiParam {String} prom_type 促销类型 
     * @apiParam {Float} price_from 价格从
     * @apiParam {Float} price_to 价格到
     * @apiParam {Int} key 排序键 1商品id 2促销价 3销量 4收藏量  5点击量
     * @apiParam {Int} order 排序值 1升序 2降序
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function store_goods() {
        $param = input('param.');

        $store_id = (int) $param['store_id'];
        if ($store_id <= 0) {
            ds_json_encode(10001,'参数错误');
        }
        $storegc_id = isset($param['storegc_id'])?(int) $param['storegc_id']:'';
        $keyword = isset($param['keyword'])?trim((string) $param['keyword']):'';

        $condition = array();
        $condition[] = array('store_id','=',$store_id);

        // 默认不显示预订商品

        if ($storegc_id > 0) {
            $condition[] = array('goods_stcids','like', '%,' . $storegc_id . ',%');
        }
        if ($keyword != '') {
            $condition[] = array('goods_name','like','%' . $keyword . '%');
        }
        $price_from = preg_match('/^[\d.]{1,20}$/', isset($param['price_from'])) ? $param['price_from'] : null;
        $price_to = preg_match('/^[\d.]{1,20}$/', isset($param['price_to'])) ? $param['price_to'] : null;
        if ($price_from && $price_from) {
            $condition[] = array('goods_promotion_price', 'between', "{$price_from},{$price_to}");
        } elseif ($price_from) {
            $condition[] = array('goods_promotion_price', '>=', $price_from);
        } elseif ($price_to) {
            $condition[] = array('goods_promotion_price', '<=', $price_to);
        }

        // 排序
        $order = (isset($param['sort_order'])&&(int) $param['sort_order'] == 1) ? 'asc' : 'desc';
        if(isset($param['sort_key'])) {
            switch (trim($param['sort_key'])) {
                case '1':
                    $order = 'goods_id ' . $order;
                    break;
                case '2':
                    $order = 'goods_price ' . $order;
                    break;
                case '3':
                    $order = 'goods_salenum ' . $order;
                    break;
                case '4':
                    $order = 'goods_collect ' . $order;
                    break;
                case '5':
                    $order = 'goods_click ' . $order;
                    break;
                default:
                    $order = 'goods_id DESC';
                    break;
            }
        }else{
            $order = 'goods_id DESC';
        }
        $goods_model = model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $goods_fields, $order, 10);
        foreach ($goods_list as $key => $value) {
            //商品图片url
            $goods_list[$key]['goods_image_url'] = goods_cthumb($value['goods_image'], 270, $value['store_id']);
        }
        $result = array_merge(array('goods_list_count' => count($goods_list),'goods_list' => $goods_list,), mobile_page($goods_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    private function getGoodsFields() {
        return implode(',', array(
            'goods_id',
            'store_id',
            'store_name',
            'goods_name',
            'goods_price',
            'goods_image',
            'goods_salenum',
            'evaluation_good_star',
            'evaluation_count',
            'goods_addtime',
        ));
    }


    /**
     * 商品评价
     */
    public function store_credit() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001,'参数错误');
        }
        $store_online_info = model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            ds_json_encode(10001,'机构不存在或未开启');
        }
        ds_json_encode(10000, '',array('store_credit' => $store_online_info['store_credit']));
    }

    /**
     * 机构商品排行
     */
    public function store_goods_rank() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001,'查询出错');
        }
        $ordertype = ($t = trim(input('param.ordertype'))) ? $t : 'salenumdesc';
        $show_num = ($t = intval(input('param.num'))) > 0 ? $t : 10;

        $condition = array();
        $condition[] = array('store_id','=',$store_id);
        // 排序
        switch ($ordertype) {
            case 'salenumdesc':
                $order = 'goods_salenum desc';
                break;
            case 'salenumasc':
                $order = 'goods_salenum asc';
                break;
            case 'collectdesc':
                $order = 'goods_collect desc';
                break;
            case 'collectasc':
                $order = 'goods_collect asc';
                break;
            case 'clickdesc':
                $order = 'goods_click desc';
                break;
            case 'clickasc':
                $order = 'goods_click asc';
                break;
        }
        if ($order) {
            $order .= ',goods_id desc';
        } else {
            $order = 'goods_id desc';
        }
        $goods_model = model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $goods_fields, $order, 0, $show_num);
        ds_json_encode(10000, '',array('goods_list' => $goods_list));
    }

    /**
     * 机构商品上新
     */
    public function store_new_goods() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10000, '',array('goods_list' => array()));
        }
        $show_day = ($t = intval(input('param.show_day'))) > 0 ? $t : 30;
        $condition = array();
        $condition[] = array('store_id','=',$store_id);
        $stime = strtotime(date('Y-m-d', TIMESTAMP - 86400 * $show_day));
        $etime = $stime + 86400 * ($show_day + 1);
        $condition[] = array('goods_addtime','between',array($stime, $etime));
        $order = 'goods_addtime desc, goods_id desc';
        $goods_model = model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $goods_fields, $order, $this->pagesize);
        if ($goods_list) {
            foreach ($goods_list as $k => $v) {
                $v['goods_addtime_text'] = $v['goods_addtime'] ? $this->checkTime($v['goods_addtime']) : '';
                $goods_list[$k] = $v;
            }
        }
        $result = array_merge(array('goods_list' => $goods_list), mobile_page($goods_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    /**
     * 机构简介
     */
    public function store_intro() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001,'参数错误');
        }
        $store_online_info = model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            ds_json_encode(10001,'机构不存在或未开启');
        }
        $store_info = $store_online_info;
        //开店时间
        $store_info['store_time_text'] = $store_info['store_addtime'] ? @date('Y-m-d', $store_info['store_addtime']) : '';
        // 机构头像
        $store_info['store_avatar'] = get_store_logo($store_online_info['store_avatar']);
        //商品数
        $store_info['goods_count'] = (int) $store_online_info['goods_count'];
        //机构被收藏次数
        $store_info['store_collect'] = (int) $store_online_info['store_collect'];
        //机构所属分类
        $store_class = model('storeclass')->getStoreclassInfo(array('storeclass_id' => $store_info['storeclass_id']));
        $store_info['storeclass_name'] = $store_class['storeclass_name'];
        //如果已登录 判断该机构是否已被收藏
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int) model('favorites')->getStoreFavoritesCountByStoreId($store_id, $member_id);
            $store_info['is_favorate'] = $c > 0 ? true : false;
        } else {
            $store_info['is_favorate'] = false;
        }
        // 页头背景图
        $store_info['mb_title_img'] = $store_online_info['mb_title_img'] ? ds_get_pic( ATTACH_STORE , $store_online_info['mb_title_img']) : '';
        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array) $mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = ds_get_pic( ATTACH_STORE , $s['img']);
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }
        ds_json_encode(10000, '',array('store_info' => $store_info));
    }

    /**
     * @api {POST} api/Store/get_store_class 获取店铺分类
     * @apiVersion 1.0.0
     * @apiGroup Store
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.store_class  返回数据
     * @apiSuccess {Int} result.store_class.storeclass_bail  店铺分类保证金数额
     * @apiSuccess {Int} result.store_class.storeclass_id  店铺分类ID
     * @apiSuccess {String} result.store_class.storeclass_name  店铺分类名
     * @apiSuccess {Int} result.store_class.storeclass_sort  排序
     */
    public function get_store_class(){
        $store_class = rkcache('storeclass', true);
        ds_json_encode(10000, '', array('store_class' => array_values($store_class)));
    }
    
    public function get_store_grade(){
        $storegrade_list = model('storegrade')->getStoregradeList();
        ds_json_encode(10000, '', array('storegrade_list' => array_values($storegrade_list)));
    }

    /**
     * 取得的时间间隔 
     */
    public function checkTime($time) {
        if ($time == '') {
            return false;
        }
        $catch_time = (TIMESTAMP - $time);
        if ($catch_time < 60) {
            return $catch_time . '秒前';
        } elseif ($catch_time < 60 * 60) {
            return intval($catch_time / 60) . '分钟前';
        } elseif ($catch_time < 60 * 60 * 24) {
            return intval($catch_time / 60 / 60) . '小时前';
        } elseif ($catch_time < 60 * 60 * 24 * 30) {
            return intval($catch_time / 60 / 60 / 24) . '天前';
        } elseif ($catch_time < 60 * 60 * 24 * 360) {
            return intval($catch_time / 60 / 60 / 24 / 30) . '个月前';
        } elseif ($catch_time < 60 * 60 * 24 * 365 * 2) {
            return intval($catch_time / 60 / 60 / 24 / 365) . '年';
        } else{
            return date('Y-m-d',$time);
        }
    }

    /**
     * @api {POST} api/store/getStoreGoods 获取机构分类下的商品
     * @apiVersion 1.0.0
     * @apiGroup Store
     * 
     * @apiParam {String} store_id 机构ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function getStoreGoods() {
        $storeclass_goods = array();
        $store_id = intval(input('param.store_id'));

        if ($store_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }



        //获取当前机构推荐的商品
        $condition = array();
        $condition[] = array('goods_commend','=',1);
        $condition[] = array('store_id','=',$store_id);
        $goods_list = $this->_getGoodsList($condition);
        if (!empty($goods_list)) {
            $storeclass_goods[] = array(
                'name' => '机构推荐',
                'foods' => $goods_list,
            );
        }

        //获取机构分类
        $condition = array();
        $condition[] = array('store_id','=',$store_id);
        $condition[] = array('storegc_parent_id','=',0);
        $storegoodsclass_model = model('storegoodsclass');
        $storegoodsclass_list = $storegoodsclass_model->getStoregoodsclassList($condition);

        if (!empty($storegoodsclass_list)) {
            foreach ($storegoodsclass_list as $key => $storegoodsclass) {
                $condition = array();
                $condition[] = array('store_id','=',$store_id);
                $condition[] = array('goods_stcids','like', '%,' . $storegoodsclass['storegc_id'] . ',%');
                $goods_list = $this->_getGoodsList($condition);
                if (!empty($goods_list)) {
                    $storeclass_goods[] = array(
                        'name' => $storegoodsclass['storegc_name'],
                        'foods' => $goods_list,
                    );
                }
            }
        }
        ds_json_encode(10000, '', $storeclass_goods);
    }

    private function _getGoodsList($condition) {
        $goods_model = model('goods');
        $fieldstr = "goods_id,goods_name,goods_advword,goods_salenum,goods_collect,goods_price,goods_image,store_id";
        $goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fieldstr, 'goods_salenum desc');
        foreach ($goods_list as $key => $goods) {
            $goods_list[$key]['goods_image'] = goods_cthumb($goods['goods_image'], 270);
        }
        return $goods_list;
    }


    /**
     * @api {POST} api/store/getStoreRatings 获取机构所有商品评论
     * @apiVersion 1.0.0
     * @apiGroup Store
     * 
     * @apiParam {String} store_id 机构ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function getStoreRatings() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $goods_eval_list = model('evaluategoods')->getEvaluategoodsList(array('geval_storeid' => $store_id), '10');
        foreach ($goods_eval_list as $key => $val) {
            $goods_eval_list[$key]['geval_goodsimage_url'] = goods_cthumb($val['geval_goodsimage'], 270, $val['geval_storeid']);
        }
        ds_json_encode(10000, '', $goods_eval_list);
    }
}
