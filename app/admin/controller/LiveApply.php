<?php

/**
 * 商品管理
 */

namespace app\admin\controller;

use think\facade\View;
use think\facade\Lang;
use think\facade\Db;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DropLiveStreamRequest;
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
class LiveApply extends AdminControl {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'admin/lang/' . config('lang.default_lang') . '/live_apply.lang.php');
    }

    /**
     * 商品管理
     */
    public function index() {
        /**
         * 查询条件
         */
        $condition = array();
        $store_model = model('store');
        if (config('ds_config.live_type') == 1) {
            $minipro_live_model = model('minipro_live');
            $minipro_live_list = $minipro_live_model->getMiniproLiveList($condition);
            $store_list = array();
            foreach ($minipro_live_list as $key => $val) {
                if (!isset($store_list[$val['store_id']])) {
                    $store_list[$val['store_id']] = $store_model->getStoreInfo(array('store_id' => $val['store_id']));
                }
                $minipro_live_list[$key]['store_name'] = $store_list[$val['store_id']]['store_name'] ? $store_list[$val['store_id']]['store_name'] : '';
            }
            View::assign('minipro_live_list', $minipro_live_list);
            View::assign('show_page', $minipro_live_model->page_info->render());
        } else {
            $live_apply_model = model('live_apply');
            $live_apply_state = input('param.live_apply_state');
            if (in_array($live_apply_state, array('0', '1', '2'))) {
                $condition[] = array('live_apply_state', '=', $live_apply_state);
            }

            $live_apply_list = $live_apply_model->getLiveApplyList($condition, '*', 10, 'live_apply_state asc,live_apply_id desc');

            $store_list = array();
            foreach ($live_apply_list as $key => $val) {
                $live_apply_list[$key]['live_apply_user_name'] = '';
                switch ($val['live_apply_user_type']) {
                    case 3:
                        if (!isset($store_list[$val['live_apply_user_id']])) {
                            $store_list[$val['live_apply_user_id']] = $store_model->getStoreInfo(array('store_id' => $val['live_apply_user_id']));
                        }
                        $live_apply_list[$key]['live_apply_user_name'] = $store_list[$val['live_apply_user_id']]['store_name'] ? $store_list[$val['live_apply_user_id']]['store_name'] : '';
                        break;
                }
            }
            View::assign('live_apply_list', $live_apply_list);
            View::assign('show_page', $live_apply_model->page_info->render());
        }


        View::assign('search', $condition);

        $this->setAdminCurItem('index');
        return View::fetch();
    }

    /**
     * 删除商品
     */
    public function del() {
        if (config('ds_config.live_type') == 1) {
            $minipro_live_id = input('param.minipro_live_id');
            $minipro_live_id_array = ds_delete_param($minipro_live_id);
            if ($minipro_live_id_array == FALSE) {
                ds_json_encode('10001', lang('ds_common_op_fail'));
            }
            $minipro_live_model = model('minipro_live');
            foreach ($minipro_live_id_array as $minipro_live_id) {
                $minipro_live_info = $minipro_live_model->getMiniproLiveInfo(array(array('minipro_live_id', '=', $minipro_live_id)));
                if (!$minipro_live_info) {
                    ds_json_encode(10001, lang('live_not_exist'));
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
                    $msg = lang('ds_common_op_fail') . $res['errcode'];
                    if (isset($res['errmsg'])) {
                        $msg = $res['errmsg'];
                    }
                    ds_json_encode(10001, $msg);
                }
                $minipro_live_model->delMiniproLive(array(array('minipro_live_id','=',$minipro_live_id)));
            }

            $this->log(lang('ds_del') . '直播' . ' ID:' . implode('、', $minipro_live_id_array), 1);
            ds_json_encode('10000', lang('ds_common_op_succ'));
        } else {
            $live_apply_id = input('param.live_apply_id');
            $live_apply_id_array = ds_delete_param($live_apply_id);
            if ($live_apply_id_array == FALSE) {
                ds_json_encode('10001', lang('ds_common_op_fail'));
            }
            $condition = array();
            $condition[] = array('live_apply_id', 'in', $live_apply_id_array);
            model('live_apply')->delLiveApply($condition);
            $this->log(lang('ds_del') . '直播' . ' ID:' . implode('、', $live_apply_id_array), 1);
            ds_json_encode('10000', lang('ds_common_op_succ'));
        }
    }

    /**
     * 审核商品
     */
    public function view() {
        if (config('ds_config.live_type') == 1) {
            $minipro_live_id = input('param.minipro_live_id');
            $minipro_live_model = model('minipro_live');
            $minipro_live_info = $minipro_live_model->getMiniproLiveInfo(array(array('minipro_live_id', '=', $minipro_live_id)));
            if (!$minipro_live_info) {
                $this->error(lang('live_not_exist'));
            }

            View::assign('live_apply_info', $minipro_live_info);
            echo View::fetch('view');
        } else {
            $live_apply_id = input('param.live_apply_id');
            $live_apply_model = model('live_apply');
            $live_apply_info = $live_apply_model->getLiveApplyInfo(array('live_apply_id' => $live_apply_id));
            if (!$live_apply_info) {
                $this->error('直播不存在');
            }
            if (request()->isPost()) {


                $live_apply_model = model('live_apply');
                $data = array(
                    'live_apply_end_time' => strtotime(input('param.live_apply_end_time')),
                    'live_apply_video' => input('param.live_apply_video'),
                );
                if (!$data['live_apply_end_time']) {
                    $this->error('请设置过期时间');
                }

                if ($live_apply_info['live_apply_state'] == 0) {
                    if (intval(input('param.verify_state')) == 0) {
                        $state = 2;
                        $remark = input('param.verify_reason');
                        if ($remark) {
                            $store_ids = Db::name('live_apply')->where(array('live_apply_user_type' => 3, 'live_apply_id' => $live_apply_id))->column('live_apply_user_id');
                            if ($store_ids) {
                                $store_model = model('store');
                                $store_list = $store_model->getStoreList(array(array('store_id', 'in', $store_ids)));
                                if ($store_list) {
                                    foreach ($store_list as $store) {
                                        $param = array();
                                        $param['code'] = 'live_apply_verify';
                                        $param['store_id'] = $store['store_id'];
                                        $param['ali_param'] = array(
                                            'remark' => $remark,
                                            'live_apply_id' => $live_apply_id
                                        );
                                        $param['ten_param'] = array(
                                            $remark,
                                            $live_apply_id
                                        );
                                        $param['param'] = $param['ali_param'];
                                        $param['weixin_param'] = array(
                                            'url' => config('ds_config.h5_store_site_url') . '/pages/seller/live_apply/LiveApplyList',
                                            'data' => array(
                                                "keyword1" => array(
                                                    "value" => $live_apply_info['live_apply_remark'],
                                                    "color" => "#333"
                                                ),
                                                "keyword2" => array(
                                                    "value" => $remark,
                                                    "color" => "#333"
                                                )
                                            ),
                                        );
                                        model('cron')->addCron(array('cron_exetime'=>TIMESTAMP,'cron_type'=>'sendStoremsg','cron_value'=>serialize($param)));
                                    }
                                }
                            }
                        }
                    } else {
                        $state = 1;
                        //生成小程序码
                        $wechat_model = model('wechat');
                        $wechat_model->getOneWxconfig();
                        $a = $wechat_model->getMiniProCode($live_apply_id, 'pages/seller/live_apply/LiveApplyDetail');
                        if (@imagecreatefromstring($a) == false) {
                            $a = json_decode($a);
                            $this->error('生成直播小程序码失败：' . $a->errmsg);
                        } else {
                            if (is_dir(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_LIVE_APPLY) || (!is_dir(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_LIVE_APPLY) && mkdir(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_LIVE_APPLY, 0755, true))) {
                                file_put_contents(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_LIVE_APPLY . DIRECTORY_SEPARATOR . $live_apply_id . '.png', $a);
                            } else {
                                $this->error('生成直播小程序码失败：没有权限生成目录');
                            }
                        }
                    }
                    $data['live_apply_state'] = $state;
                }
                $live_apply_model->editLiveApply($data, array('live_apply_id' => $live_apply_id));
                $this->log(lang('ds_verify') . '直播' . ' ID:' . $live_apply_id, 1);
                dsLayerOpenSuccess(lang('ds_common_op_succ'));
            } else {

                //判断当前流状态
                $live_apply_info['active'] = false;
                if ($live_apply_info['live_apply_state'] == 1) {
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
                                            'StreamName' => 'live_apply_' . $live_apply_info['live_apply_id'],
                                            'PageSize' => "1",
                                            'PageNum' => "1",
                                            'QueryType' => "strict",
                                        ],
                                    ])
                                    ->request();
                            if ($result->TotalNum) {
                                $live_apply_info['active'] = true;
                                //生成播放地址
                                $live_apply_info['live_apply_play_url'] = model('live_apply')->getPlayUrl('live_apply_' . $live_apply_info['live_apply_id'], $live_apply_info['live_apply_end_time']);
                            }
                        } catch (\Exception $e) {
                            
                        }
                    } else {
                        if (!config('ds_config.live_push_domain')) {
                            $this->error('未设置推流域名');
                        }
                        if (!config('ds_config.live_push_key')) {
                            $this->error('未设置推流key');
                        }
                        if (!config('ds_config.live_play_domain')) {
                            $this->error('未设置拉流域名');
                        }
                        try {

                            $cred = new Credential(config('ds_config.vod_tencent_secret_id'), config('ds_config.vod_tencent_secret_key'));
                            $httpProfile = new HttpProfile();
                            $httpProfile->setEndpoint("live.tencentcloudapi.com");

                            $clientProfile = new ClientProfile();
                            $clientProfile->setHttpProfile($httpProfile);
                            $client = new LiveClient($cred, "", $clientProfile);

                            $req = new DescribeLiveStreamStateRequest();

                            $params = '{"AppName":"live","DomainName":"' . config('ds_config.live_push_domain') . '","StreamName":"' . 'live_apply_' . $live_apply_info['live_apply_id'] . '"}';
                            $req->fromJsonString($params);


                            $resp = $client->DescribeLiveStreamState($req);
                        } catch (TencentCloudSDKException $e) {
                            $this->error($e->getMessage());
                        }
                        if ($resp->StreamState == 'active') {
                            $live_apply_info['active'] = true;
                            //生成播放地址
                            $live_apply_info['live_apply_play_url'] = model('live_apply')->getPlayUrl('live_apply_' . $live_apply_info['live_apply_id'], $live_apply_info['live_apply_end_time']);
                        }
                    }
                }



                View::assign('live_apply_info', $live_apply_info);
                echo View::fetch('view');
            }
        }
    }

    public function close() {
        $live_apply_id = input('param.live_apply_id');
        $live_apply_model = model('live_apply');
        $live_apply = $live_apply_model->getLiveApplyInfo(array('live_apply_id' => $live_apply_id));
        if (!$live_apply) {
            ds_json_encode(10001, '直播不存在');
        }
        if (config('ds_config.video_type') == 'aliyun') {
            $regionId = 'cn-shanghai';
            AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                    ->regionId($regionId)
                    ->asDefaultClient();

            try {
                $result = AlibabaCloud::rpc()
                        ->product('live')
                        // ->scheme('https') // https | http
                        ->version('2016-11-01')
                        ->action('ForbidLiveStream')
                        ->method('POST')
                        ->host('live.aliyuncs.com')
                        ->options([
                            'query' => [
                                'RegionId' => $regionId,
                                'AppName' => "live",
                                'StreamName' => 'live_apply_' . $live_apply['live_apply_id'],
                                'LiveStreamType' => "publisher",
                                'DomainName' => config('ds_config.aliyun_live_push_domain'),
                            ],
                        ])
                        ->request();
            } catch (\Exception $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        } else {
            try {

                $cred = new Credential(config('ds_config.vod_tencent_secret_id'), config('ds_config.vod_tencent_secret_key'));
                $httpProfile = new HttpProfile();
                $httpProfile->setEndpoint("live.tencentcloudapi.com");

                $clientProfile = new ClientProfile();
                $clientProfile->setHttpProfile($httpProfile);
                $client = new LiveClient($cred, "", $clientProfile);

                $req = new DropLiveStreamRequest();

                $params = '{"AppName":"live","DomainName":"' . config('ds_config.live_push_domain') . '","StreamName":"' . 'live_apply_' . $live_apply['live_apply_id'] . '"}';
                $req->fromJsonString($params);


                $resp = $client->DropLiveStream($req);
            } catch (TencentCloudSDKException $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        }

        $this->log('直播断流' . ' ID:' . $live_apply_id, 1);
        ds_json_encode(10000, lang('ds_common_op_succ'));
    }

    /**
     * 获取卖家栏目列表,针对控制器下的栏目
     */
    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index',
                'text' => lang('ds_list'),
                'url' => url('LiveApply/index')
            ),
        );
        return $menu_array;
    }

}

?>
