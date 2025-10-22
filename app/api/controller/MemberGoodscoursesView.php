<?php

namespace app\api\controller;
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
 * 用户消息控制器
 */
class MemberGoodscoursesView extends MobileMember {

    public function initialize() {
        parent::initialize();
    }

    /**
     * 课程列表
     */
    public function get_list() {
        $goodscourses_view_model = model('goodscourses_view');
        $goodscourses_model = model('goodscourses');
        $goods_model = model('goods');
        $goodscourses_view_list=$goodscourses_view_model->getGoodscoursesViewList(array(array('member_id','=',$this->member_info['member_id'])), 'goods_id,count(*) as count', 'goods_id', 'goodscourses_view_time desc', 0, 10);
        foreach($goodscourses_view_list as $key => $val){
            $goods_detail = $goods_model->getGoodsDetail($val['goods_id']);
            $goods_info = $goods_detail['goods_info'];
            if (empty($goods_info)) {
                unset($goodscourses_view_list[$key]);
                //课程没有了就删掉
                $goodscourses_view_model->delGoodscoursesView(array(array('goods_id', '=', $val['goods_id'])));
                continue;
            }
            //获取最新观看的课程，方便跳转
            $condition=array();
            $condition[]=array('goods_id','=',$val['goods_id']);
            $condition[]=array('member_id','=',$this->member_info['member_id']);
            $goodscourses_id=Db::name('goodscourses_view')->where($condition)->order('goodscourses_id desc')->value('goodscourses_id');
            $goodscourses = $goodscourses_model->getOneGoodscourses(array(array('goods_id', '=', $val['goods_id']),array('goodscourses_id', '>=', $goodscourses_id)));
            //如果是新订单增加的课程记录，则修改
            if($val['count']==1 && $goodscourses_id==0 && $goodscourses){
                $condition=array();
                $condition[]=array('goods_id','=',$val['goods_id']);
                $condition[]=array('member_id','=',$this->member_info['member_id']);
                $condition[]=array('goodscourses_id','=',0);
                $goodscourses_view_model->editGoodscoursesView(array('goodscourses_id' => $goodscourses['goodscourses_id']), $condition);
            }
            $goodscourses_view_list[$key]['goods_image_url'] = goods_cthumb($goods_info['goods_image'], 270, $goods_info['store_id']);
            $goodscourses_view_list[$key]['goods_name'] = $goods_info['goods_name'];
            $goodscourses_view_list[$key]['goodscourses_id'] = $goodscourses?$goodscourses['goodscourses_id']:0;
            $goodscourses_view_list[$key]['goodscourses_name'] = $goodscourses?$goodscourses['goodscourses_name']:'';
            //已学习的课程
            $condition=array();
            $condition[]=array('goods_id','=',$val['goods_id']);
            $condition[]=array('goodscourses_view_finish','=',1);
            $finish_count=Db::name('goodscourses_view')->where($condition)->count();
            $subQuery=Db::name('goodscourses_view')->field('goodscourses_id')->where($condition)->buildSql();
            $total_count=Db::name('goodscourses')->where(array(array('goods_id','=',$val['goods_id'])))->where('goodscourses_id not in'.$subQuery)->count();
            if($total_count){
                $goodscourses_view_list[$key]['finish_percent'] = round($finish_count/($total_count+$finish_count)*100);
            }else{
                $goodscourses_view_list[$key]['finish_percent'] = 100;
            }
        }
        $result= array_merge(array('goodscourses_view_list' => $goodscourses_view_list), mobile_page($goodscourses_view_model->page_info));
        ds_json_encode(10000, '',$result);
  
    }
    
    public function add() {
        $goodscourses_id = intval(input('param.goodscourses_id'));
        if ($goodscourses_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $condition = array();
        $condition[] = array('goodscourses_id', '=', $goodscourses_id);
        $goodscourses_model = model('goodscourses');
        $goodscourses = $goodscourses_model->getOneGoodscourses($condition);
        if (!$goodscourses) {
            ds_json_encode(10001, '课程不存在');
        }
        $goodscourses_view_model = model('goodscourses_view');
        $condition = array();
        $condition[] = array('goods_id', '=', $goodscourses['goods_id']);
        $condition[] = array('goodscourses_id', '=', $goodscourses['goodscourses_id']);
        $condition[] = array('member_id', '=', $this->member_info['member_id']);
        $goodscourses_view_info = $goodscourses_view_model->getGoodscoursesViewInfo($condition);
        if ($goodscourses_view_info) {
            $goodscourses_view_model->editGoodscoursesView(array('goodscourses_view_time' => TIMESTAMP), array(array('goodscourses_view_id', '=', $goodscourses_view_info['goodscourses_view_id'])));
        } else {
            $goodscourses_view_model->addGoodscoursesView(array(
                'goods_id' => $goodscourses['goods_id'],
                'goodscourses_id' => $goodscourses['goodscourses_id'],
                'goodscourses_view_time' => TIMESTAMP,
                'member_id' => $this->member_info['member_id']
            ));
        }
        ds_json_encode(10000);
    }
    
    /**
     * 结束观看
     * @return array
     */
    public function finish(){
        $goodscourses_id = intval(input('param.goodscourses_id'));
        if ($goodscourses_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $condition = array();
        $condition[] = array('goodscourses_id', '=', $goodscourses_id);
        $goodscourses_model = model('goodscourses');
        $goodscourses = $goodscourses_model->getOneGoodscourses($condition);
        if (!$goodscourses) {
            ds_json_encode(10001, '课程不存在');
        }
        $goodscourses_view_model = model('goodscourses_view');
        $condition = array();
        $condition[] = array('goods_id', '=', $goodscourses['goods_id']);
        $condition[] = array('goodscourses_id', '=', $goodscourses['goodscourses_id']);
        $condition[] = array('member_id', '=', $this->member_info['member_id']);
        $goodscourses_view_info = $goodscourses_view_model->getGoodscoursesViewInfo($condition);
        if(!$goodscourses_view_info){
            ds_json_encode(10001, '观看记录不存在');
        }
        $goodscourses_view_model->editGoodscoursesView(array('goodscourses_view_finish' => 1), array(array('goodscourses_view_id', '=', $goodscourses_view_info['goodscourses_view_id'])));
        ds_json_encode(10000);
    }
    
    /**
     * 删除观看记录
     * @return array
     */
    public function del(){
        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $goodscourses_view_model = model('goodscourses_view');
        $condition = array();
        $condition[] = array('goods_id', '=', $goods_id);
        $condition[] = array('member_id', '=', $this->member_info['member_id']);
        $goodscourses_view_model->delGoodscoursesView($condition);
        ds_json_encode(10000);
    }
}

?>
