<?php

namespace app\api\controller;
use think\facade\Lang;
use think\facade\Db;
use app\common\model\Storemoneylog;

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
class  MobileSeller extends MobileHome {

    protected $seller_info = array();
    protected $seller_group_info = array();
    protected $member_info = array();
    protected $store_info = array();
    protected $store_grade = array();

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/baseseller.lang.php');
        $key = request()->header('X-DS-KEY');
        $state=input('param.state');//百度网盘用户授权
        if (empty($key)) {
            if(!$state){
                ds_json_encode(13001,'请登录', array('login' => '0'));
            }else{
                $key=$state;
            }
        }
        
        $condition = array();
        $condition[] = array('platform_type', '=', 'seller');
        $condition[] = array('platform_token', '=', $key);
        $platformtoken_info = model('platformtoken')->getPlatformtokenInfo($condition);
        if (empty($platformtoken_info)) {
            ds_json_encode(13001,'请登录', array('login' => '0'));
        }

        $seller_model = model('seller');
        $member_model = model('member');
        $store_model = model('store');
        $sellergroup_model = model('sellergroup');

        $this->seller_info = $seller_model->getSellerInfo(array('seller_id' => $platformtoken_info['platform_userid']));
        $this->seller_info['seller_token'] = $platformtoken_info['platform_token'];
        
        $this->member_info = $member_model->getMemberInfoByID($this->seller_info['member_id']);
        $this->store_info = $store_model->getStoreInfoByID($this->seller_info['store_id']);
        $this->seller_group_info = $sellergroup_model->getSellergroupInfo(array('sellergroup_id' => $this->seller_info['sellergroup_id']));
        if(!is_array($this->seller_group_info)){
            $this->seller_group_info=array();
        }
        // 机构等级
            $store_grade = rkcache('storegrade', true);
            $this->store_grade = $store_grade[$this->store_info['grade_id']];

        if (empty($this->member_info)) {
            ds_json_encode(13001,'请登录', array('login' => '0'));
        }
        if ($this->seller_info['is_admin'] !== 1 && request()->controller() !== 'Sellerindex' && !(request()->controller() == 'Sellergoodsclass' && request()->action() == 'get_tree')) {
            $seller_limits=isset($this->seller_group_info['sellergroup_limits'])?explode(',', $this->seller_group_info['sellergroup_limits']):'';
                if (!in_array(request()->controller(), $seller_limits)) {
                    ds_json_encode(10001,lang('have_no_legalpower'));
                }
            }
        $seller_model->createSellerSession($this->member_info,$this->store_info,$this->seller_info, $this->seller_group_info);
    }
    /**
     * 记录卖家日志
     *
     * @param $content 日志内容
     * @param $state 1成功 0失败
     */
    protected function recordSellerlog($content = '', $state = 1) {
        $seller_info = array();
        $seller_info['sellerlog_content'] = $content;
        $seller_info['sellerlog_time'] = TIMESTAMP;
        $seller_info['sellerlog_seller_id'] =$this->seller_info['seller_id'];
        $seller_info['sellerlog_seller_name'] = $this->seller_info['seller_name'];
        $seller_info['sellerlog_store_id'] = $this->store_info['store_id'];
        $seller_info['sellerlog_seller_ip'] = request()->ip();
        $seller_info['sellerlog_url'] =  'api/' . request()->controller() . '/' . request()->action();
        $seller_info['sellerlog_state'] = $state;
        $sellerlog_model = model('sellerlog');
        $sellerlog_model->addSellerlog($seller_info);
    }
    
    /**
     * 记录机构费用
     *
     * @param $storecost_price 费用金额
     * @param $storecost_remark 费用备注
     */
    protected function recordStorecost($storecost_price, $storecost_remark) {

        Db::startTrans();
        try {
            $storecost_model = model('storecost');
            $param = array();
            $param['storecost_store_id'] = $this->store_info['store_id'];
            $param['storecost_seller_id'] = $this->seller_info['seller_id'];
            $param['storecost_price'] = $storecost_price;
            $param['storecost_remark'] = $storecost_remark;
            $param['storecost_state'] = 1;
            $param['storecost_time'] = TIMESTAMP;
            $storecost_model->addStorecost($param);

            $storemoneylog_model = model('storemoneylog');
            //扣除店铺费用
            $data = array(
                'store_id' => $this->store_info['store_id'],
                'storemoneylog_type' => Storemoneylog::TYPE_STORE_COST,
                'storemoneylog_state' => Storemoneylog::STATE_VALID,
                'storemoneylog_add_time' => TIMESTAMP,
                'storemoneylog_avaliable_money' => -$storecost_price,
                'storemoneylog_desc' => $storecost_remark,
            );
            $storemoneylog_model->changeStoremoney($data);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            ds_json_encode(10001, $e->getMessage());
        }

        // 发送机构消息
        $param = array();
        $param['code'] = 'store_cost';
        $param['store_id'] = $this->store_info['store_id'];
        $param['ali_param'] = array(
            'price' => $storecost_price,
            'seller_name' => $this->seller_info['seller_name'],
            'remark' => $storecost_remark
        );
        $param['ten_param'] = array(
            $storecost_price,
            $this->seller_info['seller_name'],
            $storecost_remark
        );
        $param['param'] = $param['ali_param'];
        //微信模板消息
        $param['weixin_param'] = array(
            'url' => config('ds_config.h5_store_site_url') . '/pages/seller/cost/CostList',
            'data' => array(
                "keyword1" => array(
                    "value" => $storecost_price,
                    "color" => "#333"
                ),
                "keyword2" => array(
                    "value" => date('Y-m-d H:i'),
                    "color" => "#333"
                )
            ),
        );
        model('cron')->addCron(array('cron_exetime' => TIMESTAMP, 'cron_type' => 'sendStoremsg', 'cron_value' => serialize($param)));
    }
}

?>
