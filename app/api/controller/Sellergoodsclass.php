<?php

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
 * 商品分类控制器
 */
class  Sellergoodsclass extends MobileSeller {
	public function initialize() {
	    parent::initialize();
	    Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellergoodsclass.lang.php');
	}


    /**
     * @api {POST} api/Sellergoodsclass/index 卖家商品分类
     * @apiVersion 1.0.0
     * @apiGroup Sellergoodsclass
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_class  商品分类列表
     * @apiSuccess {Int} result.goods_class.deep  深度
     * @apiSuccess {Int} result.goods_class.store_id  店铺ID
     * @apiSuccess {Int} result.goods_class.storegc_id  店铺分类ID
     * @apiSuccess {Int} result.goods_class.storegc_name  店铺分类名称
     * @apiSuccess {Int} result.goods_class.storegc_parent_id  上级分类ID
     * @apiSuccess {Int} result.goods_class.storegc_sort  排序
     * @apiSuccess {Int} result.goods_class.storegc_state  状态 0不显示1显示
     */
    public function index() {
        $storegoodsclass_model = model('storegoodsclass');
        $goods_class = $storegoodsclass_model->getTreeClassList(array('store_id' => $this->store_info['store_id']), 2);

        ds_json_encode(10000, '',array('goods_class'=>$goods_class));
    }
 
    public function get_tree(){
        $storegoodsclass_model = model('storegoodsclass');
        $goods_class = $storegoodsclass_model->getTreeClassList(array('store_id' => $this->store_info['store_id']), 2);
        $result=array('class_list'=>[]);
        if($goods_class){
            $tree = new \mall\Tree();
            $tree->setTree($goods_class, 'storegc_id', 'storegc_parent_id', 'storegc_name');
            $result['class_list'] = $tree->getArrayList();
        }
        ds_json_encode(10000, '',$result);
    }

    /**
     * @api {POST} api/Sellergoodsclass/get_storegoodsclass_list 卖家商品分类新增/编辑公共数据
     * @apiVersion 1.0.0
     * @apiGroup Sellergoodsclass
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_class  商品分类列表 （返回字段参考storegoodsclass表）
     */
    public function get_storegoodsclass_list() {
        $storegoodsclass_model = model('storegoodsclass');
        $goods_class = $storegoodsclass_model->getStoregoodsclassList(array(
            'store_id' => $this->store_info['store_id'],
            'storegc_parent_id' => 0
        ));
        ds_json_encode(10000, '',array('goods_class'=>$goods_class));
    }

    /**
     * @api {POST} api/Sellergoodsclass/goods_class_edit 机构商品分类编辑信息
     * @apiVersion 1.0.0
     * @apiGroup Sellergoodsclass
     *
     * @apiParam {Int} top_class_id 分类ID
     * 
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_class_edit() {
        $class_id = input('param.top_class_id');
        $storegoodsclass_model = model('storegoodsclass');
        $class_info = $storegoodsclass_model->getStoregoodsclassInfo(array('storegc_id' => intval($class_id)));

        ds_json_encode(10000, '',array('class_info'=>$class_info));
    }

    /**
     * @api {POST} api/Sellergoodsclass/goods_class_save 卖家商品分类保存
     * @apiVersion 1.0.0
     * @apiGroup Sellergoodsclass
     *
     * @apiParam {Int} storegc_id 分类ID 0新增
     * @apiParam {String} storegc_name 分类名称
     * @apiParam {Int} storegc_parent_id 父分类ID
     * @apiParam {Int} storegc_state 分类状态 1启用0禁用
     * @apiParam {Int} storegc_sort 分类排序
     * 
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_class_save() {
        $storegoodsclass_model = model('storegoodsclass');
        $storegc_id = intval(input('post.storegc_id'));
        if ($storegc_id) {

            
 
            $class_array = array();
            if (input('post.storegc_name') != '') {
                $class_array['storegc_name'] = input('post.storegc_name');
            }
            if (input('post.storegc_parent_id') != '') {
                $class_array['storegc_parent_id'] = input('post.storegc_parent_id');
            }
            if (input('post.storegc_state') != '') {
                $class_array['storegc_state'] = input('post.storegc_state');
            }
            if (input('post.storegc_sort') != '') {
                $class_array['storegc_sort'] = input('post.storegc_sort');
            }
			if ($class_array['storegc_parent_id'] == $storegc_id) {
				ds_json_encode(10001,lang('storegc_parent_goods_class_equal_self_error'));
			}
            $condition = array();
            $condition[] = array('store_id','=',$this->store_info['store_id']);
            $condition[] = array('storegc_id','=',intval(input('post.storegc_id')));
            $state = $storegoodsclass_model->editStoregoodsclass($class_array, $condition,$this->store_info['store_id']);
            if ($state) {
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
        } else {
            $class_array = array();
            $class_array['storegc_name'] = input('post.storegc_name');
            $class_array['storegc_parent_id'] = input('post.storegc_parent_id', 0);
            $class_array['storegc_state'] = input('post.storegc_state');
            $class_array['store_id'] = $this->store_info['store_id'];
            $class_array['storegc_sort'] = input('post.storegc_sort');
            $state = $storegoodsclass_model->addStoregoodsclass($class_array);
            if ($state) {
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
        }
    }

   
    /**
     * @api {POST} api/Sellergoodsclass/goods_class_save 卖家商品分类删除
     * @apiVersion 1.0.0
     * @apiGroup Sellergoodsclass
     *
     * @apiParam {String} class_id 分类ID 用,分隔多个ID
     * 
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function drop_goods_class() {
        $storegoodsclass_model = model('storegoodsclass');
        $stcid_array = explode(',', input('param.class_id'));

        foreach ($stcid_array as $key => $val) {
            if (!is_numeric($val))
                unset($stcid_array[$key]);
        }

        $where = array();
        $where[] = array('storegc_id|storegc_parent_id','in', $stcid_array);
        $where[] = array('store_id','=', $this->store_info['store_id']);

        $drop_state = $storegoodsclass_model->delStoregoodsclass($where,$this->store_info['store_id']);
        if ($drop_state) {
            ds_json_encode(10000, lang('ds_common_del_succ'));
        } else {
            ds_json_encode(10001, lang('ds_common_del_fail'));
        }
    }

}
