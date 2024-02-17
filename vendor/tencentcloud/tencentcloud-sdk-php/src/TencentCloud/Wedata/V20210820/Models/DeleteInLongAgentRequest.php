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
namespace TencentCloud\Wedata\V20210820\Models;
use TencentCloud\Common\AbstractModel;

/**
 * DeleteInLongAgent请求参数结构体
 *
 * @method string getAgentId() 获取采集器ID
 * @method void setAgentId(string $AgentId) 设置采集器ID
 * @method string getProjectId() 获取WeData项目ID
 * @method void setProjectId(string $ProjectId) 设置WeData项目ID
 */
class DeleteInLongAgentRequest extends AbstractModel
{
    /**
     * @var string 采集器ID
     */
    public $AgentId;

    /**
     * @var string WeData项目ID
     */
    public $ProjectId;

    /**
     * @param string $AgentId 采集器ID
     * @param string $ProjectId WeData项目ID
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
        if (array_key_exists("AgentId",$param) and $param["AgentId"] !== null) {
            $this->AgentId = $param["AgentId"];
        }

        if (array_key_exists("ProjectId",$param) and $param["ProjectId"] !== null) {
            $this->ProjectId = $param["ProjectId"];
        }
    }
}
