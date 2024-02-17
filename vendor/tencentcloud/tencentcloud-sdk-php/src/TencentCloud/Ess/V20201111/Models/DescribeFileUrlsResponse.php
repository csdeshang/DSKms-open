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
namespace TencentCloud\Ess\V20201111\Models;
use TencentCloud\Common\AbstractModel;

/**
 * DescribeFileUrls返回参数结构体
 *
 * @method array getFileUrls() 获取文件URL信息；
链接不是永久链接,  过期时间收UrlTtl入参的影响,  默认有效期5分钟后,  到期后链接失效。
 * @method void setFileUrls(array $FileUrls) 设置文件URL信息；
链接不是永久链接,  过期时间收UrlTtl入参的影响,  默认有效期5分钟后,  到期后链接失效。
 * @method integer getTotalCount() 获取URL数量
 * @method void setTotalCount(integer $TotalCount) 设置URL数量
 * @method string getRequestId() 获取唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 * @method void setRequestId(string $RequestId) 设置唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 */
class DescribeFileUrlsResponse extends AbstractModel
{
    /**
     * @var array 文件URL信息；
链接不是永久链接,  过期时间收UrlTtl入参的影响,  默认有效期5分钟后,  到期后链接失效。
     */
    public $FileUrls;

    /**
     * @var integer URL数量
     */
    public $TotalCount;

    /**
     * @var string 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
     */
    public $RequestId;

    /**
     * @param array $FileUrls 文件URL信息；
链接不是永久链接,  过期时间收UrlTtl入参的影响,  默认有效期5分钟后,  到期后链接失效。
     * @param integer $TotalCount URL数量
     * @param string $RequestId 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
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
        if (array_key_exists("FileUrls",$param) and $param["FileUrls"] !== null) {
            $this->FileUrls = [];
            foreach ($param["FileUrls"] as $key => $value){
                $obj = new FileUrl();
                $obj->deserialize($value);
                array_push($this->FileUrls, $obj);
            }
        }

        if (array_key_exists("TotalCount",$param) and $param["TotalCount"] !== null) {
            $this->TotalCount = $param["TotalCount"];
        }

        if (array_key_exists("RequestId",$param) and $param["RequestId"] !== null) {
            $this->RequestId = $param["RequestId"];
        }
    }
}
