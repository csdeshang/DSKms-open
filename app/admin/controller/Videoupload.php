<?php

/*
 * 空间管理
 */

namespace app\admin\controller;

use think\facade\View;
use think\facade\Lang;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\DeleteMediaRequest;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Vod\Vod;

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
class Videoupload extends AdminControl {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'admin/lang/' . config('lang.default_lang') . '/videoupload.lang.php');
    }

    /**
     * 视频列表
     */
    public function index() {
        $videoupload_model = model('videoupload');
        $video_list = $videoupload_model->getVideouploadList(array(), '*', 16);

        
        foreach($video_list as $key => $val){
                $url=$videoupload_model->getVideoExpire($val);
                if($url){
                    $video_list[$key]['videoupload_url']=$url;
                }
        }
        View::assign('video_list', $video_list);
        View::assign('show_page', $videoupload_model->page_info->render());
        $this->setAdminCurItem('index');
        return View::fetch();
    }

    /**
     * 删除视频
     *
     */
    public function del_video() {
        $videoupload_id = input('param.videoupload_id');
        $videoupload_id_array = ds_delete_param($videoupload_id);
        if ($videoupload_id_array === FALSE) {
            ds_json_encode('10001', lang('param_error'));
        }
        $condition = array();
        $condition[] = array('videoupload_id', 'in', $videoupload_id_array);
        $videoupload_model = model('videoupload');
        //批量删除视频
        $videoupload_list = $videoupload_model->getVideouploadList($condition, '*', 0);
        $if_create_aliyun = false;
        $if_create_tencent = false;
        foreach ($videoupload_list as $videoupload) {
            if ($videoupload['video_type'] == 'aliyun') {
                $regionId = 'cn-shanghai';
                if (!$if_create_aliyun) {
                    $if_create_aliyun = true;
                    AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                            ->regionId($regionId)
                            ->asDefaultClient();
                }
                try {
                    $result = AlibabaCloud::rpc()
                            ->product('vod')
                            // ->scheme('https') // https | http
                            ->version('2017-03-21')
                            ->action('DeleteVideo')
                            ->method('POST')
                            ->host('vod.' . $regionId . '.aliyuncs.com')
                            ->options([
                                'query' => [
                                    'RegionId' => $regionId,
                                    'VideoIds' => $videoupload['videoupload_fileid'],
                                ],
                            ])
                            ->request();
                    $videoupload_model->delVideoUpload(array(array('videoupload_id', '=', $videoupload['videoupload_id'])));
                } catch (\Exception $e) {
                    ds_json_encode(10001, $e->getMessage());
                }
            } else if($videoupload['video_type'] == 'tencent') {
                try {
                    if (!$if_create_tencent) {
                        $if_create_tencent = true;
                        $cred = new Credential(config('ds_config.vod_tencent_secret_id'), config('ds_config.vod_tencent_secret_key'));
                        $httpProfile = new HttpProfile();
                        $httpProfile->setEndpoint("vod.tencentcloudapi.com");

                        $clientProfile = new ClientProfile();
                        $clientProfile->setHttpProfile($httpProfile);
                        $client = new VodClient($cred, "", $clientProfile);

                        $req = new DeleteMediaRequest();
                    }



                    $params = '{"FileId":"' . $videoupload['videoupload_fileid'] . '"}';
                    $req->fromJsonString($params);
                    $resp = $client->DeleteMedia($req);
                    
                } catch (TencentCloudSDKException $e) {
                    if($e->getMessage()!='file not exist!'){
                      ds_json_encode(10001, $e->getMessage());
                    }
                }
                $videoupload_model->delVideoUpload(array(array('videoupload_id', '=', $videoupload['videoupload_id'])));
            } else if($videoupload['video_type'] == 'local'){
                @unlink(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_GOODS . DIRECTORY_SEPARATOR . $videoupload['store_id'] . DIRECTORY_SEPARATOR . $videoupload['videoupload_url']);
                $videoupload_model->delVideoUpload(array(array('videoupload_id', '=', $videoupload['videoupload_id'])));
            }else{
                $videoupload_model->delVideoUpload(array(array('videoupload_id', '=', $videoupload['videoupload_id'])));
            }
        }
        $this->log(lang('ds_del') . lang('videoupload') . '[ID:' . $videoupload_id . ']', 1);
        ds_json_encode('10000', lang('ds_common_op_succ'));
    }

    /**
     * 审核视频
     *
     */
    public function verify_video() {
        $videoupload_id = input('param.videoupload_id');
        $videoupload_id_array = ds_delete_param($videoupload_id);
        if ($videoupload_id_array === FALSE) {
            ds_json_encode('10001', lang('param_error'));
        }
        $condition = array();
        $condition[] = array('videoupload_id', 'in', $videoupload_id_array);
        $videoupload_model = model('videoupload');
        //批量审核视频
        $videoupload_model->editVideoupload(array('videoupload_state' => 1), $condition);
        $this->log(lang('ds_verify') . lang('videoupload') . '[ID:' . $videoupload_id . ']', 1);
        ds_json_encode('10000', lang('ds_common_op_succ'));
    }

    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index',
                'text' => lang('ds_list'),
                'url' => (string) url('Videoupload/index')
            )
        );
        return $menu_array;
    }

}

?>
