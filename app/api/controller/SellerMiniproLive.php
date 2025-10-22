<?php

/**
 * 机构直播管理
 */

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
 * 控制器
 */
class SellerMiniproLive extends MobileSeller {

    private $errorMsg = array(
        '-1' => '系统错误',
        '1' => '未创建直播间',
        '1003' => '商品id不存在',
        '47001' => '入参格式不符合规范',
        '200002' => '入参错误',
        '300001' => '禁止创建/更新商品 或 禁止编辑&更新房间',
        '300002' => '名称长度不符合规则',
        '300006' => '图片上传失败（如:mediaID过期）',
        '300022' => '此房间号不存在',
        '300023' => '房间状态 拦截（当前房间状态不允许此操作）',
        '300024' => '商品不存在',
        '300025' => '商品审核未通过',
        '300026' => '房间商品数量已经满额',
        '300027' => '导入商品失败',
        '300028' => '房间名称违规',
        '300029' => '主播昵称违规',
        '300030' => '主播微信号不合法',
        '300031' => '直播间封面图不合规',
        '300032' => '直播间分享图违规',
        '300033' => '添加商品超过直播间上限',
        '300034' => '主播微信昵称长度不符合要求',
        '300035' => '主播微信号不存在',
        '300036' => '主播微信号未实名认证',
        '300037' => '购物直播频道封面图不合规',
        '300038' => '未在小程序管理后台配置客服',
        '300039' => '主播副号微信号不合法',
        '300040' => '名称含有非限定字符（含有特殊字符）',
        '300041' => '创建者微信号不合法',
        '300042' => '推流中禁止编辑房间',
        '300043' => '每天只允许一场直播开启关注',
        '500001' => '副号不合规',
        '500002' => '副号未实名',
        '500003' => '已经设置过副号了，不能重复设置',
        '500004' => '不能设置重复的副号',
        '500005' => '副号不能和主号重复',
        '600001' => '用户已被添加为小助手',
        '600002' => '找不到用户',
        '9410000' => '直播间列表为空',
        '9410001' => '获取房间失败',
        '9410002' => '获取商品失败',
        '9410003' => '获取回放失败'
    );

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/live.lang.php');
    }

    /**
     * @api {POST} api/SellerMiniproLive/get_room_list 直播间列表
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function get_room_list() {
        $minipro_live_model = model('minipro_live');
        $room_list = $minipro_live_model->getMiniproLiveList(array(array('member_id', '=', $this->store_info['member_id'])));

        $result = array_merge(array('minipro_live_list' => $room_list), mobile_page($minipro_live_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/SellerMiniproLive/get_room_info 直播间信息
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} minipro_live_id 直播id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function get_room_info() {
        $minipro_live_id = intval(input('param.minipro_live_id'));
        $minipro_live_model = model('minipro_live');
        $room_info = $minipro_live_model->getMiniproLiveInfo(array(array('member_id', '=', $this->store_info['member_id']), array('minipro_live_id', '=', $minipro_live_id)));
        if (!$room_info) {
            ds_json_encode(10001, lang('minipro_live_empty'));
        }
        ds_json_encode(10000, '', array('minipro_live_info' => $room_info));
    }

    /**
     * @api {POST} api/SellerMiniproLive/add_room 新增直播间
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} goods_id 商品id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function add_room() {
        $wechat_model = model('wechat');
        $wechat_model->getOneWxconfig();
        $accessToken = $wechat_model->getAccessToken('miniprogram', 0);
        if ($wechat_model->error_code) {
            ds_json_encode(10001, $wechat_model->error_message);
        }
        $data = array(
            'name' => input('param.minipro_live_name'),
            'coverImg' => input('param.coverImg'),
            'startTime' => input('param.minipro_live_start_time'),
            'endTime' => input('param.minipro_live_end_time'),
            'anchorName' => input('param.minipro_live_nickname'),
            'anchorWechat' => input('param.minipro_live_wxid'),
            'shareImg' => input('param.feedsImg'),
            'feedsImg' => input('param.feedsImg'),
            'isFeedsPublic' => 0,
            'type' => 0,
            'screenType' => 0,
            'closeLike' => 0,
            'closeGoods' => 0,
            'closeComment' => 0,
            'closeReplay' => 0,
            'closeShare' => 0,
            'closeKf' => 1,
        );
        if (!$data['name'] || !$data['coverImg'] || !$data['startTime'] || !$data['endTime'] || !$data['anchorName'] || !$data['anchorWechat'] || !$data['shareImg']) {
            ds_json_encode(10001, lang('minipro_live_data_incomplete'));
        }
        if (mb_strlen($data['name']) < 3 || mb_strlen($data['name']) > 17) {
            ds_json_encode(10001, lang('minipro_live_name_error'));
        }
        if ($data['startTime'] < (TIMESTAMP - 10 * 60)) {
            ds_json_encode(10001, lang('minipro_live_start_time_error'));
        }
        $time = $data['endTime'] - $data['startTime'];
        if ($time < 0 || $time > (72 * 3600)) {
            ds_json_encode(10001, lang('minipro_live_time_error'));
        }
        if (mb_strlen($data['anchorName']) < 2 || mb_strlen($data['anchorName']) > 15) {
            ds_json_encode(10001, lang('minipro_live_author_error'));
        }
        $res = http_request('https://api.weixin.qq.com/wxaapi/broadcast/room/create?access_token=' . $accessToken, 'POST', $data);
        $res = json_decode($res, true);
        if (!$res || $res['errcode']) {
            $msg = lang('minipro_live_add_fail') . $res['errcode'];
            if (isset($this->errorMsg[$res['errcode']])) {
                $msg = $this->errorMsg[$res['errcode']];
            } else if (isset($res['errmsg'])) {
                $msg = $res['errmsg'];
            }
            ds_json_encode(10001, $msg);
        }

        $minipro_live_model = model('minipro_live');
        $minipro_live_model->addMiniproLive(array(
            'minipro_live_name' => $data['name'],
            'member_id' => $this->store_info['member_id'],
            'store_id' => $this->store_info['store_id'],
            'minipro_live_wxid' => $data['anchorWechat'],
            'minipro_live_nickname' => $data['anchorName'],
            'minipro_live_start_time' => $data['startTime'],
            'minipro_live_end_time' => $data['endTime'],
            'minipro_live_image' => input('param.minipro_live_image'),
            'minipro_live_add_time' => TIMESTAMP,
            'minipro_live_room_id' => $res['roomId'],
        ));
        ds_json_encode(10000, '', array('roomId' => $res['roomId']));
    }

    /**
     * @api {POST} api/SellerMiniproLive/del_room 删除直播间
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} goods_id 商品id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function del_room() {
        $minipro_live_id = input('param.minipro_live_id');
        $minipro_live_model = model('minipro_live');
        $minipro_live_info = $minipro_live_model->getMiniproLiveInfo(array(array('store_id' ,'=', $this->store_info['store_id']),array('minipro_live_id', '=', $minipro_live_id)));
        if (!$minipro_live_info) {
            ds_json_encode(10001, lang('minipro_live_empty'));
        }
        $wechat_model = model('wechat');
        $wechat_model->getOneWxconfig();
        $accessToken = $wechat_model->getAccessToken('miniprogram', 0);
        if ($wechat_model->error_code) {
            ds_json_encode(10001, $wechat_model->error_message);
        }
        $data = array(
            'id' => $minipro_live_info['minipro_live_room_id']
        );
        $res = http_request('https://api.weixin.qq.com/wxaapi/broadcast/room/deleteroom?access_token=' . $accessToken, 'POST', $data);
        $res = json_decode($res, true);
        if (!$res || $res['errcode']) {
            $msg = lang('minipro_live_add_fail') . $res['errcode'];
            if (isset($this->errorMsg[$res['errcode']])) {
                $msg = $this->errorMsg[$res['errcode']];
            } else if (isset($res['errmsg'])) {
                $msg = $res['errmsg'];
            }
            ds_json_encode(10001, $msg);
        }
        $minipro_live_model->delMiniproLive(array(array('minipro_live_id','=',$minipro_live_id)));
        ds_json_encode(10000, lang('ds_common_op_succ'));
    }

    /**
     * @api {POST} api/SellerMiniproLive/import_room_goods 导入直播间商品
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} goods_id 商品id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function import_room_goods() {
        $wechat_model = model('wechat');
        $wechat_model->getOneWxconfig();
        $accessToken = $wechat_model->getAccessToken('miniprogram', 0);
        if ($wechat_model->error_code) {
            ds_json_encode(10001, $wechat_model->error_message);
        }
        $roomId = input('param.roomId');
        $ids = input('param.ids/a');

        $minipro_live_model = model('minipro_live');
        $minipro_live_info = $minipro_live_model->getMiniproLiveInfo(array(array('minipro_live_room_id', '=', $roomId), array('member_id', '=', $this->store_info['member_id'])));
        if (!$minipro_live_info) {
            ds_json_encode(10001, lang('minipro_live_empty'));
        }
        $data = array(
            'roomId' => $roomId,
            'ids' => $ids,
        );

        $res = http_request('https://api.weixin.qq.com/wxaapi/broadcast/room/addgoods?access_token=' . $accessToken, 'POST', $data);
        $res = json_decode($res, true);
        if (!$res || $res['errcode']) {
            ds_json_encode(10001, isset($res['errmsg']) ? $res['errmsg'] : lang('minipro_live_import_goods_fail') . $res['errcode']);
        }
        $minipro_live_room_goods_model = model('minipro_live_room_goods');
        $minipro_live_goods_model = model('minipro_live_goods');
        $goods_model = model('goods');
        $minipro_live_goods_list = $minipro_live_goods_model->getMiniproLiveGoodsList(array(array('minipro_live_goods_result_id', 'in', $ids)));
        foreach ($minipro_live_goods_list as $val) {
            $data = array(
                'minipro_live_id' => $minipro_live_info['minipro_live_id'],
                'store_id' => $val['store_id'],
                'store_name' => $val['store_name'],
                'goods_id' => $val['goods_id'],
                'goods_commonid' => $val['goods_commonid'],
                'goods_name' => $val['goods_name'],
                'goods_image' => $val['goods_image'],
                'goods_price' => $val['goods_price'],
                'minipro_live_goods_result_id' => $val['minipro_live_goods_result_id'],
            );
            $goods_info = $goods_model->getGoodsCommonInfoByID($val['goods_commonid']);
            if ($goods_info) {
                $data['gc_id'] = $goods_info['gc_id'];
                $data['gc_id_1'] = $goods_info['gc_id_1'];
                $data['gc_id_2'] = $goods_info['gc_id_2'];
                $data['gc_id_3'] = $goods_info['gc_id_3'];
                $data['gc_name'] = $goods_info['gc_name'];
            }
            $minipro_live_room_goods_model->addMiniproLiveRoomGoods($data);
        }
        ds_json_encode(10000);
    }

    /**
     * @api {POST} api/SellerMiniproLive/get_goods_list 商品列表
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} goods_id 商品id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function get_goods_list() {
        $condition = array();
        $condition[] = array('goodscommon.store_id', '=', $this->store_info['store_id']);
        $condition[] = array('goodscommon.goods_verify', '=', 1);
        $condition[] = array('goodscommon.goods_state', '=', 1);
        $fields = 'goods_id,goodscommon.gc_id,goodscommon.store_id,goodscommon.goods_commonid,goodscommon.goods_name,goodscommon.goods_price,goodscommon.goods_addtime,goodscommon.goods_image,goodscommon.goods_state,goodscommon.goods_lock';
        $goods_model = model('goods');
        $goods_list = $goods_model->getGoodsUnionList($condition, $fields, 'goodscommon.goods_commonid desc', 'goodscommon.goods_commonid', $this->pagesize);
        $minipro_live_goods_model = model('minipro_live_goods');
        foreach ($goods_list as $key => $val) {
            $goods_list[$key]['goods_image_url'] = goods_cthumb($val['goods_image'], 480, $val['store_id']);

            $minipro_live_goods_info = $minipro_live_goods_model->getMiniproLiveGoodsInfo(array(array('goods_commonid', '=', $val['goods_commonid'])));
            $goods_list[$key]['minipro_live_goods_info'] = $minipro_live_goods_info;
        }
        $result = array_merge(array('goods_list' => $goods_list), mobile_page($goods_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/SellerMiniproLive/upload_media 上传临时素材
     * @apiVersion 1.0.0
     * @apiGroup SellerMiniproLive
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {File} file 文件
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  返回数据
     */
    public function upload_media() {
        $wechat_model = model('wechat');
        $wechat_model->getOneWxconfig();
        $res = $wechat_model->uploadMedia('image', $_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
        if (!$res['code']) {
            ds_json_encode(10001, $res['msg']);
        }
        $media_id = $res['data'];
        $file_name = '';

        ds_json_encode(10000, '', array('media_id' => $media_id, 'file_name' => $file_name));
    }

}
