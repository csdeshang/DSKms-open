<?php

/**
 * 机构视频管理
 */

namespace app\api\controller;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\DeleteMediaRequest;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Vod\Vod;
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
class  Sellervideo extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellervideo.lang.php');
    }

    /**
     * 显示机构所有视频列表
     */
    public function video_list() {

        $videoupload_model = model('videoupload');
        $condition = array();
        $condition[] = array('store_id','=',$this->store_info['store_id']);
        $videoupload_list = $videoupload_model->getVideouploadList($condition, '*', 10);

        foreach($videoupload_list as $key => $val){
                $url=$videoupload_model->getVideoExpire($val);
                if($url){
                    $videoupload_list[$key]['videoupload_url']=$url;
                }
        }
        $condition=array();
        $condition[]=array('store_id','=',$this->store_info['store_id']);
        $condition[]=array('video_type','in',['aliyun','tencent']);
        $total_cloud_space=Db::name('videoupload')->where($condition)->sum('videoupload_size');
        $condition=array();
        $condition[]=array('store_id','=',$this->store_info['store_id']);
        $condition[]=array('video_type','=','local');
        $total_local_space=Db::name('videoupload')->where($condition)->sum('videoupload_size');
        $result = array_merge(array('total_cloud_space'=>$total_cloud_space,'total_local_space'=>$total_local_space,'cloud_limit'=>$this->store_grade['storegrade_cloud_limit'],'local_limit'=>$this->store_grade['storegrade_local_limit'],'video_list' => $videoupload_list), mobile_page($videoupload_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    /**
     * 获取腾讯客户端上传签名 
     * https://cloud.tencent.com/document/product/266/9221
     */
    public function video_sign() {
        // 确定 App 的云 API 密钥
        $secret_id = config('ds_config.vod_tencent_secret_id');
        $secret_key = config('ds_config.vod_tencent_secret_key');

        // 确定签名的当前时间和失效时间
        $current = TIMESTAMP;
        $expired = $current + 86400;  // 签名有效期：1天
        // 向参数列表填入参数
        $arg_list = array(
            "secretId" => $secret_id,
            "currentTimeStamp" => $current,
            "expireTime" => $expired,
            "procedure" => "LongVideoPreset",//转自适应码任务流
            "taskPriority" => 10,
            "random" => rand());

        // 计算签名
        $orignal = http_build_query($arg_list);
        $signature = base64_encode(hash_hmac('SHA1', $orignal, $secret_key, true) . $orignal);
        ds_json_encode(10000, '', $signature);
    }

    /**
     * 获取视频上传地址和凭证
     * https://help.aliyun.com/document_detail/55407.html
     */
    public function getAliyunCreateToken() {
        $file_name = input('param.file_name');
        $regionId = 'cn-shanghai';
        AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                ->regionId($regionId)
                ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                    ->product('vod')
                    // ->scheme('https') // https | http
                    ->version('2017-03-21')
                    ->action('CreateUploadVideo')
                    ->method('POST')
                    ->host('vod.' . $regionId . '.aliyuncs.com')
                    ->options([
                        'query' => [
                            'RegionId' => $regionId,
                            'Title' => pathinfo($file_name, PATHINFO_FILENAME),
                            'FileName' => $file_name,
                        ],
                    ])
                    ->request();
        } catch (\Exception $e) {
            ds_json_encode(10001, $e->getMessage());
        }

        ds_json_encode(10000, '', $result->toArray());
    }

    /**
     * 刷新视频上传凭证
     * https://help.aliyun.com/document_detail/55408.html
     */
    public function getAliyunRefreshToken() {
        $video_id = input('param.video_id');
        $regionId = 'cn-shanghai';
        AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                ->regionId($regionId)
                ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                    ->product('vod')
                    // ->scheme('https') // https | http
                    ->version('2017-03-21')
                    ->action('RefreshUploadVideo')
                    ->method('POST')
                    ->host('vod.' . $regionId . '.aliyuncs.com')
                    ->options([
                        'query' => [
                            'RegionId' => $regionId,
                            'VideoId' => $video_id,
                        ],
                    ])
                    ->request();
        } catch (\Exception $e) {
            ds_json_encode(10001, $e->getMessage());
        }

        ds_json_encode(10000, '', $result->toArray());
    }
    /**
     * 删除视频
     */
    public function del_video() {

        $videoupload_id = intval(input('param.id'));
        if ($videoupload_id <= 0) {
            ds_json_encode(10001, '参数错误');
        }
        $videoupload_model = model('videoupload');
        $condition = array();
        $condition[] = array('store_id', '=', $this->store_info['store_id']);
        $condition[] = array('videoupload_id', '=', $videoupload_id);

        $videoupload = $videoupload_model->getOneVideoupload($condition);
        if (empty($videoupload)) {
            ds_json_encode(10001, '参数错误');
        }
        if ($videoupload['video_type'] == 'aliyun') {
            $regionId = 'cn-shanghai';
            AlibabaCloud::accessKeyClient(config('ds_config.aliyun_access_key_id'), config('ds_config.aliyun_access_key_secret'))
                    ->regionId($regionId)
                    ->asDefaultClient();
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
                $videoupload_model->delVideoUpload($condition);
            } catch (\Exception $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        } else if($videoupload['video_type'] == 'tencent') {
            try {
                $cred = new Credential(config('ds_config.vod_tencent_secret_id'), config('ds_config.vod_tencent_secret_key'));
                $httpProfile = new HttpProfile();
                $httpProfile->setEndpoint("vod.tencentcloudapi.com");

                $clientProfile = new ClientProfile();
                $clientProfile->setHttpProfile($httpProfile);
                $client = new VodClient($cred, "", $clientProfile);

                $req = new DeleteMediaRequest();


                $params = '{"FileId":"' . $videoupload['videoupload_fileid'] . '"}';
                $req->fromJsonString($params);


                $resp = $client->DeleteMedia($req);

                $videoupload_model->delVideoUpload($condition);
            } catch (TencentCloudSDKException $e) {
                ds_json_encode(10001, $e->getMessage());
            }
        } else if($videoupload['video_type'] == 'local'){
                @unlink(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_GOODS . DIRECTORY_SEPARATOR . $videoupload['store_id'] . DIRECTORY_SEPARATOR . $videoupload['videoupload_url']);
                $videoupload_model->delVideoUpload($condition);
            }else{
                $videoupload_model->delVideoUpload($condition);
            }
        ds_json_encode(10000, lang('ds_common_del_succ'));
    }


    public function save_video() {
        $video_type = input('param.video_type');
        $videoupload_fileid = input('param.file_id');
        $videoupload_name = input('param.file_name');
        $videoupload_url = input('param.url');
        $videoupload_size = intval(input('param.size'));
        $item_id = intval(input('param.item_id'));
        if (empty($video_type)) {
            ds_json_encode(10001, '参数错误');
        }

        if($video_type=='aliyun'){
            $videoupload_url='https://'.config('ds_config.aliyun_vod_play_domain').'/'.$videoupload_url;
        }elseif($video_type=='tencent'){
            $temp=parse_url($videoupload_url);
            $videoupload_url='https://'.config('ds_config.vod_tencent_play_domain').$temp['path'];
        }


        $videoupload_model = model('videoupload');

        $data = array(
            'video_type' => $video_type,
            'videoupload_fileid' => $videoupload_fileid,
            'videoupload_name' => $videoupload_name,
            'videoupload_url' => $videoupload_url,
            'videoupload_size' => $videoupload_size,
            'videoupload_time' => TIMESTAMP,
            'item_id' => $item_id,
            'store_id' => $this->store_info['store_id'],
        );
        $videoupload_model->addVideoupload($data);
        ds_json_encode(10000, '', array('videoupload_url' => $videoupload_url));
    }

    public function upload() {
        $uniqueTag = input('param.unique_tag', ''); //当前文件的唯一标识
        $nowNum = input('param.now_num', 0); //当前是第几片
        $totalNum = input('param.total_num', 0); //共切了几片
        $file = request()->file('file'); //上传的文件

        if (empty($file) || empty($nowNum) || empty($totalNum) || empty($uniqueTag)) {
            ds_json_encode(10001, lang('param_error'));
        }
        $store_id = $this->store_info['store_id'];
        $upload_path = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_GOODS . DIRECTORY_SEPARATOR . $store_id;


        $uniqueTagDir = $upload_path . '/' . $uniqueTag; //当前文件的文件夹
        $file_config = array(
            'disks' => array(
                'local' => array(
                    'root' => $uniqueTagDir
                )
            )
        );
        config($file_config, 'filesystem');
        try {
            $savename = \think\facade\Filesystem::putFileAs('', $file, $nowNum); //return false or savename
        } catch (\Exception $e) {
            ds_json_encode(10001, $e->getMessage());
        }



        ds_json_encode(10000);
    }

    public function merge() {
        $uniqueTag = input('param.unique_tag', ''); //当前文件的唯一标识
        $totalNum = input('param.total_num', 0); //共切了几片
        if (empty($totalNum) || empty($uniqueTag)) {
            ds_json_encode(10001, lang('param_error'));
        }
        $store_id = $this->store_info['store_id'];
        $upload_path = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_GOODS . DIRECTORY_SEPARATOR . $store_id;
        $uniqueTagDir = $upload_path . '/' . $uniqueTag; //当前文件的文件夹
        //文件名字
        $fileName = md5((string) microtime(true));
        $mergeFile = $uniqueTagDir . DIRECTORY_SEPARATOR . $fileName; //合并的文件
        //文件不存在则创建之.
        if (!file_exists($mergeFile)) {
            $myfile = fopen($mergeFile, 'w+');
            fclose($myfile);
        }
        //打开文件
        $myfile = fopen($mergeFile, 'a');

        //进行合并操作.
        for ($i = 1; $i <= $totalNum; $i++) {
            // 单文件路径
            $filePart = $uniqueTagDir . DIRECTORY_SEPARATOR . $i;
            if (file_exists($filePart)) {
                $chunk = file_get_contents($filePart);
                // 写入chunk
                fwrite($myfile, $chunk);
            } else {
                ds_json_encode(10001, "缺少第{$i}片文件，请重新上传");
            }
        }

        fclose($myfile);

        //合并好的文件存储目录(需要移动到这里.)

        $save_name = $store_id . '_' . date('YmdHis') . rand(10000, 99999) . '.mp4';



        $newFile = $upload_path . DIRECTORY_SEPARATOR . $save_name;
        //检测当前文件的文件夹是否存在. 不存在就创建
        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0777, true);
        }

        //组合完成的文件移动到存储文件的目录
        $renameResult = rename($mergeFile, $newFile);


        if ($renameResult === false) {
            ds_json_encode(10001, "文件移动失败");
        }

        /*         * *************进行存储切片的目录进行删除.***************** */
        for ($i = 1; $i <= $totalNum; $i++) {
            // 单文件路径
            $filePart = $uniqueTagDir . DIRECTORY_SEPARATOR . $i;
            @unlink($filePart);
        }
        @unlink($mergeFile);
        @rmdir($uniqueTagDir);
        /*         * *************结束********************** */
        ds_json_encode(10000,'',array('name'=>$save_name));
    }


}
