<?php

namespace app\common\model;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Vod\Vod;
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
class Videoupload extends BaseModel {

    public $page_info;

    public function getVideouploadList($condition, $field = '*', $pagesize, $order = 'videoupload_id desc') {
        if ($pagesize) {
            $result = Db::name('videoupload')->field($field)->where($condition)->order($order)->paginate(['list_rows' => $pagesize, 'query' => request()->param()], false);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = Db::name('videoupload')->field($field)->where($condition)->order($order)->select()->toArray();
            return $result;
        }
    }

    /**
     * 取单个内容
     * @access public
     * @author csdeshang
     * @param int $id 分类ID
     * @return array 数组类型的返回结果
     */
    public function getOneVideoupload($condition) {
        $result = Db::name('videoupload')->where($condition)->find();
        return $result;
    }

    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addVideoupload($data) {
        $result = Db::name('videoupload')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 数据
     * @param array $condition 条件
     * @return bool
     */
    public function editVideoupload($data, $condition) {
        $result = Db::name('videoupload')->where($condition)->update($data);
        return $result;
    }

    /**
     * 删除分类
     * @access public
     * @author csdeshang
     * @param int $condition 记录ID
     * @return bool 
     */
    public function delVideoupload($condition) {
        return Db::name('videoupload')->where($condition)->delete();
    }

    public function getVideoExpire($val, $exper = 0) {
        $temp = parse_url($val['videoupload_url']);
        $url = $val['videoupload_url'];
        $data='';
        if (strpos($val['videoupload_url'], '?')) {
            $url = substr($val['videoupload_url'], 0, strpos($val['videoupload_url'], '?'));
        }
        if ($val['video_type'] == 'aliyun') {
            $time = TIMESTAMP + 86400;
            $key = config('ds_config.aliyun_vod_play_key');
            $rand = rand(10000, 99999);
            $md5_str = $temp['path'] . '-' . $time . '-' . $rand . '-0-' . $key;
            if ($exper) {
                $md5_str .= '-' . $exper;
            }

            $data = $url . '?auth_key=' . $time . '-' . $rand . '-0-' . md5($md5_str);
            if ($exper) {
                $data .= '&end=' . $exper;
            }
        } elseif ($val['video_type'] == 'tencent') {
            $key = config('ds_config.vod_tencent_play_key');
            $time = dechex(TIMESTAMP + 86400);
            $md5_str = $key . substr($temp['path'], 0, strrpos($temp['path'], '/') + 1) . $time;
            if ($exper) {
                $md5_str .= $exper;
            }
            $data = $url . '?t=' . $time;
            if ($exper) {
                $data .= '&exper=' . $exper;
            }
            $data .= '&sign=' . md5($md5_str);
        } elseif ($val['video_type'] == 'local') {
            $data = UPLOAD_SITE_URL . '/' . ATTACH_GOODS . '/' . $val['store_id'] . '/' . $url;
        } elseif ($val['video_type'] == 'link'){
            $data = strip_tags(htmlspecialchars_decode($val['videoupload_url']));
        }
        return $data;
    }

}
