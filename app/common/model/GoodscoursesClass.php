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
class  GoodscoursesClass extends BaseModel
{
    
    /**
     * 类别列表
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getGoodscoursesClassList($condition,$order='goodscourses_class_sort asc,goodscourses_class_id asc'){
        $result = Db::name('goodscourses_class')->where($condition)->order($order)->select()->toArray();
        return $result;
    }

    /**
     * 类别
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getGoodscoursesClassInfo($condition){
        $result = Db::name('goodscourses_class')->where($condition)->find();
        return $result;
    }
    
    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addGoodscoursesClass($data){
        $result = Db::name('goodscourses_class')->insertGetId($data);
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
    public function editGoodscoursesClass($data,$condition){
        $result =Db::name('goodscourses_class')->where($condition)->update($data);
        return $result;
    }

    /**
     * 删除分类
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return bool 布尔类型的返回结果
     */
    public function delGoodscoursesClass($condition){
        return Db::name('goodscourses_class')->where($condition)->delete();
    }

}