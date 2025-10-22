<?php

namespace app\crontab\controller;
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
 * 定时器
 */
class  Date extends BaseCron {

    /**
     * 该文件中所有任务执行频率，默认1天，单位：秒
     * @var int
     */
    const EXE_TIMES = 86400;

    /**
     * 优惠券即将到期提醒时间，单位：天
     * @var int
     */
    const VOUCHER_INTERVAL = 5;


    /**
     * 订单结束后可评论时间，15天，60*60*24*15
     * @var int
     */
    const ORDER_EVALUATE_TIME = 1296000;

    /**
     * 默认方法
     */
    public function index() {

        //刷新百度网盘token
        $this->_baidu_pan_refresh();
        
        //订单超期后不允许评价
        $this->_order_eval_expire_update();

        //未付款订单超期自动关闭
        $this->_order_timeout_cancel();

        //增加会员积分和经验值
        $this->_add_points();
        
        //订单自动完成
        $this->_order_auto_complete();
        
        //店铺到期关闭
        $this->_store_auto_close();
        
        //生成机构结算单
//        $this->_creat_bill();

        //代金券即将过期提醒
        $this->_voucher_will_expire();

        //更新商品访问量
        $this->_goods_click_update();

        //更新浏览量
        $this->_goods_browse_update();
    }
    
    /*
     * 刷新百度网盘token
     */
    function _baidu_pan_refresh(){
        if(config('ds_config.baidu_pan_api_key')){
            $store_model=model('store');
            $condition=array();
            $condition[]=array('baidu_pan_expires_in','>',0);
            $condition[]=array('baidu_pan_expires_in','<',TIMESTAMP-86400);
            $store_list = Db::name('store')->field('store_id,baidu_pan_refresh_token')->where($condition)->limit(100)->select()->toArray();
            foreach($store_list as $val){
                $res = http_request('https://openapi.baidu.com/oauth/2.0/token?grant_type=refresh_token&refresh_token=' . $val['baidu_pan_refresh_token'] . '&client_id=' . config('ds_config.baidu_pan_api_key') . '&client_secret=' . config('ds_config.baidu_pan_secret_key'));
                $res = json_decode($res, true);
                if (isset($res['error']) && $res['errno']) {
                    continue;
                }
                $store_model->editStore(array(
                    'baidu_pan_access_token' => $res['access_token'],
                    'baidu_pan_expires_in' => TIMESTAMP + $res['expires_in'],
                    'baidu_pan_refresh_token' => $res['refresh_token'],
                        ), array('store_id' => $val['store_id']));
            }
        }
        
    }
    
    /*
     * 店铺到期关闭
     */
    function _store_auto_close() {
        $store_model = model('store');
        $condition = array();
        $condition[] = array('store_state', '=', 1);
        $condition[] = array('store_endtime', '>', 0);
        $condition[] = array('store_endtime', '<', TIMESTAMP);
        $store_ids = Db::name('store')->where($condition)->limit(100)->column('store_id');
        $goods_model = model('goods');
        foreach ($store_ids as $store_id) {
            Db::startTrans();
            try {
                $store_model->editStore(array('store_state' => 0, 'store_recommend' => 0), array(array('store_id', '=', $store_id)));
                //根据店铺状态修改该店铺所有商品状态
                $goods_model->editProducesOffline(array(array('store_id', '=', $store_id)));
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->log('到期店铺关闭失败[店铺ID：' . $store_id . ']' . $e->getMessage());
            }
        }
    }

    /*
     * 生成机构结算单
     */

    public function _creat_bill() {
        //搜索机构结算日期小于当前时间减结算周期的所有机构，每次100个
        $store_ids = Db::name('store')->where(array(array('store_bill_time','<', strtotime(date('Y-m-d 0:0:0')) - intval(config('ds_config.store_bill_cycle')) * 86400)))->limit(100)->column('store_id');
        $storemoneylog_mod = model('storemoneylog');
        $orderinviter_model = model('orderinviter');
        $bill_model = model('bill');
        foreach ($store_ids as $store_id) {
            
            $store_info = Db::name('store')->where('store_id', $store_id)->field('store_id,store_name,seller_name,store_bill_time')->lock(true)->find();
            if ($store_info) {
                Db::startTrans();
                try {
                    $data = array();
                    //如果机构没有结算过，则查询与结算单相关项目的最小时间作为结算单开始时间
                    if (!$store_info['store_bill_time']) {
                        $ob_startdate = 0;
                        $storecost_time = Db::name('storecost')->where(array(array('storecost_store_id' ,'=', $store_id), array('storecost_state' ,'=', 0), array('storecost_time','<', strtotime(date('Y-m-d 0:0:0')) - intval(config('ds_config.store_bill_cycle')) * 86400)))->order('storecost_time asc')->lock(true)->value('storecost_time');
     
                        //已完成的虚拟订单为超过可退款期，可直接根据订单状态为已完成进行判断
                        $vr_order_time = Db::name('vrorder')->where(array(array('store_id' ,'=', $store_id), array('order_state' ,'=', ORDER_STATE_SUCCESS), array('finnshed_time','<', strtotime(date('Y-m-d 0:0:0')) - intval(config('ds_config.store_bill_cycle')) * 86400), array('ob_no' ,'=', 0)))->order('add_time asc')->lock(true)->value('add_time');
                        $ob_startdate = (!$ob_startdate || ($ob_startdate && $storecost_time && $storecost_time < $ob_startdate)) ? $storecost_time : $ob_startdate;
                        $ob_startdate = (!$ob_startdate || ($ob_startdate && $vr_order_time && $vr_order_time < $ob_startdate)) ? $vr_order_time : $ob_startdate;
                    } else {
                        $ob_startdate = $store_info['store_bill_time'];
                    }
                    if ($ob_startdate) {
                        $ob_enddate = $ob_startdate + intval(config('ds_config.store_bill_cycle')) * 86400;
                        //机构促销活动费用
                        $storecost_sum = Db::name('storecost')->where(array(array('storecost_store_id' ,'=', $store_id), array('storecost_state' ,'=', 0), array('storecost_time', '<', $ob_enddate)))->field('SUM(storecost_price) AS ob_store_cost_totals,COUNT(*) AS count')->lock(true)->find();

         
                        $vr_order_ids = Db::name('vrorder')->where(array(array('store_id' ,'=', $store_id), array('order_state' ,'=', ORDER_STATE_SUCCESS), array('finnshed_time', '<', $ob_enddate), array('ob_no' ,'=', 0)))->lock(true)->column('order_id');
                        if ($storecost_sum && $storecost_sum['count']) {
                            $data['ob_store_cost_totals'] = $storecost_sum['ob_store_cost_totals'];
                        }
                        if ($vr_order_ids) {
                            foreach ($vr_order_ids as $order_id) {
                                $orderinviter_model->giveMoney($order_id, 1);
                            }
                            //分销的佣金
                            $condition = array();
                            $condition[] = array('orderinviter_order_id','in', $vr_order_ids);
                            $condition[] = array('orderinviter_valid','=', 1);
                            $condition[] = array('orderinviter_order_type','=', 1);
                            $orderinviter_sum = Db::name('orderinviter')->where($condition)->field('SUM(orderinviter_money) AS ob_vr_inviter_totals')->lock(true)->find();
                            $vr_order_sum = Db::name('vrorder')->where(array(array('order_id','in', $vr_order_ids)))->field('SUM(order_amount) AS ob_vr_order_totals,SUM(ROUND(order_amount*commis_rate/100,2)) AS ob_vr_commis_totals,SUM(refund_amount) AS ob_vr_order_return_totals,SUM(ROUND(refund_amount*commis_rate/100,2)) AS ob_vr_commis_return_totals')->lock(true)->find();
                            $data['ob_vr_order_totals'] = $vr_order_sum['ob_vr_order_totals'];
                            $data['ob_vr_commis_totals'] = $vr_order_sum['ob_vr_commis_totals'];
                            $data['ob_vr_order_return_totals'] = $vr_order_sum['ob_vr_order_return_totals'];
                            $data['ob_vr_commis_return_totals'] = $vr_order_sum['ob_vr_commis_return_totals'];
                            $data['ob_vr_inviter_totals'] = (!empty($orderinviter_sum)) ? $orderinviter_sum['ob_vr_inviter_totals'] : 0;
                        }
                        if (!empty($data)) {
                            $data['ob_createdate'] = TIMESTAMP;
                            $data['ob_startdate'] = $ob_startdate;
                            $data['ob_enddate'] = $ob_enddate;
                            $data['ob_store_id'] = $store_id;
                            $data['ob_store_name'] = $store_info['store_name']; //平台自营机构不需要增加机构资金
                            $data['ob_state'] = 1;
                            if (!isset($data['ob_store_cost_totals'])) {
                                $data['ob_store_cost_totals'] = 0;
                            }
                            if (!isset($data['ob_inviter_totals'])) {
                                $data['ob_inviter_totals'] = 0;
                            }
                            if (!isset($data['ob_vr_order_totals'])) {
                                $data['ob_vr_order_totals'] = 0;
                            }
                            if (!isset($data['ob_vr_order_return_totals'])) {
                                $data['ob_vr_order_return_totals'] = 0;
                            }
                            if (!isset($data['ob_vr_commis_totals'])) {
                                $data['ob_vr_commis_totals'] = 0;
                            }
                            if (!isset($data['ob_vr_commis_return_totals'])) {
                                $data['ob_vr_commis_return_totals'] = 0;
                            }
                            if (!isset($data['ob_vr_inviter_totals'])) {
                                $data['ob_vr_inviter_totals'] = 0;
                            }
                            //计算实际可得金额
                            $data['ob_result_totals'] = round($data['ob_vr_order_totals'] + $data['ob_vr_order_return_totals'] - $data['ob_store_cost_totals'] -$data['ob_vr_commis_totals'] - $data['ob_vr_commis_return_totals'] - $data['ob_vr_inviter_totals'], 2);
                            //插入到结算账单
                            $ob_no = $bill_model->addOrderbill($data);
                            if (!$ob_no) {
                                throw new \think\Exception('新增结算单失败', 10006);
                            }

                            if ($vr_order_ids) {
                                //订单更新结算单号
                                if (!Db::name('vrorder')->where(array(array('order_id','in', $vr_order_ids)))->update(array('ob_no' => $ob_no))) {
                                    throw new \think\Exception('更新虚拟订单结算单号失败', 10006);
                                }
                            }
                        }
                        //机构活动改成已结算
                        if (!empty($storecost_sum) && $storecost_sum['count'] && !Db::name('storecost')->where(array(array('storecost_store_id' ,'=', $store_id), array('storecost_state' ,'=', 0), array('storecost_time','<', $ob_enddate)))->update(array('storecost_state' => 1))) {
                            throw new \think\Exception('更新机构活动结算单号失败', 10006);
                        }
                    } else {
                        $ob_enddate = strtotime(date('Y-m-d 0:0:0')) - intval(config('ds_config.store_bill_cycle')) * 86400;
                    }
                    //更新机构结算时间
                    if (!Db::name('store')->where('store_id', $store_id)->update(array('store_bill_time' => $ob_enddate))) {
                        throw new \think\Exception('更新机构结算时间失败', 10006);
                    }
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->log('[机构名称：' . $store_info['store_name'] . ']' . $e->getMessage());
                }
            }
            
        }
        //如果还有未更新结算日期的机构，则重定向继续生产结算单
        if (Db::name('store')->where(array(array('store_bill_time','<', strtotime(date('Y-m-d 0:0:0')) - intval(config('ds_config.store_bill_cycle')) * 86400)))->count() > 0) {
            $this->redirect('date/index');
        }
    }

    /**
     * 未付款订单超期自动关闭
     */
    private function _order_timeout_cancel() {


        //虚拟订单超期未支付系统自动关闭
        $_break = false;
        $vrorder_model = model('vrorder');
        $logic_vrorder = model('vrorder', 'logic');
        $condition = array();
        $condition[] = array('order_state','=',ORDER_STATE_NEW);
        $condition[] = array('add_time','<', TIMESTAMP - config('ds_config.order_auto_cancel_day') * self::EXE_TIMES);

        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) {
                break;
            }
            $order_list = $vrorder_model->getVrorderList($condition, '', '*', '', 100);
            if (empty($order_list))
                break;
            foreach ($order_list as $order_info) {
                $result = $logic_vrorder->changeOrderStateCancel($order_info, 'system', '超期未支付系统自动关闭订单', false);
            }
            if (!$result['code']) {
                $this->log('虚拟订单超期未支付关闭失败SN:' . $order_info['order_sn']);
                $_break = true;
                break;
            }
        }
    }


    /**
     * 订单自动完成
     */
    private function _order_auto_complete() {

        //虚拟订单过使用期自动完成
        $_break = false;
        $vrorder_model = model('vrorder');
        $logic_vrorder = model('vrorder', 'logic');
        $condition = array();
        $condition[] = array('order_state','=',ORDER_STATE_PAY);
        //订单自动完成, 支付时间 + 允许退款时间 大于 当前时间
        $condition[] = array('payment_time','<', TIMESTAMP - config('ds_config.order_refund_time')*60*60*24);
        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) {
                break;
            }
            $order_list = $vrorder_model->getVrorderList($condition, '', 'order_id,order_sn', 'payment_time asc', 100);
            if (empty($order_list))
                break;
            foreach ($order_list as $order_info) {
                $result = $logic_vrorder->changeOrderStateSuccess($order_info['order_id']);
                if (!$result['code']) {
                    $this->log('虚拟订单过使用期自动完成失败SN:' . $order_info['order_sn']);
                    $_break = true;
                    break;
                }
            }
        }
    }
    
    /**
     * 增加会员积分和经验值
     */
    private function _add_points() {
        return;
        $points_model = model('points');
        $exppoints_model = model('exppoints');

        //24小时之内登录的会员送积分和经验值,每次最多处理5W个会员
        $member_model = model('member');
        $condition = array();
        $condition[] = array('member_logintime','>', TIMESTAMP - self::EXE_TIMES);
        for ($i = 0; $i < 50000; $i = $i + 100) {
            $member_list = $member_model->getMemberList($condition, 'member_name,member_id', 0, '', "{$i},100");
            if (!empty($member_list)) {
                foreach ($member_list as $member_info) {
                    if (config('ds_config.points_isuse')) {
                        $points_model->savePointslog('login', array('pl_memberid' => $member_info['member_id'], 'pl_membername' => $member_info['member_name']), true);
                    }
                    $exppoints_model->saveExppointslog('login', array('explog_memberid' => $member_info['member_id'], 'explog_membername' => $member_info['member_name']), true);
                }
            } else {
                break;
            }
        }

        //24小时之内注册的会员送积分,每次最多处理5W个会员
        if (config('ds_config.points_isuse')) {
            $condition = array();
            $condition[] = array('member_addtime','>', TIMESTAMP - self::EXE_TIMES);
            for ($i = 0; $i < 50000; $i = $i + 100) {
                $member_list = $member_model->getMemberList($condition, 'member_name,member_id', 0, 'member_id desc', "{$i},100");
                if (!empty($member_list)) {
                    foreach ($member_list as $member_info) {
                        $points_model->savePointslog('regist', array('pl_memberid' => $member_info['member_id'], 'pl_membername' => $member_info['member_name']), true);
                    }
                } else {
                    break;
                }
            }
        }


        //24小时之内完成了实物订单送积分和经验值,每次最多处理5W个订单
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('finnshed_time','>', TIMESTAMP - self::EXE_TIMES);
        for ($i = 0; $i < 50000; $i = $i + 100) {
            $order_list = $vrorder_model->getVrorderList($condition, '', 'buyer_name,buyer_id,order_amount,order_sn,order_id', '', "{$i},100");
            if (!empty($order_list)) {
                foreach ($order_list as $order_info) {
                    if (config('ds_config.points_isuse')) {
                        $points_model->savePointslog('vrorder', array('pl_memberid' => $order_info['buyer_id'], 'pl_membername' => $order_info['buyer_name'], 'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn'], 'order_id' => $order_info['order_id']), true);
                    }
                    $exppoints_model->saveExppointslog('vrorder', array('explog_memberid' => $order_info['buyer_id'], 'explog_membername' => $order_info['buyer_name'], 'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn'], 'order_id' => $order_info['order_id']), true);
                }
            } else {
                break;
            }
        }
    }

    /**
     * 代金券即将过期提醒
     */
    private function _voucher_will_expire() {
        $time_start = mktime(0, 0, 0, date("m"), date("d") + self::VOUCHER_INTERVAL, date("Y"));
        $time_stop = $time_start + self::EXE_TIMES - 1;
        $condition = array();
        $condition[] = array('voucher_enddate','>=', $time_start);
        $condition[] = array('voucher_enddate','<=', $time_stop);
        
        $list = model('voucher')->getVoucherUnusedList($condition);
        if (!empty($list)) {
            foreach ($list as $val) {
                $param = array();
                $param['code'] = 'voucher_will_expire';
                $param['member_id'] = $val['voucher_owner_id'];
                $param['ali_param'] = array(
                    'indate' => date('Y-m-d H:i:s', $val['voucher_enddate']),
                );
                $param['ten_param'] = array(
                    date('Y-m-d H:i:s', $val['voucher_enddate']),
                );
                $param['param'] = array_merge($param['ali_param'],array(
                    'voucher_url' => HOME_SITE_URL .'/Membervoucher/index'
                ));
                $param['weixin_param'] = array(
                    'url' => config('ds_config.h5_site_url').'/pages/member/voucher/VoucherList',
                    'data'=>array(
                        "keyword1" => array(
                            "value" => $val['voucher_code'],
                            "color" => "#333"
                        ),
                        "keyword2" => array(
                            "value" => date('Y-m-d', $val['voucher_startdate']).'~'.date('Y-m-d', $val['voucher_enddate']),
                            "color" => "#333"
                        )
                    ),
                );
                model('cron')->addCron(array('cron_exetime'=>TIMESTAMP,'cron_type'=>'sendMemberMsg','cron_value'=>serialize($param)));
            }
        }
    }


    /**
     * 订单超期后不允许评价
     */
    private function _order_eval_expire_update() {


        //虚拟订单超期未评价自动更新状态，每次最多更新1000个订单
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('order_state','=',ORDER_STATE_SUCCESS);
        $condition[] = array('evaluation_state','=',0);
        $condition[] = array('finnshed_time','<', TIMESTAMP - self::ORDER_EVALUATE_TIME);
        $update = array();
        $update['evaluation_state'] = 2;
        $update = $vrorder_model->editVrorder($update, $condition, 1000);
        if (!$update) {
            $this->log('更新虚拟订单超期不能评价失败');
        }
    }

    /**
     * 更新商品访问量(redis)
     */
    private function _goods_click_update() {
        $data = rcache('updateRedisDate', 'goodsClick');
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                model('goods')->editGoodsById(array('goods_click' => Db::raw('goods_click+' . $val)), $key);
            }
        }
        dcache('updateRedisDate', 'goodsClick');
    }

    /**
     * 将缓存中的浏览记录存入数据库中，并删除30天前的浏览历史
     */
    private function _goods_browse_update() {
        $goodsbrowse_model = model('goodsbrowse');
        //将cache中的记录存入数据库
        //如果浏览记录已经存入了缓存中，则将其整理到数据库中
        //上次更新缓存的时间
        $latest_record = $goodsbrowse_model->getOneGoodsbrowse(array(), '', 'goodsbrowse_time desc');
        $starttime = ($t = intval($latest_record['goodsbrowse_time'])) ? $t : 0;
        $monthago = strtotime(date('Y-m-d', TIMESTAMP)) - 86400 * 30;
        $member_model = model('member');

        //查询会员信息总条数
        $countnum = $member_model->getMemberCount(array());
        $eachnum = 100;
        for ($i = 0; $i < $countnum; $i += $eachnum) {//每次查询100条
            $member_list = $member_model->getMemberList(array(), '*', 0, 'member_id asc', "$i,$eachnum");
            foreach ((array) $member_list as $k => $v) {
                $insert_arr = array();
                $goodsid_arr = array();
                //生成缓存的键值
                $hash_key = $v['member_id'];
                $browse_goodsid = rcache($hash_key, 'goodsbrowse');

                if ($browse_goodsid) {
                    //删除缓存中多余的浏览历史记录，仅保留最近的30条浏览历史，先取出最近30条浏览历史的商品ID
                    $cachegoodsid_arr = $browse_goodsid['goodsid'] ? unserialize($browse_goodsid['goodsid']) : array();
                    unset($browse_goodsid['goodsid']);

                    if ($cachegoodsid_arr) {
                        $cachegoodsid_arr = array_slice($cachegoodsid_arr, -30, 30, true);
                    }
                    //处理存入数据库的浏览历史缓存信息
                    $_cache = rcache($hash_key, 'goodsbrowse');
                    foreach ((array) $_cache as $c_k => $c_v) {
                        $c_v = unserialize($c_v);
                        if (isset($c_v['goodsbrowse_time']) && $c_v['goodsbrowse_time'] >= $starttime) {//如果 缓存中的数据未更新到数据库中（即添加时间大于上次更新到数据库中的数据时间）则将数据更新到数据库中
                            $tmp_arr = array();
                            $tmp_arr['goods_id'] = $c_v['goods_id'];
                            $tmp_arr['member_id'] = $v['member_id'];
                            $tmp_arr['goodsbrowse_time'] = $c_v['goodsbrowse_time'];
                            $tmp_arr['gc_id'] = $c_v['gc_id'];
                            $tmp_arr['gc_id_1'] = $c_v['gc_id_1'];
                            $tmp_arr['gc_id_2'] = $c_v['gc_id_2'];
                            $tmp_arr['gc_id_3'] = $c_v['gc_id_3'];
                            $insert_arr[] = $tmp_arr;
                            $goodsid_arr[] = $c_v['goods_id'];
                        }
                        //除了最近的30条浏览历史之外多余的浏览历史记录或者30天之前的浏览历史从缓存中删除
                        if (!in_array($c_v['goods_id'], $cachegoodsid_arr) || $c_v['goodsbrowse_time'] < $monthago) {
                            unset($_cache[$c_k]);
                        }
                    }
                    //删除已经存在的该商品浏览记录
                    if ($goodsid_arr) {
                        $condition = array();
                        $condition[] = array('member_id','=',$v['member_id']);
                        $condition[] = array('goods_id','in',$goodsid_arr);
                        $goodsbrowse_model->delGoodsbrowse($condition);
                    }
                    //将缓存中的浏览历史存入数据库
                    if ($insert_arr) {
                        $goodsbrowse_model->addGoodsbrowseAll($insert_arr);
                    }
                    //重新赋值浏览历史缓存
                    dcache($hash_key, 'goodsbrowse');
                    $_cache['goodsid'] = serialize($cachegoodsid_arr);
                    wcache($hash_key, $_cache, 'goodsbrowse');
                }
            }
        }
        //删除30天前的浏览历史
        $goodsbrowse_model->delGoodsbrowse(array(array('goodsbrowse_time','<', $monthago)));
    }

}

?>
