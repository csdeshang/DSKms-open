<?php

namespace app\crontab\controller;

use app\common\logic\Queue;
use think\facade\Cache;
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
class  Minutes extends BaseCron {

    /**
     * 默认方法
     */
    public function index() {
        $this->_cron_common();
        $this->_cron_mail_send();
        $this->_cron_bonus();
        $this->_cron_sms();
    }
 
    /**
     * 短信的处理
     */
    private function _cron_sms(){
        $smslog_model=model('smslog');
        $condition=array();
        $condition[]=array('smslog_state','=',0);
        $smslog_list=$smslog_model->getSmsList($condition, '', 100, 'smslog_id asc');
        $sms = new \sendmsg\Sms();
        foreach($smslog_list as $val){
            $smslog_param=json_decode($val['smslog_msg'],true);
            $smslog_msg=$smslog_param['message'];
            $send_result = $sms->send($val['smslog_phone'], $smslog_param);
            if ($send_result['code'] == true) {
                $smslog_state=1;
            }else{
                $smslog_state=2;
            }
            $condition=array();
            $condition[]=array('smslog_id','=',$val['smslog_id']);
            $update=array(
                'smslog_state'=>$smslog_state,
                'smslog_msg'=>$smslog_msg,
                'smslog_smstime'=>TIMESTAMP,
            );
            $smslog_model->editSms($update,$condition);
        }
    }
  
    /**
     * 处理过期红包
     */
    private function _cron_bonus() {
        $condition = array();
        $condition[] = array('bonus_endtime','<', TIMESTAMP);
        $condition[] = array('bonus_state','=',1);
        $data = array(
            'bonus_state' => 2,
        );
        model('bonus')->editBonus($condition, $data);
    }
    /**
     * 发送邮件消息
     */
    private function _cron_mail_send() {
        //每次发送数量
        $_num = 50;
        $storemsgcron_model = model('mailcron');
        $cron_array = $storemsgcron_model->getMailCronList(array(), $_num);
        if (!empty($cron_array)) {
            $email = new \sendmsg\Email();
            $mail_array = array();
            foreach ($cron_array as $val) {
                $return = $email->send_sys_email($val['mailcron_address'], $val['mailcron_subject'], $val['mailcron_contnet']);
                if ($return) {
                    // 记录需要删除的id
                    $mail_array[] = $val['mailcron_id'];
                }
            }
            // 删除已发送的记录
            $storemsgcron_model->delMailCron(array(array('mailcron_id','in', $mail_array)));
        }
    }

    /**
     * 执行通用任务
     */
    private function _cron_common(){

        //查找待执行任务
        $cron_model = model('cron');
        $cron = $cron_model->getCronList(array(array('cron_exetime','<=',TIMESTAMP)));

        if (!is_array($cron)) return ;
        $cron_array = array();
        $QueueLogic = new Queue();
        foreach ($cron as $v) {
            $value = unserialize($v['cron_value']);
            $key = $v['cron_type'];
            $res=$QueueLogic->$key($value);
            if($res['code']){
                $cron_model->delCron(array(array('cron_id','=',$v['cron_id'])));
            }
        }
    }

}

?>
