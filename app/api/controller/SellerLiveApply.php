<?php

/**
 * 机构直播管理
 */

namespace app\api\controller;

use think\facade\Lang;
use GatewayClient\Gateway;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamStateRequest;
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
class SellerLiveApply extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/seller_live_apply.lang.php');
    }

    /**
     * 显示机构所有直播列表
     */
    public function get_live_apply_list() {

        $live_apply_model = model('live_apply');
        $condition[] = array('live_apply_type', 'in', [LIVE_APPLY_TYPE_COURSE]);
        $condition[] = array('live_apply_user_type', '=', 3);
        $condition[] = array('live_apply_user_id', '=', $this->store_info['store_id']);
        $live_apply_list = $live_apply_model->getLiveApplyList($condition, '*', 10);

        foreach ($live_apply_list as $key => $val) {
            $live_apply_list[$key]['minipro_code'] = '';
            if ($val['live_apply_state'] == 1 && file_exists(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_LIVE_APPLY . DIRECTORY_SEPARATOR . $val['live_apply_id'] . '.png')) {
                $live_apply_list[$key]['minipro_code'] = UPLOAD_SITE_URL . '/' . ATTACH_LIVE_APPLY . '/' . $val['live_apply_id'] . '.png';
            }
        }

        $result = array_merge(array('live_apply_list' => $live_apply_list), mobile_page($live_apply_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * 删除直播
     */
    public function del_live_apply() {


        $live_apply_id = intval(input('param.live_apply_id'));
        if ($live_apply_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $live_apply_model = model('live_apply');
        $condition[] = array('live_apply_type', '=', LIVE_APPLY_TYPE_COURSE);
        $condition[] = array('live_apply_user_type', '=', 3);
        $condition[] = array('live_apply_user_id', '=', $this->store_info['store_id']);
        $condition[] = array('live_apply_id', '=', $live_apply_id);

        $live_apply = $live_apply_model->getLiveApplyInfo($condition);
        if (empty($live_apply)) {
            ds_json_encode(10001, '参数错误');
        }




        $live_apply_model->delLiveApply($condition);
        ds_json_encode(10000, '');
    }

    public function get_live_apply_info() {

        $msg = '';
        $live_apply_id = intval(input('param.live_apply_id'));
        if ($live_apply_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $live_apply_model = model('live_apply');
        $condition = array();
        $condition[] = array('live_apply_user_type', '=', 3);
        $condition[] = array('live_apply_user_id', '=', $this->store_info['store_id']);
        $condition[] = array('live_apply_id', '=', $live_apply_id);

        $live_apply = $live_apply_model->getLiveApplyInfo($condition);
        if (empty($live_apply)) {
            ds_json_encode(10001, '直播不存在');
        }

        //判断当前流状态
//        require_once root_path() . 'vendor/tencentcloud/tencentcloud-sdk-php/TCloudAutoLoader.php';
        if (config('ds_config.video_type') == 'aliyun') {
            if (!config('ds_config.aliyun_live_push_domain')) {
                ds_json_encode(10001, '未设置推流域名');
            }
            if (!config('ds_config.aliyun_live_push_key')) {
                ds_json_encode(10001, '未设置推流key');
            }
            if (!config('ds_config.aliyun_live_play_domain')) {
                ds_json_encode(10001, '未设置播流域名');
            }
            if (!config('ds_config.aliyun_live_play_key')) {
                ds_json_encode(10001, '未设置播流key');
            }
            $regionId = 'cn-shanghai';
            AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                    ->regionId($regionId)
                    ->asDefaultClient();

            try {
                $result = AlibabaCloud::rpc()
                        ->product('live')
                        // ->scheme('https') // https | http
                        ->version('2016-11-01')
                        ->action('DescribeLiveStreamsOnlineList')
                        ->method('POST')
                        ->host('live.aliyuncs.com')
                        ->options([
                            'query' => [
                                'RegionId' => $regionId,
                                'DomainName' => config('ds_config.aliyun_live_push_domain'),
                                'AppName' => "live",
                                'StreamName' => 'live_apply_' . $live_apply['live_apply_id'],
                                'PageSize' => "1",
                                'PageNum' => "1",
                                'QueryType' => "strict",
                            ],
                        ])
                        ->request();
                if ($result->TotalNum) {
                    ds_json_encode(10001, '已有另外客户端占用了此直播，请通知管理员解除占用');
                }
            } catch (\Exception $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        } else {
            if (!config('ds_config.live_push_domain')) {
                ds_json_encode(10001, '未设置推流域名');
            }
            if (!config('ds_config.live_push_key')) {
                ds_json_encode(10001, '未设置推流key');
            }
            if (!config('ds_config.live_play_domain')) {
                ds_json_encode(10001, '未设置拉流域名');
            }
            try {

                $cred = new Credential(config('ds_config.vod_tencent_secret_id'), config('ds_config.vod_tencent_secret_key'));
                $httpProfile = new HttpProfile();
                $httpProfile->setEndpoint("live.tencentcloudapi.com");

                $clientProfile = new ClientProfile();
                $clientProfile->setHttpProfile($httpProfile);
                $client = new LiveClient($cred, "", $clientProfile);

                $req = new DescribeLiveStreamStateRequest();

                $params = '{"AppName":"live","DomainName":"' . config('ds_config.live_push_domain') . '","StreamName":"' . 'live_apply_' . $live_apply['live_apply_id'] . '"}';
                $req->fromJsonString($params);


                $resp = $client->DescribeLiveStreamState($req);
            } catch (TencentCloudSDKException $e) {
                ds_json_encode(10001, $e->getMessage());
            }
            if ($resp->StreamState == 'active') {
                ds_json_encode(10001, '已有另外客户端占用了此直播，请通知管理员解除占用');
            }
        }
        //生成推流url
        $live_apply['live_apply_push_url'] = model('live_apply')->getPushUrl('live_apply_' . $live_apply['live_apply_id'], $live_apply['live_apply_end_time']);
        //生成拉流url
        $live_apply['live_apply_play_url'] = model('live_apply')->getPlayUrl('live_apply_' . $live_apply['live_apply_id'], $live_apply['live_apply_end_time']);

        $extral_info = array('live_apply_name' => $live_apply['live_apply_id'] . '号直播间', 'live_apply_image' => goods_cthumb(''));
        if ($live_apply['live_apply_type_id']) {
            switch ($live_apply['live_apply_type']) {
                case LIVE_APPLY_TYPE_COURSE:
                    $goodscourses = model('goodscourses')->getOneGoodscourses(array('goodscourses_id' => $live_apply['live_apply_type_id']));
                    if (!$goodscourses) {
                        ds_json_encode(10001, '课程章节不存在');
                    }
                    $goods = model('goods')->getGoodsOnlineInfoByID($goodscourses['goods_id']);
                    if (!$goods) {
                        ds_json_encode(10001, '课程不存在');
                    }
                    $extral_info['live_apply_name'] = $goods['goods_name'] . '：' . $goodscourses['goodscourses_name'];
                    $extral_info['live_apply_image'] = goods_cthumb($goods['goods_image']);
                    break;
            }
        }
        $online_info = false;
        if (config('ds_config.instant_message_register_url')) {
            require_once ROOT_PATH . '/GatewayWorker/vendor/workerman/gatewayclient/Gateway.php';
            try {
                // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
                Gateway::$registerAddress = config('ds_config.instant_message_register_url');
                $online_info = array(
                    'online_count' => Gateway::getClientIdCountByGroup('live_apply_' . $live_apply_id),
                    'online_list' => Gateway::getClientSessionsByGroup('live_apply_' . $live_apply_id),
                );
            } catch (\Exception $e) {
                $msg = $e->getMessage();
            }
        }

        ds_json_encode(10000, '', array('live_apply_info' => array_merge($live_apply, $extral_info, array('instant_message_url' => config('ds_config.instant_message_gateway_url'))), 'online_info' => $online_info));
    }

    public function save_live_apply() {

        $live_apply_model = model('live_apply');

        $data = array(
            'live_apply_type' => LIVE_APPLY_TYPE_COURSE,
            'live_apply_remark' => input('param.live_apply_remark'),
            'live_apply_play_time' => strtotime(input('param.live_apply_play_time')),
            'live_apply_add_time' => TIMESTAMP,
            'live_apply_user_type' => 3,
            'live_apply_user_id' => $this->store_info['store_id'],
        );
        $live_apply_validate = ds_validate('live_apply');
        if (!$live_apply_validate->scene('live_apply_save')->check($data)) {
            ds_json_encode(10001, $live_apply_validate->getError());
        }
        $live_apply_model->addLiveApply($data);
        ds_json_encode(10000);
    }

    function join_live() {
        $live_apply_id = input('param.live_apply_id');
        $client_id = input('param.client_id');
        if (!config('ds_config.instant_message_register_url')) {
            ds_json_encode(10001, '未设置直播聊天gateway地址');
        }
        $live_apply_model = model('live_apply');
        $condition[] = array('live_apply_user_type', '=', 3);
        $condition[] = array('live_apply_state', '=', 1);
        $condition[] = array('live_apply_user_id', '=', $this->store_info['store_id']);
        $condition[] = array('live_apply_id', '=', $live_apply_id);


        $live_apply = $live_apply_model->getLiveApplyInfo($condition);
        if (empty($live_apply)) {
            ds_json_encode(10001, '直播不存在');
        }
        require_once ROOT_PATH . '/GatewayWorker/vendor/workerman/gatewayclient/Gateway.php';
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        try {
            Gateway::$registerAddress = config('ds_config.instant_message_register_url');
            // client_id与uid绑定
            Gateway::bindUid($client_id, '1:' . $this->store_info['store_id']);
            $online_item = array(
                'instant_message_from_avatar' => get_store_logo($this->store_info['store_avatar'], 'store_avatar'),
                'instant_message_from_id' => $this->store_info['store_id'],
                'instant_message_from_type' => 1,
                'instant_message_from_name' => $this->store_info['store_name']
            );
            Gateway::setSession($client_id, $online_item);
            // 加入某个群组（可调用多次加入多个群组）
            Gateway::joinGroup($client_id, 'live_apply_' . $live_apply_id);
            //更新在线人数
            Gateway::sendToGroup('live_apply_' . $live_apply_id, json_encode(array(
                'type' => 'join',
                'online_count' => Gateway::getClientIdCountByGroup('live_apply_' . $live_apply_id),
                'online_list' => Gateway::getClientSessionsByGroup('live_apply_' . $live_apply_id)
            )));
        } catch (\Exception $e) {
            ds_json_encode(10001, $e->getMessage());
        }
        ds_json_encode(10000, '');
    }

    function leave_live() {
        $live_apply_id = input('param.live_apply_id');
        $client_id = input('param.client_id');
        $live_apply_push_message = input('param.live_apply_push_message', '');
//        if (!$live_apply_push_message) {
//            ds_json_encode(10001, '请输入关闭直播理由');
//        }
        $this->change_live($live_apply_id, 2, $live_apply_push_message);


        if ($client_id) {
            if (!config('ds_config.instant_message_register_url')) {
                ds_json_encode(10001, '未设置直播聊天gateway地址');
            }
            require_once ROOT_PATH . '/GatewayWorker/vendor/workerman/gatewayclient/Gateway.php';
            // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
            try {
                Gateway::$registerAddress = config('ds_config.instant_message_register_url');
                // client_id与uid绑定
                Gateway::unbindUid($client_id, '1:' . $this->store_info['store_id']);

                // 加入某个群组（可调用多次加入多个群组）
                Gateway::leaveGroup($client_id, 'live_apply_' . $live_apply_id);
                //更新在线人数
                Gateway::sendToGroup('live_apply_' . $live_apply_id, json_encode(array(
                    'type' => 'leave',
                    'online_count' => Gateway::getClientIdCountByGroup('live_apply_' . $live_apply_id),
                    'online_list' => Gateway::getClientSessionsByGroup('live_apply_' . $live_apply_id),
                )));
            } catch (\Exception $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        }
        ds_json_encode(10000, '');
    }

    function change_live($live_apply_id = 0, $live_apply_push_state = 0, $live_apply_push_message = '') {
        $if_fun = false;
        if (input('param.live_apply_push_state')) {
            $live_apply_id = input('param.live_apply_id');
            $live_apply_push_state = input('param.live_apply_push_state');
            $live_apply_push_message = input('param.live_apply_push_message', '');
        } else {
            $if_fun = true;
        }
        if ($live_apply_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $live_apply_model = model('live_apply');
        $condition = array();
        $condition[] = array('live_apply_user_type', '=', 3);
        $condition[] = array('live_apply_user_id', '=', $this->store_info['store_id']);
        $condition[] = array('live_apply_id', '=', $live_apply_id);

        $live_apply = $live_apply_model->getLiveApplyInfo($condition);
        if (empty($live_apply)) {
            ds_json_encode(10001, '直播不存在');
        }
        $data = array(
            'live_apply_push_state' => $live_apply_push_state,
            'live_apply_push_message' => $live_apply_push_message,
        );
        $live_apply_validate = ds_validate('live_apply');
        if (!$live_apply_validate->scene('live_apply_change')->check($data)) {
            ds_json_encode(10001, $live_apply_validate->getError());
        }
        $live_apply_model->editLiveApply($data, $condition);
        if (!$if_fun) {
            ds_json_encode(10000);
        }
    }

}
