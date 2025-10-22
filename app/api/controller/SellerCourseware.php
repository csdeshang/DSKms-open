<?php

/**
 * 机构课件管理
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
class SellerCourseware extends MobileSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/seller_courseware.lang.php');
    }

    /**
     * 百度网盘授权
     */
    public function baidu_pan_auth() {
        $code = input('param.code');
        if (!$code) {
            $this->error(lang('param_error'));
        }
        $res = http_request('https://openapi.baidu.com/oauth/2.0/token?grant_type=authorization_code&code=' . $code . '&client_id=' . config('ds_config.baidu_pan_api_key') . '&client_secret=' . config('ds_config.baidu_pan_secret_key') . '&redirect_uri=' . urlencode(str_replace('/index.php', '', API_SITE_URL) . '/seller_courseware/baidu_pan_auth'));
        $res = json_decode($res, true);
        if (isset($res['error']) && $res['error']) {
            $this->error($res['error_description']);
        }
        $store_model = model('store');
        $store_model->editStore(array(
            'baidu_pan_access_token' => $res['access_token'],
            'baidu_pan_expires_in' => TIMESTAMP + $res['expires_in'],
            'baidu_pan_refresh_token' => $res['refresh_token'],
                ), array('store_id' => $this->store_info['store_id']));
        $this->success(lang('ds_common_op_succ'),config('ds_config.h5_store_site_url').'/pages/seller/goods/Goodsonline');
    }


    /**
     * 百度网盘文件列表
     */
    public function baidu_pan_file() {

        $access_token = $this->store_info['baidu_pan_access_token'];

        $param = '';
        $back_dir = '';
        $dir = input('param.dir');
        if ($dir) {
            $param .= '&dir=' . $dir;
            $temp = explode('/', $dir);
            unset($temp[count($temp) - 1]);
            $back_dir = implode('/', $temp);
            if ($back_dir == '/') {
                $back_dir = '';
            }
        }



        $res = http_request('https://pan.baidu.com/rest/2.0/xpan/file?method=list&access_token=' . $access_token . $param);
        $res = json_decode($res, true);
        if (isset($res['errno']) && $res['errno']) {
            ds_json_encode(10001, '获取网盘文件出错，错误码'.$res['errno']);
        }
        $file_list = $res['list'];
        foreach ($file_list as $key => $val) {
            if (isset(lang('baidu_pan_category')[$val['category']])) {
                $file_list[$key]['category_text'] = lang('baidu_pan_category')[$val['category']];
            } else {
                $file_list[$key]['category_text'] = '未知';
            }
            $file_list[$key]['size_text'] = format_bytes($val['size'], ' ');
            $file_list[$key]['date'] = date('Y-m-d H:i:s', $val['server_mtime']);
        }
        ds_json_encode(10000, '',array('file_list'=> $file_list,'dir'=> $dir,'back_dir'=> $back_dir));

    }

    /*
     * 百度网盘预上传
     */

    public function baidu_pan_precreate() {
        $size = input('param.size');
        $path = input('param.path');
        $block_list = input('param.block_list/a');

        $access_token = $this->store_info['baidu_pan_access_token'];
        $param = array(
            'isdir' => 0,
            'autoinit' => 1,
            'path' => $path,
            'block_list' => json_encode($block_list),
            'size' => $size,
        );
        $res = http_request('https://pan.baidu.com/rest/2.0/xpan/file?method=precreate&access_token=' . $access_token, 'POST', $param);
        $res = json_decode($res, true);
        if (isset($res['errno']) && $res['errno']) {
            ds_json_encode(10001, '预上传网盘文件出错，错误码'.$res['errno']);
        }
        ds_json_encode(10000, '', $res);
    }

    /*
     * 百度网盘上传
     */

    public function baidu_pan_upload() {
        $uploadid = input('param.uploadid');
        $path = input('param.path');
        $partseq = input('param.partseq');
        
        $access_token = $this->store_info['baidu_pan_access_token'];
        

        // 创建一个 CURLFile 对象
        $cfile = curl_file_create($_FILES['file']['tmp_name'],$_FILES['file']['type'],$_FILES['file']['name']);

        // 设置 POST 数据
        $param = array('file' => $cfile);
        $postfields=array(
            'type'=>'tmpfile',
            'path'=>$path,
            'uploadid'=>$uploadid,
            'partseq'=>$partseq
        );
        $postfields=http_build_query($postfields);
        
        $url = 'https://d.pcs.baidu.com/rest/2.0/pcs/superfile2?method=upload&access_token=' . $access_token . '&' . $postfields;
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $param);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        $res = json_decode($sContent, true);
        if (isset($res['errno']) && $res['errno']) {
            ds_json_encode(10001, '上传网盘文件出错，错误码'.$res['errno']);
        }
        ds_json_encode(10000, '', $res);
    }

    /*
     * 百度网盘创建文件
     */

    public function baidu_pan_create() {
        $uploadid = input('param.uploadid');
        $size = input('param.size');
        $path = input('param.path');
        $block_list = input('param.block_list/a');
        
        $access_token = $this->store_info['baidu_pan_access_token'];
        $param = array(
            'isdir' => 0,
            'path' => $path,
            'size' => $size,
            'uploadid'=>$uploadid,
            'block_list' => json_encode($block_list)
        );
        $res = http_request('https://pan.baidu.com/rest/2.0/xpan/file?method=create&access_token=' . $access_token, 'POST', $param);
        $res = json_decode($res, true);
        if (isset($res['errno']) && $res['errno']) {
            ds_json_encode(10001, '创建网盘文件出错，错误码'.$res['errno']);
        }
        ds_json_encode(10000, lang('ds_common_op_succ'), $res);
    }
}
