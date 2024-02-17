<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Tse\V20201207\Models;
use TencentCloud\Common\AbstractModel;

/**
 * 获取云原生API网关实例列表响应结果。
 *
 * @method integer getTotalCount() 获取总数。
 * @method void setTotalCount(integer $TotalCount) 设置总数。
 * @method array getGatewayList() 获取云原生API网关实例列表。
 * @method void setGatewayList(array $GatewayList) 设置云原生API网关实例列表。
 */
class ListCloudNativeAPIGatewayResult extends AbstractModel
{
    /**
     * @var integer 总数。
     */
    public $TotalCount;

    /**
     * @var array 云原生API网关实例列表。
     */
    public $GatewayList;

    /**
     * @param integer $TotalCount 总数。
     * @param array $GatewayList 云原生API网关实例列表。
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("TotalCount",$param) and $param["TotalCount"] !== null) {
            $this->TotalCount = $param["TotalCount"];
        }

        if (array_key_exists("GatewayList",$param) and $param["GatewayList"] !== null) {
            $this->GatewayList = [];
            foreach ($param["GatewayList"] as $key => $value){
                $obj = new DescribeCloudNativeAPIGatewayResult();
                $obj->deserialize($value);
                array_push($this->GatewayList, $obj);
            }
        }
    }
}
