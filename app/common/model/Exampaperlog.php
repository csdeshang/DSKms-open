<?php

namespace app\common\model;

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
 * 数据层模型
 */
class  Exampaperlog extends BaseModel {

    public $page_info;

    /**
     * 获取题库列表
     * @author csdeshang
     * @param type $condition 查询条件
     * @param type $pagesize      分页信息
     * @param type $order     排序
     * @return type
     */
    public function getExampaperlogList($condition, $pagesize = '', $order='exampaperlog_id desc') {
        if ($pagesize) {
            $result = Db::name('exampaperlog')->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        } else {
            return Db::name('exampaperlog')->where($condition)->order($order)->select()->toArray();
        }
    }

    /**
     * 删除题库
     * @author csdeshang
     * @param type $condition 删除条件
     * @return type
     */
    public function delExampaperlog($condition) {
        return Db::name('exampaperlog')->where($condition)->delete();
    }
    
    /**
     * 获取单条题库
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getOneExampaperlog($condition) {
        return Db::name('exampaperlog')->where($condition)->find();
    }
    
    
    /**
     * 增加题库
     * @author csdeshang
     * @param type $data
     * @return type
     */
    public function addExampaperlog($data) {
        return Db::name('exampaperlog')->insertGetId($data);
    }
    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editExampaperlog($condition,$data){
        $result = Db::name('exampaperlog')->where($condition)->update($data);
        return $result;
    }

}
