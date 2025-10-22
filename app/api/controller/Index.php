<?php

namespace app\api\controller;
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
 * 公共数据控制器
 */
class  Index extends MobileMall {

    public function initialize() {
        parent::initialize();
    }


    /**
     * @api {POST} api/Index/search_key_list 默认搜索词列表
     * @apiVersion 1.0.0
     * @apiGroup Index
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String[]} result.list  热门搜索列表
     * @apiSuccess {String[]} result.his_list  历史搜索列表
     */
    public function search_key_list() {
        $list = @explode(',', config('ds_config.hot_search'));
        if (!$list || !is_array($list)) {
            $list = array();
        }
        if (cookie('hisSearch') != '') {
            $his_search_list = explode('~', cookie('hisSearch'));
        } else {
            $his_search_list = array();
        }
        if (!is_array($his_search_list)) {
            $his_search_list = array();
        }
        ds_json_encode(10000, '', array('list' => $list, 'his_list' => $his_search_list));
    }

    /**
     * @api {POST} api/Index/getAppadList VueJS 获取广告图
     * @apiVersion 1.0.0
     * @apiGroup Index
     * @apiParam {String} ap_id  广告分类ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.ad_list  广告图列表
     * @apiSuccess {Int} result.ad_list.adv_id  广告ID
     * @apiSuccess {Int} result.ad_list.ap_id  广告位ID
     * @apiSuccess {String} result.ad_list.adv_title  广告标题
     * @apiSuccess {String} result.ad_list.adv_type  APP广告类型,goods,store,article
     * @apiSuccess {String} result.ad_list.adv_typedate  APP广告类型对应的值
     * @apiSuccess {String} result.ad_list.adv_code  APP广告图片地址
     * @apiSuccess {Int} result.ad_list.adv_startdate  APP广告开始时间，Unix时间戳
     * @apiSuccess {Int} result.ad_list.adv_enddate  APP广告结束时间，Unix时间戳
     * @apiSuccess {Int} result.ad_list.adv_sort  广告图排序
     * @apiSuccess {Int} result.ad_list.adv_enabled  是否开启广告图 1是0否
     */
    public function getAppadList() {
        $ap_id = intval(input('param.ap_id'));
        if ($ap_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $prefix = 'api-getAppadList-';
        $result = rcache($ap_id, $prefix);
        if (empty($result)) {
            $condition[] = array('ap_id','=',$ap_id);
            $condition[] = array('adv_enabled','=',1);
			$condition[] = array('adv_startdate','<',TIMESTAMP);
			$condition[] = array('adv_enddate','>',TIMESTAMP);
            $ad_list = model('appadv')->getAppadvList($condition);
            if (!empty($ad_list)) {
                foreach ($ad_list as $key => $banner) {
                    $ad_list[$key]['adv_code'] = get_appadv_code($banner['adv_code']);
                }
            }
            $result['ad_list'] = $ad_list;
            wcache($ap_id, $result, $prefix, 3600);
        }
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/Index/getIndexAdList 获取首页广告图
     * @apiVersion 1.0.0
     * @apiGroup Index
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.banners  广告图列表
     * @apiSuccess {Int} result.banners.adv_id  广告ID
     * @apiSuccess {Int} result.banners.ap_id  广告位ID
     * @apiSuccess {String} result.banners.adv_title  广告标题
     * @apiSuccess {String} result.banners.adv_type  APP广告类型,goods,store,article
     * @apiSuccess {String} result.banners.adv_typedate  APP广告类型对应的值
     * @apiSuccess {String} result.banners.adv_code  APP广告图片地址
     * @apiSuccess {Int} result.banners.adv_startdate  APP广告开始时间，Unix时间戳
     * @apiSuccess {Int} result.banners.adv_enddate  APP广告结束时间，Unix时间戳
     * @apiSuccess {Int} result.banners.adv_sort  广告图排序
     * @apiSuccess {Int} result.banners.adv_enabled  是否开启广告图 1是0否
     * @apiSuccess {Object[]} result.promotion_ads  广告图列表
     * @apiSuccess {Int} result.promotion_ads.adv_id  广告ID
     * @apiSuccess {Int} result.promotion_ads.ap_id  广告位ID
     * @apiSuccess {String} result.promotion_ads.adv_title  广告标题
     * @apiSuccess {String} result.promotion_ads.adv_type  APP广告类型,goods,store,article
     * @apiSuccess {String} result.promotion_ads.adv_typedate  APP广告类型对应的值
     * @apiSuccess {String} result.promotion_ads.adv_code  APP广告图片地址
     * @apiSuccess {Int} result.promotion_ads.adv_startdate  APP广告开始时间，Unix时间戳
     * @apiSuccess {Int} result.promotion_ads.adv_enddate  APP广告结束时间，Unix时间戳
     * @apiSuccess {Int} result.promotion_ads.adv_sort  广告图排序
     * @apiSuccess {Int} result.promotion_ads.adv_enabled  是否开启广告图 1是0否
     * @apiSuccess {Object[]} result.navs  广告图列表
     * @apiSuccess {Int} result.navs.adv_id  广告ID
     * @apiSuccess {Int} result.navs.ap_id  广告位ID
     * @apiSuccess {String} result.navs.adv_title  广告标题
     * @apiSuccess {String} result.navs.adv_type  APP广告类型,goods,store,article
     * @apiSuccess {String} result.navs.adv_typedate  APP广告类型对应的值
     * @apiSuccess {String} result.navs.adv_code  APP广告图片地址
     * @apiSuccess {Int} result.navs.adv_startdate  APP广告开始时间，Unix时间戳
     * @apiSuccess {Int} result.navs.adv_enddate  APP广告结束时间，Unix时间戳
     * @apiSuccess {Int} result.navs.adv_sort  广告图排序
     * @apiSuccess {Int} result.navs.adv_enabled  是否开启广告图 1是0否
     * @apiSuccess {Object[]} result.floor_ads  广告图列表
     * @apiSuccess {Int} result.floor_ads.adv_id  广告ID
     * @apiSuccess {Int} result.floor_ads.ap_id  广告位ID
     * @apiSuccess {String} result.floor_ads.adv_title  广告标题
     * @apiSuccess {String} result.floor_ads.adv_type  APP广告类型,goods,store,article
     * @apiSuccess {String} result.floor_ads.adv_typedate  APP广告类型对应的值
     * @apiSuccess {String} result.floor_ads.adv_code  APP广告图片地址
     * @apiSuccess {Int} result.floor_ads.adv_startdate  APP广告开始时间，Unix时间戳
     * @apiSuccess {Int} result.floor_ads.adv_enddate  APP广告结束时间，Unix时间戳
     * @apiSuccess {Int} result.floor_ads.adv_sort  广告图排序
     * @apiSuccess {Int} result.floor_ads.adv_enabled  是否开启广告图 1是0否
     */
    public function getIndexAdList() {
        $cache_key = "api-getIndexAdList";
        $result = rcache($cache_key);
        if (empty($result)) {
            $condition = array();
            $condition[]=array('adv_enabled','=',1);
            // 首页轮播图
            $condition[] = array('ap_id','=',1);
			$condition[] = array('adv_startdate','<',TIMESTAMP);
			$condition[] = array('adv_enddate','>',TIMESTAMP);
            $banners_list = model('appadv')->getAppadvList($condition);
            if (!empty($banners_list)) {
                foreach ($banners_list as $key => $banner) {
                    $banners_list[$key]['adv_code'] = get_appadv_code($banner['adv_code']);
                }
            }
            $result['banners'] = $banners_list;

            // 首页促销
            $condition = array();
            $condition[] = array('adv_enabled','=',1);
            $condition[] = array('ap_id','=',2);
			$condition[] = array('adv_startdate','<',TIMESTAMP);
			$condition[] = array('adv_enddate','>',TIMESTAMP);
            $promotion_ads = model('appadv')->getAppadvList($condition);
            if (!empty($promotion_ads)) {
                foreach ($promotion_ads as $key => $banner) {
                    $promotion_ads[$key]['adv_code'] = get_appadv_code($banner['adv_code']);
                }
            }
            $result['promotion_ads'] = $promotion_ads;

            // 首页Navs
            $condition = array();
            $condition[] = array('adv_enabled','=',1);
            $condition[] = array('ap_id','=',3);
			$condition[] = array('adv_startdate','<',TIMESTAMP);
			$condition[] = array('adv_enddate','>',TIMESTAMP);
            $navs_list = model('appadv')->getAppadvList($condition);
            if (!empty($navs_list)) {
                foreach ($navs_list as $key => $banner) {
                    $navs_list[$key]['adv_code'] = get_appadv_code($banner['adv_code']);
                }
            }
            $result['navs'] = $navs_list;

            // 首页横图广告
            $condition = array();
            $condition[] = array('adv_enabled','=',1);
            $condition[] = array('ap_id','=',4);
			$condition[] = array('adv_startdate','<',TIMESTAMP);
			$condition[] = array('adv_enddate','>',TIMESTAMP);
            $floor_ads = model('appadv')->getAppadvList($condition);
            if (!empty($floor_ads)) {
                foreach ($floor_ads as $key => $banner) {
                    $floor_ads[$key]['adv_code'] = get_appadv_code($banner['adv_code']);
                }
            }
            $result['floor_ads'] = $floor_ads;
            wcache($cache_key, $result);
        }
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/Index/getConfigList 配置列表
     * @apiVersion 1.0.0
     * @apiGroup Index
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.config_list  配置列表
     * @apiSuccess {String} result.config_list.site_name  站点名称
     * @apiSuccess {String} result.config_list.site_mobile_logo  站点logo
     * @apiSuccess {String} result.config_list.qq_isuse  是否使用QQ互联 1是0否
     * @apiSuccess {String} result.config_list.sms_login  是否手机登录 1是0否
     * @apiSuccess {String} result.config_list.inviter_open  推广开关 1是0否
     * @apiSuccess {String} result.config_list.inviter_level  推广级别
     * @apiSuccess {String} result.config_list.inviter_show  详情页显示推广佣金
     * @apiSuccess {String} result.config_list.inviter_return  推广员返佣 1是0否
     * @apiSuccess {String} result.config_list.inviter_view  推广员审核 1是0否
     * @apiSuccess {String} result.config_list.business_licence  营业执照
     * @apiSuccess {String} result.config_list.points_isuse  是否开启积分 1是0否
     * @apiSuccess {String} result.config_list.points_signin_isuse  是否开启签到送积分 1是0否
     * @apiSuccess {String} result.config_list.points_signin_cycle  签到持续周期
     * @apiSuccess {String} result.config_list.points_signin_reward  签到持续周期额外奖励
     */
    public function getConfigList() {
        $list_config = rkcache('config', true);
        $wechat_model=model('wechat');
        $wx_config = $wechat_model->getOneWxconfig();
         if(!empty($list_config['business_licence'])){
            $list_config['business_licence'] = ds_get_pic( ATTACH_COMMON , $list_config['business_licence']);
        }
        $config_list = array(
            'site_name' => $list_config['site_name'], 
            'site_mobile_logo' => ds_get_pic(ATTACH_COMMON , $list_config['site_mobile_logo']),
            'qq_isuse'=>$list_config['qq_isuse'], //是否使用QQ互联
            'sina_isuse'=>$list_config['sina_isuse'], //是否使用微博互联
            'weixin_pc_isuse'=>$list_config['weixin_pc_isuse'], //是否使用微信互联
            'weixin_h5_isuse'=>$list_config['weixin_h5_isuse'], 
            'weixin_xcx_isuse'=>$list_config['weixin_xcx_isuse'], 
            'weixin_app_isuse'=>$list_config['weixin_app_isuse'], 
            'member_normal_register'=>$list_config['member_normal_register'], //是否普通注册
            'sms_register'=>$list_config['sms_register'], //是否手机注册
            'sms_login'=>$list_config['sms_login'], //是否手机登录
            'inviter_open'=>$list_config['inviter_open'], //推广开关
            'inviter_level'=>$list_config['inviter_level'], //推广级别
            'inviter_show'=>$list_config['inviter_show'], //详情页显示推广佣金
            'inviter_return'=>$list_config['inviter_return'], //推广员返佣
            'inviter_view'=>$list_config['inviter_view'], //推广员审核
            'business_licence'=>$list_config['business_licence'], //营业执照,
            'points_isuse'=>$list_config['points_isuse'], //是否开启积分
            'points_signin_isuse'=>$list_config['points_signin_isuse'], //是否开启签到送积分
            'points_signin_cycle'=>$list_config['points_signin_cycle'], //签到持续周期
            'points_signin_reward'=>$list_config['points_signin_reward'], //签到持续周期额外奖励
            'wechat_appid'=>!empty($wx_config)?$wx_config['appid']:'', //微信appid
            'captcha_status_register'=>$list_config['captcha_status_register'], //注册验证码
            'captcha_status_login'=>$list_config['captcha_status_login'], //登录验证码
            'captcha_status_goodsqa'=>$list_config['captcha_status_goodsqa'], //咨询验证码
            'vod_tencent_appid'=>$list_config['vod_tencent_appid'], //腾讯云appid
            'vod_tencent_play_domain'=>$list_config['vod_tencent_play_domain'], //腾讯云点播域名
            'store_joinin_open'=>$list_config['store_joinin_open'], //店铺入驻开启
            'flow_static_code'=>$list_config['flow_static_code'], //版权信息
            'video_type'=>$list_config['video_type'], //视频服务商
            'aliyun_user_id'=>$list_config['aliyun_user_id'], //版权信息
            'baidu_pan_id'=>$list_config['baidu_pan_id'], //百度网盘
            'baidu_pan_api_key'=>$list_config['baidu_pan_api_key'], //百度网盘
            'baidu_pan_app_name'=>$list_config['baidu_pan_app_name'], //百度网盘
            'live_type'=>$list_config['live_type'], //直播类型
            'wab_number'=>$list_config['wab_number'], //网安备
            'icp_number'=>$list_config['icp_number'], //ICP
        );
        ds_json_encode(10000, '', array('config_list' => $config_list));
    }

    /**
     * @api {POST} api/Index/getProductList 产品列表
     * @apiVersion 1.0.0
     * @apiGroup Index
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.hot_products  热卖商品列表 （返回字段参考goods）
     * @apiSuccess {Object[]} result.recently_products  最新商品列表 （返回字段参考goods）
     */
    public function getProductList() {
        $cache_key = "api-getProductList";
        $result = rcache($cache_key);
        if (empty($result)) {
            $goods_model = model('goods');
            //所需字段
            $fieldstr = "goods_id,store_id,goods_name,goods_advword,goods_price,goods_image,goods_salenum,evaluation_good_star,evaluation_count";
            $fieldstr .= ',goods_advword,store_id,store_name';

            $hot_products = $goods_model->getGoodsListByColorDistinct(array(), $fieldstr, 'goods_salenum desc', $this->pagesize);
            if ($hot_products) {
                foreach ($hot_products as $key => $val) {
                    $hot_products[$key]['goods_img_480'] = goods_thumb($val, 270);
                }
            }
            $result['hot_products'] = $hot_products;

            $recently_products = $goods_model->getGoodsListByColorDistinct(array(), $fieldstr, 'goods_edittime desc', $this->pagesize);
            if ($recently_products) {
                foreach ($recently_products as $key => $val) {
                    $recently_products[$key]['goods_img_480'] = goods_thumb($val, 270);
                }
            }
            $result['recently_products'] = $recently_products;
            
            $good_products = $goods_model->getGoodsListByColorDistinct(array(array('mall_goods_commend','=',1)), $fieldstr, 'mall_goods_sort asc', $this->pagesize);
            if ($good_products) {
                foreach ($good_products as $key => $val) {
                    $good_products[$key]['goods_img_480'] = goods_thumb($val, 270);
                }
            }
            $result['good_products'] = $good_products;
            
            wcache($cache_key, $result);
        }
        ds_json_encode(10000, '', $result);
    }
    /**
     * @api {POST} api/Index/getWechatShare 微信js分享的配置信息
     * @apiVersion 1.0.0
     * @apiGroup Index
     * @apiParam {String} url  分享链接
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.signPackage  微信配置信息
     * @apiSuccess {String} result.signPackage.appId  微信appid
     * @apiSuccess {String} result.signPackage.nonceStr  随机字符串
     * @apiSuccess {String} result.signPackage.timestamp  微信时间戳
     * @apiSuccess {String} result.signPackage.url  分享链接
     * @apiSuccess {String} result.signPackage.signature  微信签名
     * @apiSuccess {String} result.signPackage.rawString  原始数据
     */
    public function getWechatShare(){
        $wechat_model=model('wechat');
        $wechat_model->getOneWxconfig();
        $signPackage = model('wechat')->GetSignPackage(urldecode(input('param.url')));
    	$goods_detail['signPackage']=$signPackage;
        if($wechat_model->error_code){
            ds_json_encode(10001,$wechat_model->error_message);
        }else{
            ds_json_encode(10000, '', array('signPackage' => $signPackage));
        }
    }
    
    /**
     * @api {POST} api/Index/getQrcode 获取二维码
     * @apiVersion 1.0.0
     * @apiGroup Index
     * @apiParam {String} url  链接
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function getQrcode() {
        include_once root_path(). 'extend/qrcode/phpqrcode.php';
        $value = strip_tags(htmlspecialchars_decode(input('get.url')));
        $errorCorrectionLevel = "L";
        $matrixPointSize = "4";
        \QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize,2);
        exit;
    }

    /**
     * @api {POST} api/Index/getGuessLike 猜你喜欢
     * @apiVersion 1.0.0
     * @apiGroup Index
     * @apiParam {Int} member_id  用户id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function getGuessLike(){
        $member_id=intval(input('param.member_id'));
        $goods_list = model('goodsbrowse')->getGuessLikeGoods($member_id, 20);
        if ($goods_list) {
            foreach ($goods_list as $key => $val) {
                $goods_list[$key]['goods_img_480'] = goods_thumb($val, 270);
            }
        }
        if(empty($goods_list)){//随机请求
            $max_id=Db::name('goods')->where(array(array('goods_state','=',1),array('goods_verify','=',1)))->max('goods_id');
            if($max_id){
                $goods_model=model('goods');
                $goods_ids=array();
                for($i=0;$i<20;$i++){
                    $condition=array();
                    $condition[]=array('goods_state','=',1);
                    $condition[]=array('goods_verify','=',1);
                    $rand_id=rand(1,$max_id);
                    $condition[]=array('goods_id','>=',$rand_id);
                    $goods=$goods_model->getGoodsInfo($condition);
                    if($goods && !in_array($goods['goods_id'],$goods_ids)){
                        $goods_ids[]=$goods['goods_id'];
                        $goods['goods_img_480'] = goods_thumb($goods, 270);
                        $goods_list[]=$goods;
                    }
                    $i++;
                }
            }
        }
        ds_json_encode(10000, '', array('goods_list' => $goods_list));
    }
}

?>
