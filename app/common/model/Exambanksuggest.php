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
class  Exambanksuggest extends BaseModel {

    public $page_info;

    /**
     * 获取错题列表
     * @author csdeshang
     * @param type $condition 查询条件
     * @param type $pagesize      分页信息
     * @param type $order     排序
     * @return type
     */
    public function getExambanksuggestList($condition,$fields, $pagesize = '', $limit = 0) {
        if ($pagesize) {
            $res = Db::name('exambanksuggest')->alias('es')->join('exambank e', 'es.exambank_id=e.exambank_id')
                    ->where($condition)
                    ->field($fields)
                    ->order('exambanksuggest_id desc')
                    ->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $res;
            $result= $res->items();
        } else {
             $result = Db::name('exambanksuggest')->alias('es')->join('exambank e', 'es.exambank_id=e.exambank_id')->where($condition)->field($fields)->limit($limit)->order('exambanksuggest_id desc')->select()->toArray();
        }
         return $result;
    }

    /**
     * 删除错题
     * @author csdeshang
     * @param type $condition 删除条件
     * @return type
     */
    public function delExambanksuggest($condition) {
        return Db::name('exambanksuggest')->where($condition)->delete();
    }
    
    /**
     * 获取单条错题信息
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getOneExambanksuggest($condition) {
        return Db::name('exambanksuggest')->where($condition)->find();
    }
    
    
    /**
     * 增加错题
     * @author csdeshang
     * @param type $data
     * @return type
     */
    public function addExambanksuggest($data) {
        return Db::name('exambanksuggest')->insertGetId($data);
    }
    /**
     * 更新错题信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editExambanksuggest($condition,$data){
        $result = Db::name('exambanksuggest')->where($condition)->update($data);
        return $result;
    }
}
