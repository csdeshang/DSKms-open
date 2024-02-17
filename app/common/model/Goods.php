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
class  Goods extends BaseModel {

    const STATE1 = 1;       // 出售中
    const STATE0 = 0;       // 下架
    const STATE10 = 10;     // 违规
    const VERIFY1 = 1;      // 审核通过
    const VERIFY0 = 0;      // 审核失败
    const VERIFY10 = 10;    // 等待审核

    public $page_info;

    /**
     * 新增商品数据
     * @access public
     * @author csdeshang
     * @param type $data 数据
     * @return type
     */
    public function addGoods($data) {
        $result = Db::name('goods')->insertGetId($data);
        if ($result) {
            $this->_dGoodsCache($result);
        }
        return $result;
    }


    /**
     * 商品SKU列表
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $field 字段
     * @param type $group 分组
     * @param type $order 排序
     * @param type $limit 限制
     * @param type $pagesize 分页
     * @return array
     */
    public function getGoodsList($condition, $field = '*', $pagesize = 0,$order = '',$limit = 0) {
//        $condition = $this->_getRecursiveClass($condition);
        if ($pagesize) {
            $result = Db::name('goods')->field($field)->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = Db::name('goods')->field($field)->where($condition)->limit($limit)->order($order)->select()->toArray();
            return $result;
        }
    }

    
    /**
     * 获取指定分类指定机构下的随机商品列表 
     * @access public
     * @author csdeshang
     * @param int $gcId 一级分类ID
     * @param int $storeId 机构ID
     * @param int $notEqualGoodsId 此商品ID除外
     * @param int $size 列表最大长度
     * @return array|null
     */
    public function getGoodsGcStoreRandList($gcId, $storeId, $notEqualGoodsId = 0, $size = 4) {
        $where = array(
            array('store_id' ,'=', (int) $storeId),
            array('gc_id_1' ,'=', (int) $gcId),
        );
        if ($notEqualGoodsId > 0) {
            $where[] = array('goods_id','<>', (int) $notEqualGoodsId);
        }
        return Db::name('goods')->where($where)->limit($size)->select()->toArray();
    }

    /**
     * 出售中的商品SKU列表（只显示不同颜色的商品，前台商品索引，机构也商品列表等使用）
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param string $field 字段
     * @param type $order 排序
     * @param type $pagesize 分页
     * @param type $limit 限制
     * @return type
     */
    public function getGoodsListByColorDistinct($condition, $field = '*', $order = 'goods_id asc', $pagesize = 0, $limit = 0) {
        $condition[]=array('goods_state','=',self::STATE1);
        $condition[]=array('goods_verify','=',self::VERIFY1);
//        $condition = $this->_getRecursiveClass($condition);
        
        $count = Db::name('goods')->where($condition)->field("distinct CONCAT(goods_id)")->count();
        $goods_list = array();
        if ($count != 0) {
            $goods_list = $this->getGoodsOnlineList($condition, $field, $pagesize, $order, $limit, 'CONCAT(goods_id)', false, $count);
        }
        return $goods_list;
    }


    /**
     * 在售商品SKU列表
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param str $field 字段
     * @param int $pagesize 分页
     * @param str $order 排序
     * @param int $limit 限制
     * @param str $group 分组
     * @param bool $lock 是否锁定
     * @param int $count 计数
     * @return array
     */
    public function getGoodsOnlineList($condition, $field = '*', $pagesize = 0, $order = 'goods_id desc', $limit = 0, $group = '', $lock = false, $count = 0) {
        $condition[]=array('goods_state','=',self::STATE1);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return $this->getGoodsList($condition, $field, $pagesize, $order,$limit);
    }

    /**
     * 出售中的普通商品列表，即不包括虚拟商品、F码商品、预售商品
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $field 字段
     * @param type $pagesize 分页
     * @param type $type 类型
     * @return array
     */
    public function getGoodsListForPromotion($condition, $field = '*', $pagesize = 0, $type = '') {
        switch ($type) {
            case 'xianshi':
                $condition[]=array('goods_price','>',0);
                $condition[]=array('goods_state','=',self::STATE1);
                $condition[]=array('goods_verify','=',self::VERIFY1);
                break;
            case 'combo':
                $condition[]=array('goods_state','=',self::STATE1);
                $condition[]=array('goods_verify','=',self::VERIFY1);
                break;
            default:
                break;
        }
        return $this->getGoodsList($condition, $field, $pagesize);
    }





    /**
     * 仓库中的商品列表 卖家中心使用
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $pagesize 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsOfflineList($condition, $field = '*', $pagesize = 10, $order = "goods_id desc") {
        $condition[]=array('goods_state','=',self::STATE0);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return $this->getGoodsList($condition, $field, $pagesize, $order);
    }

    /**
     * 违规的商品列表 卖家中心使用
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $pagesize 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsLockUpList($condition, $field = '*', $pagesize = 10, $order = "goods_id desc") {
        $condition[]=array('goods_state','=',self::STATE10);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return $this->getGoodsList($condition, $field, $pagesize, $order);
    }

    /**
     * 等待审核或审核失败的商品列表 卖家中心使用
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $pagesize 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsWaitVerifyList($condition, $field = '*', $pagesize = 10, $order = "goods_id desc") {
        $condition[]=array('goods_verify','<>', self::VERIFY1);
        return $this->getGoodsList($condition, $field, $pagesize, $order);
    }

    /**
     * 查询商品SUK及其机构信息
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param string $field 字段
     * @return array
     */
    public function getGoodsStoreList($condition, $field = '*') {
//        $condition = $this->_getRecursiveClass($condition);
        return Db::name('goods')->alias('goods')->field($field)->join('store store', 'goods.store_id = store.store_id', 'inner')->where($condition)->select()->toArray();
    }

    /**
     * 查询推荐商品(随机排序) 
     * @access public
     * @author csdeshang
     * @param int $store_id 机构
     * @param int $limit 限制
     * @return array
     */
    public function getGoodsCommendList($store_id, $limit = 5) {
        $goods_commend_list = $this->getGoodsOnlineList(array(array('store_id' ,'=', $store_id), array('goods_commend' ,'=', 1)), 'goods_id,goods_name,goods_advword,goods_image,store_id,goods_price', 0, '', $limit, 'goods_id');
        if (!empty($goods_id_list)) {
            $tmp = array();
            foreach ($goods_id_list as $v) {
                $tmp[] = $v['goods_id'];
            }
            $goods_commend_list = $this->getGoodsOnlineList(array(array('goods_id','in', $tmp)), 'goods_id,goods_name,goods_advword,goods_image,store_id', 0, '', $limit);
        }
        return $goods_commend_list;
    }


    /**
     * 更新商品SUK数据
     * @access public
     * @author csdeshang
     * @param array $update 更新数据
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoods($update, $condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id');
        if (empty($goods_list)) {
            return true;
        }
        $goodsid_array = array();
        foreach ($goods_list as $value) {
            $goodsid_array[] = $value['goods_id'];
        }
        return $this->editGoodsById($update, $goodsid_array);
    }

    
    /**
     * 更新商品SUK数据
     * @access public
     * @author csdeshang
     * @param array $update 更新数据
     * @param int|array $goodsid_array 商品ID
     * @return boolean|unknown
     */
    public function editGoodsById($update, $goodsid_array) {
        if (empty($goodsid_array)) {
            return true;
        }
        $condition = array();
        $condition[] = array('goods_id','in', (array)$goodsid_array);
        $update['goods_edittime'] = TIMESTAMP;
        $result = Db::name('goods')->where($condition)->update($update);
        if ($result) {
            foreach ((array) $goodsid_array as $value) {
                $this->_dGoodsCache($value);
            }
        }
        return $result;
    }

    /**
     * 更新商品促销价 (需要验证抢购和秒杀是否进行)
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoodsPromotionPrice($condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id');
        $goods_array = array();
        foreach ($goods_list as $val) {
            $goods_array[$val['goods_id']] = $val;
        }
        $pxianshigoods_model = model('pxianshigoods');
        foreach ($goods_array as $k => $v) {
            
                // 查询秒杀时候进行
                $condition = array();
                $condition[] = array('goods_id','=',$k);
                $condition[] = array('xianshigoods_state','=',1);
                $condition[] = array('xianshigoods_starttime','<',TIMESTAMP);
                $condition[] = array('xianshigoods_end_time','>',TIMESTAMP);
                $xianshigoods = $pxianshigoods_model->getXianshigoodsInfo($condition);
                if (!empty($xianshigoods)) {
                    // 更新价格
                    $this->editGoodsById(array('goods_promotion_price' => $xianshigoods['xianshigoods_price'], 'goods_promotion_type' => 2), $k);
                    continue;
                }

                // 没有促销使用原价
                $this->editGoodsById(array('goods_promotion_price' => Db::raw('goods_price'), 'goods_promotion_type' => 0), $k);
        }
        return true;
    }


    /**
     * 锁定商品
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoodsLock($condition) {
        $update = array('goods_lock' => 1);
        return $this->editGoods($update, $condition);
    }

    /**
     * 解锁商品
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoodsUnlock($condition) {
        $update = array('goods_lock' => 0);
        return $this->editGoods($update, $condition);
    }

    /**
     * 更新商品信息
     * @access public
     * @author csdeshang
     * @param array $condition 更新条件
     * @param array $update1 更新数据1
     * @param array $update2 更新数据2
     * @return boolean
     */
    public function editProduces($condition, $update1, $update2 = array()) {
        $update2 = empty($update2) ? $update1 : $update2;
        $goods_array = $this->getGoodsList($condition, 'goods_id');
        if (empty($goods_array)) {
            return true;
        }
        $goods_id_array = array();
        foreach ($goods_array as $val) {
            $goods_id_array[] = $val['goods_id'];
        }
        $return2 = $this->editGoods($update2, array(array('goods_id','in', $goods_id_array)));
        if ($return2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新商品信息（审核失败）
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param array $update1 更新数据1
     * @param array $update2 更新数据2
     * @return boolean
     */
    public function editProducesVerifyFail($condition, $update1, $update2 = array()) {
        $result = $this->editProduces($condition, $update1, $update2);
        if ($result) {
            $commonlist = $this->getGoodsList($condition, 'goods_id,gc_id,goods_name,store_id,goods_verifyremark');
            foreach ($commonlist as $val) {
                $message = array();
                $message['goods_id'] = $val['goods_id'];
                $message['remark'] = $val['goods_verifyremark'];
                $ten_message=array($message['remark'],$message['goods_id']);
                $weixin_param = array(
                    'url' => config('ds_config.h5_store_site_url').'/pages/seller/goods/GoodsForm2?goods_id='.$val['goods_id'].'&class_id='.$val['gc_id'],
                    'data'=>array(
                        "keyword1" => array(
                            "value" => $val['goods_name'],
                            "color" => "#333"
                        ),
                        "keyword2" => array(
                            "value" => $val['goods_verifyremark'],
                            "color" => "#333"
                        )
                        )
                    );
                $this->_sendStoremsg('goods_verify', $val['store_id'], $message,$weixin_param, $message, $ten_message);
            }
        }
    }

    /**
     * 更新未锁定商品信息
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param array $update1 更新数据1
     * @param array $update2 更新数据2
     * @return boolean
     */
    public function editProducesNoLock($condition, $update1, $update2 = array()) {
        $condition[]=array('goods_lock','=',0);
        return $this->editProduces($condition, $update1, $update2);
    }

    /**
     * 商品下架
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function editProducesOffline($condition) {
        $update = array('goods_state' => self::STATE0);
        return $this->editProducesNoLock($condition, $update);
    }

    /**
     * 商品上架
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function editProducesOnline($condition) {
        $update = array('goods_state' => self::STATE1);
        // 禁售商品、审核失败商品不能上架。
        $condition[]=array('goods_state','=',self::STATE0);
        $condition[]=array('goods_verify','<>', self::VERIFY0);
        return $this->editProduces($condition, $update);
    }

    /**
     * 违规下架
     * @access public
     * @author csdeshang
     * @param array $update 数据
     * @param array $condition 条件
     * @return boolean
     */
    public function editProducesLockUp($update, $condition) {
        $update_param['goods_state'] = self::STATE10;
        $update = array_merge($update, $update_param);
        $return = $this->editProduces($condition, $update, $update_param);
        if ($return) {
            // 商品违规下架发送机构消息
            $common_list = $this->getGoodsList($condition, 'goods_id,gc_id,goods_name,store_id,goods_stateremark');
            foreach ($common_list as $val) {
                $message = array();
                $message['remark'] = $val['goods_stateremark'];
                $message['goods_id'] = $val['goods_id'];
                $ten_message=array($message['remark'],$message['goods_id']);
                $weixin_param = array(
                    'url' => config('ds_config.h5_store_site_url').'/pages/seller/goods/GoodsForm2?goods_id='.$val['goods_id'].'&class_id='.$val['gc_id'],
                    'data'=>array(
                        "keyword1" => array(
                            "value" => $val['goods_name'],
                            "color" => "#333"
                        ),
                        "keyword2" => array(
                            "value" => $val['goods_stateremark'],
                            "color" => "#333"
                        )
                        )
                    );
                $this->_sendStoremsg('goods_violation', $val['store_id'], $message,$weixin_param, $message, $ten_message);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取单条商品SKU信息
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param string $field 字段
     * @return array
     */
    public function getGoodsInfo($condition, $field = '*') {

        return Db::name('goods')->field($field)->where($condition)->find();
    }

    /**
     * 获取单条商品SKU信息及其促销信息
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID
     * @return type
     */
    public function getGoodsOnlineInfoForShare($goods_id) {
        $goods_info = $this->getGoodsOnlineInfoAndPromotionById($goods_id);
        if (empty($goods_info)) {
            return array();
        }
        return $goods_info;
    }

    /**
     * 查询出售中的商品详细信息及其促销信息
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID
     * @return array
     */
    public function getGoodsOnlineInfoAndPromotionById($goods_id) {
        $goods_info = $this->getGoodsInfoAndPromotionById($goods_id);
        if (empty($goods_info) || $goods_info['goods_state'] != self::STATE1 || $goods_info['goods_verify'] != self::VERIFY1) {
            return array();
        }
        return $goods_info;
    }

    /**
     * 查询商品详细信息及其促销信息
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID
     * @return array
     */
    public function getGoodsInfoAndPromotionById($goods_id) {
        $goods_info = $this->getGoodsInfoByID($goods_id);
        if (empty($goods_info)) {
            return array();
        }
        
        $goods_info['xianshi_info'] = '';
        //原价
        $goods_info['goods_original_price']=$goods_info['goods_price'];
        
        //秒杀
        if (config('ds_config.promotion_allow')) {
            $goods_info['xianshi_info'] = model('pxianshigoods')->getXianshigoodsInfoByGoodsID($goods_info['goods_id']);
        }
        return $goods_info;
    }

    /**
     * 查询出售中的商品列表及其促销信息
     * @access public
     * @author csdeshang
     * @param array $goodsid_array 商品ID数组
     * @return array
     */
    public function getGoodsOnlineListAndPromotionByIdArray($goodsid_array) {
        if (empty($goodsid_array) || !is_array($goodsid_array))
            return array();

        $goods_list = array();
        foreach ($goodsid_array as $goods_id) {
            $goods_info = $this->getGoodsOnlineInfoAndPromotionById($goods_id);
            if (!empty($goods_info))
                $goods_list[] = $goods_info;
        }

        return $goods_list;
    }
    
    /**
     * 获得商品SKU数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getGoodsCount($condition) {
        return Db::name('goods')->where($condition)->count();
    }

    /**
     * 获得出售中商品SKU数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param string $field 字段
     * @return int
     */
    public function getGoodsOnlineCount($condition, $field = '*') {
        $condition[]=array('goods_state','=',self::STATE1);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return Db::name('goods')->where($condition)->count($field);
    }



    /**
     * 仓库中的商品数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getGoodsOfflineCount($condition) {
        $condition[]=array('goods_state','=',self::STATE0);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return $this->getGoodsCount($condition);
    }

    /**
     * 等待审核的商品数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getGoodsWaitVerifyCount($condition) {
        $condition[]=array('goods_verify','=',self::VERIFY10);
        return $this->getGoodsCount($condition);
    }

    /**
     * 审核失败的商品数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getGoodsVerifyFailCount($condition) {
        $condition[]=array('goods_verify','=',self::VERIFY0);
        return $this->getGoodsCount($condition);
    }

    /**
     * 违规下架的商品数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getGoodsLockUpCount($condition) {
        $condition[]=array('goods_state','=',self::STATE10);
        $condition[]=array('goods_verify','=',self::VERIFY1);
        return $this->getGoodsCount($condition);
    }

    /**
     * 删除商品SKU信息
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return boolean
     */
    public function delGoods($condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id,goods_id,store_id');
        if (!empty($goods_list)) {
            $goodsid_array = array();
            // 删除商品二维码
            foreach ($goods_list as $val) {
                $goodsid_array[] = $val['goods_id'];
                @unlink(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_STORE . DIRECTORY_SEPARATOR . $val['store_id'] . DIRECTORY_SEPARATOR . $val['goods_id'] . '.png');
                // 删除商品缓存
                $this->_dGoodsCache($val['goods_id']);
            }
            // 秒杀
            model('pxianshigoods')->delXianshigoods(array(array('goods_id','in', $goodsid_array)));
            //删除商品浏览记录
            model('goodsbrowse')->delGoodsbrowse(array(array('goods_id','in', $goodsid_array)));
            // 删除买家收藏表数据
            $condition_fav = array();
            $condition_fav[] = array('fav_id','in', $goodsid_array);
            $condition_fav[] = array('fav_type','=', 'goods');
            model('favorites')->delFavorites($condition_fav);
            // 删除商品课程表数据
            model('goodscourses')->delGoodscourses(array(array('goods_id','in', $goodsid_array)));
            // 删除推荐组合
            model('goodscombo')->delGoodscombo(array(array('goods_id','in', $goodsid_array)));
            model('goodscombo')->delGoodscombo(array(array('combo_goodsid','in', $goodsid_array)));
        }
        return Db::name('goods')->where($condition)->delete();
    }

    /**
     * 商品删除及相关信息
     * @access public
     * @author csdeshang
     * @param  array $condition 列表条件
     * @return boolean
     */
    public function delGoodsAll($condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id,goods_id,store_id');
        if (empty($goods_list)) {
            return false;
        }
        $goodsid_array = array();
        $goods_id_array = array();
        foreach ($goods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
            $goods_id_array[] = $val['goods_id'];
            // 商品公共缓存
            $this->_dGoodsCache($val['goods_id']);
        }
        $goods_id_array = array_unique($goods_id_array);

        // 删除商品表数据
        $this->delGoods(array(array('goods_id','in', $goodsid_array)));
        return true;
    }

    /*     * 删除未锁定商品
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return type
     */

    public function delGoodsNoLock($condition) {
        $condition[]=array('goods_lock','=',0);
        $common_array = $this->getGoodsList($condition, 'goods_id');
        $common_array = array_under_reset($common_array, 'goods_id');
        $goods_id_array = array_keys($common_array);
        return $this->delGoodsAll(array(array('goods_id','in', $goods_id_array)));
    }

    /**
     * 发送机构消息
     * @access public
     * @author csdeshang
     * @param string $code 编码
     * @param int $store_id 机构OD
     * @param array $param 参数
     */
    private function _sendStoremsg($code, $store_id, $param,$weixin_param=array(),$ali_param=array(),$ten_param=array()) {
        model('cron')->addCron(array('cron_exetime'=>TIMESTAMP,'cron_type'=>'sendStoremsg','cron_value'=>serialize(array('code' => $code, 'store_id' => $store_id, 'param' => $param, 'weixin_param' => $weixin_param, 'ali_param' => $ali_param, 'ten_param' => $ten_param))));
    }

    /**
     * 获得商品子分类的ID
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return array
     */
    public function _getRecursiveClass($condition,$gc_id,$prefix='') {
        if (!is_array($gc_id)) {
            $gc_list = model('goodsclass')->getGoodsclassForCacheModel();
            if (!empty($gc_list[$gc_id])) {
                $all_gc_id=array($gc_id);
                $gcchild_id = empty($gc_list[$gc_id]['child']) ? array() : explode(',', $gc_list[$gc_id]['child']);
                $gcchildchild_id = empty($gc_list[$gc_id]['childchild']) ? array() : explode(',', $gc_list[$gc_id]['childchild']);
                $all_gc_id = array_merge($all_gc_id, $gcchild_id, $gcchildchild_id);
                if($prefix){
                    $prefix=$prefix.'.';
                }
                $condition[] = array($prefix.'gc_id','in', implode(',', $all_gc_id));
            }
        }
        return $condition;
    }

    /**
     * 由ID取得在售单个虚拟商品信息
     * @access public
     * @author csdeshang
     * @param array $goods_id 商品ID
     * @return array
     */
    public function getVirtualGoodsOnlineInfoByID($goods_id) {
        $goods_info = $this->getGoodsInfoByID($goods_id);
        return $goods_info;
    }

    /**
     * 取得商品详细信息（优先查询缓存）（在售）
     * 如果未找到，则缓存所有字段
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID
     * @return array
     */
    public function getGoodsOnlineInfoByID($goods_id) {
        $goods_info = $this->getGoodsInfoByID($goods_id);
        if ($goods_info['goods_state'] != self::STATE1 || $goods_info['goods_verify'] != self::VERIFY1) {
            $goods_info = array();
        }
        return $goods_info;
    }

    /**
     * 取得商品详细信息（优先查询缓存）
     * 如果未找到，则缓存所有字段
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID
     * @return array
     */
    public function getGoodsInfoByID($goods_id) {
        $goods_info = $this->_rGoodsCache($goods_id);
        if (empty($goods_info)) {
            $goods_info = $this->getGoodsInfo(array('goods_id' => $goods_id));
            $this->_wGoodsCache($goods_id, $goods_info);
        }
        return $goods_info;
    }



    /**
     * 读取商品缓存
     * @access public
     * @author csdeshang
     * @param type $goods_id 商品id
     * @return type
     */
    private function _rGoodsCache($goods_id) {
        return rcache($goods_id, 'goods');
    }

    /**
     * 写入商品缓存
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品id
     * @param array $goods_info 商品信息
     * @return boolean
     */
    private function _wGoodsCache($goods_id, $goods_info) {
        return wcache($goods_id, $goods_info, 'goods');
    }

    /**
     * 删除商品缓存
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品id
     * @return boolean
     */
    private function _dGoodsCache($goods_id) {
        return dcache($goods_id, 'goods');
    }



    /**
     * 获取单条商品信息
     * @access public
     * @author csdeshang
     * @param int $goods_id 商品ID 
     * @return array
     */
    public function getGoodsDetail($goods_id) {
        if ($goods_id <= 0) {
            return null;
        }
        $result1 = $this->getGoodsInfoAndPromotionById($goods_id);

        if (empty($result1)) {
            return null;
        }
        $result2 = $this->getGoodsInfoByID($result1['goods_id']);
        $goods_info = array_merge($result2, $result1);
        // 手机商品描述
        if ($goods_info['mobile_body'] != '') {
            $mobile_body_array = unserialize($goods_info['mobile_body']);
            if (is_array($mobile_body_array)) {
                $mobile_body = '';
                foreach ($mobile_body_array as $val) {
                    switch ($val['type']) {
                        case 'text':
                            $mobile_body .= '<div>' . $val['value'] . '</div>';
                            break;
                        case 'image':
                            $mobile_body .= '<img src="' . $val['value'] . '">';
                            break;
                    }
                }
                $goods_info['mobile_body'] = $mobile_body;
            }
        }


        $goods_image = array();
        $goods_image[] = array(goods_thumb($goods_info, 270), goods_thumb($goods_info, 1260));


        //秒杀
        if (!empty($goods_info['xianshi_info'])) {
            $goods_info['promotion_type'] = 'xianshi';
            $goods_info['title'] = $goods_info['xianshi_info']['xianshi_title'];
            $goods_info['remark'] = $goods_info['xianshi_info']['xianshi_title'];
            $goods_info['promotion_price'] = $goods_info['xianshi_info']['xianshigoods_price'];
            $goods_info['down_price'] = ds_price_format($goods_info['goods_price'] - $goods_info['xianshi_info']['xianshigoods_price']);
            $goods_info['lower_limit'] = $goods_info['xianshi_info']['xianshigoods_lower_limit'];
            $goods_info['explain'] = $goods_info['xianshi_info']['xianshi_explain'];
            $goods_info['promotion_end_time'] = $goods_info['xianshi_info']['xianshigoods_end_time'];
            unset($goods_info['xianshi_info']);
        }
        
        // 商品受关注次数加1
        $goods_info['goods_click'] = intval($goods_info['goods_click']) + 1;
        if (config('ds_config.cache_open')) {
            wcache('updateRedisDate', array($goods_id => $goods_info['goods_click']), 'goodsClick');
        } else {
            $this->editGoodsById(array('goods_click' => Db::raw('goods_click+1')), $goods_id);
        }
        $result = array();
        $result['goods_info'] = $goods_info;
        $result['goods_image'] = $goods_image;
        return $result;
    }

    /**
     * 获取移动端商品
     * @access public
     * @author csdeshang
     * @param type $goods_id 商品ID
     * @return array
     */
    public function getMobileBodyByGoodsID($goods_id) {
        $common_info = $this->_rGoodsCache($goods_id);
        if (empty($common_info)) {
            $common_info = $this->getGoodsInfo(array('goods_id' => $goods_id));
            $this->_wGoodsCache($goods_id, $common_info);
        }


        // 手机商品描述
        if ($common_info['mobile_body'] != '') {
            $mobile_body_array = unserialize($common_info['mobile_body']);
            if (is_array($mobile_body_array)) {
                $mobile_body = '';
                foreach ($mobile_body_array as $val) {
                    switch ($val['type']) {
                        case 'text':
                            $mobile_body .= '<div>' . $val['value'] . '</div>';
                            break;
                        case 'image':
                            $mobile_body .= '<img src="' . $val['value'] . '">';
                            break;
                    }
                }
                $common_info['mobile_body'] = $mobile_body;
            }
        }
        return $common_info;
    }


    /**
     * 下单变更库存销量
     * @param unknown $goods_buy_quantity
     */
    public function createOrderUpdateStorage($goods_buy_quantity)
    {
        foreach ($goods_buy_quantity as $goods_id => $quantity) {
            $data = array();
            $data['goods_salenum'] = Db::raw('goods_salenum+'.$quantity);
            $result = $this->editGoodsById($data, $goods_id);
        }
        if (!$result) {
            return ds_callback(false, '变更商品库存与销量失败');
        }
        else {
            return ds_callback(true);
        }
    }

    /**
     * 取消订单变更库存销量
     * @param unknown $goods_buy_quantity
     */
    public function cancelOrderUpdateStorage($goods_buy_quantity)
    {
        foreach ($goods_buy_quantity as $goods_id => $quantity) {
            $data = array();
            $data['goods_salenum'] = Db::raw('goods_salenum-'.$quantity);
            $result = $this->editGoodsById($data, $goods_id);
            if (!$result) {
                return ds_callback(false, '变更商品库存与销量失败');
            }
        }
            return ds_callback(true);
    }

}
