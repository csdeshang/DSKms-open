<?php

namespace app\crontab\controller;
use app\BaseController;
use think\facade\Log;
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
class  BaseCron extends BaseController {

    public function shutdown(){
        exit("run ".request()->controller()." success at ".date('Y-m-d H:i:s',TIMESTAMP)."\n");
    }

    public function initialize(){
        parent::initialize();
        $config_list = rkcache('config', true);
        config($config_list,'ds_config');
        set_time_limit(600);
        error_reporting(E_ALL & ~E_NOTICE);
        register_shutdown_function(array($this,"shutdown"));
    }

    /**
     * 记录日志
     * @param unknown $content 日志内容
     * @param boolean $if_sql 是否记录SQL
     */
    protected function log($content, $if_sql = true) {

        Log::record('queue\\'.$content);
    }

    /**
     * 更新订单中的佣金比例[多个地方调用，写到父类中]
     */
    protected function _order_commis_rate_update() {

        //虚拟订单，每次最多处理50W个商品佣金
        $_break = false;
        $vrorder_model = model('vrorder');
        $vrrefund_model = model('vrrefund');

        for ($i = 0; $i < 5000; $i++) {
            if ($_break) {
                break;
            }
            Db::startTrans();
            $goods_list = $vrorder_model->getVrorderList(array('commis_rate' => 200), '', 'order_id,store_id,gc_id', '', 100);
            if (!empty($goods_list)) {
                //$commis_rate_list : store_id => array(gc_id => commis_rate)
                $commis_rate_list = $storebindclass_model->getStoreGcidCommisRateList($goods_list);
                //更新订单商品佣金值
                foreach ($goods_list as $v) {
                    //如果未查到机构或分类ID，则佣金置0
                    if (!isset($commis_rate_list[$v['store_id']][$v['gc_id']])) {
                        $commis_rate = 0;
                    } else {
                        $commis_rate = $commis_rate_list[$v['store_id']][$v['gc_id']];
                    }
                    $update = $vrorder_model->editVrorder(array('commis_rate' => $commis_rate), array('order_id' => $v['order_id']));
                    if (!$update) {
                        $this->log('更新虚拟订单商品佣金值失败');
                        $_break = true;
                        break;
                    }
                    $update = $vrrefund_model->editVrrefund(array('order_id' => $v['order_id']), array('commis_rate' => $commis_rate));
                    if (!$update) {
                        $this->log('更新虚拟订单商品退款佣金值失败');
                        $_break = true;
                        break;
                    }
                }
            } else {
                break;
            }
            Db::commit();
        }
        Db::commit();
    }
}
?>
