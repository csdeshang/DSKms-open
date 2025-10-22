<?php

namespace app\home\controller;

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
 * 控制器
 */
class Goods extends BaseGoods {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/goods.lang.php');
    }

    /**
     * 单个商品信息页
     */
    public function index() {
        $goods_id = intval(input('param.goods_id'));

        // 商品详细信息
        $goods_model = model('goods');
        $goods_detail = $goods_model->getGoodsDetail($goods_id);
        $goods_info = $goods_detail['goods_info'];

        if (empty($goods_info)) {
            $this->error(lang('goods_index_no_goods'), HOME_SITE_URL);
        }
        // 获取销量 END
        $this->getStoreInfo($goods_info['store_id']);
        // 看了又看（同分类本店随机商品）
        $goods_rand_list = model('goods')->getGoodsGcStoreRandList($goods_info['gc_id_1'], $goods_info['store_id'], $goods_info['goods_id'], 2);
        View::assign('goods_rand_list', $goods_rand_list);
        View::assign('goods_image', $goods_detail['goods_image']);
        $inform_switch = true;
        // 检测商品是否下架,检查是否为店主本人
        if ($goods_info['goods_state'] != 1 || $goods_info['goods_verify'] != 1 || $goods_info['store_id'] == session('store_id')) {
            $inform_switch = false;
        }
        View::assign('inform_switch', $inform_switch);

        //判断当前用户是否已购买此商品
        $if_have_buy = $this->_check_buy_goods($goods_id);
        View::assign('if_have_buy', $if_have_buy);
        //获取当前商品下的章节
        View::assign('goodscourses_group', $this->_getGoodscoursesList($goods_info, $if_have_buy));

        //获取商品的推广佣金
        $inviter_model = model('inviter');
        $goods_info['inviter_money'] = 0;
        if (config('ds_config.inviter_show') && config('ds_config.inviter_open') && $goods_info['inviter_open'] && session('member_id') && $inviter_model->getInviterInfo('i.inviter_id=' . session('member_id') . ' AND i.inviter_state=1')) {
            $inviter_money = round($goods_info['inviter_ratio'] / 100 * $goods_info['goods_price'] * floatval(config('ds_config.inviter_ratio_1')) / 100, 2);
            if ($inviter_money > 0) {
                $goods_info['inviter_money'] = $inviter_money;
            }
        }
        // halt($goods_info);
        View::assign('goods', $goods_info);


        $storeplate_model = model('storeplate');
        // 顶部关联版式
        if ($goods_info['plateid_top'] > 0) {
            $plate_top = $storeplate_model->getStoreplateInfoByID($goods_info['plateid_top']);
            View::assign('plate_top', $plate_top);
        }
        // 底部关联版式
        if ($goods_info['plateid_bottom'] > 0) {
            $plate_bottom = $storeplate_model->getStoreplateInfoByID($goods_info['plateid_bottom']);
            View::assign('plate_bottom', $plate_bottom);
        }
        View::assign('store_id', $goods_info['store_id']);

        // 当前位置导航
        $nav_link_list = model('goodsclass')->getGoodsclassnav($goods_info['gc_id'], 0);
        $nav_link_list[] = array('title' => $goods_info['goods_name']);
        View::assign('nav_link_list', $nav_link_list);

        //评价信息
        $goods_evaluate_info = model('evaluategoods')->getEvaluategoodsInfoByGoodsID($goods_id);
        View::assign('goods_evaluate_info', $goods_evaluate_info);

        //SEO 设置
        $seo_param = array();
        $seo_param['name'] = $goods_info['goods_name'];
		$seo_param['key'] = $goods_info['goods_name'];
        $seo_param['description'] = ds_substing(htmlspecialchars_decode($goods_info['goods_body']));
        $this->_assign_seo(model('seo')->type('product')->param($seo_param)->show());

        return View::fetch($this->template_dir . 'goods');
    }

    /**
     * 显示当前产品的课程列表
     */
    public function courses() {
        $goodscourses_id = intval(input('param.goodscourses_id'));
        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            $this->error(lang('param_error'));
        }
        $condition = array();
        if ($goodscourses_id > 0) {
            $condition[] = array('goodscourses_id', '=', $goodscourses_id);
        }
        $condition[] = array('goods_id', '=', $goods_id);
        $goodscourses_model = model('goodscourses');
        $goodscourses = $goodscourses_model->getOneGoodscourses($condition);
        if(!$goodscourses){
          $this->error('课程不存在');
        }
        $if_have_buy = $this->_check_buy_goods($goods_id);
        $goodscourses['goodscourses_url'] = '';
        
        //是否是机构自己查看
        $ifstore = false;
        $store_id = session('store_id');
        if($goodscourses['store_id'] == $store_id){
            $ifstore = true;
        }
        
        //判断此商品是否被购买
        if ($goodscourses['goodscourses_free'] || $goodscourses['goodscourses_exper'] || $if_have_buy || $ifstore) {
            // 商品详细信息
            $goods_model = model('goods');
            $goods_detail = $goods_model->getGoodsDetail($goods_id);
            $goods_info = $goods_detail['goods_info'];
            if (empty($goods_info) || !$goods_info['goods_state'] || !$goods_info['goods_verify']) {
                $this->error(lang('goods_index_no_goods'), HOME_SITE_URL);
            }
            View::assign('goods', $goods_info);
            View::assign('goods_id', $goods_id);

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
                        $this->error('视频已被删除');
                    } else {
                        $this->error('视频未审核');
                    }
                }
            }
            if (session('member_id') && ($goodscourses['goodscourses_free'] || $if_have_buy)) {//记录学习进度
                $goodscourses_view_model = model('goodscourses_view');
                $condition = array();
                $condition[] = array('goods_id', '=', $goodscourses['goods_id']);
                $codnition[] = array('goodscourses_id', '=', $goodscourses['goodscourses_id']);
                $codnition[] = array('member_id', '=', session('member_id'));
                $goodscourses_view_info = $goodscourses_view_model->getGoodscoursesViewInfo($condition);
                if ($goodscourses_view_info) {
                    $goodscourses_view_model->editGoodscoursesView(array('goodscourses_view_time' => TIMESTAMP), array(array('goodscourses_view_id', '=', $goodscourses_view_info['goodscourses_view_id'])));
                } else {
                    $goodscourses_view_model->addGoodscoursesView(array(
                        'goods_id' => $goodscourses['goods_id'],
                        'goodscourses_id' => $goodscourses['goodscourses_id'],
                        'goodscourses_view_time' => TIMESTAMP,
                        'member_id' => session('member_id')
                    ));
                }
            }
            //课程附件
            $goodscourses['baidu_pan_fsids'] = json_decode($goodscourses['baidu_pan_fsids'], true);
            View::assign('goodscourses', $goodscourses);
            View::assign('canDownload', $goodscourses['goodscourses_free'] || $if_have_buy);
            //获取当前商品下的章节
            View::assign('goodscourses_group', $this->_getGoodscoursesList($goods_info, $if_have_buy));
            return View::fetch($this->template_dir . 'courses');
        } else {
            $this->error('您没有观看权限');
        }
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
                        $result = $val;
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

    /**
     * 获取当前商品下的视频
     * @param type $goods_info
     * @return type
     */
    private function _getGoodscoursesList($goods_info, $if_have_buy) {
        $goodscourses_list = model('goodscourses')->getGoodscoursesList(array('goods_id' => $goods_info['goods_id']));
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
                //根据课程是否免费以及是否购买判断
                if ($goodscourses['goodscourses_free'] || $goodscourses['goodscourses_exper'] || $if_have_buy || $if_goods_free) {
                    $goodscourses_list[$key]['goodscourses_view'] = url('Goods/Courses', ['goodscourses_id' => $goodscourses['goodscourses_id'], 'goods_id' => $goodscourses['goods_id']]);
                }
                if ($if_have_buy || $if_goods_free) {
                    $goodscourses_list[$key]['goodscourses_text'] = '开始学习';
                } elseif ($goodscourses['goodscourses_free']) {
                    $goodscourses_list[$key]['goodscourses_text'] = '免费试看';
                } else {
                    $goodscourses_list[$key]['goodscourses_text'] = '您需要先购买课程才能观看本章节';
                }
                $goodscourses_group[$goodscourses['goodscourses_class_id']]['list'][] = $goodscourses_list[$key];
            }
            array_multisort($sort, $id, $goodscourses_group);
        }

        return $goodscourses_group;
    }

    /**
     * 检测当前用户是否购买此商品
     */
    private function _check_buy_goods($goods_id) {
        $if_have_buy = FALSE;
        if (session('member_id')) {
            $condition = array();
            $condition[] = array('buyer_id', '=', session('member_id'));
            $condition[] = array('goods_id', '=', $goods_id);
            $condition[] = array('order_state', 'in', array(ORDER_STATE_PAY, ORDER_STATE_SUCCESS));
            $condition[] = array('refund_state', '=', 0);
            $vrorder = model('vrorder')->getVrorderInfo($condition);
            if (!empty($vrorder)) {
                $if_have_buy = TRUE;
            }
        }
        return $if_have_buy;
    }

    /**
     * 记录浏览历史
     */
    public function addbrowse() {
        $goods_id = intval(input('param.gid'));
        model('goodsbrowse')->addViewedGoods($goods_id, session('member_id'), session('store_id'));
        exit();
    }

    /**
     * 商品评论
     */
    public function comments() {
        $goods_id = intval(input('param.goods_id'));
        $type = input('param.type');
        $this->_get_comments($goods_id, $type, 10);
        echo View::fetch($this->template_dir . 'goods_comments');
    }

    /**
     * 商品评价详细页
     */
    public function comments_list() {
        $goods_id = intval(input('param.goods_id'));

        // 商品详细信息
        $goods_model = model('goods');
        $goods_info = $goods_model->getGoodsInfoByID($goods_id);
        // 验证商品是否存在
        if (empty($goods_info)) {
            $this->error(lang('goods_index_no_goods'));
        }
        View::assign('goods', $goods_info);

        $this->getStoreInfo($goods_info['store_id']);

        //评价信息
        $goods_evaluate_info = model('evaluategoods')->getEvaluategoodsInfoByGoodsID($goods_id);
        View::assign('goods_evaluate_info', $goods_evaluate_info);

        //SEO 设置
        $seo_param = array();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['description'] = ds_substing($goods_info['goods_name']);
        $this->_assign_seo(model('seo')->type('product')->param($seo_param)->show());

        $this->_get_comments($goods_id, input('param.type'), 20);

        return View::fetch($this->template_dir . 'comments_list');
    }

    private function _get_comments($goods_id, $type, $page) {
        $condition = array();
        $condition[] = array('geval_goodsid', '=', $goods_id);
        switch ($type) {
            case '1':
                $condition[] = array('geval_scores', 'in', '5,4');
                View::assign('type', '1');
                break;
            case '2':
                $condition[] = array('geval_scores', 'in', '3,2');
                View::assign('type', '2');
                break;
            case '3':
                $condition[] = array('geval_scores', 'in', '1');
                View::assign('type', '3');
                break;
            default:
                View::assign('type', '');
                break;
        }

        //查询商品评分信息
        $evaluategoods_model = model('evaluategoods');
        $goodsevallist = $evaluategoods_model->getEvaluategoodsList($condition, $page);

        View::assign('goodsevallist', $goodsevallist);
        View::assign('show_page', $evaluategoods_model->page_info->render());
    }

    /**
     * 销售记录
     */
    public function salelog() {
        $goods_id = intval(input('param.goods_id'));
        $vrorder_model = model('vrorder');
        $sales = $vrorder_model->getVrorderAndOrderGoodsSalesRecordList(array(array('goods_id', '=', $goods_id)), '*', 10);
        View::assign('show_page', $vrorder_model->page_info->render());
        View::assign('sales', $sales);
        View::assign('order_type', array(2 => lang('ds_xianshi_rob'), 3 => lang('ds_xianshi_flag'), '4' => lang('ds_xianshi_suit')));
        echo View::fetch($this->template_dir . 'goods_salelog');
    }

    /**
     * 产品咨询
     */
    public function consulting() {
        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            $this->error(lang('param_error'), '', 'html', 'error');
        }

        //得到商品咨询信息
        $consult_model = model('consult');
        $condition = array();
        $condition[] = array('goods_id', '=', $goods_id);

        $ctid = intval(input('param.ctid'));
        if ($ctid > 0) {
            $condition[] = array('consulttype_id', '=', $ctid);
        }
        $consult_list = $consult_model->getConsultList($condition, '*', '10');
        View::assign('consult_list', $consult_list);

        // 咨询类型
        $consult_type = rkcache('consulttype', true);
        View::assign('consult_type', $consult_type);

        View::assign('consult_able', $this->checkConsultAble());
        echo View::fetch($this->template_dir . 'goods_consulting');
    }

    /**
     * 产品咨询
     */
    public function consulting_list() {

        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            $this->error(lang('param_error'));
        }

        // 商品详细信息
        $goods_model = model('goods');
        $goods_info = $goods_model->getGoodsInfoByID($goods_id);
        // 验证商品是否存在
        if (empty($goods_info)) {
            $this->error(lang('goods_index_no_goods'));
        }
        View::assign('goods', $goods_info);

        $this->getStoreInfo($goods_info['store_id']);


        //得到商品咨询信息
        $consult_model = model('consult');
        $condition = array();
        $condition[] = array('goods_id', '=', $goods_id);
        if (intval(input('param.ctid')) > 0) {
            $condition[] = array('consulttype_id', '=', intval(input('param.ctid')));
        }
        $consult_list = $consult_model->getConsultList($condition, '*');
        View::assign('consult_list', $consult_list);
        View::assign('show_page', $consult_model->page_info->render());

        // 咨询类型
        $consult_type = rkcache('consulttype', true);
        View::assign('consult_type', $consult_type);

        //SEO 设置
        $seo_param = array();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['description'] = ds_substing($goods_info['goods_name']);
        $this->_assign_seo(model('seo')->type('product')->param($seo_param)->show());

        View::assign('consult_able', $this->checkConsultAble($goods_info['store_id']));
        return View::fetch($this->template_dir . 'consulting_list');
    }

    private function checkConsultAble($store_id = 0) {
        //检查是否为店主本身
        $store_self = false;
        if (session('store_id')) {
            if (($store_id == 0 && intval(input('param.store_id')) == session('store_id')) || ($store_id != 0 && $store_id == session('store_id'))) {
                $store_self = true;
            }
        }
        //查询会员信息
        $member_info = array();
        $member_model = model('member');
        if (session('member_id'))
            $member_info = $member_model->getMemberInfoByID(session('member_id'));
        //检查是否可以评论
        $consult_able = true;
        if ((!config('ds_config.guest_comment') && !session('member_id') ) || $store_self == true || (session('member_id') > 0 && $member_info['is_allowtalk'] == 0)) {
            $consult_able = false;
        }
        return $consult_able;
    }

    /**
     * 商品咨询添加
     */
    public function save_consult() {
        //检查是否可以评论
        if (!config('ds_config.guest_comment') && !session('member_id')) {
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

        if (session('member_id')) {
            //查询会员信息
            $member_model = model('member');
            $member_info = $member_model->getMemberInfo(array('member_id' => session('member_id')));
            if (empty($member_info) || $member_info['is_allowtalk'] == 0) {
                ds_json_encode(10001, lang('goods_index_goods_noallow'));
            }
        }
        //判断商品编号的存在性和合法性
        $goods = model('goods');
        $goods_info = $goods->getGoodsInfoByID($goods_id);
        if (empty($goods_info)) {
            ds_json_encode(10001, lang('goods_index_goods_not_exists'));
        }
        //判断是否是店主本人
        if (session('store_id') && $goods_info['store_id'] == session('store_id')) {
            ds_json_encode(10001, lang('goods_index_consult_store_error'));
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
        $input['member_id'] = intval(session('member_id')) > 0 ? session('member_id') : 0;
        $input['member_name'] = session('member_name') ? session('member_name') : '';
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
     * 异步显示优惠套装/推荐组合
     */
    public function get_bundling() {
        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            exit();
        }
        $goods_model = model('goods');
        $goods_info = $goods_model->getGoodsOnlineInfoByID($goods_id);
        if (empty($goods_info)) {
            exit();
        }

        // 推荐组合
        if (!empty($goods_info)) {
            $array = model('goodscombo')->getGoodscomboCacheByGoodsId($goods_id);
            View::assign('goods_info', $goods_info);
            View::assign('gcombo_list', unserialize($array['gcombo_list']));
        }

        echo View::fetch($this->template_dir . 'goods_bundling');
    }

    public function json_area() {
        echo input('param.callback') . '(' . json_encode(model('area')->getAreaArrayForJson()) . ')';
    }

}

?>
