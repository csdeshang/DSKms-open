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
class  GoodscoursesView extends BaseModel
{

    public $page_info;
    /**
     * 类别列表
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getGoodscoursesViewList($condition, $field = '*', $group = '', $order = 'goodscourses_view_id desc', $limit = 0, $pagesize = 0) {
        if ($pagesize) {
            $result = Db::name('goodscourses_view')->field($field)->where($condition);
            if($group){
                $result=$result->group($group);
            }
            $result=$result->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = Db::name('goodscourses_view')->field($field)->where($condition)->limit($limit)->group($group)->order($order)->select()->toArray();
            return $result;
        }
    }

    /**
     * 类别
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getGoodscoursesViewInfo($condition){
        $result = Db::name('goodscourses_view')->where($condition)->find();
        return $result;
    }
    
    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addGoodscoursesView($data){
        $result = Db::name('goodscourses_view')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @param array $condition 检索条件
     * @return bool 布尔类型的返回结果
     */
    public function editGoodscoursesView($data,$condition){
        $result =Db::name('goodscourses_view')->where($condition)->update($data);
        return $result;
    }

    /**
     * 删除分类
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return bool 布尔类型的返回结果
     */
    public function delGoodscoursesView($condition){
        return Db::name('goodscourses_view')->where($condition)->delete();
    }

}