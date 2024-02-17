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
class  Courseware extends AdminControl
{

    public function initialize()
    {
        parent::initialize();
        Lang::load(base_path() . 'admin/lang/'.config('lang.default_lang').'/courseware.lang.php');
    }

    
    public function setting()
    {
        if (!request()->isPost()) {
            $list_config = rkcache('config', true);
            View::assign('list_config',$list_config);
            $this->setAdminCurItem('setting');
            return View::fetch();
        }else{
            $update_array=input('param.');
            $result = model('config')->editConfig($update_array);
            if($result){
                $this->success(lang('ds_common_save_succ'));
            }else{
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }

    /**
     * 获取卖家栏目列表,针对控制器下的栏目
     */
    protected function getAdminItemList()
    {
        $menu_array = array(
            array(
                'name' => 'setting', 'text' => lang('ds_setting'), 'url' => url('Courseware/setting')
            )
        );
        return $menu_array;
    }

}

?>
