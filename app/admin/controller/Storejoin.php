<?php

namespace app\admin\controller;

use think\facade\View;
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
class Storejoin extends AdminControl {

    public function initialize() {
        parent::initialize(); // TODO: Change the autogenerated stub
        Lang::load(base_path() . 'admin/lang/' . config('lang.default_lang') . '/storejoin.lang.php');
    }

    /**
     * 前台头部图片传
     */
    public function index() {
        $size = 3; //上传显示图片总数
        $i = 1;
        $info['pic'] = array();
        $info['show_txt'] = '';
        $config_model = model('config');
        $list_config = rkcache('config', true);
        $code_info = $list_config['store_joinin_pic'];
        if (!empty($code_info)) {
            $info = unserialize($code_info);
        }

        $post = input('post.'); #获取POST 数据

        if (request()->isPost()) {
            $store_joinin_open = input('post.store_joinin_open');
            if(!in_array($store_joinin_open,array(0,1,2,3))){
                $this->error(lang('param_error'));
            }
            $info['show_txt'] = input('post.show_txt');
            for ($i; $i <= $size; $i++) {
                $file = 'pic' . $i;
                $info['pic'][$i] = $post['show_pic' . $i];
                if (!empty($_FILES[$file]['name'])) {//上传图片
                    $filename_tmparr = explode('.', $_FILES[$file]['name']);
                    $ext = end($filename_tmparr);
                    $file_name = 'store_joinin_' . $i . '.' . $ext;
                    $res = ds_upload_pic('admin/Storejion', $file, $file_name);
                    if ($res['code']) {
                        $file_name = $res['data']['file_name'];
                        $info['pic'][$i] = $file_name;
                    } else {
                        $this->error($res['msg']);
                    }
                }
            }
            $code_info = serialize($info);

            $update_array = array();
            $update_array['store_joinin_pic'] = $code_info;
            $update_array['store_joinin_open'] = $store_joinin_open;
            $config_model->editConfig($update_array);
            $this->success(lang('ds_common_save_succ'), 'Storejoin/index');
        }
        View::assign('store_joinin_open', $list_config['store_joinin_open']);
        View::assign('size', $size);
        View::assign('pic', $info['pic']);
        View::assign('show_txt', $info['show_txt']);
        $this->setAdminCurItem('index');
        return View::fetch();
    }

    /**
     * 入驻指南
     *
     */
    public function help_list() {
        $help_model = model('help');
        $condition = array();
        $condition[] = array('helptype_id', '=', '1');
        $help_list = $help_model->getStoreHelpList($condition);
        View::assign('help_list', $help_list);
        $this->setAdminCurItem('help_list');
        return View::fetch();
    }

    /**
     * 编辑入驻指南
     *
     */
    public function edit_help() {
        $help_model = model('help');
        $condition = array();
        $help_id = intval(input('param.help_id'));
        $condition[] = array('help_id', '=', $help_id);
        $help_list = $help_model->getStoreHelpList($condition);
        $help = $help_list[0];
        $help['help_info'] = str_replace("\r\n", "", $help['help_info']);
        View::assign('help', $help);
        if (request()->isPost()) {
            $help_array = array();
            $help_array['help_title'] = input('post.help_title');
            $help_array['help_info'] = input('post.help_info');
            $help_array['help_sort'] = intval(input('post.help_sort'));
            $help_array['help_updatetime'] = TIMESTAMP;
            $state = $help_model->editHelp($condition, $help_array);
            if ($state) {
                $this->log('编辑机构入驻指南，编号' . $help_id);
                $this->success(lang('ds_common_save_succ'), 'storejoin/help_list');
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
        $condition = array();
        $condition[] = array('item_id', '=', $help_id);
        $pic_list = $help_model->getHelpPicList($condition);
        View::assign('pic_list', $pic_list);
        $this->setAdminCurItem('edit');
        return View::fetch();
    }

    /**
     * 上传图片
     */
    public function upload_pic() {
        $data = array();
        if (!empty($_FILES['fileupload']['name'])) {//上传图片
            $fprefix = 'admin/storehelp';
            $res = ds_upload_pic($fprefix, 'fileupload');
            if ($res['code']) {
                $file_name = $res['data']['file_name'];
            } else {
                echo json_encode($data);
                exit;
            }
            $upload_model = model('upload');
            $insert_array = array();
            $insert_array['file_name'] = $file_name;
            $insert_array['file_size'] = $_FILES['fileupload']['size'];
            $insert_array['upload_time'] = TIMESTAMP;
            $insert_array['item_id'] = intval(input('param.item_id'));
            $insert_array['upload_type'] = '2';
            $result = $upload_model->addUpload($insert_array);
            if ($result) {
                $data['file_id'] = $result;
                $data['file_name'] = $file_name;
                $data['file_path'] = ds_get_pic( $fprefix , $file_name);
            }
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 删除图片
     */
    public function del_pic() {
        $condition = array();
        $condition[] = array('upload_id', '=', intval(input('param.file_id')));
        $help_model = model('help');
        $state = $help_model->delHelpPic($condition);
        if ($state) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }

    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index', 'text' => lang('image_and_notice'), 'url' => (string) url('Storejoin/index')
            ), array(
                'name' => 'help_list', 'text' => lang('joinin_guide'), 'url' => (string) url('Storejoin/help_list')
            )
        );
        if (request()->action() == 'edit_help') {
            $menu_array[] = array('name' => 'edit', 'text' => lang('edit_content'), 'url' => 'javascript:void(0)');
        }
        return $menu_array;
    }

}
