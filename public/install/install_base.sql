DROP TABLE IF EXISTS `#__address`;
CREATE TABLE IF NOT EXISTS `#__address` (
  `address_id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `member_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `address_realname` varchar(50) NOT NULL COMMENT '会员姓名',
  `city_id` mediumint(9) DEFAULT NULL COMMENT '市级ID',
  `area_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区ID',
  `area_info` varchar(255) NOT NULL DEFAULT '' COMMENT '地区内容',
  `address_detail` varchar(255) NOT NULL COMMENT '详细地址',
  `address_tel_phone` varchar(20) DEFAULT NULL COMMENT '座机',
  `address_mob_phone` varchar(15) DEFAULT NULL COMMENT '手机',
  `address_is_default` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1默认收货地址',
  `address_longitude` varchar(20) DEFAULT '' COMMENT '经度',
  `address_latitude` varchar(20) DEFAULT '' COMMENT '纬度',
  PRIMARY KEY (`address_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='买家地址信息表';

DROP TABLE IF EXISTS `#__admin`;
CREATE TABLE IF NOT EXISTS `#__admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员自增ID',
  `admin_name` varchar(20) NOT NULL COMMENT '管理员名称',
  `admin_password` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员密码',
  `admin_login_time` int(10) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `admin_login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `admin_is_super` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否超级管理员',
  `admin_gid` smallint(6) DEFAULT '0' COMMENT '权限组ID',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员表';

DROP TABLE IF EXISTS `#__adminlog`;
CREATE TABLE IF NOT EXISTS `#__adminlog` (
  `adminlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员记录自增ID',
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `admin_name` char(20) NOT NULL COMMENT '管理员名称',
  `adminlog_content` varchar(255) NOT NULL COMMENT '操作内容',
  `adminlog_time` int(10) unsigned DEFAULT NULL COMMENT '发生时间',
  `adminlog_ip` char(15) NOT NULL COMMENT '管理员操作IP',
  `adminlog_url` varchar(50) NOT NULL DEFAULT '' COMMENT 'controller/action',
  PRIMARY KEY (`adminlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员操作日志';

DROP TABLE IF EXISTS `#__adv`;
CREATE TABLE IF NOT EXISTS `#__adv` (
  `adv_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告自增ID',
  `ap_id` mediumint(8) unsigned NOT NULL COMMENT '广告位ID',
  `adv_title` varchar(255) NOT NULL COMMENT '广告内容描述',
  `adv_link` varchar(255) NOT NULL COMMENT '广告链接地址',
  `adv_code` varchar(1000) DEFAULT NULL COMMENT '广告图片地址',
  `adv_startdate` int(10) DEFAULT NULL COMMENT '广告开始时间',
  `adv_enddate` int(10) DEFAULT NULL COMMENT '广告结束时间',
  `adv_sort` tinyint(3) unsigned DEFAULT '255' COMMENT '广告图片排序',
  `adv_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT '广告是否有效',
  `adv_clicknum` int(10) unsigned DEFAULT '0' COMMENT '广告点击次数',
  `adv_bgcolor` varchar(50) DEFAULT NULL COMMENT '广告背景颜色',
  PRIMARY KEY (`adv_id`),
  KEY `ap_id` (`ap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告表';

DROP TABLE IF EXISTS `#__advposition`;
CREATE TABLE IF NOT EXISTS `#__advposition` (
  `ap_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位置自增ID',
  `ap_name` varchar(100) NOT NULL COMMENT '广告位名称',
  `ap_intro` varchar(255) NOT NULL DEFAULT '' COMMENT '广告位简介',
  `ap_isuse` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '广告位是否启用：0不启用1启用',
  `ap_width` int(10) DEFAULT '0' COMMENT '广告位宽度',
  `ap_height` int(10) DEFAULT '0' COMMENT '广告位高度',
  PRIMARY KEY (`ap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告位表';

DROP TABLE IF EXISTS `#__albumclass`;
CREATE TABLE IF NOT EXISTS `#__albumclass` (
  `aclass_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册自增ID',
  `aclass_name` varchar(100) NOT NULL COMMENT '相册名称',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `aclass_des` varchar(255) NOT NULL COMMENT '相册描述',
  `aclass_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '相册排序',
  `aclass_cover` varchar(255) DEFAULT NULL COMMENT '相册封面',
  `aclass_uploadtime` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `aclass_isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为默认相册,1代表默认',
  PRIMARY KEY (`aclass_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='相册表';

DROP TABLE IF EXISTS `#__albumpic`;
CREATE TABLE IF NOT EXISTS `#__albumpic` (
  `apic_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册图片自增ID',
  `apic_name` varchar(100) NOT NULL COMMENT '图片名称',
  `apic_tag` varchar(255) NOT NULL COMMENT '图片标签',
  `aclass_id` int(10) unsigned NOT NULL COMMENT '相册ID',
  `apic_cover` varchar(255) NOT NULL COMMENT '图片路径',
  `apic_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `apic_spec` varchar(100) NOT NULL COMMENT '图片规格',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `apic_uploadtime` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  PRIMARY KEY (`apic_id`),
  KEY `aclass_id` (`aclass_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='相册图片表';

DROP TABLE IF EXISTS `#__appadv`;
CREATE TABLE IF NOT EXISTS `#__appadv` (
  `adv_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP广告自增ID',
  `ap_id` mediumint(8) unsigned NOT NULL COMMENT 'APP广告位ID',
  `adv_title` varchar(255) NOT NULL COMMENT 'APP广告内容描述',
  `adv_type` varchar(255) DEFAULT NULL COMMENT 'APP广告类型,goods,store,article',
  `adv_typedate` varchar(255) DEFAULT NULL COMMENT 'APP广告类型对应的值,判断具体跳转内容',
  `adv_code` varchar(1000) DEFAULT NULL COMMENT 'APP广告图片地址',
  `adv_startdate` int(10) DEFAULT NULL COMMENT 'APP广告开始时间',
  `adv_enddate` int(10) DEFAULT NULL COMMENT 'APP广告结束时间',
  `adv_sort` tinyint(3) unsigned DEFAULT '255' COMMENT 'APP广告图片排序',
  `adv_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT 'APP广告是否有效',
  PRIMARY KEY (`adv_id`),
  KEY `ap_id` (`ap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APP广告表';

DROP TABLE IF EXISTS `#__appadvposition`;
CREATE TABLE IF NOT EXISTS `#__appadvposition` (
  `ap_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'APP广告位自增ID',
  `ap_name` varchar(100) NOT NULL COMMENT 'APP广告位名称',
  `ap_intro` varchar(255) NOT NULL DEFAULT '' COMMENT 'APP广告位简介',
  `ap_isuse` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'APP广告位是否启用：0不启用1启用',
  `ap_width` int(10) DEFAULT '0' COMMENT 'APP广告位宽度',
  `ap_height` int(10) DEFAULT '0' COMMENT 'APP广告位高度',
  PRIMARY KEY (`ap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='APP广告位表';

DROP TABLE IF EXISTS `#__area`;
CREATE TABLE IF NOT EXISTS `#__area` (
  `area_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '地区自增ID',
  `area_name` varchar(50) NOT NULL COMMENT '地区名称',
  `area_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区上级ID',
  `area_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '地区排序',
  `area_deep` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '地区深度',
  `area_region` varchar(3) DEFAULT NULL COMMENT '大区名称',
  PRIMARY KEY (`area_id`),
  KEY `area_parent_id` (`area_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='地区表';

DROP TABLE IF EXISTS `#__article`;
CREATE TABLE IF NOT EXISTS `#__article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章自增ID',
  `ac_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `article_url` varchar(100) DEFAULT NULL COMMENT '文章跳转链接',
  `article_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '文章是否显示，0为否，1为是，默认为1',
  `article_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '文章排序',
  `article_title` varchar(100) DEFAULT NULL COMMENT '文章标题',
  `article_content` text COMMENT '内容',
  `article_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章发布时间',
  `article_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '文章主图',
  PRIMARY KEY (`article_id`),
  KEY `ac_id` (`ac_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文章表';

DROP TABLE IF EXISTS `#__articleclass`;
CREATE TABLE IF NOT EXISTS `#__articleclass` (
  `ac_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章分类自增ID',
  `ac_code` varchar(20) DEFAULT NULL COMMENT '文章分类标识码',
  `ac_name` varchar(100) NOT NULL COMMENT '文章分类名称',
  `ac_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章分类父ID',
  `ac_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '文章分类排序',
  PRIMARY KEY (`ac_id`),
  KEY `ac_parent_id` (`ac_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文章分类表';

DROP TABLE IF EXISTS `#__bonus`;
CREATE TABLE IF NOT EXISTS `#__bonus` (
  `bonus_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '红包自增ID',
  `bonus_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '红包类型 1活动红包 2关注红包 3奖品红包',
  `bonus_name` varchar(20) NOT NULL DEFAULT '' COMMENT '红包名称',
  `bonus_blessing` varchar(255) NOT NULL DEFAULT '' COMMENT '红包祝福语',
  `bonus_totalprice` decimal(10,2) NOT NULL COMMENT '总面额',
  `bonus_pricetype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否固定金额 1固定金额',
  `bonus_fixedprice` decimal(10,2) NOT NULL COMMENT '固定金额',
  `bonus_randomprice_start` decimal(10,2) NOT NULL COMMENT '金额范围',
  `bonus_randomprice_end` decimal(10,2) NOT NULL COMMENT '金额范围',
  `bonus_receivecount` int(11) NOT NULL DEFAULT '0' COMMENT '领取人数',
  `bonus_receiveprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '领取金额',
  `bonus_state` tinyint(2) NOT NULL DEFAULT '0' COMMENT '红包状态 1正在进行 2过期 3失效',
  `bonus_begintime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `bonus_endtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `bonus_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '红包备注',
  PRIMARY KEY (`bonus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='吸粉红包表';

DROP TABLE IF EXISTS `#__bonusreceive`;
CREATE TABLE IF NOT EXISTS `#__bonusreceive` (
  `bonusreceive_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `bonus_id` int(11) NOT NULL COMMENT '吸粉红包ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL DEFAULT '' COMMENT '会员名',
  `bonusreceive_time` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
  `bonusreceive_price` decimal(10,2) NOT NULL COMMENT '领取金额',
  `bonusreceive_transformed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否转入预存款',
  PRIMARY KEY (`bonusreceive_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='吸粉红包领取信息表';

DROP TABLE IF EXISTS `#__config`;
CREATE TABLE IF NOT EXISTS `#__config` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `value` text,
  `remark` varchar(100) DEFAULT '解释,备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='配置参数表';

DROP TABLE IF EXISTS `#__consult`;
CREATE TABLE IF NOT EXISTS `#__consult` (
  `consult_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '咨询自增ID',
  `goods_id` int(11) unsigned DEFAULT '0' COMMENT '商品编号',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '咨询发布者会员ID 0:游客',
  `member_name` varchar(100) DEFAULT NULL COMMENT '会员名称',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `store_name` varchar(50) NOT NULL COMMENT '机构名称',
  `consulttype_id` int(10) unsigned NOT NULL COMMENT '咨询类型',
  `consult_content` varchar(255) DEFAULT NULL COMMENT '咨询内容',
  `consult_addtime` int(10) DEFAULT NULL COMMENT '咨询发布时间',
  `consult_reply` varchar(255) DEFAULT '' COMMENT '咨询回复内容',
  `consult_replytime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '咨询回复时间',
  `consult_isanonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表示不匿名 1表示匿名',
  PRIMARY KEY (`consult_id`),
  KEY `goods_id` (`goods_id`),
  KEY `seller_id` (`store_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品咨询表';

DROP TABLE IF EXISTS `#__consulttype`;
CREATE TABLE IF NOT EXISTS `#__consulttype` (
  `consulttype_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '咨询类型ID',
  `consulttype_name` varchar(10) NOT NULL COMMENT '咨询类型名称',
  `consulttype_introduce` text NOT NULL COMMENT '咨询类型详细介绍',
  `consulttype_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '咨询类型排序',
  PRIMARY KEY (`consulttype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='咨询类型表';

DROP TABLE IF EXISTS `#__cron`;
CREATE TABLE IF NOT EXISTS `#__cron` (
  `cron_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '执行自增ID',
  `cron_type` varchar(255) NOT NULL COMMENT '任务类型',
  `cron_value` text NOT NULL COMMENT '关联任务的值',
  `cron_exetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行时间',
  PRIMARY KEY (`cron_id`),
  KEY `cron_type` (`cron_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='任务队列表';

DROP TABLE IF EXISTS `#__document`;
CREATE TABLE IF NOT EXISTS `#__document` (
  `document_id` mediumint(11) NOT NULL AUTO_INCREMENT COMMENT '系统文章自增ID',
  `document_code` varchar(255) NOT NULL COMMENT '调用标识码',
  `document_title` varchar(255) NOT NULL COMMENT '系统文章标题',
  `document_content` text NOT NULL COMMENT '系统文章内容',
  `document_time` int(10) unsigned NOT NULL COMMENT '添加时间/修改时间',
  PRIMARY KEY (`document_id`),
  UNIQUE KEY `document_code` (`document_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统文章表';

DROP TABLE IF EXISTS `#__evaluategoods`;
CREATE TABLE IF NOT EXISTS `#__evaluategoods` (
  `geval_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '信誉评价自增ID',
  `geval_orderid` int(11) NOT NULL COMMENT '订单表ID',
  `geval_orderno` varchar(20) NOT NULL COMMENT '订单编号',
  `geval_ordergoodsid` int(11) NOT NULL COMMENT '订单商品表编号',
  `geval_goodsid` int(11) NOT NULL COMMENT '商品表编号',
  `geval_goodsname` varchar(100) NOT NULL COMMENT '商品名称',
  `geval_goodsprice` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `geval_goodsimage` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `geval_scores` tinyint(1) NOT NULL COMMENT '1-5分',
  `geval_content` varchar(255) DEFAULT NULL COMMENT '信誉评价内容',
  `geval_isanonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:不是 1:匿名评价',
  `geval_addtime` int(11) NOT NULL COMMENT '评价时间',
  `geval_storeid` int(11) NOT NULL COMMENT '机构编号',
  `geval_storename` varchar(100) NOT NULL COMMENT '机构名称',
  `geval_frommemberid` int(11) NOT NULL COMMENT '评价人编号',
  `geval_frommembername` varchar(100) NOT NULL COMMENT '评价人名称',
  `geval_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评价信息的状态 0为正常 1为禁止显示',
  `geval_remark` varchar(255) DEFAULT NULL COMMENT '管理员对评价的处理备注',
  `geval_explain` varchar(255) DEFAULT NULL COMMENT '解释内容',
  `geval_image` varchar(255) DEFAULT NULL COMMENT '晒单图片',
  PRIMARY KEY (`geval_id`),
  KEY `geval_orderid` (`geval_orderid`),
  KEY `geval_ordergoodsid` (`geval_ordergoodsid`),
  KEY `geval_goodsid` (`geval_goodsid`),
  KEY `geval_storeid` (`geval_storeid`),
  KEY `geval_frommemberid` (`geval_frommemberid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='信誉评价表';

DROP TABLE IF EXISTS `#__evaluatestore`;
CREATE TABLE IF NOT EXISTS `#__evaluatestore` (
  `seval_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '机构评分自增ID',
  `seval_orderid` int(11) unsigned NOT NULL COMMENT '订单ID',
  `seval_orderno` varchar(100) NOT NULL COMMENT '订单编号',
  `seval_addtime` int(11) unsigned NOT NULL COMMENT '评价时间',
  `seval_storeid` int(11) unsigned NOT NULL COMMENT '机构ID',
  `seval_storename` varchar(100) NOT NULL COMMENT '机构名称',
  `seval_memberid` int(11) unsigned NOT NULL COMMENT '买家ID',
  `seval_membername` varchar(100) NOT NULL COMMENT '买家名称',
  `seval_desccredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '描述相符评分',
  `seval_servicecredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '服务态度评分',
  `seval_deliverycredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '发货速度评分',
  PRIMARY KEY (`seval_id`),
  KEY `seval_orderid` (`seval_orderid`),
  KEY `seval_storeid` (`seval_storeid`),
  KEY `seval_memberid` (`seval_memberid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构评分表';

DROP TABLE IF EXISTS `#__exambank`;
CREATE TABLE IF NOT EXISTS `#__exambank` (
  `exambank_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '题库自增ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID 0为平台',
  `examclass_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '题库分类ID',
  `examtype_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '1单选/2多选/3判断/4填空/5问答',
  `exambank_question` text COMMENT '题库题干',
  `exambank_answer` text COMMENT '参考答案',
  `exambank_select` text COMMENT '选项数组 单选/多选',
  `exambank_selectnum` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '选项数量 填空数量',
  `exambank_describe` text COMMENT '习题解析',
  `exambank_level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '难度',
  `exambank_addtime` int(10) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`exambank_id`),
  KEY `examclass_id` (`examclass_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题库表';

DROP TABLE IF EXISTS `#__exambanksuggest`;
CREATE TABLE IF NOT EXISTS `#__exambanksuggest` (
  `exambanksuggest_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '错题反馈ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '错题所属店铺ID',
  `exambank_id` int(10) unsigned NOT NULL COMMENT '题干ID',
  `exambanksuggest_describe` text NOT NULL COMMENT '错题描述',
  `exambanksuggest_memberid` int(11) NOT NULL COMMENT '会员ID',
  `exambanksuggest_membername` varchar(50) NOT NULL COMMENT '提交错题的会员名',
  `exambanksuggest_addtime` int(10) DEFAULT NULL COMMENT '错题添加的时间',
  `exambanksuggest_feedback` text NOT NULL COMMENT '错题回复',
  PRIMARY KEY (`exambanksuggest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='错题反馈表';

DROP TABLE IF EXISTS `#__examclass`;
CREATE TABLE IF NOT EXISTS `#__examclass` (
  `examclass_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '题库分类自增ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID 0为平台',
  `examclass_name` varchar(20) NOT NULL COMMENT '题库分类名称',
  `examclass_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类父ID',
  `examclass_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '分类排序',
  PRIMARY KEY (`examclass_id`),
  KEY `examclass_parent_id` (`examclass_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题库分类表';

DROP TABLE IF EXISTS `#__exampaper`;
CREATE TABLE IF NOT EXISTS `#__exampaper` (
  `exampaper_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷自增ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID 0为平台',
  `exampaper_name` varchar(20) NOT NULL COMMENT '试卷名称',
  `exampaper_time` int(10) DEFAULT NULL COMMENT '考试时间',
  `exampaper_score` int(10) DEFAULT NULL COMMENT '考试总分',
  `exampaper_passscore` int(10) DEFAULT NULL COMMENT '及格分数',
  `exampaper_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '考试人数',
  `exampaper_setting` text COMMENT '试卷配置',
  `exampaper_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '试卷状态',
  `exampaper_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0普通试卷1随机试卷',
  `exampaper_addtime` int(10) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`exampaper_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试卷表';

DROP TABLE IF EXISTS `#__exampaperlog`;
CREATE TABLE IF NOT EXISTS `#__exampaperlog` (
  `exampaperlog_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷记录自增ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID 0为平台',
  `exampaper_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '考试会员ID',
  `member_name` varchar(100) DEFAULT NULL COMMENT '考试会员名称',
  `exampaperlog_begintime` int(10) DEFAULT NULL COMMENT '开始时间',
  `exampaperlog_endtime` int(10) DEFAULT NULL COMMENT '结束时间',
  `exampaperlog_score` int(4) DEFAULT NULL COMMENT '考试得分',
  `exampaperlog_ip` varchar(20) DEFAULT NULL COMMENT '考试当前IP',
  `exampaperlog_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0未交卷，1已交卷待批改，2已批改',
  `exampaperlog_data` text COMMENT '试卷答案 check_score评分 check_remark点评',
  PRIMARY KEY (`exampaperlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考试记录表';

DROP TABLE IF EXISTS `#__exppointslog`;
CREATE TABLE IF NOT EXISTS `#__exppointslog` (
  `explog_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '经验值日志自增ID',
  `explog_memberid` int(11) NOT NULL COMMENT '会员ID',
  `explog_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `explog_points` int(11) NOT NULL DEFAULT '0' COMMENT '经验值负数表示扣除',
  `explog_addtime` int(11) NOT NULL COMMENT '经验值添加时间',
  `explog_desc` varchar(100) NOT NULL COMMENT '经验值操作描述',
  `explog_stage` varchar(50) NOT NULL COMMENT '经验值操作状态',
  PRIMARY KEY (`explog_id`),
  KEY `explog_memberid` (`explog_memberid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='经验值日志表';

DROP TABLE IF EXISTS `#__favorites`;
CREATE TABLE IF NOT EXISTS `#__favorites` (
  `favlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏记录自增ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名',
  `fav_id` int(10) unsigned NOT NULL COMMENT '商品ID或机构ID',
  `fav_type` char(5) NOT NULL DEFAULT 'goods' COMMENT '类型:goods为商品,store为机构',
  `fav_time` int(10) unsigned NOT NULL COMMENT '收藏时间',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `store_name` varchar(20) NOT NULL COMMENT '机构名称',
  `storeclass_id` int(10) unsigned DEFAULT '0' COMMENT '机构分类ID',
  `goods_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `gc_id` int(10) unsigned DEFAULT '0' COMMENT '商品分类ID',
  `favlog_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品收藏时价格',
  `favlog_msg` varchar(20) DEFAULT NULL COMMENT '收藏备注',
  PRIMARY KEY (`favlog_id`),
  KEY `member_id` (`member_id`),
  KEY `store_id` (`store_id`),
  KEY `fav_id` (`fav_id`,`fav_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏表';

DROP TABLE IF EXISTS `#__feedback`;
CREATE TABLE IF NOT EXISTS `#__feedback` (
  `fb_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '反馈自增ID',
  `fb_content` varchar(500) DEFAULT NULL COMMENT '反馈内容',
  `fb_type` varchar(50) DEFAULT NULL COMMENT '1:手机端 2:PC端',
  `fb_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '反馈时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`fb_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

DROP TABLE IF EXISTS `#__gadmin`;
CREATE TABLE IF NOT EXISTS `#__gadmin` (
  `gid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限自增id',
  `gname` varchar(50) DEFAULT NULL COMMENT '权限组名',
  `glimits` text COMMENT '权限组序列',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限组';

DROP TABLE IF EXISTS `#__goods`;
CREATE TABLE IF NOT EXISTS `#__goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品公共表id',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_advword` varchar(150) DEFAULT NULL COMMENT '商品广告词',
  `goods_type` tinyint(1) unsigned DEFAULT '0' COMMENT '课程类型（0录播1直播）',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类',
  `gc_id_1` int(10) unsigned DEFAULT NULL COMMENT '一级分类id',
  `gc_id_2` int(10) unsigned DEFAULT NULL COMMENT '二级分类id',
  `gc_id_3` int(10) unsigned DEFAULT NULL COMMENT '三级分类id',
  `gc_name` varchar(200) NOT NULL COMMENT '商品分类',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `store_name` varchar(50) NOT NULL COMMENT '机构名称',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品主图',
  `goods_bg_image` varchar(100) DEFAULT NULL COMMENT '商品详情背景图',
  `goods_body` text COMMENT '商品内容',
  `mobile_body` text COMMENT '手机端商品描述',
  `goods_state` tinyint(3) unsigned DEFAULT '0' COMMENT '商品状态 0:下架 1:正常 10:违规（禁售）',
  `goods_stateremark` varchar(255) DEFAULT NULL COMMENT '违规原因',
  `goods_verify` tinyint(3) unsigned DEFAULT NULL COMMENT '商品审核 1通过，0未通过，10审核中',
  `goods_verifyremark` varchar(255) DEFAULT NULL COMMENT '审核失败原因',
  `goods_lock` tinyint(3) unsigned DEFAULT '0' COMMENT '商品锁定 0未锁，1已锁',
  `goods_addtime` int(10) unsigned NOT NULL COMMENT '商品添加时间',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_serial` varchar(50) NOT NULL COMMENT '机构编号',
  `goods_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品推荐 1:是 0:否',
  `goods_vat` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品是否开具增值税发票 1:是 0:否',
  `goods_stcids` varchar(255) NOT NULL DEFAULT '' COMMENT '机构分类id 首尾用,隔开',
  `goods_promotion_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品促销价格',
  `goods_promotion_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '促销类型 0:无促销 1:抢购 2:限时折扣',
  `goods_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量',
  `goods_salenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品销售数量',
  `goods_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品收藏数量',
  `goods_edittime` int(10) unsigned NOT NULL COMMENT '商品编辑时间',
  `evaluation_good_star` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级',
  `evaluation_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数',
  `plateid_top` int(10) unsigned DEFAULT NULL COMMENT '顶部关联板式',
  `plateid_bottom` int(10) unsigned DEFAULT NULL COMMENT '底部关联板式',
  `is_platform_store` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为平台自营',
  `inviter_ratio` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分销比例',
  `inviter_total_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已分销的商品数量',
  `inviter_total_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已分销的商品金额',
  `inviter_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品已结算的分销佣金',
  `inviter_open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启推广',
  `mall_goods_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商场推荐 1:是 0:否',
  `mall_goods_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '商品推荐排序',
  PRIMARY KEY (`goods_id`),
  KEY `store_id` (`store_id`),
  KEY `gc_id` (`gc_id`),
  KEY `gc_id_1` (`gc_id_1`),
  KEY `gc_id_2` (`gc_id_2`),
  KEY `gc_id_3` (`gc_id_3`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品公共内容表';

DROP TABLE IF EXISTS `#__goodsbrowse`;
CREATE TABLE IF NOT EXISTS `#__goodsbrowse` (
  `goodsbrowse_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `goodsbrowse_time` int(11) NOT NULL COMMENT '浏览时间',
  `gc_id` int(11) NOT NULL COMMENT '商品分类',
  `gc_id_1` int(11) NOT NULL COMMENT '商品一级分类',
  `gc_id_2` int(11) NOT NULL COMMENT '商品二级分类',
  `gc_id_3` int(11) NOT NULL COMMENT '商品三级分类',
  PRIMARY KEY (`goodsbrowse_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品浏览历史表';

DROP TABLE IF EXISTS `#__goodsclass`;
CREATE TABLE IF NOT EXISTS `#__goodsclass` (
  `gc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品分类自增ID',
  `gc_name` varchar(100) NOT NULL COMMENT '商品分类名称',
  `gc_image` varchar(100) DEFAULT '' COMMENT '商品分类图',
  `gc_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类上级ID',
  `commis_rate` float unsigned NOT NULL DEFAULT '0' COMMENT '商品分类佣金比例',
  `gc_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '商品分类排序',
  `gc_title` varchar(200) DEFAULT NULL COMMENT '商品分类名称',
  `gc_keywords` varchar(255) DEFAULT '' COMMENT '商品分类关键词',
  `gc_description` varchar(255) DEFAULT '' COMMENT '商品分类描述',
  `gc_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品分类前台显示 0:否 1:是',
  PRIMARY KEY (`gc_id`),
  KEY `store_id` (`gc_parent_id`),
  KEY `gc_parent_id` (`gc_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品分类表';

DROP TABLE IF EXISTS `#__goodscombo`;
CREATE TABLE IF NOT EXISTS `#__goodscombo` (
  `combo_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐组合自增ID ',
  `goods_id` int(10) unsigned NOT NULL COMMENT '主商品ID',
  `combo_goodsid` int(10) unsigned NOT NULL COMMENT '推荐组合商品ID',
  PRIMARY KEY (`combo_id`),
  KEY `goods_id` (`goods_id`),
  KEY `combo_goodsid` (`combo_goodsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品推荐组合表';

DROP TABLE IF EXISTS `#__goodscourses`;
CREATE TABLE IF NOT EXISTS `#__goodscourses` (
  `goodscourses_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品课程自增ID ',
  `goodscourses_class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课程分类ID',
  `goodscourses_class_name` varchar(50) NOT NULL DEFAULT '' COMMENT '课程分类名称',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `goodscourses_free` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费试看 1:是 0:否',
  `goodscourses_exper` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试看时长（秒）',
  `goodscourses_name` varchar(50) NOT NULL COMMENT '课程名称',
  `goodscourses_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型（0视频1小程序原生直播2小程序组件直播）',
  `goodscourses_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型id',
  `goodscourses_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '课程排序',
  `goodscourses_pan` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有课件',
  `baidu_pan_fsids` text NOT NULL DEFAULT '' COMMENT '百度云盘文件id',
  `baidu_pan_shorturl` varchar(255) NOT NULL DEFAULT '' COMMENT '百度云盘查看链接',
  `baidu_pan_pwd` varchar(10) NOT NULL DEFAULT '' COMMENT '百度云盘提取码',
  PRIMARY KEY (`goodscourses_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品课程表';

DROP TABLE IF EXISTS `#__goodscourses_class`;
CREATE TABLE IF NOT EXISTS `#__goodscourses_class` (
  `goodscourses_class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程分类ID ',
  `goodscourses_class_name` varchar(50) NOT NULL COMMENT '课程分类名称',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `goodscourses_class_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '课程分类排序',
  PRIMARY KEY (`goodscourses_class_id`),
  KEY `goods_id` (`goods_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品课程分类表';


DROP TABLE IF EXISTS `#__goodscourses_view`;
CREATE TABLE IF NOT EXISTS `#__goodscourses_view` (
  `goodscourses_view_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `goodscourses_view_time` int(10) unsigned NOT NULL COMMENT '学习时间',
  `goodscourses_id` int(10) unsigned NOT NULL COMMENT '课程id',
  `goodscourses_view_finish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已学完',
  PRIMARY KEY (`goodscourses_view_id`),
  KEY `goods_id` (`goods_id`),
  KEY `goodscourses_id` (`goodscourses_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程学习表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#__help`;
CREATE TABLE IF NOT EXISTS `#__help` (
  `help_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮助自增ID',
  `help_sort` tinyint(3) unsigned DEFAULT '255' COMMENT '帮助排序',
  `help_title` varchar(100) NOT NULL COMMENT '帮助标题',
  `help_info` text COMMENT '帮助内容',
  `help_url` varchar(100) DEFAULT '' COMMENT '帮助跳转链接',
  `help_updatetime` int(10) unsigned NOT NULL COMMENT '帮助更新时间',
  `helptype_id` int(10) unsigned NOT NULL COMMENT '帮助类型',
  `page_show` tinyint(1) unsigned DEFAULT '1' COMMENT '页面类型 1:机构 2:会员',
  PRIMARY KEY (`help_id`),
  KEY `helptype_id` (`helptype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='帮助内容表';

DROP TABLE IF EXISTS `#__helptype`;
CREATE TABLE IF NOT EXISTS `#__helptype` (
  `helptype_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮助类型自增ID',
  `helptype_name` varchar(50) NOT NULL COMMENT '帮助类型名称',
  `helptype_sort` tinyint(3) unsigned DEFAULT '255' COMMENT '帮助类型',
  `helptype_code` varchar(10) DEFAULT 'auto' COMMENT '调用编号(auto的可删除)',
  `helptype_show` tinyint(1) unsigned DEFAULT '1' COMMENT '帮助类型是否显示 0:否 1:是',
  `page_show` tinyint(1) unsigned DEFAULT '1' COMMENT '页面类型:1:机构 2:会员',
  PRIMARY KEY (`helptype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='帮助类型表';

DROP TABLE IF EXISTS `#__inform`;
CREATE TABLE IF NOT EXISTS `#__inform` (
  `inform_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报自增ID',
  `inform_member_id` int(11) NOT NULL COMMENT '举报人ID',
  `inform_member_name` varchar(50) NOT NULL COMMENT '举报人会员名',
  `inform_goods_id` int(11) NOT NULL COMMENT '被举报的商品ID',
  `inform_goods_name` varchar(200) NOT NULL COMMENT '被举报的商品名称',
  `informsubject_id` int(11) NOT NULL COMMENT '举报主题ID',
  `informsubject_content` varchar(50) NOT NULL COMMENT '举报主题',
  `inform_content` varchar(100) NOT NULL COMMENT '举报信息',
  `inform_pic1` varchar(100) NOT NULL COMMENT '图片1',
  `inform_pic2` varchar(100) NOT NULL COMMENT '图片2',
  `inform_pic3` varchar(100) NOT NULL COMMENT '图片3',
  `inform_datetime` int(11) NOT NULL COMMENT '举报时间',
  `inform_store_id` int(11) NOT NULL COMMENT '被举报商品的机构ID',
  `inform_state` tinyint(4) NOT NULL COMMENT '举报状态(1未处理/2已处理)',
  `inform_handle_type` tinyint(4) DEFAULT NULL COMMENT '举报处理结果(1无效举报/2恶意举报/3有效举报)',
  `inform_handle_message` varchar(100) NOT NULL COMMENT '举报处理信息',
  `inform_handle_datetime` int(11) NOT NULL DEFAULT '0' COMMENT '举报处理时间',
  `inform_handle_member_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `inform_goods_image` varchar(150) DEFAULT NULL COMMENT '商品图',
  `inform_store_name` varchar(100) DEFAULT NULL COMMENT '机构名',
  PRIMARY KEY (`inform_id`),
  KEY `inform_member_id` (`inform_member_id`),
  KEY `inform_store_id` (`inform_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报表';

DROP TABLE IF EXISTS `#__informsubject`;
CREATE TABLE IF NOT EXISTS `#__informsubject` (
  `informsubject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报主题自增ID',
  `informsubject_content` varchar(100) NOT NULL COMMENT '举报主题内容',
  `informsubject_type_id` int(11) NOT NULL COMMENT '举报类型ID',
  `informsubject_type_name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  `informsubject_state` tinyint(3) NOT NULL COMMENT '举报主题状态 1:可用 2:失效',
  PRIMARY KEY (`informsubject_id`),
  KEY `informsubject_type_id` (`informsubject_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='举报主题表';

DROP TABLE IF EXISTS `#__informsubjecttype`;
CREATE TABLE IF NOT EXISTS `#__informsubjecttype` (
  `informtype_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报类型自增ID',
  `informtype_name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  `informtype_desc` varchar(100) NOT NULL COMMENT '举报类型描述',
  `informtype_state` tinyint(4) NOT NULL COMMENT '举报类型状态 1:有效 2:失效',
  PRIMARY KEY (`informtype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='举报类型表';

DROP TABLE IF EXISTS `#__instant_message`;
CREATE TABLE IF NOT EXISTS `#__instant_message` (
  `instant_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息自增ID',
  `instant_message_from_id` int(10) unsigned NOT NULL COMMENT '发送ID',
  `instant_message_from_type` tinyint(1) unsigned NOT NULL COMMENT '发送类型（0用户1教育机构2直播）',
  `instant_message_from_name` varchar(500) NOT NULL COMMENT '发送名称',
  `instant_message_from_ip` varchar(15) NOT NULL COMMENT '发自IP',
  `instant_message_to_id` int(10) unsigned NOT NULL COMMENT '接收ID',
  `instant_message_to_type` tinyint(1) unsigned NOT NULL COMMENT '接收类型（0用户1教育机构2直播）',
  `instant_message_to_name` varchar(500) NOT NULL COMMENT '接收名称',
  `instant_message` text COMMENT '消息内容',
  `instant_message_type` tinyint(1) unsigned DEFAULT '0' COMMENT '消息类型',
  `instant_message_state` tinyint(1) unsigned DEFAULT '2' COMMENT '状态:1为已读,2为未读,默认为2',
  `instant_message_verify` tinyint(1) unsigned DEFAULT '0' COMMENT '审核:0审核中,1为已通过,2为未通过',
  `instant_message_add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `instant_message_verify_time` int(10) unsigned DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`instant_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';

DROP TABLE IF EXISTS `#__inviter`;
CREATE TABLE IF NOT EXISTS `#__inviter` (
  `inviter_id` int(10) unsigned NOT NULL COMMENT '推广员id即会员id',
  `inviter_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推广员状态（0审核中1已审核2已清退）',
  `inviter_total_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总共结算的佣金 ',
  `inviter_1_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '1级成员数量',
  `inviter_2_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '2级成员数量',
  `inviter_3_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '3级成员数量',
  `inviter_applytime` int(10) unsigned NOT NULL COMMENT '申请时间',
  `inviter_goods_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总共分销的商品件数',
  `inviter_goods_amount` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '总共分销的商品金额',
  PRIMARY KEY (`inviter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广员表';

DROP TABLE IF EXISTS `#__inviterclass`;
CREATE TABLE IF NOT EXISTS `#__inviterclass` (
  `inviterclass_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推广员等级id',
  `inviterclass_name` varchar(60) NOT NULL COMMENT '等级名',
  `inviterclass_amount` decimal(10,2) NOT NULL COMMENT '等级门槛',
  PRIMARY KEY (`inviterclass_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广等级表';

DROP TABLE IF EXISTS `#__invoice`;
CREATE TABLE IF NOT EXISTS `#__invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '发票信息ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `invoice_state` enum('1','2') DEFAULT NULL COMMENT '发票类型 1:普通发票 2:增值税发票',
  `invoice_title` varchar(50) DEFAULT '' COMMENT '发票抬头[普通发票]',
  `invoice_code` varchar(50) DEFAULT '' COMMENT '纳税人识别号[普通发票]',
  `invoice_content` varchar(10) DEFAULT '' COMMENT '发票内容[普通发票]',
  `invoice_company` varchar(50) DEFAULT '' COMMENT '单位名称',
  `invoice_company_code` varchar(50) DEFAULT '' COMMENT '纳税人识别号',
  `invoice_reg_addr` varchar(50) DEFAULT '' COMMENT '注册地址',
  `invoice_reg_phone` varchar(30) DEFAULT '' COMMENT '注册电话',
  `invoice_reg_bname` varchar(30) DEFAULT '' COMMENT '开户银行',
  `invoice_reg_baccount` varchar(30) DEFAULT '' COMMENT '银行帐户',
  `invoice_rec_name` varchar(20) DEFAULT '' COMMENT '收票人姓名',
  `invoice_rec_mobphone` varchar(15) DEFAULT '' COMMENT '收票人手机号',
  `invoice_rec_province` varchar(30) DEFAULT '' COMMENT '收票人省份',
  `invoice_goto_addr` varchar(50) DEFAULT '' COMMENT '送票地址',
  PRIMARY KEY (`invoice_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='买家发票信息表';

DROP TABLE IF EXISTS `#__link`;
CREATE TABLE IF NOT EXISTS `#__link` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接自增ID',
  `link_title` varchar(100) DEFAULT NULL COMMENT '友情链接标题',
  `link_url` varchar(100) DEFAULT NULL COMMENT '友情链接地址',
  `link_pic` varchar(100) DEFAULT NULL COMMENT '友情链接图片',
  `link_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '友情链接排序',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='友情链接';

DROP TABLE IF EXISTS `#__live_apply`;
CREATE TABLE IF NOT EXISTS `#__live_apply` (
  `live_apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '直播申请id',
  `live_apply_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '直播申请状态(0申请中1已通过2已拒绝)',
  `live_apply_push_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推流状态（0主播未进入直播间1主播已进入2主播停止直播）',
  `live_apply_push_message` varchar(255) NOT NULL DEFAULT '' COMMENT '断流提示信息',
  `live_apply_play_time` int(10) unsigned NOT NULL COMMENT '直播时间',
  `live_apply_end_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
  `live_apply_video` varchar(500) DEFAULT NULL COMMENT '回放视频',
  `live_apply_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请类别 0:无 1:教育机构课程视频',
  `live_apply_type_id` int(10) NOT NULL DEFAULT '0' COMMENT '申请类别id',
  `live_apply_add_time` int(10) unsigned NOT NULL COMMENT '申请时间',
  `live_apply_user_type` tinyint(1) unsigned NOT NULL COMMENT '申请用户类型（1用户2商家3教育机构）',
  `live_apply_user_id` int(10) unsigned NOT NULL COMMENT '申请用户id',
  `live_apply_remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`live_apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播申请';

DROP TABLE IF EXISTS `#__mailcron`;
CREATE TABLE IF NOT EXISTS `#__mailcron` (
  `mailcron_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息任务计划自增ID',
  `mailcron_address` varchar(100) NOT NULL COMMENT '邮箱地址',
  `mailcron_subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `mailcron_contnet` text NOT NULL COMMENT '邮件内容',
  PRIMARY KEY (`mailcron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件任务计划表';

DROP TABLE IF EXISTS `#__mailmsgtemlates`;
CREATE TABLE IF NOT EXISTS `#__mailmsgtemlates` (
  `mailmt_name` varchar(100) NOT NULL COMMENT '模板名称',
  `mailmt_title` varchar(100) DEFAULT NULL COMMENT '模板标题',
  `mailmt_code` varchar(30) NOT NULL COMMENT '模板调用代码',
  `mailmt_content` text NOT NULL COMMENT '模板内容',
  `ali_template_code` varchar(255) DEFAULT NULL COMMENT '阿里云短信模板ID',
  `ten_template_code` varchar(255) DEFAULT NULL COMMENT '腾讯云短信模板ID',
  PRIMARY KEY (`mailmt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

DROP TABLE IF EXISTS `#__mallconsult`;
CREATE TABLE IF NOT EXISTS `#__mallconsult` (
  `mallconsult_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台客服咨询自增ID',
  `mallconsulttype_id` int(10) unsigned NOT NULL COMMENT '咨询类型ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `mallconsult_content` varchar(500) NOT NULL COMMENT '咨询内容',
  `mallconsult_addtime` int(10) unsigned NOT NULL COMMENT '咨询时间',
  `mallconsult_isreply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否回复 1:是 0:否',
  `mallconsult_reply_content` varchar(500) NOT NULL DEFAULT '' COMMENT '平台回复内容',
  `mallconsult_replytime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '平台回复时间',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `admin_name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员名称',
  PRIMARY KEY (`mallconsult_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台客服咨询表';

DROP TABLE IF EXISTS `#__mallconsulttype`;
CREATE TABLE IF NOT EXISTS `#__mallconsulttype` (
  `mallconsulttype_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台客服咨询类型ID',
  `mallconsulttype_name` varchar(50) NOT NULL COMMENT '咨询类型名称',
  `mallconsulttype_introduce` text NOT NULL COMMENT '平台客服咨询类型备注',
  `mallconsulttype_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '咨询类型排序',
  PRIMARY KEY (`mallconsulttype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='平台客服咨询类型表';

DROP TABLE IF EXISTS `#__marketmanage`;
CREATE TABLE IF NOT EXISTS `#__marketmanage` (
  `marketmanage_id` int(11) NOT NULL AUTO_INCREMENT,
  `marketmanage_name` varchar(100) NOT NULL DEFAULT '' COMMENT '营销活动活动名称',
  `marketmanage_detail` varchar(255) NOT NULL DEFAULT '' COMMENT '营销活动详情',
  `marketmanage_begintime` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `marketmanage_endtime` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `marketmanage_jointype` tinyint(2) NOT NULL DEFAULT '0' COMMENT '参与类型 0共几次 1每天几次 2无限制',
  `marketmanage_joincount` int(11) NOT NULL DEFAULT '0' COMMENT '参与次数限制',
  `marketmanage_totalcount` int(11) NOT NULL DEFAULT '0' COMMENT '总参与次数',
  `marketmanage_totalwin` int(11) NOT NULL DEFAULT '0' COMMENT '总中奖次数',
  `marketmanage_point` int(11) NOT NULL DEFAULT '0' COMMENT '0不消耗积分 大于0消耗积分',
  `marketmanage_addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `marketmanage_failed` varchar(255) NOT NULL DEFAULT '' COMMENT '未中奖说明',
  `marketmanage_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '营销活动类型 1刮刮卡2大转盘3砸金蛋4生肖翻翻看',
  PRIMARY KEY (`marketmanage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营销活动活动 刮刮卡大转盘砸金蛋生肖翻翻看';

DROP TABLE IF EXISTS `#__marketmanageaward`;
CREATE TABLE IF NOT EXISTS `#__marketmanageaward` (
  `marketmanageaward_id` int(11) NOT NULL AUTO_INCREMENT,
  `marketmanage_id` int(11) NOT NULL DEFAULT '0' COMMENT '营销活动ID',
  `marketmanageaward_level` tinyint(3) NOT NULL COMMENT '奖品等级',
  `marketmanageaward_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '奖品类型 1积分 2红包 3优惠券',
  `marketmanageaward_count` int(11) NOT NULL DEFAULT '0' COMMENT '奖品数量',
  `marketmanageaward_send` int(11) NOT NULL DEFAULT '0' COMMENT '中奖数量',
  `marketmanageaward_probability` int(3) NOT NULL DEFAULT '0' COMMENT '奖品概率',
  `marketmanageaward_point` int(11) NOT NULL DEFAULT '0' COMMENT '奖品积分',
  `bonus_id` int(11) NOT NULL DEFAULT '0' COMMENT '红包ID',
  `vouchertemplate_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  PRIMARY KEY (`marketmanageaward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营销活动活动奖品记录';

DROP TABLE IF EXISTS `#__marketmanagelog`;
CREATE TABLE IF NOT EXISTS `#__marketmanagelog` (
  `marketmanagelog_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名',
  `marketmanage_id` int(11) NOT NULL DEFAULT '0' COMMENT '营销活动活动ID',
  `marketmanageaward_id` int(11) NOT NULL DEFAULT '0' COMMENT '中奖的奖品ID',
  `marketmanagelog_win` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否中奖 0未中奖 1中奖',
  `marketmanagelog_time` int(11) NOT NULL DEFAULT '0' COMMENT '参与时间',
  `marketmanagelog_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注领取信息',
  PRIMARY KEY (`marketmanagelog_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营销活动活动领取记录';

DROP TABLE IF EXISTS `#__platformtoken`;
CREATE TABLE IF NOT EXISTS `#__platformtoken` (
  `platformtoken_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `platform_userid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `platform_username` varchar(50) NOT NULL COMMENT '用户名',
  `platform_token` char(32) NOT NULL COMMENT '登录令牌',
  `platform_type` char(12) NOT NULL COMMENT '用户类型 member seller chain distributor fuwu',
  `platform_logintime` int(10) unsigned NOT NULL COMMENT '登录时间',
  `platform_operationtime` int(10) unsigned NOT NULL COMMENT '最近活跃时间',
  `platform_clienttype` varchar(10) NOT NULL DEFAULT '' COMMENT '客户端访问类型 APP H5 weixin 小程序',
  `platform_devicetype` varchar(20) NOT NULL DEFAULT '' COMMENT '客户端设备类型及系统',
  `platform_openid` varchar(50) DEFAULT NULL COMMENT '微信支付jsapi的openid缓存',
  PRIMARY KEY (`platformtoken_id`),
  KEY `platform_userid` (`platform_userid`),
  KEY `platform_token` (`platform_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='移动端登录令牌表';

DROP TABLE IF EXISTS `#__member`;
CREATE TABLE IF NOT EXISTS `#__member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员自增ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员用户名',
  `member_truename` varchar(20) DEFAULT NULL COMMENT '会员真实姓名',
  `member_nickname` varchar(20) DEFAULT NULL COMMENT '会员昵称',
  `member_auth_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名认证状态（0默认1审核中2未通过3已认证）',
  `member_idcard` varchar(255) DEFAULT NULL COMMENT '实名认证身份证号',
  `member_idcard_image1` varchar(255) DEFAULT NULL COMMENT '手持身份证照',
  `member_idcard_image2` varchar(255) DEFAULT NULL COMMENT '身份证正面照',
  `member_idcard_image3` varchar(255) DEFAULT NULL COMMENT '身份证反面照',
  `member_avatar` varchar(50) DEFAULT NULL COMMENT '会员头像',
  `member_sex` tinyint(1) DEFAULT NULL COMMENT '会员性别',
  `member_birthday` int(11) DEFAULT NULL COMMENT '会员生日',
  `member_password` varchar(32) NOT NULL COMMENT '会员密码',
  `member_paypwd` varchar(32) DEFAULT NULL COMMENT '支付密码',
  `member_email` varchar(50) DEFAULT NULL COMMENT '会员邮箱',
  `member_emailbind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否绑定邮箱',
  `member_mobile` varchar(11) DEFAULT NULL COMMENT '手机号码',
  `member_mobilebind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否绑定手机',
  `member_qq` varchar(20) DEFAULT NULL COMMENT '会员QQ',
  `member_ww` varchar(20) DEFAULT NULL COMMENT '会员旺旺',
  `member_loginnum` int(11) NOT NULL DEFAULT '0' COMMENT '会员登录次数',
  `member_addtime` int(11) NOT NULL COMMENT '会员添加时间',
  `member_logintime` int(11) DEFAULT '0' COMMENT '会员当前登录时间',
  `member_old_logintime` int(11) DEFAULT '0' COMMENT '会员上次登录时间',
  `member_login_ip` varchar(20) DEFAULT NULL COMMENT '会员当前登录IP',
  `member_old_login_ip` varchar(20) DEFAULT NULL COMMENT '会员上次登录IP',
  `member_qqopenid` varchar(100) DEFAULT NULL COMMENT 'qq互联id',
  `member_qqinfo` text COMMENT 'qq账号相关信息',
  `member_sinaopenid` varchar(100) DEFAULT NULL COMMENT '新浪微博登录id',
  `member_sinainfo` text COMMENT '新浪账号相关信息序列化值',
  `member_pc_wxopenid` char(30) DEFAULT '' COMMENT 'PC微信互联openid',
  `member_h5_wxopenid` char(30) DEFAULT '' COMMENT 'H5微信互联openid',
  `member_mini_wxopenid` char(30) DEFAULT '' COMMENT '小程序微信互联openid',
  `member_app_wxopenid` char(30) DEFAULT '' COMMENT 'APP微信互联openid',
  `member_wxunionid` char(100) DEFAULT '' COMMENT '微信用户统一标识',
  `member_wxnickname` char(100) DEFAULT '' COMMENT '微信用户昵称',
  `member_points` int(11) NOT NULL DEFAULT '0' COMMENT '会员积分',
  `available_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款可用金额',
  `freeze_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款冻结金额',
  `available_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '可用充值卡余额',
  `freeze_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '冻结充值卡余额',
  `inform_allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许举报(1可以/2不可以)',
  `is_buylimit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员是否有购买权限 1为开启 0为关闭',
  `is_allowtalk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员是否有咨询和发送站内信的权限 1为开启 0为关闭',
  `member_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员的开启状态 1为开启 0为关闭',
  `member_areaid` int(11) DEFAULT NULL COMMENT '地区ID',
  `member_cityid` int(11) DEFAULT NULL COMMENT '城市ID',
  `member_provinceid` int(11) DEFAULT NULL COMMENT '省份ID',
  `member_areainfo` varchar(255) DEFAULT NULL COMMENT '地区内容',
  `member_privacy` text COMMENT '隐私设定',
  `member_exppoints` int(11) NOT NULL DEFAULT '0' COMMENT '会员经验值',
  `inviter_id` int(11) DEFAULT NULL COMMENT '邀请人ID',
  `member_signin_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次签到时间',
  `member_signin_days_cycle` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '持续签到天数，每周期后清零',
  `member_signin_days_total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到总天数',
  `member_signin_days_series` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '持续签到天数总数，非连续周期清零',
  PRIMARY KEY (`member_id`),
  KEY `member_name` (`member_name`),
  KEY `member_email` (`member_email`,`member_emailbind`),
  KEY `member_mobile` (`member_mobile`,`member_mobilebind`),
  KEY `member_wxunionid` (`member_wxunionid`),
  KEY `member_qqopenid` (`member_qqopenid`),
  KEY `member_sinaopenid` (`member_sinaopenid`),
  KEY `inviter_id` (`inviter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='会员表';

DROP TABLE IF EXISTS `#__memberbank`;
CREATE TABLE IF NOT EXISTS `#__memberbank` (
  `memberbank_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `memberbank_type` varchar(10) NOT NULL DEFAULT 'bank' COMMENT 'bank银行 alipay 支付宝收款',
  `memberbank_truename` varchar(20) NOT NULL COMMENT '收款方真实姓名',
  `memberbank_name` varchar(20) NOT NULL DEFAULT '' COMMENT '收款银行',
  `memberbank_no` varchar(30) NOT NULL COMMENT '收款银行卡号/支付宝账户',
  `member_id` int(11) NOT NULL COMMENT '所属用户id',
  PRIMARY KEY (`memberbank_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收款账户表';

DROP TABLE IF EXISTS `#__membermsgsetting`;
CREATE TABLE IF NOT EXISTS `#__membermsgsetting` (
  `membermt_code` varchar(50) NOT NULL COMMENT '用户消息模板代码',
  `membermt_isreceive` tinyint(3) unsigned NOT NULL COMMENT '是否接收 1是，0否',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  PRIMARY KEY (`membermt_code`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户消息接收设置表';

DROP TABLE IF EXISTS `#__membermsgtpl`;
CREATE TABLE IF NOT EXISTS `#__membermsgtpl` (
  `membermt_code` varchar(50) NOT NULL COMMENT '用户消息模板编号',
  `membermt_name` varchar(50) NOT NULL COMMENT '模板名称',
  `membermt_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信接收开关',
  `membermt_message_content` varchar(255) NOT NULL COMMENT '站内信消息内容',
  `membermt_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短信接收开关',
  `membermt_short_content` varchar(255) NOT NULL COMMENT '短信接收内容',
  `membermt_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件接收开关',
  `membermt_mail_subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `membermt_mail_content` text NOT NULL COMMENT '邮件内容',
  `ali_template_code` varchar(255) DEFAULT NULL COMMENT '阿里云短信模板ID',
  `ten_template_code` varchar(255) DEFAULT NULL COMMENT '腾讯云短信模板ID',
  `membermt_weixin_switch` tinyint(3) unsigned NOT NULL COMMENT '微信接收开关',
  `membermt_weixin_code` varchar(255) NOT NULL COMMENT '微信消息code',
  PRIMARY KEY (`membermt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户消息模板';

DROP TABLE IF EXISTS `#__message`;
CREATE TABLE IF NOT EXISTS `#__message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '短消息自增ID',
  `message_parent_id` int(11) NOT NULL COMMENT '回复短消息message_id',
  `from_member_id` int(11) NOT NULL COMMENT '短消息发送人',
  `to_member_id` varchar(1000) NOT NULL COMMENT '短消息接收人',
  `message_title` varchar(50) DEFAULT NULL COMMENT '短消息标题',
  `message_body` varchar(255) NOT NULL COMMENT '短消息内容',
  `message_time` varchar(10) NOT NULL COMMENT '短消息发送时间',
  `message_update_time` varchar(10) DEFAULT NULL COMMENT '短消息回复更新时间',
  `message_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '短消息打开状态',
  `message_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '短消息状态，0为正常状态，1为发送人删除状态，2为接收人删除状态',
  `message_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0为私信、1为系统消息、2为留言',
  `read_member_id` varchar(1000) DEFAULT NULL COMMENT '已经读过该消息的会员id',
  `del_member_id` varchar(1000) DEFAULT NULL COMMENT '已经删除该消息的会员id',
  `message_ismore` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站内信是否为一条发给多个用户 0为否 1为多条 ',
  `from_member_name` varchar(100) DEFAULT NULL COMMENT '发信息人用户名',
  `to_member_name` varchar(100) DEFAULT NULL COMMENT '接收人用户名',
  PRIMARY KEY (`message_id`),
  KEY `from_member_id` (`from_member_id`),
  KEY `to_member_id` (`to_member_id`(255)),
  KEY `message_ismore` (`message_ismore`),
  KEY `message_parent_id` (`message_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短消息';



DROP TABLE IF EXISTS `#__minipro_live`;
CREATE TABLE IF NOT EXISTS `#__minipro_live` (
  `minipro_live_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `minipro_live_name` varchar(60) NOT NULL COMMENT '直播间名称',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `minipro_live_wxid` varchar(60) NOT NULL COMMENT '微信号',
  `minipro_live_nickname` varchar(60) NOT NULL COMMENT '微信昵称',
  `minipro_live_start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
  `minipro_live_end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
  `minipro_live_image` varchar(60) NOT NULL COMMENT '封面图地址',
  `minipro_live_add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `minipro_live_room_id` int(10) unsigned NOT NULL COMMENT '直播间id',
  `minipro_live_video` text NOT NULL DEFAULT '' COMMENT '回放视频',
  PRIMARY KEY (`minipro_live_id`),
  KEY `member_id` (`member_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序直播' AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `#__navigation`;
CREATE TABLE IF NOT EXISTS `#__navigation` (
  `nav_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '页面导航自增ID',
  `nav_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '页面导航类别，0自定义导航，1商品分类，2文章导航，3活动导航，默认为0',
  `nav_title` varchar(100) DEFAULT NULL COMMENT '页面导航标题',
  `nav_url` varchar(255) DEFAULT NULL COMMENT '页面导航链接',
  `nav_location` varchar(10) NOT NULL COMMENT '页面导航位置，header头部，middle中部，footer底部',
  `nav_new_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否以新窗口打开，0为否，1为是，默认为0',
  `nav_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '页面导航排序',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面导航类别ID，对应着nav_type中的内容，默认为0',
  `nav_is_close` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否前台显示，0为否，1为是，默认为1',
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='页面导航表';


DROP TABLE IF EXISTS `#__orderinviter`;
CREATE TABLE IF NOT EXISTS `#__orderinviter` (
  `orderinviter_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分销详情自增ID',
  `orderinviter_order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `orderinviter_member_id` int(10) unsigned NOT NULL COMMENT '推荐人ID',
  `orderinviter_money` decimal(10,2) unsigned NOT NULL COMMENT '佣金',
  `orderinviter_remark` varchar(255) NOT NULL COMMENT '分销详情备注',
  `orderinviter_member_name` varchar(60) NOT NULL COMMENT '推荐人用户名',
  `orderinviter_order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `orderinviter_goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `orderinviter_level` int(10) unsigned NOT NULL COMMENT '分销级别',
  `orderinviter_ratio` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分销比例',
  `orderinviter_goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `orderinviter_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0待结算1已结算2无效',
  `orderinviter_store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `orderinviter_order_type` tinyint(1) unsigned NOT NULL COMMENT '订单类型（0实物订单1虚拟订单）',
  `orderinviter_goods_quantity` int(10) unsigned NOT NULL COMMENT '商品数量',
  `orderinviter_goods_amount` decimal(10,2) unsigned NOT NULL COMMENT '商品金额',
  `orderinviter_store_name` varchar(60) NOT NULL COMMENT '机构名',
  `orderinviter_addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`orderinviter_id`),
  KEY `orderinviter_member_id` (`orderinviter_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销详情表';


DROP TABLE IF EXISTS `#__orderlog`;
CREATE TABLE IF NOT EXISTS `#__orderlog` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单记录自增ID',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `log_msg` varchar(150) DEFAULT '' COMMENT '订单记录文字描述',
  `log_time` int(10) unsigned NOT NULL COMMENT '订单记录处理时间',
  `log_role` char(5) NOT NULL COMMENT '操作角色',
  `log_user` varchar(30) DEFAULT '' COMMENT '操作人',
  `log_type` char(10) NOT NULL DEFAULT 'order' COMMENT 'order普通订单 vrorder虚拟订单 fuwu服务订单 errand跑腿订单',
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单记录表';


DROP TABLE IF EXISTS `#__payment`;
CREATE TABLE IF NOT EXISTS `#__payment` (
  `payment_code` char(20) NOT NULL COMMENT '支付代码',
  `payment_name` char(20) NOT NULL COMMENT '支付名称',
  `payment_config` text COMMENT '支付接口配置信息',
  `payment_platform` char(10) NOT NULL COMMENT '支付方式所适应平台 pc h5 app',
  `payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  PRIMARY KEY (`payment_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表';

DROP TABLE IF EXISTS `#__pdcash`;
CREATE TABLE IF NOT EXISTS `#__pdcash` (
  `pdc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '提现记录自增ID',
  `pdc_sn` varchar(20) NOT NULL COMMENT '记录唯一标示',
  `pdc_member_id` int(11) NOT NULL COMMENT '会员ID',
  `pdc_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `pdc_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pdc_bank_type` varchar(10) NOT NULL DEFAULT 'bank' COMMENT 'bank银行 alipay 支付宝 weixin 微信收款',
  `pdc_bank_name` varchar(40) NOT NULL COMMENT '收款银行',
  `pdc_bank_no` varchar(30) DEFAULT NULL COMMENT '收款账号',
  `pdc_bank_user` varchar(10) DEFAULT NULL COMMENT '开户人姓名',
  `pdc_addtime` int(11) NOT NULL COMMENT '添加时间',
  `pdc_payment_time` int(11) DEFAULT NULL COMMENT '付款时间',
  `pdc_payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '提现支付状态 0:默认1:支付完成',
  `pdc_payment_admin` varchar(30) DEFAULT NULL COMMENT '支付管理员',
  `pdc_payment_code` varchar(20) DEFAULT '' COMMENT '支付方式',
  `pdc_trade_sn` varchar(50) DEFAULT '' COMMENT '第三方支付接口交易号',
  PRIMARY KEY (`pdc_id`),
  KEY `pdc_member_id` (`pdc_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款提现记录表';

DROP TABLE IF EXISTS `#__pdlog`;
CREATE TABLE IF NOT EXISTS `#__pdlog` (
  `lg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '预存款变更日志自增ID',
  `lg_member_id` int(11) NOT NULL COMMENT '会员ID',
  `lg_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `lg_admin_name` varchar(50) DEFAULT NULL COMMENT '管理员名称',
  `lg_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'order_pay下单支付预存款,order_freeze下单冻结预存款,order_cancel取消订单解冻预存款,order_comb_pay下单支付被冻结的预存款,recharge充值,cash_apply申请提现冻结预存款,cash_pay提现成功,cash_del取消提现申请，解冻预存款,refund退款',
  `lg_av_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用金额变更0:未变更',
  `lg_av_total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用金额总金额(总额)',
  `lg_freeze_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额变更0:未变更',
  `lg_freeze_total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额总金额(总额)',
  `lg_addtime` int(11) NOT NULL COMMENT '变更添加时间',
  `lg_desc` varchar(150) DEFAULT NULL COMMENT '变更描述',
  PRIMARY KEY (`lg_id`),
  KEY `lg_member_id` (`lg_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款变更日志表';

DROP TABLE IF EXISTS `#__pdrecharge`;
CREATE TABLE IF NOT EXISTS `#__pdrecharge` (
  `pdr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '充值自增ID',
  `pdr_sn` varchar(20) NOT NULL COMMENT '记录唯一标示',
  `pdr_member_id` int(11) NOT NULL COMMENT '会员ID',
  `pdr_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `pdr_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `pdr_payment_code` varchar(20) DEFAULT '' COMMENT '支付方式',
  `pdr_trade_sn` varchar(50) DEFAULT '' COMMENT '第三方支付接口交易号',
  `pdr_addtime` int(11) NOT NULL COMMENT '充值添加时间',
  `pdr_payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '支付状态 0未支付1支付',
  `pdr_paymenttime` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pdr_admin` varchar(30) DEFAULT '' COMMENT '管理员名',
  PRIMARY KEY (`pdr_id`),
  KEY `pdr_member_id` (`pdr_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款充值表';

DROP TABLE IF EXISTS `#__pointscart`;
CREATE TABLE IF NOT EXISTS `#__pointscart` (
  `pcart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分购物车自增ID',
  `pmember_id` int(11) NOT NULL COMMENT '会员ID',
  `pgoods_id` int(11) NOT NULL COMMENT '积分礼品序号',
  `pgoods_name` varchar(200) NOT NULL COMMENT '积分礼品名称',
  `pgoods_points` int(11) NOT NULL COMMENT '积分礼品兑换积分',
  `pgoods_choosenum` int(11) NOT NULL COMMENT '选择积分礼品数量',
  `pgoods_image` varchar(100) DEFAULT NULL COMMENT '积分礼品图片',
  PRIMARY KEY (`pcart_id`),
  KEY `pmember_id` (`pmember_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分礼品兑换购物车';

DROP TABLE IF EXISTS `#__pointsgoods`;
CREATE TABLE IF NOT EXISTS `#__pointsgoods` (
  `pgoods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分礼品自增ID',
  `pgoods_name` varchar(200) NOT NULL COMMENT '积分礼品名称',
  `pgoods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分礼品原价',
  `pgoods_points` int(11) NOT NULL COMMENT '积分礼品兑换积分',
  `pgoods_image` varchar(100) DEFAULT NULL COMMENT '积分礼品封面图片',
  `pgoods_tag` varchar(100) NOT NULL COMMENT '积分礼品标签',
  `pgoods_serial` varchar(50) NOT NULL COMMENT '积分礼品货号',
  `pgoods_storage` int(11) NOT NULL DEFAULT '0' COMMENT '积分礼品库存',
  `pgoods_show` tinyint(1) NOT NULL COMMENT '积分礼品上架 0:下架 1:上架',
  `pgoods_commend` tinyint(1) NOT NULL COMMENT '积分礼品是否推荐',
  `pgoods_addtime` int(11) NOT NULL COMMENT '积分礼品添加时间',
  `pgoods_keywords` varchar(100) DEFAULT NULL COMMENT '积分礼品关键字',
  `pgoods_description` varchar(200) DEFAULT NULL COMMENT '积分礼品描述',
  `pgoods_body` text COMMENT '积分礼品内容',
  `pgoods_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '积分礼品状态 0:开启 1:禁售',
  `pgoods_close_reason` varchar(255) DEFAULT NULL COMMENT '积分礼品禁售原因',
  `pgoods_salenum` int(11) NOT NULL DEFAULT '0' COMMENT '积分礼品售出数量',
  `pgoods_view` int(11) NOT NULL DEFAULT '0' COMMENT '积分商品浏览量',
  `pgoods_islimit` tinyint(1) NOT NULL COMMENT '是否限制兑换数量',
  `pgoods_limitnum` int(11) DEFAULT NULL COMMENT '限制兑换数量',
  `pgoods_islimittime` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否限制兑换时间 0:不限制 1:限制',
  `pgoods_limitmgrade` int(11) NOT NULL DEFAULT '0' COMMENT '限制参与兑换的会员级别',
  `pgoods_starttime` int(11) DEFAULT NULL COMMENT '积分兑换开始时间',
  `pgoods_endtime` int(11) DEFAULT NULL COMMENT '积分兑换结束时间',
  `pgoods_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '积分礼品排序',
  PRIMARY KEY (`pgoods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分礼品表';

DROP TABLE IF EXISTS `#__pointslog`;
CREATE TABLE IF NOT EXISTS `#__pointslog` (
  `pl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分日志自增ID',
  `pl_memberid` int(11) NOT NULL COMMENT '会员ID',
  `pl_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `pl_adminid` int(11) DEFAULT NULL COMMENT '管理员ID',
  `pl_adminname` varchar(100) DEFAULT NULL COMMENT '管理员名称',
  `pl_points` int(11) NOT NULL DEFAULT '0' COMMENT '积分数 负数为扣除',
  `pl_addtime` int(11) NOT NULL COMMENT '积分添加时间',
  `pl_desc` varchar(100) NOT NULL COMMENT '积分操作描述',
  `pl_stage` varchar(50) NOT NULL COMMENT '积分操作阶段',
  PRIMARY KEY (`pl_id`),
  KEY `pl_memberid` (`pl_memberid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员积分日志表';

DROP TABLE IF EXISTS `#__pointsorder`;
CREATE TABLE IF NOT EXISTS `#__pointsorder` (
  `point_orderid` int(11) NOT NULL AUTO_INCREMENT COMMENT '兑换订单自增ID',
  `point_ordersn` varchar(20) NOT NULL COMMENT '兑换订单编号',
  `point_buyerid` int(11) NOT NULL COMMENT '兑换会员ID',
  `point_buyername` varchar(50) NOT NULL COMMENT '兑换会员姓名',
  `point_buyeremail` varchar(100) DEFAULT NULL COMMENT '兑换会员email',
  `point_addtime` int(11) NOT NULL COMMENT '兑换订单添加时间',
  `point_shippingtime` int(11) DEFAULT NULL COMMENT '兑换配送时间',
  `point_shippingcode` varchar(50) DEFAULT NULL COMMENT '兑换物流单号',
  `point_shipping_ecode` varchar(30) DEFAULT NULL COMMENT '兑换物流公司编码',
  `point_finnshedtime` int(11) DEFAULT NULL COMMENT '兑换订单完成时间',
  `point_allpoint` int(11) NOT NULL DEFAULT '0' COMMENT '兑换总积分',
  `point_ordermessage` varchar(300) DEFAULT NULL COMMENT '兑换订单留言',
  `point_orderstate` int(11) NOT NULL DEFAULT '20' COMMENT '订单状态：20:已兑换并扣除积分;30:已发货;40:已收货;50:已完成;2:已取消',
  PRIMARY KEY (`point_orderid`),
  KEY `point_buyerid` (`point_buyerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换订单表';

DROP TABLE IF EXISTS `#__pointsorderaddress`;
CREATE TABLE IF NOT EXISTS `#__pointsorderaddress` (
  `pointoa_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分兑换地址自增ID',
  `pointoa_orderid` int(11) NOT NULL COMMENT '订单id',
  `pointoa_truename` varchar(50) NOT NULL COMMENT '收货人姓名',
  `pointoa_areaid` int(11) NOT NULL COMMENT '地区id',
  `pointoa_areainfo` varchar(100) NOT NULL COMMENT '地区内容',
  `pointoa_address` varchar(200) NOT NULL COMMENT '详细地址',
  `pointoa_zipcode` varchar(20) NOT NULL COMMENT '邮政编码',
  `pointoa_telphone` varchar(20) NOT NULL COMMENT '电话号码',
  `pointoa_mobphone` varchar(20) NOT NULL COMMENT '手机号码',
  PRIMARY KEY (`pointoa_id`),
  KEY `pointoa_orderid` (`pointoa_orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分兑换地址表';

DROP TABLE IF EXISTS `#__pointsordergoods`;
CREATE TABLE IF NOT EXISTS `#__pointsordergoods` (
  `pointog_recid` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分订单商品自增ID',
  `pointog_orderid` int(11) NOT NULL COMMENT '订单ID',
  `pointog_goodsid` int(11) NOT NULL COMMENT '礼品ID',
  `pointog_goodsname` varchar(100) NOT NULL COMMENT '礼品名称',
  `pointog_goodspoints` int(11) NOT NULL COMMENT '礼品兑换积分',
  `pointog_goodsnum` int(11) NOT NULL COMMENT '礼品数量',
  `pointog_goodsimage` varchar(100) DEFAULT NULL COMMENT '礼品图片',
  PRIMARY KEY (`pointog_recid`),
  KEY `pointog_orderid` (`pointog_orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换订单商品表';



DROP TABLE IF EXISTS `#__pxianshi`;
CREATE TABLE IF NOT EXISTS `#__pxianshi` (
  `xianshi_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时折扣自增ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `xianshiquota_id` int(10) unsigned NOT NULL COMMENT '套餐ID',
  `xianshi_name` varchar(50) NOT NULL COMMENT '活动名称',
  `xianshi_title` varchar(10) DEFAULT NULL COMMENT '活动标题',
  `xianshi_explain` varchar(50) DEFAULT NULL COMMENT '活动说明',
  `xianshi_starttime` int(10) unsigned NOT NULL COMMENT '活动开始时间',
  `xianshi_end_time` int(10) unsigned NOT NULL COMMENT '活动结束时间',
  `xianshi_lower_limit` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '购买下限，1:不限制',
  `xianshi_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，0:取消 1:正常',
  PRIMARY KEY (`xianshi_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣活动表';



DROP TABLE IF EXISTS `#__pxianshigoods`;
CREATE TABLE IF NOT EXISTS `#__pxianshigoods` (
  `xianshigoods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时折扣商品表',
  `xianshi_id` int(10) unsigned NOT NULL COMMENT '限时折扣ID',
  `xianshi_name` varchar(50) NOT NULL COMMENT '限时折扣名称',
  `xianshi_title` varchar(10) DEFAULT NULL COMMENT '限时折扣标题',
  `xianshi_explain` varchar(50) DEFAULT NULL COMMENT '限时折扣说明',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '店铺价格',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `xianshigoods_price` decimal(10,2) NOT NULL COMMENT '限时折扣价格',
  `xianshigoods_starttime` int(10) unsigned NOT NULL COMMENT '限时折扣开始时间',
  `xianshigoods_end_time` int(10) unsigned NOT NULL COMMENT '限时折扣结束时间',
  `xianshigoods_lower_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买下限，0为不限制',
  `xianshigoods_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '限时折扣状态，0-取消 1-正常',
  `xianshigoods_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '限时折扣推荐标志 0-未推荐 1-已推荐',
  `gc_id_1` mediumint(9) DEFAULT '0' COMMENT '商品分类一级ID',
  PRIMARY KEY (`xianshigoods_id`),
  KEY `store_id` (`store_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣商品表';



DROP TABLE IF EXISTS `#__pxianshiquota`;
CREATE TABLE IF NOT EXISTS `#__pxianshiquota` (
  `xianshiquota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时折扣套餐ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `xianshiquota_starttime` int(10) unsigned NOT NULL COMMENT '限时折扣套餐开始时间',
  `xianshiquota_endtime` int(10) unsigned NOT NULL COMMENT '限时折扣套餐结束时间',
  PRIMARY KEY (`xianshiquota_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣套餐表';




DROP TABLE IF EXISTS `#__rcblog`;
CREATE TABLE IF NOT EXISTS `#__rcblog` (
  `rcblog_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '余额变更日志自增ID',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `rcblog_type` varchar(15) NOT NULL DEFAULT '' COMMENT 'order_pay:下单使用 order_freeze:下单冻结 order_cancel:取消订单解冻 order_comb_pay:下单扣除被冻结 recharge:平台充值卡充值 refund:确认退款 vr_refund:虚拟兑码退款',
  `rcblog_addtime` int(11) NOT NULL COMMENT '变更添加时间',
  `available_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用充值卡余额变更 0表示未变更',
  `freeze_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结充值卡余额变更 0表示未变更',
  `rcblog_description` varchar(150) DEFAULT NULL COMMENT '变更描述',
  PRIMARY KEY (`rcblog_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值卡余额变更日志表';

DROP TABLE IF EXISTS `#__rechargecard`;
CREATE TABLE IF NOT EXISTS `#__rechargecard` (
  `rc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '充值卡自增ID',
  `rc_sn` varchar(50) NOT NULL COMMENT '充值卡卡号',
  `rc_denomination` decimal(10,2) NOT NULL COMMENT '充值卡面额',
  `rc_batchflag` varchar(20) NOT NULL COMMENT '充值卡批次标识',
  `rc_admin_name` varchar(50) DEFAULT NULL COMMENT '充值卡创建者名称',
  `rc_tscreated` int(10) unsigned NOT NULL COMMENT '充值卡创建时间',
  `rc_tsused` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡使用时间',
  `rc_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡状态 0可用 1已用 2已删',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '使用者会员ID',
  `member_name` varchar(50) DEFAULT NULL COMMENT '使用者会员名称',
  PRIMARY KEY (`rc_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台充值卡';

DROP TABLE IF EXISTS `#__seller`;
CREATE TABLE IF NOT EXISTS `#__seller` (
  `seller_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家自增ID',
  `seller_name` varchar(50) NOT NULL COMMENT '卖家用户名',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `sellergroup_id` int(10) unsigned NOT NULL COMMENT '卖家组ID',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `is_admin` tinyint(3) unsigned NOT NULL COMMENT '是否管理员 0:不是 1:是',
  `last_logintime` int(10) unsigned DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`seller_id`),
  KEY `member_id` (`member_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='卖家用户表';

DROP TABLE IF EXISTS `#__sellergroup`;
CREATE TABLE IF NOT EXISTS `#__sellergroup` (
  `sellergroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家组自增ID',
  `sellergroup_name` varchar(50) NOT NULL COMMENT '卖家组名称',
  `sellergroup_limits` text NOT NULL COMMENT '卖家组权限',
  `smt_limits` text NOT NULL COMMENT '消卖家组息权限范围',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  PRIMARY KEY (`sellergroup_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家用户组表';

DROP TABLE IF EXISTS `#__sellerlog`;
CREATE TABLE IF NOT EXISTS `#__sellerlog` (
  `sellerlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家日志自增ID',
  `sellerlog_content` varchar(100) NOT NULL COMMENT '卖家日志内容',
  `sellerlog_time` int(10) unsigned NOT NULL COMMENT '卖家日志时间',
  `sellerlog_seller_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家ID',
  `sellerlog_seller_name` varchar(50) NOT NULL COMMENT '卖家帐号',
  `sellerlog_store_id` int(10) unsigned NOT NULL COMMENT '机构编号',
  `sellerlog_seller_ip` varchar(50) NOT NULL COMMENT '卖家ip',
  `sellerlog_url` varchar(100) NOT NULL COMMENT '卖家日志url',
  `sellerlog_state` tinyint(3) unsigned NOT NULL COMMENT '日志状态 0:失败 1:成功',
  PRIMARY KEY (`sellerlog_id`),
  KEY `sellerlog_seller_id` (`sellerlog_seller_id`),
  KEY `sellerlog_store_id` (`sellerlog_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家日志表';

DROP TABLE IF EXISTS `#__seo`;
CREATE TABLE IF NOT EXISTS `#__seo` (
  `seo_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'SEO自增ID',
  `seo_title` varchar(255) NOT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(255) NOT NULL COMMENT 'SEO关键词',
  `seo_description` text NOT NULL COMMENT 'SEO描述',
  `seo_type` varchar(20) NOT NULL COMMENT 'SEO类型',
  PRIMARY KEY (`seo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='SEO信息存放表';

DROP TABLE IF EXISTS `#__smslog`;
CREATE TABLE IF NOT EXISTS `#__smslog` (
  `smslog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '短信记录自增ID',
  `smslog_phone` char(11) NOT NULL COMMENT '短信手机号',
  `smslog_captcha` char(6) NOT NULL COMMENT '短信动态码',
  `smslog_ip` varchar(15) NOT NULL COMMENT '短信请求IP',
  `smslog_msg` text COMMENT '短信内容',
  `smslog_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '短信类型:1为注册,2为登录,3为找回密码,4绑定手机,5安全验证,默认为1',
  `smslog_smstime` int(10) unsigned NOT NULL COMMENT '短信添加时间',
  `member_id` int(10) unsigned DEFAULT '0' COMMENT '短信会员ID,注册为0',
  `member_name` varchar(50) DEFAULT '' COMMENT '短信会员名',
  `smslog_state` tinyint(1) unsigned DEFAULT '0' COMMENT '0待发送1发送成功2发送失败',
  PRIMARY KEY (`smslog_id`),
  KEY `smslog_phone` (`smslog_phone`),
  KEY `member_id` (`member_id`),
  KEY `smslog_state` (`smslog_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机短信记录表';

DROP TABLE IF EXISTS `#__snsalbumclass`;
CREATE TABLE IF NOT EXISTS `#__snsalbumclass` (
  `ac_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册自增ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `ac_name` varchar(100) NOT NULL COMMENT '相册名称',
  `ac_des` varchar(255) NOT NULL COMMENT '相册描述',
  `ac_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '相册排序',
  `ac_cover` varchar(255) DEFAULT NULL COMMENT '相册封面',
  `ac_uploadtime` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `ac_isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为买家秀相册  1:是 0:否',
  PRIMARY KEY (`ac_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册表';

DROP TABLE IF EXISTS `#__snsalbumpic`;
CREATE TABLE IF NOT EXISTS `#__snsalbumpic` (
  `ap_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册图片自增ID',
  `ac_id` int(10) unsigned DEFAULT NULL COMMENT '相册ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `ap_name` varchar(100) NOT NULL COMMENT '图片名称',
  `ap_cover` varchar(255) NOT NULL COMMENT '图片路径',
  `ap_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `ap_spec` varchar(100) NOT NULL COMMENT '图片规格',
  `ap_uploadtime` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `ap_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '图片类型，0为无、1为买家秀',
  `item_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '信息ID',
  PRIMARY KEY (`ap_id`),
  KEY `member_id` (`member_id`),
  KEY `ac_id` (`ac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册图片表';

DROP TABLE IF EXISTS `#__snsfriend`;
CREATE TABLE IF NOT EXISTS `#__snsfriend` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '好友自增ID',
  `friend_frommid` int(11) NOT NULL COMMENT '会员ID',
  `friend_frommname` varchar(100) DEFAULT NULL COMMENT '会员名称',
  `friend_frommavatar` varchar(100) DEFAULT NULL COMMENT '会员头像',
  `friend_tomid` int(11) NOT NULL COMMENT '朋友ID',
  `friend_tomname` varchar(100) NOT NULL COMMENT '好友会员名称',
  `friend_tomavatar` varchar(100) DEFAULT NULL COMMENT '好友头像',
  `friend_addtime` int(11) NOT NULL COMMENT '好友添加时间',
  `friend_followstate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '关注状态 1:单方关注 2:双方关注',
  PRIMARY KEY (`friend_id`),
  KEY `friend_frommid` (`friend_frommid`),
  KEY `friend_tomid` (`friend_tomid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友数据表';

DROP TABLE IF EXISTS `#__store`;
CREATE TABLE IF NOT EXISTS `#__store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '机构自增ID',
  `store_name` varchar(100) NOT NULL COMMENT '机构名称',
  `grade_id` int(11) DEFAULT '0' COMMENT '机构等级ID',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `seller_name` varchar(50) NOT NULL COMMENT '店主卖家用户名',
  `storeclass_id` int(11) DEFAULT '0' COMMENT '机构分类ID',
  `store_company_name` varchar(50) DEFAULT NULL COMMENT '机构公司名称',
  `region_id` int(10) DEFAULT NULL COMMENT '地区ID',
  `area_info` varchar(100) DEFAULT NULL COMMENT '地区名称',
  `store_address` varchar(100) DEFAULT NULL COMMENT '机构地址',
  `store_zip` varchar(10) DEFAULT NULL COMMENT '邮政编码',
  `store_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '机构状态:0关闭，1开启，2审核中',
  `store_close_info` varchar(255) DEFAULT NULL COMMENT '机构关闭原因',
  `store_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '机构排序',
  `store_addtime` int(11) NOT NULL DEFAULT '0' COMMENT '机构时间',
  `store_endtime` int(11) NOT NULL DEFAULT '0' COMMENT '机构关闭时间',
  `store_logo` varchar(255) DEFAULT NULL COMMENT '机构LOGO',
  `store_banner` varchar(255) DEFAULT NULL COMMENT '机构Banner',
  `store_avatar` varchar(255) DEFAULT NULL COMMENT '机构头像',
  `store_keywords` varchar(255) DEFAULT NULL COMMENT '机构SEO关键字',
  `store_description` varchar(255) DEFAULT NULL COMMENT '机构SEO描述',
  `store_qq` varchar(50) DEFAULT NULL COMMENT '机构QQ',
  `store_ww` varchar(50) DEFAULT NULL COMMENT '机构WW',
  `store_phone` varchar(20) DEFAULT NULL COMMENT '机构电话',
  `store_mainbusiness` text COMMENT '主营商品',
  `store_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐:0否 1是',
  `store_theme` varchar(50) NOT NULL DEFAULT 'default' COMMENT '机构当前主题',
  `store_credit` int(11) NOT NULL DEFAULT '0' COMMENT '机构信用',
  `store_desccredit` float unsigned NOT NULL DEFAULT '0' COMMENT '描述相符度分数',
  `store_servicecredit` float unsigned NOT NULL DEFAULT '0' COMMENT '服务态度分数',
  `store_deliverycredit` float unsigned NOT NULL DEFAULT '0' COMMENT '发货速度分数',
  `store_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构收藏数量',
  `store_slide` text COMMENT '机构幻灯片',
  `store_slide_url` text COMMENT '机构幻灯片链接',
  `store_sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构销量',
  `store_presales` text COMMENT '售前客服',
  `store_aftersales` text COMMENT '售后客服',
  `store_workingtime` varchar(100) DEFAULT NULL COMMENT '工作时间',
  `store_decoration_switch` int(10) unsigned DEFAULT '0' COMMENT '机构装修开关(0-关闭 装修编号-开启)',
  `store_decoration_only` tinyint(1) unsigned DEFAULT '0' COMMENT '开启机构装修时，仅显示机构装修(1-是 0-否',
  `store_decoration_image_count` int(10) unsigned DEFAULT '0' COMMENT '机构装修相册图片数量',
  `is_platform_store` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否自营机构 1是 0否',
  `bind_all_gc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自营店是否绑定全部分类 0否1是',
  `store_longitude` varchar(20) DEFAULT '' COMMENT '经度',
  `store_latitude` varchar(20) DEFAULT '' COMMENT '纬度',
  `mb_title_img` varchar(150) DEFAULT NULL COMMENT '手机机构背景图',
  `mb_sliders` text COMMENT '手机机构轮播图',
  `store_avaliable_deposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '机构已缴保证金',
  `store_freeze_deposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '机构审核保证金',
  `store_payable_deposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '机构应缴保证金',
  `store_avaliable_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '机构可用金额',
  `store_freeze_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '机构冻结金额',
  `baidu_pan_access_token` varchar(255) DEFAULT NULL COMMENT '百度云盘授权token',
  `baidu_pan_expires_in` int(10) unsigned DEFAULT '0' COMMENT '百度云盘token有效期',
  `baidu_pan_refresh_token` varchar(255) DEFAULT NULL COMMENT '百度云盘刷新access_token',
  PRIMARY KEY (`store_id`),
  KEY `seller_name` (`seller_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='机构数据表';

DROP TABLE IF EXISTS `#__storebindclass`;
CREATE TABLE IF NOT EXISTS `#__storebindclass` (
  `storebindclass_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类目自增ID',
  `store_id` int(11) unsigned DEFAULT '0' COMMENT '机构ID',
  `commis_rate` tinyint(4) unsigned DEFAULT '0' COMMENT '佣金比例',
  `class_1` mediumint(9) unsigned DEFAULT '0' COMMENT '一级分类',
  `class_2` mediumint(9) unsigned DEFAULT '0' COMMENT '二级分类',
  `class_3` mediumint(9) unsigned DEFAULT '0' COMMENT '三级分类',
  `storebindclass_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态0:审核中1:已审核 2:平台自营机构',
  PRIMARY KEY (`storebindclass_id`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`),
  KEY `class_1` (`class_1`),
  KEY `class_2` (`class_2`),
  KEY `class_3` (`class_3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构可发布商品类目表';

DROP TABLE IF EXISTS `#__storeclass`;
CREATE TABLE IF NOT EXISTS `#__storeclass` (
  `storeclass_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构分类自增ID',
  `storeclass_name` varchar(50) NOT NULL COMMENT '机构分类名称',
  `storeclass_bail` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '机构分类保证金数额',
  `storeclass_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '机构分类排序',
  PRIMARY KEY (`storeclass_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='机构分类表';

DROP TABLE IF EXISTS `#__storecost`;
CREATE TABLE IF NOT EXISTS `#__storecost` (
  `storecost_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构消费自增ID',
  `storecost_store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `storecost_seller_id` int(10) unsigned NOT NULL COMMENT '卖家ID',
  `storecost_price` int(10) unsigned NOT NULL COMMENT '机构消费价格',
  `storecost_remark` varchar(255) NOT NULL COMMENT '机构消费备注',
  `storecost_state` tinyint(3) unsigned NOT NULL COMMENT '费用状态 0:未结算 1:已结算',
  `storecost_time` int(10) unsigned NOT NULL COMMENT '费用发生时间',
  PRIMARY KEY (`storecost_id`),
  KEY `storecost_store_id` (`storecost_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构消费表';

DROP TABLE IF EXISTS `#__storedepositlog`;
CREATE TABLE IF NOT EXISTS `#__storedepositlog` (
  `storedepositlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '保证金日志id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(60) NOT NULL COMMENT '店铺名称',
  `storedepositlog_avaliable_deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已缴保证金',
  `storedepositlog_freeze_deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '审核保证金',
  `storedepositlog_payable_deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应缴保证金',
  `storedepositlog_state` tinyint(1) unsigned NOT NULL COMMENT '状态（0无效1有效2待审核3已同意4已拒绝5已缴纳6已取消）',
  `storedepositlog_type` smallint(5) unsigned NOT NULL COMMENT '日志类型',
  `storedepositlog_desc` varchar(255) NOT NULL COMMENT '日志详情',
  `storedepositlog_add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`storedepositlog_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构保证金日志表';

DROP TABLE IF EXISTS `#__storegoodsclass`;
CREATE TABLE IF NOT EXISTS `#__storegoodsclass` (
  `storegc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '机构商品分类ID',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构ID',
  `storegc_name` varchar(50) NOT NULL COMMENT '机构商品分类名称',
  `storegc_parent_id` int(11) NOT NULL COMMENT '上级ID',
  `storegc_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '机构商品分类状态',
  `storegc_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '商品分类排序',
  PRIMARY KEY (`storegc_id`),
  KEY `storegc_parent_id` (`storegc_parent_id`,`storegc_sort`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构商品分类表';

DROP TABLE IF EXISTS `#__storegrade`;
CREATE TABLE IF NOT EXISTS `#__storegrade` (
  `storegrade_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '机构等级自增ID',
  `storegrade_name` varchar(50) NOT NULL COMMENT '机构等级名称',
  `storegrade_goods_limit` int(10) DEFAULT '0' COMMENT '允许发布商品数量',
  `storegrade_album_limit` int(10) DEFAULT '0' COMMENT '允许发布图片数量',
  `storegrade_space_limit` int(10) DEFAULT '0' COMMENT '允许上传空间大小,单位MB',
  `storegrade_cloud_limit` int(10) DEFAULT '0' COMMENT '允许上传视频云空间大小,单位MB',
  `storegrade_local_limit` int(10) DEFAULT '0' COMMENT '允许上传商品本地空间大小,单位MB',
  `storegrade_template_number` tinyint(3) DEFAULT '0' COMMENT '机构等级选择机构模板套数',
  `storegrade_template` varchar(255) DEFAULT NULL COMMENT '机构等级模板内容',
  `storegrade_price` int(10) DEFAULT '0' COMMENT '机构等级费用',
  `storegrade_confirm` tinyint(1) DEFAULT '1' COMMENT '机构等级审核,0为否 1为是',
  `storegrade_description` text COMMENT '机构等级申请说明',
  `storegrade_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '机构等级排序',
  PRIMARY KEY (`storegrade_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='机构等级表';

DROP TABLE IF EXISTS `#__storejoinin`;
CREATE TABLE IF NOT EXISTS `#__storejoinin` (
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `member_name` varchar(50) DEFAULT NULL COMMENT '店主用户名',
  `store_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '机构类型:0公司，1个人',
  `company_name` varchar(50) DEFAULT NULL COMMENT '公司名称',
  `company_province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所在地省ID',
  `company_address` varchar(50) DEFAULT NULL COMMENT '公司地址',
  `company_address_detail` varchar(50) DEFAULT NULL COMMENT '公司详细地址',
  `company_registered_capital` int(10) unsigned DEFAULT NULL COMMENT '注册资金',
  `contacts_name` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `contacts_phone` varchar(20) DEFAULT NULL COMMENT '联系人电话',
  `contacts_email` varchar(50) DEFAULT NULL COMMENT '联系人邮箱',
  `business_licence_number` varchar(50) DEFAULT NULL COMMENT '营业执照号',
  `business_licence_address` varchar(50) DEFAULT NULL COMMENT '营业执所在地',
  `business_licence_start` date DEFAULT NULL COMMENT '营业执照有效期开始',
  `business_licence_end` date DEFAULT NULL COMMENT '营业执照有效期结束',
  `business_sphere` varchar(1000) DEFAULT NULL COMMENT '法定经营范围',
  `business_licence_number_electronic` varchar(50) DEFAULT NULL COMMENT '营业执照电子版',
  `bank_account_name` varchar(50) DEFAULT NULL COMMENT '银行开户名',
  `bank_account_number` varchar(50) DEFAULT NULL COMMENT '公司银行账号',
  `bank_name` varchar(50) DEFAULT NULL COMMENT '开户银行支行名称',
  `bank_address` varchar(50) DEFAULT NULL COMMENT '开户银行所在地',
  `is_settlement_account` tinyint(1) DEFAULT NULL COMMENT '开户行账号是否为结算账号 1-开户行就是结算账号 2-独立的计算账号',
  `settlement_bank_account_name` varchar(50) DEFAULT NULL COMMENT '结算银行开户名',
  `settlement_bank_account_number` varchar(50) DEFAULT NULL COMMENT '结算公司银行账号',
  `settlement_bank_name` varchar(50) DEFAULT NULL COMMENT '结算开户银行支行名称',
  `settlement_bank_address` varchar(50) DEFAULT NULL COMMENT '结算开户银行所在地',
  `seller_name` varchar(50) DEFAULT NULL COMMENT '卖家帐号',
  `store_name` varchar(50) DEFAULT NULL COMMENT '机构名称',
  `store_class_ids` varchar(1000) DEFAULT NULL COMMENT '机构分类编号集合',
  `store_class_names` varchar(1000) DEFAULT NULL COMMENT '机构分类名称集合',
  `store_longitude` varchar(20) DEFAULT '' COMMENT '经度',
  `store_latitude` varchar(20) DEFAULT '' COMMENT '纬度',
  `joinin_state` varchar(50) DEFAULT NULL COMMENT '申请状态 10-已提交申请 11-缴费完成  20-审核成功 30-审核失败 31-缴费审核失败 40-审核通过开店',
  `joinin_message` varchar(200) DEFAULT NULL COMMENT '管理员审核信息',
  `joinin_year` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '开店时长(年)',
  `storegrade_name` varchar(50) DEFAULT NULL COMMENT '机构等级名称',
  `storegrade_id` int(10) unsigned DEFAULT NULL COMMENT '机构等级编号',
  `sg_info` varchar(200) DEFAULT NULL COMMENT '机构等级下的收费等信息',
  `storeclass_name` varchar(50) DEFAULT NULL COMMENT '机构分类名称',
  `storeclass_id` int(10) unsigned DEFAULT NULL COMMENT '机构分类编号',
  `storeclass_bail` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '机构分类保证金',
  `store_class_commis_rates` varchar(200) DEFAULT NULL COMMENT '分类佣金比例',
  `paying_money_certificate` varchar(50) DEFAULT NULL COMMENT '付款凭证',
  `paying_money_certificate_explain` varchar(200) DEFAULT NULL COMMENT '付款凭证说明',
  `paying_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `pay_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '支付单号',
  `payment_code` varchar(20) DEFAULT '' COMMENT '支付方式',
  `trade_sn` varchar(50) DEFAULT '' COMMENT '第三方支付接口交易号',
  `rcb_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡支付金额',
  `pd_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款支付金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构入住表';

DROP TABLE IF EXISTS `#__storemoneylog`;
CREATE TABLE IF NOT EXISTS `#__storemoneylog` (
  `storemoneylog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '金额日志id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(60) NOT NULL COMMENT '店铺名称',
  `storemoneylog_avaliable_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动可用金额',
  `storemoneylog_avaliable_total_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额(可用)',
  `storemoneylog_freeze_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动冻结金额',
  `storemoneylog_freeze_total_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额(冻结)',
  `storemoneylog_type` smallint(5) unsigned NOT NULL COMMENT '日志类型',
  `storemoneylog_desc` varchar(255) NOT NULL COMMENT '日志详情',
  `storemoneylog_state` tinyint(1) unsigned NOT NULL COMMENT '状态（0无效1有效2待审核3已同意4已拒绝）',
  `storemoneylog_add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `storemoneylog_payment_code` varchar(20) DEFAULT '' COMMENT '支付方式',
  `storemoneylog_trade_sn` varchar(50) DEFAULT '' COMMENT '第三方支付接口交易号',
  PRIMARY KEY (`storemoneylog_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构金额日志表';

DROP TABLE IF EXISTS `#__storemsg`;
CREATE TABLE IF NOT EXISTS `#__storemsg` (
  `storemsg_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构消息自增ID',
  `storemt_code` varchar(100) NOT NULL COMMENT '模板编码',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `storemsg_content` varchar(255) NOT NULL COMMENT '消息内容',
  `storemsg_addtime` int(10) unsigned NOT NULL COMMENT '发送时间',
  `storemsg_readids` varchar(255) DEFAULT NULL COMMENT '已读卖家id',
  PRIMARY KEY (`storemsg_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构消息表';

DROP TABLE IF EXISTS `#__storemsgread`;
CREATE TABLE IF NOT EXISTS `#__storemsgread` (
  `storemsg_id` int(11) NOT NULL COMMENT '机构消息ID',
  `seller_id` int(11) NOT NULL COMMENT '卖家ID',
  `storemsg_readtime` int(11) NOT NULL COMMENT '阅读时间',
  PRIMARY KEY (`storemsg_id`,`seller_id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构消息阅读表';

DROP TABLE IF EXISTS `#__storemsgsetting`;
CREATE TABLE IF NOT EXISTS `#__storemsgsetting` (
  `storemt_code` varchar(100) NOT NULL COMMENT '模板编码',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `storems_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信接收开关 0:关闭 1:开启',
  `storems_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短消息接收开关 0:关闭 1:开启',
  `storems_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件接收开关 0:关闭 1:开启',
  `storems_weixin_switch` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '微信接收开关 0:关闭 1:开启',
  `storems_short_number` varchar(11) NOT NULL COMMENT '手机号码',
  `storems_mail_number` varchar(100) NOT NULL COMMENT '邮箱号码',
  PRIMARY KEY (`storemt_code`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构消息接收设置';

DROP TABLE IF EXISTS `#__storemsgtpl`;
CREATE TABLE IF NOT EXISTS `#__storemsgtpl` (
  `storemt_code` varchar(100) NOT NULL COMMENT '机构消息模板编码',
  `storemt_name` varchar(100) NOT NULL COMMENT '机构消息模板名称',
  `storemt_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信默认开关 0:关 1:开',
  `storemt_message_content` varchar(255) NOT NULL COMMENT '站内信内容',
  `storemt_message_forced` tinyint(3) unsigned NOT NULL COMMENT '站内信强制接收 0:否 1:是',
  `storemt_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短信默认开关 0:关 1:开',
  `storemt_short_content` varchar(255) NOT NULL COMMENT '短信内容',
  `smt_short_forced` tinyint(3) unsigned NOT NULL COMMENT '短信强制接收 0:否 1:是',
  `storemt_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件默认开 0:关 1:开',
  `storemt_mail_subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `storemt_mail_content` text NOT NULL COMMENT '邮件内容',
  `storemt_mail_forced` tinyint(3) unsigned NOT NULL COMMENT '邮件强制接收 0:否 1:是',
  `ali_template_code` varchar(255) DEFAULT NULL COMMENT '阿里云短信模板ID',
  `ten_template_code` varchar(255) DEFAULT NULL COMMENT '腾讯云短信模板ID',
  `storemt_weixin_switch` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '微信默认开关 0:关 1:开',
  `storemt_weixin_code` varchar(255) NOT NULL DEFAULT '' COMMENT '微信code',
  `storemt_weixin_forced` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '微信强制接收 0:否 1:是',
  PRIMARY KEY (`storemt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构消息模板';

DROP TABLE IF EXISTS `#__storenavigation`;
CREATE TABLE IF NOT EXISTS `#__storenavigation` (
  `storenav_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家机构导航自增ID',
  `storenav_title` varchar(50) NOT NULL COMMENT '卖家机构导航名称',
  `storenav_store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家机构ID',
  `storenav_content` text COMMENT '卖家机构导航内容',
  `storenav_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '卖家机构导航排序',
  `storenav_url` varchar(255) DEFAULT NULL COMMENT '机构导航的外链URL',
  PRIMARY KEY (`storenav_id`),
  KEY `storenav_store_id` (`storenav_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家机构导航信息表';

DROP TABLE IF EXISTS `#__storeplate`;
CREATE TABLE IF NOT EXISTS `#__storeplate` (
  `storeplate_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关联板式id',
  `storeplate_name` varchar(10) NOT NULL COMMENT '关联板式名称',
  `storeplate_position` tinyint(3) unsigned NOT NULL COMMENT '关联板式位置 1:顶部 0:底部',
  `storeplate_content` text NOT NULL COMMENT '关联板式内容',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属机构ID',
  PRIMARY KEY (`storeplate_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关联板式表';

DROP TABLE IF EXISTS `#__storereopen`;
CREATE TABLE IF NOT EXISTS `#__storereopen` (
  `storereopen_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '续签自增ID',
  `storereopen_grade_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '机构等级ID',
  `storereopen_grade_name` varchar(30) DEFAULT NULL COMMENT '等级名称',
  `storereopen_grade_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '等级收费(元/年)',
  `storereopen_year` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '续签时长(年)',
  `storereopen_pay_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应付总金额',
  `storereopen_store_name` varchar(50) DEFAULT NULL COMMENT '机构名字',
  `storereopen_store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `storereopen_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:未上传凭证 1:审核中 2:审核通过',
  `storereopen_pay_cert` varchar(50) DEFAULT NULL COMMENT '付款凭证',
  `storereopen_pay_cert_explain` varchar(200) DEFAULT NULL COMMENT '付款说明',
  PRIMARY KEY (`storereopen_id`),
  KEY `storereopen_store_id` (`storereopen_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='续签内容表';

DROP TABLE IF EXISTS `#__storewatermark`;
CREATE TABLE IF NOT EXISTS `#__storewatermark` (
  `swm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '水印自增ID',
  `swm_quality` int(3) NOT NULL DEFAULT '90' COMMENT '水印图片质量',
  `swm_image_name` varchar(255) DEFAULT NULL COMMENT '水印文件名',
  `swm_image_pos` tinyint(1) NOT NULL DEFAULT '1' COMMENT '水印图片存放位置',
  `swm_image_transition` int(3) NOT NULL DEFAULT '20' COMMENT '水印图片融合度 ',
  `swm_text` text COMMENT '水印文字',
  `swm_text_size` int(3) NOT NULL DEFAULT '20' COMMENT '水印文字大小',
  `swm_text_angle` tinyint(1) NOT NULL DEFAULT '4' COMMENT '水印文字角度',
  `swm_text_pos` tinyint(1) NOT NULL DEFAULT '3' COMMENT '水印文字位置',
  `swm_text_font` varchar(50) DEFAULT NULL COMMENT '水印文字字体',
  `swm_text_color` varchar(7) NOT NULL DEFAULT '#CCCCCC' COMMENT '水印字体颜色值',
  `store_id` int(11) DEFAULT NULL COMMENT '机构ID',
  PRIMARY KEY (`swm_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构水印图片表';

DROP TABLE IF EXISTS `#__upload`;
CREATE TABLE IF NOT EXISTS `#__upload` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传文件自增ID',
  `file_name` varchar(100) DEFAULT NULL COMMENT '上传文件名',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传文件大小',
  `upload_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '上传文件类别 0:无 1:后台文章图片 2:帮助内容图片 3:机构幻灯片 4:会员协议图片 5:积分礼品切换图片 6:积分礼品内容图片',
  `upload_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传文件添加时间',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '信息ID',
  PRIMARY KEY (`upload_id`),
  KEY `upload_type` (`upload_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传文件表';

DROP TABLE IF EXISTS `#__verify_code`;
CREATE TABLE IF NOT EXISTS `#__verify_code` (
  `verify_code_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '验证码id',
  `verify_code_type` tinyint(1) unsigned NOT NULL COMMENT '验证码类型（1登录2注册3修改密码4绑定手机5绑定邮箱6安全验证）',
  `verify_code` char(6) NOT NULL COMMENT '验证码',
  `verify_code_user_type` tinyint(1) unsigned NOT NULL COMMENT '发送验证码的用户类型（1会员2店长3配送员）',
  `verify_code_user_id` int(10) unsigned NOT NULL COMMENT '发送验证码的用户id',
  `verify_code_user_name` varchar(60) NOT NULL COMMENT '发送验证码的用户名',
  `verify_code_add_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  `verify_code_ip` varchar(15) NOT NULL COMMENT 'ip地址',
  PRIMARY KEY (`verify_code_id`),
  KEY `verify_code_type` (`verify_code_type`,`verify_code_user_type`,`verify_code_user_id`,`verify_code`,`verify_code_add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='验证码表';

DROP TABLE IF EXISTS `#__videoupload`;
CREATE TABLE IF NOT EXISTS `#__videoupload` (
  `videoupload_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传视频自增ID',
  `videoupload_fileid` varchar(100) DEFAULT NULL COMMENT '上传视频识别ID',
  `videoupload_url` varchar(255) DEFAULT NULL COMMENT '上传视频地址',
  `videoupload_name` varchar(100) DEFAULT NULL COMMENT '视频名称',
  `videoupload_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '视频申请状态(0申请中1已通过2已拒绝)',
  `videoupload_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传视频大小',
  `videoupload_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传视频添加时间',
  `videoupload_remark` varchar(60) NOT NULL DEFAULT '' COMMENT '上传视频备注',
  `store_id` int(11) unsigned NOT NULL COMMENT '卖家机构ID',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '信息ID',
  `video_type` varchar(10) DEFAULT 'tencent' COMMENT '视频类型',
  PRIMARY KEY (`videoupload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='视频上传表';

DROP TABLE IF EXISTS `#__voucher`;
CREATE TABLE IF NOT EXISTS `#__voucher` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券自增ID',
  `voucher_code` varchar(32) NOT NULL COMMENT '代金券编码',
  `vouchertemplate_id` int(11) NOT NULL COMMENT '代金券模版编号',
  `voucher_title` varchar(50) NOT NULL COMMENT '代金券标题',
  `voucher_desc` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucher_startdate` int(11) NOT NULL COMMENT '代金券有效期开始时间',
  `voucher_enddate` int(11) NOT NULL COMMENT '代金券有效期结束时间',
  `voucher_price` int(11) NOT NULL COMMENT '代金券面额',
  `voucher_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `voucher_store_id` int(11) NOT NULL COMMENT '代金券的机构id',
  `voucher_state` tinyint(4) NOT NULL COMMENT '代金券状态 1:未用 2:已用 3:过期 4:收回 ',
  `voucher_activedate` int(11) NOT NULL COMMENT '代金券发放日期',
  `voucher_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '代金券类别',
  `voucher_owner_id` int(11) NOT NULL COMMENT '代金券所有者ID',
  `voucher_owner_name` varchar(50) NOT NULL COMMENT '代金券所有者名称',
  `voucher_order_id` int(11) DEFAULT NULL COMMENT '使用该代金券的订单编号',
  PRIMARY KEY (`voucher_id`),
  KEY `voucher_store_id` (`voucher_store_id`),
  KEY `voucher_owner_id` (`voucher_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券表';

DROP TABLE IF EXISTS `#__voucherprice`;
CREATE TABLE IF NOT EXISTS `#__voucherprice` (
  `voucherprice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券面值自增ID',
  `voucherprice_describe` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucherprice` int(11) NOT NULL COMMENT '代金券面值',
  `voucherprice_defaultpoints` int(11) NOT NULL DEFAULT '0' COMMENT '代金劵默认的兑换所需积分可以为0',
  PRIMARY KEY (`voucherprice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券面额表';

DROP TABLE IF EXISTS `#__voucherquota`;
CREATE TABLE IF NOT EXISTS `#__voucherquota` (
  `voucherquota_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券套餐自增ID',
  `voucherquota_applyid` int(11) DEFAULT NULL COMMENT '代金券套餐申请编号',
  `voucherquota_memberid` int(11) NOT NULL COMMENT '会员ID',
  `voucherquota_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `voucherquota_storeid` int(11) NOT NULL COMMENT '机构ID',
  `voucherquota_storename` varchar(100) NOT NULL COMMENT '机构名称',
  `voucherquota_starttime` int(11) NOT NULL COMMENT '代金券套餐开始时间',
  `voucherquota_endtime` int(11) NOT NULL COMMENT '代金券套餐结束时间',
  `voucherquota_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:可用 2:取消 3:结束',
  PRIMARY KEY (`voucherquota_id`),
  KEY `voucherquota_storeid` (`voucherquota_storeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券套餐表';

DROP TABLE IF EXISTS `#__vouchertemplate`;
CREATE TABLE IF NOT EXISTS `#__vouchertemplate` (
  `vouchertemplate_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券模版自增ID',
  `vouchertemplate_title` varchar(50) NOT NULL COMMENT '代金券模版名称',
  `vouchertemplate_desc` varchar(255) NOT NULL COMMENT '代金券模版描述',
  `vouchertemplate_startdate` int(11) NOT NULL COMMENT '代金券模版有效期开始时间',
  `vouchertemplate_enddate` int(11) NOT NULL COMMENT '代金券模版有效期结束时间',
  `vouchertemplate_price` int(11) NOT NULL COMMENT '代金券模版面额',
  `vouchertemplate_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `vouchertemplate_store_id` int(11) NOT NULL COMMENT '代金券模版的机构ID',
  `vouchertemplate_storename` varchar(100) DEFAULT NULL COMMENT '机构名称',
  `vouchertemplate_sc_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属机构分类ID',
  `vouchertemplate_creator_id` int(11) NOT NULL COMMENT '代金券模版的创建者ID',
  `vouchertemplate_state` tinyint(4) NOT NULL COMMENT '代金券模版状态 1:有效 2:失效',
  `vouchertemplate_total` int(11) NOT NULL COMMENT '模版可发放的代金券总数',
  `vouchertemplate_giveout` int(11) NOT NULL COMMENT '模版已发放的代金券数量',
  `vouchertemplate_used` int(11) NOT NULL COMMENT '模版已经使用过的代金券',
  `vouchertemplate_adddate` int(11) NOT NULL COMMENT '模版的创建时间',
  `vouchertemplate_quotaid` int(11) NOT NULL COMMENT '套餐编号',
  `vouchertemplate_points` int(11) NOT NULL DEFAULT '0' COMMENT '兑换所需积分',
  `vouchertemplate_eachlimit` int(11) NOT NULL DEFAULT '1' COMMENT '每人限领张数',
  `vouchertemplate_styleimg` varchar(200) DEFAULT NULL COMMENT '样式模版图片',
  `vouchertemplate_customimg` varchar(200) DEFAULT NULL COMMENT '自定义代金券模板图片',
  `vouchertemplate_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐 0:不推荐 1:推荐',
  `vouchertemplate_gettype` tinyint(1) NOT NULL COMMENT '代金券类型',
  `vouchertemplate_if_private` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否私密 0:否 1:是',
  PRIMARY KEY (`vouchertemplate_id`),
  KEY `vouchertemplate_store_id` (`vouchertemplate_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券模版表';

DROP TABLE IF EXISTS `#__vrorder`;
CREATE TABLE IF NOT EXISTS `#__vrorder` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '虚拟订单自增ID',
  `order_sn` varchar(20) NOT NULL COMMENT '虚拟订单编号',
  `store_id` int(11) unsigned NOT NULL COMMENT '卖家机构ID',
  `store_name` varchar(50) NOT NULL COMMENT '卖家机构名称',
  `buyer_id` int(11) unsigned NOT NULL COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家登录名',
  `add_time` int(10) unsigned NOT NULL COMMENT '虚拟订单生成时间',
  `payment_code` char(20) NOT NULL DEFAULT '' COMMENT '虚拟订单支付方式名称代码',
  `payment_time` int(10) unsigned DEFAULT '0' COMMENT '虚拟订单支付(付款)时间',
  `trade_no` varchar(35) DEFAULT NULL COMMENT '第三方平台交易号',
  `close_time` int(10) unsigned DEFAULT '0' COMMENT '虚拟订单关闭时间',
  `close_reason` varchar(50) DEFAULT NULL COMMENT '虚拟订单关闭原因',
  `finnshed_time` int(11) DEFAULT NULL COMMENT '虚拟订单完成时间',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总价格(支付金额)',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `rcb_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡支付金额',
  `pd_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款支付金额',
  `voucher_price` int(11) DEFAULT NULL COMMENT '机构代金券面额',
  `voucher_code` varchar(32) DEFAULT NULL COMMENT '机构代金券编码',
  `order_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单状态 0:已取消 10:未付款 20:已付款 40:已完成',
  `refund_state` tinyint(1) unsigned DEFAULT '0' COMMENT '退款状态 0:无退款 1:部分退款 2:全部退款',
  `buyer_msg` varchar(150) DEFAULT NULL COMMENT '虚拟订单买家留言',
  `delete_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态 0:未删除1:放入回收站2:彻底删除',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `goods_type` enum('1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '1' COMMENT '1默认2抢购商品3限时折扣商品4组合套装5赠品6拼团7会员等级折扣8砍价9批发',
  `promotions_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '促销活动ID（抢购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用',
  `commis_rate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `gc_id` mediumint(9) DEFAULT '0' COMMENT '商品最底级分类ID',
  `order_from` char(15) DEFAULT '' COMMENT '订单来源',
  `evaluation_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '评价状态 0:默认 1:已评价 2:禁止评价',
  `evaluation_time` int(11) NOT NULL DEFAULT '0' COMMENT '评价时间',
  PRIMARY KEY (`order_id`),
  KEY `store_id` (`store_id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟订单表';

DROP TABLE IF EXISTS `#__vrrefund`;
CREATE TABLE IF NOT EXISTS `#__vrrefund` (
  `refund_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '虚拟退款记录自增ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '虚拟订单ID',
  `order_sn` varchar(20) NOT NULL COMMENT '虚拟订单编号',
  `refund_sn` varchar(50) NOT NULL COMMENT '申请编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '机构ID',
  `store_name` varchar(20) NOT NULL COMMENT '机构名称',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家会员名',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `goods_num` int(10) unsigned DEFAULT '1' COMMENT '退款商品数量',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `order_goods_type` tinyint(1) unsigned DEFAULT '1' COMMENT '订单商品类型:1:默认2:抢购商品3:限时折扣商品4:组合套装 5:赠品6:拼团7:会员等级折扣8:砍价',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `admin_state` tinyint(1) unsigned DEFAULT '1' COMMENT '退款状态 1:待审核 2:同意 3:不同意',
  `add_time` int(10) unsigned NOT NULL COMMENT '虚拟退款添加时间',
  `admin_time` int(10) unsigned DEFAULT '0' COMMENT '管理员处理时间',
  `buyer_message` varchar(300) DEFAULT NULL COMMENT '虚拟退款申请原因',
  `admin_message` varchar(300) DEFAULT NULL COMMENT '管理员备注',
  `commis_rate` smallint(6) DEFAULT '0' COMMENT '佣金比例',
  PRIMARY KEY (`refund_id`),
  KEY `order_id` (`order_id`),
  KEY `store_id` (`store_id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟退款表';

DROP TABLE IF EXISTS `#__wxconfig`;
CREATE TABLE IF NOT EXISTS `#__wxconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '微信配置自增ID',
  `wxname` varchar(60) NOT NULL DEFAULT '' COMMENT '公众号名称',
  `appid` varchar(50) NOT NULL DEFAULT '' COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appsecret',
  `token` char(255) NOT NULL COMMENT 'token',
  `qrcode` varchar(200) NOT NULL DEFAULT '' COMMENT '公众号二维码',
  `access_token` varchar(250) DEFAULT NULL COMMENT '令牌',
  `expires_in` int(15) DEFAULT NULL COMMENT '过期时间',
  `xcx_appid` varchar(50) NOT NULL DEFAULT '' COMMENT '小程序appid',
  `xcx_appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT '小程序appsecret',
  `xcx_access_token` varchar(250) DEFAULT NULL COMMENT '小程序令牌',
  `xcx_expires_in` int(15) DEFAULT NULL COMMENT '小程序过期时间',
  `ticket` varchar(250) DEFAULT NULL COMMENT 'ticket',
  `ticket_expires_in` int(15) DEFAULT NULL COMMENT 'ticket过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信配置表';

DROP TABLE IF EXISTS `#__wxkeyword`;
CREATE TABLE IF NOT EXISTS `#__wxkeyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `keyword` char(255) NOT NULL COMMENT '关键词',
  `pid` int(11) NOT NULL COMMENT '对应表ID',
  `type` varchar(30) DEFAULT 'TEXT' COMMENT '关键词操作类型',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信关键词表';

DROP TABLE IF EXISTS `#__wxmenu`;
CREATE TABLE IF NOT EXISTS `#__wxmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '微信菜单id',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父菜单id',
  `name` varchar(32) NOT NULL COMMENT '微信菜单名称',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '微信菜单类型',
  `value` text COMMENT '微信菜单值',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信菜单表';

DROP TABLE IF EXISTS `#__wxmsg`;
CREATE TABLE IF NOT EXISTS `#__wxmsg` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推送ID',
  `member_id` int(11) DEFAULT NULL COMMENT '为空则推送全体微信用户',
  `content` text NOT NULL COMMENT '推送内容',
  `createtime` int(15) NOT NULL COMMENT '创建时间',
  `issend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未发送1成功2失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信推送表';

DROP TABLE IF EXISTS `#__wxtext`;
CREATE TABLE IF NOT EXISTS `#__wxtext` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `keyword` char(255) NOT NULL COMMENT '关键词',
  `text` text NOT NULL COMMENT 'text',
  `createtime` varchar(13) NOT NULL DEFAULT '' COMMENT '创建时间',
  `updatetime` varchar(13) NOT NULL DEFAULT '' COMMENT '更新时间',
  `click` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点击',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信文本回复表';

INSERT INTO `#__area` VALUES ('1','北京','0','0','1','华北'), ('2','天津','0','0','1','华北'), ('3','河北','0','0','1','华北'), ('4','山西','0','0','1','华北'), ('5','内蒙古','0','0','1','华北'), ('6','辽宁','0','0','1','东北'), ('7','吉林','0','0','1','东北'), ('8','黑龙江','0','0','1','东北'), ('9','上海','0','0','1','华东'), ('10','江苏','0','0','1','华东'), ('11','浙江','0','0','1','华东'), ('12','安徽','0','0','1','华东'), ('13','福建','0','0','1','华南'), ('14','江西','0','0','1','华东'), ('15','山东','0','0','1','华东'), ('16','河南','0','0','1','华中'), ('17','湖北','0','0','1','华中'), ('18','湖南','0','0','1','华中'), ('19','广东','0','0','1','华南'), ('20','广西','0','0','1','华南'), ('21','海南','0','0','1','华南'), ('22','重庆','0','0','1','西南'), ('23','四川','0','0','1','西南'), ('24','贵州','0','0','1','西南'), ('25','云南','0','0','1','西南'), ('26','西藏','0','0','1','西南'), ('27','陕西','0','0','1','西北'), ('28','甘肃','0','0','1','西北'), ('29','青海','0','0','1','西北'), ('30','宁夏','0','0','1','西北'), ('31','新疆','0','0','1','西北'), ('32','台湾','0','0','1','港澳台'), ('33','香港','0','0','1','港澳台'), ('34','澳门','0','0','1','港澳台'), ('35','海外','0','0','1','海外'), ('36','北京市','1','0','2',''), ('37','东城区','36','0','3',NULL), ('38','西城区','36','0','3',NULL), ('39','上海市','9','0','2',NULL), ('40','天津市','2','0','2',NULL), ('41','朝阳区','36','0','3',NULL), ('42','丰台区','36','0','3',NULL), ('43','石景山区','36','0','3',NULL), ('44','海淀区','36','0','3',NULL), ('45','门头沟区','36','0','3',NULL), ('46','房山区','36','0','3',NULL), ('47','通州区','36','0','3',NULL), ('48','顺义区','36','0','3',NULL), ('49','昌平区','36','0','3',NULL), ('50','大兴区','36','0','3',NULL), ('51','怀柔区','36','0','3',NULL), ('52','平谷区','36','0','3',NULL), ('53','密云县','36','0','3',NULL), ('54','延庆县','36','0','3',NULL), ('55','和平区','40','0','3',NULL), ('56','河东区','40','0','3',NULL), ('57','河西区','40','0','3',NULL), ('58','南开区','40','0','3',NULL), ('59','河北区','40','0','3',NULL), ('60','红桥区','40','0','3',NULL), ('61','塘沽区','40','0','3',NULL), ('62','重庆市','22','0','2',NULL), ('64','东丽区','40','0','3',NULL), ('65','西青区','40','0','3',NULL), ('66','津南区','40','0','3',NULL), ('67','北辰区','40','0','3',NULL), ('68','武清区','40','0','3',NULL), ('69','宝坻区','40','0','3',NULL), ('70','宁河县','40','0','3',NULL), ('71','静海县','40','0','3',NULL), ('72','蓟县','40','0','3',NULL), ('73','石家庄市','3','0','2',NULL), ('74','唐山市','3','0','2',NULL), ('75','秦皇岛市','3','0','2',NULL), ('76','邯郸市','3','0','2',NULL), ('77','邢台市','3','0','2',NULL), ('78','保定市','3','0','2',NULL), ('79','张家口市','3','0','2',NULL), ('80','承德市','3','0','2',NULL), ('81','衡水市','3','0','2',NULL), ('82','廊坊市','3','0','2',NULL), ('83','沧州市','3','0','2',NULL), ('84','太原市','4','0','2',NULL), ('85','大同市','4','0','2',NULL), ('86','阳泉市','4','0','2',NULL), ('87','长治市','4','0','2',NULL), ('88','晋城市','4','0','2',NULL), ('89','朔州市','4','0','2',NULL), ('90','晋中市','4','0','2',NULL), ('91','运城市','4','0','2',NULL), ('92','忻州市','4','0','2',NULL), ('93','临汾市','4','0','2',NULL), ('94','吕梁市','4','0','2',NULL), ('95','呼和浩特市','5','0','2',NULL), ('96','包头市','5','0','2',NULL), ('97','乌海市','5','0','2',NULL), ('98','赤峰市','5','0','2',NULL), ('99','通辽市','5','0','2',NULL), ('100','鄂尔多斯市','5','0','2',NULL), ('101','呼伦贝尔市','5','0','2',NULL);
INSERT INTO `#__area` VALUES ('102','巴彦淖尔市','5','0','2',NULL), ('103','乌兰察布市','5','0','2',NULL), ('104','兴安盟','5','0','2',NULL), ('105','锡林郭勒盟','5','0','2',NULL), ('106','阿拉善盟','5','0','2',NULL), ('107','沈阳市','6','0','2',NULL), ('108','大连市','6','0','2',NULL), ('109','鞍山市','6','0','2',NULL), ('110','抚顺市','6','0','2',NULL), ('111','本溪市','6','0','2',NULL), ('112','丹东市','6','0','2',NULL), ('113','锦州市','6','0','2',NULL), ('114','营口市','6','0','2',NULL), ('115','阜新市','6','0','2',NULL), ('116','辽阳市','6','0','2',NULL), ('117','盘锦市','6','0','2',NULL), ('118','铁岭市','6','0','2',NULL), ('119','朝阳市','6','0','2',NULL), ('120','葫芦岛市','6','0','2',NULL), ('121','长春市','7','0','2',NULL), ('122','吉林市','7','0','2',NULL), ('123','四平市','7','0','2',NULL), ('124','辽源市','7','0','2',NULL), ('125','通化市','7','0','2',NULL), ('126','白山市','7','0','2',NULL), ('127','松原市','7','0','2',NULL), ('128','白城市','7','0','2',NULL), ('129','延边朝鲜族自治州','7','0','2',NULL), ('130','哈尔滨市','8','0','2',NULL), ('131','齐齐哈尔市','8','0','2',NULL), ('132','鸡西市','8','0','2',NULL), ('133','鹤岗市','8','0','2',NULL), ('134','双鸭山市','8','0','2',NULL), ('135','大庆市','8','0','2',NULL), ('136','伊春市','8','0','2',NULL), ('137','佳木斯市','8','0','2',NULL), ('138','七台河市','8','0','2',NULL), ('139','牡丹江市','8','0','2',NULL), ('140','黑河市','8','0','2',NULL), ('141','绥化市','8','0','2',NULL), ('142','大兴安岭地区','8','0','2',NULL), ('143','黄浦区','39','0','3',NULL), ('144','卢湾区','39','0','3',NULL), ('145','徐汇区','39','0','3',NULL), ('146','长宁区','39','0','3',NULL), ('147','静安区','39','0','3',NULL), ('148','普陀区','39','0','3',NULL), ('149','闸北区','39','0','3',NULL), ('150','虹口区','39','0','3',NULL), ('151','杨浦区','39','0','3',NULL), ('152','闵行区','39','0','3',NULL), ('153','宝山区','39','0','3',NULL), ('154','嘉定区','39','0','3',NULL), ('155','浦东新区','39','0','3',NULL), ('156','金山区','39','0','3',NULL), ('157','松江区','39','0','3',NULL), ('158','青浦区','39','0','3',NULL), ('159','南汇区','39','0','3',NULL), ('160','奉贤区','39','0','3',NULL), ('161','崇明县','39','0','3',NULL), ('162','南京市','10','0','2',NULL), ('163','无锡市','10','0','2',NULL), ('164','徐州市','10','0','2',NULL), ('165','常州市','10','0','2',NULL), ('166','苏州市','10','0','2',NULL), ('167','南通市','10','0','2',NULL), ('168','连云港市','10','0','2',NULL), ('169','淮安市','10','0','2',NULL), ('170','盐城市','10','0','2',NULL), ('171','扬州市','10','0','2',NULL), ('172','镇江市','10','0','2',NULL), ('173','泰州市','10','0','2',NULL), ('174','宿迁市','10','0','2',NULL), ('175','杭州市','11','0','2',NULL), ('176','宁波市','11','0','2',NULL), ('177','温州市','11','0','2',NULL), ('178','嘉兴市','11','0','2',NULL), ('179','湖州市','11','0','2',NULL), ('180','绍兴市','11','0','2',NULL), ('181','舟山市','11','0','2',NULL), ('182','衢州市','11','0','2',NULL), ('183','金华市','11','0','2',NULL), ('184','台州市','11','0','2',NULL), ('185','丽水市','11','0','2',NULL), ('186','合肥市','12','0','2',NULL), ('187','芜湖市','12','0','2',NULL), ('188','蚌埠市','12','0','2',NULL), ('189','淮南市','12','0','2',NULL), ('190','马鞍山市','12','0','2',NULL), ('191','淮北市','12','0','2',NULL), ('192','铜陵市','12','0','2',NULL), ('193','安庆市','12','0','2',NULL), ('194','黄山市','12','0','2',NULL), ('195','滁州市','12','0','2',NULL), ('196','阜阳市','12','0','2',NULL), ('197','宿州市','12','0','2',NULL), ('198','巢湖市','12','0','2',NULL), ('199','六安市','12','0','2',NULL), ('200','亳州市','12','0','2',NULL), ('201','池州市','12','0','2',NULL);
INSERT INTO `#__area` VALUES ('202','宣城市','12','0','2',NULL), ('203','福州市','13','0','2',NULL), ('204','厦门市','13','0','2',NULL), ('205','莆田市','13','0','2',NULL), ('206','三明市','13','0','2',NULL), ('207','泉州市','13','0','2',NULL), ('208','漳州市','13','0','2',NULL), ('209','南平市','13','0','2',NULL), ('210','龙岩市','13','0','2',NULL), ('211','宁德市','13','0','2',NULL), ('212','南昌市','14','0','2',NULL), ('213','景德镇市','14','0','2',NULL), ('214','萍乡市','14','0','2',NULL), ('215','九江市','14','0','2',NULL), ('216','新余市','14','0','2',NULL), ('217','鹰潭市','14','0','2',NULL), ('218','赣州市','14','0','2',NULL), ('219','吉安市','14','0','2',NULL), ('220','宜春市','14','0','2',NULL), ('221','抚州市','14','0','2',NULL), ('222','上饶市','14','0','2',NULL), ('223','济南市','15','0','2',NULL), ('224','青岛市','15','0','2',NULL), ('225','淄博市','15','0','2',NULL), ('226','枣庄市','15','0','2',NULL), ('227','东营市','15','0','2',NULL), ('228','烟台市','15','0','2',NULL), ('229','潍坊市','15','0','2',NULL), ('230','济宁市','15','0','2',NULL), ('231','泰安市','15','0','2',NULL), ('232','威海市','15','0','2',NULL), ('233','日照市','15','0','2',NULL), ('234','莱芜市','15','0','2',NULL), ('235','临沂市','15','0','2',NULL), ('236','德州市','15','0','2',NULL), ('237','聊城市','15','0','2',NULL), ('238','滨州市','15','0','2',NULL), ('239','菏泽市','15','0','2',NULL), ('240','郑州市','16','0','2',NULL), ('241','开封市','16','0','2',NULL), ('242','洛阳市','16','0','2',NULL), ('243','平顶山市','16','0','2',NULL), ('244','安阳市','16','0','2',NULL), ('245','鹤壁市','16','0','2',NULL), ('246','新乡市','16','0','2',NULL), ('247','焦作市','16','0','2',NULL), ('248','濮阳市','16','0','2',NULL), ('249','许昌市','16','0','2',NULL), ('250','漯河市','16','0','2',NULL), ('251','三门峡市','16','0','2',NULL), ('252','南阳市','16','0','2',NULL), ('253','商丘市','16','0','2',NULL), ('254','信阳市','16','0','2',NULL), ('255','周口市','16','0','2',NULL), ('256','驻马店市','16','0','2',NULL), ('257','济源市','16','0','2',NULL), ('258','武汉市','17','0','2',NULL), ('259','黄石市','17','0','2',NULL), ('260','十堰市','17','0','2',NULL), ('261','宜昌市','17','0','2',NULL), ('262','襄樊市','17','0','2',NULL), ('263','鄂州市','17','0','2',NULL), ('264','荆门市','17','0','2',NULL), ('265','孝感市','17','0','2',NULL), ('266','荆州市','17','0','2',NULL), ('267','黄冈市','17','0','2',NULL), ('268','咸宁市','17','0','2',NULL), ('269','随州市','17','0','2',NULL), ('270','恩施土家族苗族自治州','17','0','2',NULL), ('271','仙桃市','17','0','2',NULL), ('272','潜江市','17','0','2',NULL), ('273','天门市','17','0','2',NULL), ('274','神农架林区','17','0','2',NULL), ('275','长沙市','18','0','2',NULL), ('276','株洲市','18','0','2',NULL), ('277','湘潭市','18','0','2',NULL), ('278','衡阳市','18','0','2',NULL), ('279','邵阳市','18','0','2',NULL), ('280','岳阳市','18','0','2',NULL), ('281','常德市','18','0','2',NULL), ('282','张家界市','18','0','2',NULL), ('283','益阳市','18','0','2',NULL), ('284','郴州市','18','0','2',NULL), ('285','永州市','18','0','2',NULL), ('286','怀化市','18','0','2',NULL), ('287','娄底市','18','0','2',NULL), ('288','湘西土家族苗族自治州','18','0','2',NULL), ('289','广州市','19','0','2',NULL), ('290','韶关市','19','0','2',NULL), ('291','深圳市','19','0','2',NULL), ('292','珠海市','19','0','2',NULL), ('293','汕头市','19','0','2',NULL), ('294','佛山市','19','0','2',NULL), ('295','江门市','19','0','2',NULL), ('296','湛江市','19','0','2',NULL), ('297','茂名市','19','0','2',NULL), ('298','肇庆市','19','0','2',NULL), ('299','惠州市','19','0','2',NULL), ('300','梅州市','19','0','2',NULL), ('301','汕尾市','19','0','2',NULL);
INSERT INTO `#__area` VALUES ('302','河源市','19','0','2',NULL), ('303','阳江市','19','0','2',NULL), ('304','清远市','19','0','2',NULL), ('305','东莞市','19','0','2',NULL), ('306','中山市','19','0','2',NULL), ('307','潮州市','19','0','2',NULL), ('308','揭阳市','19','0','2',NULL), ('309','云浮市','19','0','2',NULL), ('310','南宁市','20','0','2',NULL), ('311','柳州市','20','0','2',NULL), ('312','桂林市','20','0','2',NULL), ('313','梧州市','20','0','2',NULL), ('314','北海市','20','0','2',NULL), ('315','防城港市','20','0','2',NULL), ('316','钦州市','20','0','2',NULL), ('317','贵港市','20','0','2',NULL), ('318','玉林市','20','0','2',NULL), ('319','百色市','20','0','2',NULL), ('320','贺州市','20','0','2',NULL), ('321','河池市','20','0','2',NULL), ('322','来宾市','20','0','2',NULL), ('323','崇左市','20','0','2',NULL), ('324','海口市','21','0','2',NULL), ('325','三亚市','21','0','2',NULL), ('326','五指山市','21','0','2',NULL), ('327','琼海市','21','0','2',NULL), ('328','儋州市','21','0','2',NULL), ('329','文昌市','21','0','2',NULL), ('330','万宁市','21','0','2',NULL), ('331','东方市','21','0','2',NULL), ('332','定安县','21','0','2',NULL), ('333','屯昌县','21','0','2',NULL), ('334','澄迈县','21','0','2',NULL), ('335','临高县','21','0','2',NULL), ('336','白沙黎族自治县','21','0','2',NULL), ('337','昌江黎族自治县','21','0','2',NULL), ('338','乐东黎族自治县','21','0','2',NULL), ('339','陵水黎族自治县','21','0','2',NULL), ('340','保亭黎族苗族自治县','21','0','2',NULL), ('341','琼中黎族苗族自治县','21','0','2',NULL), ('342','西沙群岛','21','0','2',NULL), ('343','南沙群岛','21','0','2',NULL), ('344','中沙群岛的岛礁及其海域','21','0','2',NULL), ('345','万州区','62','0','3',NULL), ('346','涪陵区','62','0','3',NULL), ('347','渝中区','62','0','3',NULL), ('348','大渡口区','62','0','3',NULL), ('349','江北区','62','0','3',NULL), ('350','沙坪坝区','62','0','3',NULL), ('351','九龙坡区','62','0','3',NULL), ('352','南岸区','62','0','3',NULL), ('353','北碚区','62','0','3',NULL), ('354','双桥区','62','0','3',NULL), ('355','万盛区','62','0','3',NULL), ('356','渝北区','62','0','3',NULL), ('357','巴南区','62','0','3',NULL), ('358','黔江区','62','0','3',NULL), ('359','长寿区','62','0','3',NULL), ('360','綦江县','62','0','3',NULL), ('361','潼南县','62','0','3',NULL), ('362','铜梁县','62','0','3',NULL), ('363','大足县','62','0','3',NULL), ('364','荣昌县','62','0','3',NULL), ('365','璧山县','62','0','3',NULL), ('366','梁平县','62','0','3',NULL), ('367','城口县','62','0','3',NULL), ('368','丰都县','62','0','3',NULL), ('369','垫江县','62','0','3',NULL), ('370','武隆县','62','0','3',NULL), ('371','忠县','62','0','3',NULL), ('372','开县','62','0','3',NULL), ('373','云阳县','62','0','3',NULL), ('374','奉节县','62','0','3',NULL), ('375','巫山县','62','0','3',NULL), ('376','巫溪县','62','0','3',NULL), ('377','石柱土家族自治县','62','0','3',NULL), ('378','秀山土家族苗族自治县','62','0','3',NULL), ('379','酉阳土家族苗族自治县','62','0','3',NULL), ('380','彭水苗族土家族自治县','62','0','3',NULL), ('381','江津市','62','0','3',NULL), ('382','合川市','62','0','3',NULL), ('383','永川市','62','0','3',NULL), ('384','南川市','62','0','3',NULL), ('385','成都市','23','0','2',NULL), ('386','自贡市','23','0','2',NULL), ('387','攀枝花市','23','0','2',NULL), ('388','泸州市','23','0','2',NULL), ('389','德阳市','23','0','2',NULL), ('390','绵阳市','23','0','2',NULL), ('391','广元市','23','0','2',NULL), ('392','遂宁市','23','0','2',NULL), ('393','内江市','23','0','2',NULL), ('394','乐山市','23','0','2',NULL), ('395','南充市','23','0','2',NULL), ('396','眉山市','23','0','2',NULL), ('397','宜宾市','23','0','2',NULL), ('398','广安市','23','0','2',NULL), ('399','达州市','23','0','2',NULL), ('400','雅安市','23','0','2',NULL), ('401','巴中市','23','0','2',NULL);
INSERT INTO `#__area` VALUES ('402','资阳市','23','0','2',NULL), ('403','阿坝藏族羌族自治州','23','0','2',NULL), ('404','甘孜藏族自治州','23','0','2',NULL), ('405','凉山彝族自治州','23','0','2',NULL), ('406','贵阳市','24','0','2',NULL), ('407','六盘水市','24','0','2',NULL), ('408','遵义市','24','0','2',NULL), ('409','安顺市','24','0','2',NULL), ('410','铜仁地区','24','0','2',NULL), ('411','黔西南布依族苗族自治州','24','0','2',NULL), ('412','毕节地区','24','0','2',NULL), ('413','黔东南苗族侗族自治州','24','0','2',NULL), ('414','黔南布依族苗族自治州','24','0','2',NULL), ('415','昆明市','25','0','2',NULL), ('416','曲靖市','25','0','2',NULL), ('417','玉溪市','25','0','2',NULL), ('418','保山市','25','0','2',NULL), ('419','昭通市','25','0','2',NULL), ('420','丽江市','25','0','2',NULL), ('421','思茅市','25','0','2',NULL), ('422','临沧市','25','0','2',NULL), ('423','楚雄彝族自治州','25','0','2',NULL), ('424','红河哈尼族彝族自治州','25','0','2',NULL), ('425','文山壮族苗族自治州','25','0','2',NULL), ('426','西双版纳傣族自治州','25','0','2',NULL), ('427','大理白族自治州','25','0','2',NULL), ('428','德宏傣族景颇族自治州','25','0','2',NULL), ('429','怒江傈僳族自治州','25','0','2',NULL), ('430','迪庆藏族自治州','25','0','2',NULL), ('431','拉萨市','26','0','2',NULL), ('432','昌都地区','26','0','2',NULL), ('433','山南地区','26','0','2',NULL), ('434','日喀则地区','26','0','2',NULL), ('435','那曲地区','26','0','2',NULL), ('436','阿里地区','26','0','2',NULL), ('437','林芝地区','26','0','2',NULL), ('438','西安市','27','0','2',NULL), ('439','铜川市','27','0','2',NULL), ('440','宝鸡市','27','0','2',NULL), ('441','咸阳市','27','0','2',NULL), ('442','渭南市','27','0','2',NULL), ('443','延安市','27','0','2',NULL), ('444','汉中市','27','0','2',NULL), ('445','榆林市','27','0','2',NULL), ('446','安康市','27','0','2',NULL), ('447','商洛市','27','0','2',NULL), ('448','兰州市','28','0','2',NULL), ('449','嘉峪关市','28','0','2',NULL), ('450','金昌市','28','0','2',NULL), ('451','白银市','28','0','2',NULL), ('452','天水市','28','0','2',NULL), ('453','武威市','28','0','2',NULL), ('454','张掖市','28','0','2',NULL), ('455','平凉市','28','0','2',NULL), ('456','酒泉市','28','0','2',NULL), ('457','庆阳市','28','0','2',NULL), ('458','定西市','28','0','2',NULL), ('459','陇南市','28','0','2',NULL), ('460','临夏回族自治州','28','0','2',NULL), ('461','甘南藏族自治州','28','0','2',NULL), ('462','西宁市','29','0','2',NULL), ('463','海东地区','29','0','2',NULL), ('464','海北藏族自治州','29','0','2',NULL), ('465','黄南藏族自治州','29','0','2',NULL), ('466','海南藏族自治州','29','0','2',NULL), ('467','果洛藏族自治州','29','0','2',NULL), ('468','玉树藏族自治州','29','0','2',NULL), ('469','海西蒙古族藏族自治州','29','0','2',NULL), ('470','银川市','30','0','2',NULL), ('471','石嘴山市','30','0','2',NULL), ('472','吴忠市','30','0','2',NULL), ('473','固原市','30','0','2',NULL), ('474','中卫市','30','0','2',NULL), ('475','乌鲁木齐市','31','0','2',NULL), ('476','克拉玛依市','31','0','2',NULL), ('477','吐鲁番地区','31','0','2',NULL), ('478','哈密地区','31','0','2',NULL), ('479','昌吉回族自治州','31','0','2',NULL), ('480','博尔塔拉蒙古自治州','31','0','2',NULL), ('481','巴音郭楞蒙古自治州','31','0','2',NULL), ('482','阿克苏地区','31','0','2',NULL), ('483','克孜勒苏柯尔克孜自治州','31','0','2',NULL), ('484','喀什地区','31','0','2',NULL), ('485','和田地区','31','0','2',NULL), ('486','伊犁哈萨克自治州','31','0','2',NULL), ('487','塔城地区','31','0','2',NULL), ('488','阿勒泰地区','31','0','2',NULL), ('489','石河子市','31','0','2',NULL), ('490','阿拉尔市','31','0','2',NULL), ('491','图木舒克市','31','0','2',NULL), ('492','五家渠市','31','0','2',NULL), ('493','台北市','32','0','2',NULL), ('494','高雄市','32','0','2',NULL), ('495','基隆市','32','0','2',NULL), ('496','台中市','32','0','2',NULL), ('497','台南市','32','0','2',NULL), ('498','新竹市','32','0','2',NULL), ('499','嘉义市','32','0','2',NULL), ('500','台北县','32','0','2',NULL), ('501','宜兰县','32','0','2',NULL);
INSERT INTO `#__area` VALUES ('502','桃园县','32','0','2',NULL), ('503','新竹县','32','0','2',NULL), ('504','苗栗县','32','0','2',NULL), ('505','台中县','32','0','2',NULL), ('506','彰化县','32','0','2',NULL), ('507','南投县','32','0','2',NULL), ('508','云林县','32','0','2',NULL), ('509','嘉义县','32','0','2',NULL), ('510','台南县','32','0','2',NULL), ('511','高雄县','32','0','2',NULL), ('512','屏东县','32','0','2',NULL), ('513','澎湖县','32','0','2',NULL), ('514','台东县','32','0','2',NULL), ('515','花莲县','32','0','2',NULL), ('516','中西区','33','0','2',NULL), ('517','东区','33','0','2',NULL), ('518','九龙城区','33','0','2',NULL), ('519','观塘区','33','0','2',NULL), ('520','南区','33','0','2',NULL), ('521','深水埗区','33','0','2',NULL), ('522','黄大仙区','33','0','2',NULL), ('523','湾仔区','33','0','2',NULL), ('524','油尖旺区','33','0','2',NULL), ('525','离岛区','33','0','2',NULL), ('526','葵青区','33','0','2',NULL), ('527','北区','33','0','2',NULL), ('528','西贡区','33','0','2',NULL), ('529','沙田区','33','0','2',NULL), ('530','屯门区','33','0','2',NULL), ('531','大埔区','33','0','2',NULL), ('532','荃湾区','33','0','2',NULL), ('533','元朗区','33','0','2',NULL), ('534','澳门特别行政区','34','0','2',NULL), ('535','美国','45055','0','3',NULL), ('536','加拿大','45055','0','3',NULL), ('537','澳大利亚','45055','0','3',NULL), ('538','新西兰','45055','0','3',NULL), ('539','英国','45055','0','3',NULL), ('540','法国','45055','0','3',NULL), ('541','德国','45055','0','3',NULL), ('542','捷克','45055','0','3',NULL), ('543','荷兰','45055','0','3',NULL), ('544','瑞士','45055','0','3',NULL), ('545','希腊','45055','0','3',NULL), ('546','挪威','45055','0','3',NULL), ('547','瑞典','45055','0','3',NULL), ('548','丹麦','45055','0','3',NULL), ('549','芬兰','45055','0','3',NULL), ('550','爱尔兰','45055','0','3',NULL), ('551','奥地利','45055','0','3',NULL), ('552','意大利','45055','0','3',NULL), ('553','乌克兰','45055','0','3',NULL), ('554','俄罗斯','45055','0','3',NULL), ('555','西班牙','45055','0','3',NULL), ('556','韩国','45055','0','3',NULL), ('557','新加坡','45055','0','3',NULL), ('558','马来西亚','45055','0','3',NULL), ('559','印度','45055','0','3',NULL), ('560','泰国','45055','0','3',NULL), ('561','日本','45055','0','3',NULL), ('562','巴西','45055','0','3',NULL), ('563','阿根廷','45055','0','3',NULL), ('564','南非','45055','0','3',NULL), ('565','埃及','45055','0','3',NULL), ('566','其他','36','0','3',NULL), ('1126','井陉县','73','0','3',NULL), ('1127','井陉矿区','73','0','3',NULL), ('1128','元氏县','73','0','3',NULL), ('1129','平山县','73','0','3',NULL), ('1130','新乐市','73','0','3',NULL), ('1131','新华区','73','0','3',NULL), ('1132','无极县','73','0','3',NULL), ('1133','晋州市','73','0','3',NULL), ('1134','栾城县','73','0','3',NULL), ('1135','桥东区','73','0','3',NULL), ('1136','桥西区','73','0','3',NULL), ('1137','正定县','73','0','3',NULL), ('1138','深泽县','73','0','3',NULL), ('1139','灵寿县','73','0','3',NULL), ('1140','藁城市','73','0','3',NULL), ('1141','行唐县','73','0','3',NULL), ('1142','裕华区','73','0','3',NULL), ('1143','赞皇县','73','0','3',NULL), ('1144','赵县','73','0','3',NULL), ('1145','辛集市','73','0','3',NULL), ('1146','长安区','73','0','3',NULL), ('1147','高邑县','73','0','3',NULL), ('1148','鹿泉市','73','0','3',NULL), ('1149','丰南区','74','0','3',NULL), ('1150','丰润区','74','0','3',NULL), ('1151','乐亭县','74','0','3',NULL), ('1152','古冶区','74','0','3',NULL), ('1153','唐海县','74','0','3',NULL), ('1154','开平区','74','0','3',NULL), ('1155','滦南县','74','0','3',NULL), ('1156','滦县','74','0','3',NULL), ('1157','玉田县','74','0','3',NULL), ('1158','路北区','74','0','3',NULL), ('1159','路南区','74','0','3',NULL), ('1160','迁安市','74','0','3',NULL);
INSERT INTO `#__area` VALUES ('1161','迁西县','74','0','3',NULL), ('1162','遵化市','74','0','3',NULL), ('1163','北戴河区','75','0','3',NULL), ('1164','卢龙县','75','0','3',NULL), ('1165','山海关区','75','0','3',NULL), ('1166','抚宁县','75','0','3',NULL), ('1167','昌黎县','75','0','3',NULL), ('1168','海港区','75','0','3',NULL), ('1169','青龙满族自治县','75','0','3',NULL), ('1170','丛台区','76','0','3',NULL), ('1171','临漳县','76','0','3',NULL), ('1172','复兴区','76','0','3',NULL), ('1173','大名县','76','0','3',NULL), ('1174','峰峰矿区','76','0','3',NULL), ('1175','广平县','76','0','3',NULL), ('1176','成安县','76','0','3',NULL), ('1177','曲周县','76','0','3',NULL), ('1178','武安市','76','0','3',NULL), ('1179','永年县','76','0','3',NULL), ('1180','涉县','76','0','3',NULL), ('1181','磁县','76','0','3',NULL), ('1182','肥乡县','76','0','3',NULL), ('1183','邯山区','76','0','3',NULL), ('1184','邯郸县','76','0','3',NULL), ('1185','邱县','76','0','3',NULL), ('1186','馆陶县','76','0','3',NULL), ('1187','魏县','76','0','3',NULL), ('1188','鸡泽县','76','0','3',NULL), ('1189','临城县','77','0','3',NULL), ('1190','临西县','77','0','3',NULL), ('1191','任县','77','0','3',NULL), ('1192','内丘县','77','0','3',NULL), ('1193','南和县','77','0','3',NULL), ('1194','南宫市','77','0','3',NULL), ('1195','威县','77','0','3',NULL), ('1196','宁晋县','77','0','3',NULL), ('1197','巨鹿县','77','0','3',NULL), ('1198','平乡县','77','0','3',NULL), ('1199','广宗县','77','0','3',NULL), ('1200','新河县','77','0','3',NULL), ('1201','柏乡县','77','0','3',NULL), ('1202','桥东区','77','0','3',NULL), ('1203','桥西区','77','0','3',NULL), ('1204','沙河市','77','0','3',NULL), ('1205','清河县','77','0','3',NULL), ('1206','邢台县','77','0','3',NULL), ('1207','隆尧县','77','0','3',NULL), ('1208','北市区','78','0','3',NULL), ('1209','南市区','78','0','3',NULL), ('1210','博野县','78','0','3',NULL), ('1211','唐县','78','0','3',NULL), ('1212','安国市','78','0','3',NULL), ('1213','安新县','78','0','3',NULL), ('1214','定兴县','78','0','3',NULL), ('1215','定州市','78','0','3',NULL), ('1216','容城县','78','0','3',NULL), ('1217','徐水县','78','0','3',NULL), ('1218','新市区','78','0','3',NULL), ('1219','易县','78','0','3',NULL), ('1220','曲阳县','78','0','3',NULL), ('1221','望都县','78','0','3',NULL), ('1222','涞水县','78','0','3',NULL), ('1223','涞源县','78','0','3',NULL), ('1224','涿州市','78','0','3',NULL), ('1225','清苑县','78','0','3',NULL), ('1226','满城县','78','0','3',NULL), ('1227','蠡县','78','0','3',NULL), ('1228','阜平县','78','0','3',NULL), ('1229','雄县','78','0','3',NULL), ('1230','顺平县','78','0','3',NULL), ('1231','高碑店市','78','0','3',NULL), ('1232','高阳县','78','0','3',NULL), ('1233','万全县','79','0','3',NULL), ('1234','下花园区','79','0','3',NULL), ('1235','宣化区','79','0','3',NULL), ('1236','宣化县','79','0','3',NULL), ('1237','尚义县','79','0','3',NULL), ('1238','崇礼县','79','0','3',NULL), ('1239','康保县','79','0','3',NULL), ('1240','张北县','79','0','3',NULL), ('1241','怀安县','79','0','3',NULL), ('1242','怀来县','79','0','3',NULL), ('1243','桥东区','79','0','3',NULL), ('1244','桥西区','79','0','3',NULL), ('1245','沽源县','79','0','3',NULL), ('1246','涿鹿县','79','0','3',NULL), ('1247','蔚县','79','0','3',NULL), ('1248','赤城县','79','0','3',NULL), ('1249','阳原县','79','0','3',NULL), ('1250','丰宁满族自治县','80','0','3',NULL), ('1251','兴隆县','80','0','3',NULL), ('1252','双桥区','80','0','3',NULL), ('1253','双滦区','80','0','3',NULL), ('1254','围场满族蒙古族自治县','80','0','3',NULL), ('1255','宽城满族自治县','80','0','3',NULL), ('1256','平泉县','80','0','3',NULL), ('1257','承德县','80','0','3',NULL), ('1258','滦平县','80','0','3',NULL), ('1259','隆化县','80','0','3',NULL), ('1260','鹰手营子矿区','80','0','3',NULL);
INSERT INTO `#__area` VALUES ('1261','冀州市','81','0','3',NULL), ('1262','安平县','81','0','3',NULL), ('1263','故城县','81','0','3',NULL), ('1264','景县','81','0','3',NULL), ('1265','枣强县','81','0','3',NULL), ('1266','桃城区','81','0','3',NULL), ('1267','武强县','81','0','3',NULL), ('1268','武邑县','81','0','3',NULL), ('1269','深州市','81','0','3',NULL), ('1270','阜城县','81','0','3',NULL), ('1271','饶阳县','81','0','3',NULL), ('1272','三河市','82','0','3',NULL), ('1273','固安县','82','0','3',NULL), ('1274','大厂回族自治县','82','0','3',NULL), ('1275','大城县','82','0','3',NULL), ('1276','安次区','82','0','3',NULL), ('1277','广阳区','82','0','3',NULL), ('1278','文安县','82','0','3',NULL), ('1279','永清县','82','0','3',NULL), ('1280','霸州市','82','0','3',NULL), ('1281','香河县','82','0','3',NULL), ('1282','东光县','83','0','3',NULL), ('1283','任丘市','83','0','3',NULL), ('1284','南皮县','83','0','3',NULL), ('1285','吴桥县','83','0','3',NULL), ('1286','孟村回族自治县','83','0','3',NULL), ('1287','新华区','83','0','3',NULL), ('1288','沧县','83','0','3',NULL), ('1289','河间市','83','0','3',NULL), ('1290','泊头市','83','0','3',NULL), ('1291','海兴县','83','0','3',NULL), ('1292','献县','83','0','3',NULL), ('1293','盐山县','83','0','3',NULL), ('1294','肃宁县','83','0','3',NULL), ('1295','运河区','83','0','3',NULL), ('1296','青县','83','0','3',NULL), ('1297','黄骅市','83','0','3',NULL), ('1298','万柏林区','84','0','3',NULL), ('1299','古交市','84','0','3',NULL), ('1300','娄烦县','84','0','3',NULL), ('1301','小店区','84','0','3',NULL), ('1302','尖草坪区','84','0','3',NULL), ('1303','晋源区','84','0','3',NULL), ('1304','杏花岭区','84','0','3',NULL), ('1305','清徐县','84','0','3',NULL), ('1306','迎泽区','84','0','3',NULL), ('1307','阳曲县','84','0','3',NULL), ('1308','南郊区','85','0','3',NULL), ('1309','城区','85','0','3',NULL), ('1310','大同县','85','0','3',NULL), ('1311','天镇县','85','0','3',NULL), ('1312','左云县','85','0','3',NULL), ('1313','广灵县','85','0','3',NULL), ('1314','新荣区','85','0','3',NULL), ('1315','浑源县','85','0','3',NULL), ('1316','灵丘县','85','0','3',NULL), ('1317','矿区','85','0','3',NULL), ('1318','阳高县','85','0','3',NULL), ('1319','城区','86','0','3',NULL), ('1320','平定县','86','0','3',NULL), ('1321','盂县','86','0','3',NULL), ('1322','矿区','86','0','3',NULL), ('1323','郊区','86','0','3',NULL), ('1324','城区','87','0','3',NULL), ('1325','壶关县','87','0','3',NULL), ('1326','屯留县','87','0','3',NULL), ('1327','平顺县','87','0','3',NULL), ('1328','武乡县','87','0','3',NULL), ('1329','沁县','87','0','3',NULL), ('1330','沁源县','87','0','3',NULL), ('1331','潞城市','87','0','3',NULL), ('1332','襄垣县','87','0','3',NULL), ('1333','郊区','87','0','3',NULL), ('1334','长子县','87','0','3',NULL), ('1335','长治县','87','0','3',NULL), ('1336','黎城县','87','0','3',NULL), ('1337','城区','88','0','3',NULL), ('1338','沁水县','88','0','3',NULL), ('1339','泽州县','88','0','3',NULL), ('1340','阳城县','88','0','3',NULL), ('1341','陵川县','88','0','3',NULL), ('1342','高平市','88','0','3',NULL), ('1343','右玉县','89','0','3',NULL), ('1344','山阴县','89','0','3',NULL), ('1345','平鲁区','89','0','3',NULL), ('1346','应县','89','0','3',NULL), ('1347','怀仁县','89','0','3',NULL), ('1348','朔城区','89','0','3',NULL), ('1349','介休市','90','0','3',NULL), ('1350','和顺县','90','0','3',NULL), ('1351','太谷县','90','0','3',NULL), ('1352','寿阳县','90','0','3',NULL), ('1353','左权县','90','0','3',NULL), ('1354','平遥县','90','0','3',NULL), ('1355','昔阳县','90','0','3',NULL), ('1356','榆次区','90','0','3',NULL), ('1357','榆社县','90','0','3',NULL), ('1358','灵石县','90','0','3',NULL), ('1359','祁县','90','0','3',NULL), ('1360','万荣县','91','0','3',NULL);
INSERT INTO `#__area` VALUES ('1361','临猗县','91','0','3',NULL), ('1362','垣曲县','91','0','3',NULL), ('1363','夏县','91','0','3',NULL), ('1364','平陆县','91','0','3',NULL), ('1365','新绛县','91','0','3',NULL), ('1366','永济市','91','0','3',NULL), ('1367','河津市','91','0','3',NULL), ('1368','盐湖区','91','0','3',NULL), ('1369','稷山县','91','0','3',NULL), ('1370','绛县','91','0','3',NULL), ('1371','芮城县','91','0','3',NULL), ('1372','闻喜县','91','0','3',NULL), ('1373','五台县','92','0','3',NULL), ('1374','五寨县','92','0','3',NULL), ('1375','代县','92','0','3',NULL), ('1376','保德县','92','0','3',NULL), ('1377','偏关县','92','0','3',NULL), ('1378','原平市','92','0','3',NULL), ('1379','宁武县','92','0','3',NULL), ('1380','定襄县','92','0','3',NULL), ('1381','岢岚县','92','0','3',NULL), ('1382','忻府区','92','0','3',NULL), ('1383','河曲县','92','0','3',NULL), ('1384','神池县','92','0','3',NULL), ('1385','繁峙县','92','0','3',NULL), ('1386','静乐县','92','0','3',NULL), ('1387','乡宁县','93','0','3',NULL), ('1388','侯马市','93','0','3',NULL), ('1389','古县','93','0','3',NULL), ('1390','吉县','93','0','3',NULL), ('1391','大宁县','93','0','3',NULL), ('1392','安泽县','93','0','3',NULL), ('1393','尧都区','93','0','3',NULL), ('1394','曲沃县','93','0','3',NULL), ('1395','永和县','93','0','3',NULL), ('1396','汾西县','93','0','3',NULL), ('1397','洪洞县','93','0','3',NULL), ('1398','浮山县','93','0','3',NULL), ('1399','翼城县','93','0','3',NULL), ('1400','蒲县','93','0','3',NULL), ('1401','襄汾县','93','0','3',NULL), ('1402','隰县','93','0','3',NULL), ('1403','霍州市','93','0','3',NULL), ('1404','中阳县','94','0','3',NULL), ('1405','临县','94','0','3',NULL), ('1406','交口县','94','0','3',NULL), ('1407','交城县','94','0','3',NULL), ('1408','兴县','94','0','3',NULL), ('1409','孝义市','94','0','3',NULL), ('1410','岚县','94','0','3',NULL), ('1411','文水县','94','0','3',NULL), ('1412','方山县','94','0','3',NULL), ('1413','柳林县','94','0','3',NULL), ('1414','汾阳市','94','0','3',NULL), ('1415','石楼县','94','0','3',NULL), ('1416','离石区','94','0','3',NULL), ('1417','和林格尔县','95','0','3',NULL), ('1418','回民区','95','0','3',NULL), ('1419','土默特左旗','95','0','3',NULL), ('1420','托克托县','95','0','3',NULL), ('1421','新城区','95','0','3',NULL), ('1422','武川县','95','0','3',NULL), ('1423','清水河县','95','0','3',NULL), ('1424','玉泉区','95','0','3',NULL), ('1425','赛罕区','95','0','3',NULL), ('1426','东河区','96','0','3',NULL), ('1427','九原区','96','0','3',NULL), ('1428','固阳县','96','0','3',NULL), ('1429','土默特右旗','96','0','3',NULL), ('1430','昆都仑区','96','0','3',NULL), ('1431','白云矿区','96','0','3',NULL), ('1432','石拐区','96','0','3',NULL), ('1433','达尔罕茂明安联合旗','96','0','3',NULL), ('1434','青山区','96','0','3',NULL), ('1435','乌达区','97','0','3',NULL), ('1436','海勃湾区','97','0','3',NULL), ('1437','海南区','97','0','3',NULL), ('1438','元宝山区','98','0','3',NULL), ('1439','克什克腾旗','98','0','3',NULL), ('1440','喀喇沁旗','98','0','3',NULL), ('1441','宁城县','98','0','3',NULL), ('1442','巴林右旗','98','0','3',NULL), ('1443','巴林左旗','98','0','3',NULL), ('1444','敖汉旗','98','0','3',NULL), ('1445','松山区','98','0','3',NULL), ('1446','林西县','98','0','3',NULL), ('1447','红山区','98','0','3',NULL), ('1448','翁牛特旗','98','0','3',NULL), ('1449','阿鲁科尔沁旗','98','0','3',NULL), ('1450','奈曼旗','99','0','3',NULL), ('1451','库伦旗','99','0','3',NULL), ('1452','开鲁县','99','0','3',NULL), ('1453','扎鲁特旗','99','0','3',NULL), ('1454','科尔沁区','99','0','3',NULL), ('1455','科尔沁左翼中旗','99','0','3',NULL), ('1456','科尔沁左翼后旗','99','0','3',NULL), ('1457','霍林郭勒市','99','0','3',NULL), ('1458','东胜区','100','0','3',NULL), ('1459','乌审旗','100','0','3',NULL), ('1460','伊金霍洛旗','100','0','3',NULL);
INSERT INTO `#__area` VALUES ('1461','准格尔旗','100','0','3',NULL), ('1462','杭锦旗','100','0','3',NULL), ('1463','达拉特旗','100','0','3',NULL), ('1464','鄂东胜区','100','0','3',NULL), ('1465','鄂托克前旗','100','0','3',NULL), ('1466','鄂托克旗','100','0','3',NULL), ('1467','扎兰屯市','101','0','3',NULL), ('1468','新巴尔虎右旗','101','0','3',NULL), ('1469','新巴尔虎左旗','101','0','3',NULL), ('1470','根河市','101','0','3',NULL), ('1471','海拉尔区','101','0','3',NULL), ('1472','满洲里市','101','0','3',NULL), ('1473','牙克石市','101','0','3',NULL), ('1474','莫力达瓦达斡尔族自治旗','101','0','3',NULL), ('1475','鄂伦春自治旗','101','0','3',NULL), ('1476','鄂温克族自治旗','101','0','3',NULL), ('1477','阿荣旗','101','0','3',NULL), ('1478','陈巴尔虎旗','101','0','3',NULL), ('1479','额尔古纳市','101','0','3',NULL), ('1480','临河区','102','0','3',NULL), ('1481','乌拉特中旗','102','0','3',NULL), ('1482','乌拉特前旗','102','0','3',NULL), ('1483','乌拉特后旗','102','0','3',NULL), ('1484','五原县','102','0','3',NULL), ('1485','杭锦后旗','102','0','3',NULL), ('1486','磴口县','102','0','3',NULL), ('1487','丰镇市','103','0','3',NULL), ('1488','兴和县','103','0','3',NULL), ('1489','凉城县','103','0','3',NULL), ('1490','化德县','103','0','3',NULL), ('1491','卓资县','103','0','3',NULL), ('1492','商都县','103','0','3',NULL), ('1493','四子王旗','103','0','3',NULL), ('1494','察哈尔右翼中旗','103','0','3',NULL), ('1495','察哈尔右翼前旗','103','0','3',NULL), ('1496','察哈尔右翼后旗','103','0','3',NULL), ('1497','集宁区','103','0','3',NULL), ('1498','乌兰浩特市','104','0','3',NULL), ('1499','扎赉特旗','104','0','3',NULL), ('1500','科尔沁右翼中旗','104','0','3',NULL), ('1501','科尔沁右翼前旗','104','0','3',NULL), ('1502','突泉县','104','0','3',NULL), ('1503','阿尔山市','104','0','3',NULL), ('1504','东乌珠穆沁旗','105','0','3',NULL), ('1505','二连浩特市','105','0','3',NULL), ('1506','多伦县','105','0','3',NULL), ('1507','太仆寺旗','105','0','3',NULL), ('1508','正蓝旗','105','0','3',NULL), ('1509','正镶白旗','105','0','3',NULL), ('1510','苏尼特右旗','105','0','3',NULL), ('1511','苏尼特左旗','105','0','3',NULL), ('1512','西乌珠穆沁旗','105','0','3',NULL), ('1513','锡林浩特市','105','0','3',NULL), ('1514','镶黄旗','105','0','3',NULL), ('1515','阿巴嘎旗','105','0','3',NULL), ('1516','阿拉善右旗','106','0','3',NULL), ('1517','阿拉善左旗','106','0','3',NULL), ('1518','额济纳旗','106','0','3',NULL), ('1519','东陵区','107','0','3',NULL), ('1520','于洪区','107','0','3',NULL), ('1521','和平区','107','0','3',NULL), ('1522','大东区','107','0','3',NULL), ('1523','康平县','107','0','3',NULL), ('1524','新民市','107','0','3',NULL), ('1525','沈北新区','107','0','3',NULL), ('1526','沈河区','107','0','3',NULL), ('1527','法库县','107','0','3',NULL), ('1528','皇姑区','107','0','3',NULL), ('1529','苏家屯区','107','0','3',NULL), ('1530','辽中县','107','0','3',NULL), ('1531','铁西区','107','0','3',NULL), ('1532','中山区','108','0','3',NULL), ('1533','庄河市','108','0','3',NULL), ('1534','旅顺口区','108','0','3',NULL), ('1535','普兰店市','108','0','3',NULL), ('1536','沙河口区','108','0','3',NULL), ('1537','瓦房店市','108','0','3',NULL), ('1538','甘井子区','108','0','3',NULL), ('1539','西岗区','108','0','3',NULL), ('1540','金州区','108','0','3',NULL), ('1541','长海县','108','0','3',NULL), ('1542','千山区','109','0','3',NULL), ('1543','台安县','109','0','3',NULL), ('1544','岫岩满族自治县','109','0','3',NULL), ('1545','海城市','109','0','3',NULL), ('1546','立山区','109','0','3',NULL), ('1547','铁东区','109','0','3',NULL), ('1548','铁西区','109','0','3',NULL), ('1549','东洲区','110','0','3',NULL), ('1550','抚顺县','110','0','3',NULL), ('1551','新宾满族自治县','110','0','3',NULL), ('1552','新抚区','110','0','3',NULL), ('1553','望花区','110','0','3',NULL), ('1554','清原满族自治县','110','0','3',NULL), ('1555','顺城区','110','0','3',NULL), ('1556','南芬区','111','0','3',NULL), ('1557','平山区','111','0','3',NULL), ('1558','明山区','111','0','3',NULL), ('1559','本溪满族自治县','111','0','3',NULL), ('1560','桓仁满族自治县','111','0','3',NULL);
INSERT INTO `#__area` VALUES ('1561','溪湖区','111','0','3',NULL), ('1562','东港市','112','0','3',NULL), ('1563','元宝区','112','0','3',NULL), ('1564','凤城市','112','0','3',NULL), ('1565','宽甸满族自治县','112','0','3',NULL), ('1566','振兴区','112','0','3',NULL), ('1567','振安区','112','0','3',NULL), ('1568','义县','113','0','3',NULL), ('1569','凌河区','113','0','3',NULL), ('1570','凌海市','113','0','3',NULL), ('1571','北镇市','113','0','3',NULL), ('1572','古塔区','113','0','3',NULL), ('1573','太和区','113','0','3',NULL), ('1574','黑山县','113','0','3',NULL), ('1575','大石桥市','114','0','3',NULL), ('1576','盖州市','114','0','3',NULL), ('1577','站前区','114','0','3',NULL), ('1578','老边区','114','0','3',NULL), ('1579','西市区','114','0','3',NULL), ('1580','鲅鱼圈区','114','0','3',NULL), ('1581','太平区','115','0','3',NULL), ('1582','彰武县','115','0','3',NULL), ('1583','新邱区','115','0','3',NULL), ('1584','海州区','115','0','3',NULL), ('1585','清河门区','115','0','3',NULL), ('1586','细河区','115','0','3',NULL), ('1587','蒙古族自治县','115','0','3',NULL), ('1588','太子河区','116','0','3',NULL), ('1589','宏伟区','116','0','3',NULL), ('1590','弓长岭区','116','0','3',NULL), ('1591','文圣区','116','0','3',NULL), ('1592','灯塔市','116','0','3',NULL), ('1593','白塔区','116','0','3',NULL), ('1594','辽阳县','116','0','3',NULL), ('1595','兴隆台区','117','0','3',NULL), ('1596','双台子区','117','0','3',NULL), ('1597','大洼县','117','0','3',NULL), ('1598','盘山县','117','0','3',NULL), ('1599','开原市','118','0','3',NULL), ('1600','昌图县','118','0','3',NULL), ('1601','清河区','118','0','3',NULL), ('1602','西丰县','118','0','3',NULL), ('1603','调兵山市','118','0','3',NULL), ('1604','铁岭县','118','0','3',NULL), ('1605','银州区','118','0','3',NULL), ('1606','凌源市','119','0','3',NULL), ('1607','北票市','119','0','3',NULL), ('1608','双塔区','119','0','3',NULL), ('1609','喀喇沁左翼蒙古族自治县','119','0','3',NULL), ('1610','建平县','119','0','3',NULL), ('1611','朝阳县','119','0','3',NULL), ('1612','龙城区','119','0','3',NULL), ('1613','兴城市','120','0','3',NULL), ('1614','南票区','120','0','3',NULL), ('1615','建昌县','120','0','3',NULL), ('1616','绥中县','120','0','3',NULL), ('1617','连山区','120','0','3',NULL), ('1618','龙港区','120','0','3',NULL), ('1619','九台市','121','0','3',NULL), ('1620','二道区','121','0','3',NULL), ('1621','农安县','121','0','3',NULL), ('1622','南关区','121','0','3',NULL), ('1623','双阳区','121','0','3',NULL), ('1624','宽城区','121','0','3',NULL), ('1625','德惠市','121','0','3',NULL), ('1626','朝阳区','121','0','3',NULL), ('1627','榆树市','121','0','3',NULL), ('1628','绿园区','121','0','3',NULL), ('1629','丰满区','122','0','3',NULL), ('1630','昌邑区','122','0','3',NULL), ('1631','桦甸市','122','0','3',NULL), ('1632','永吉县','122','0','3',NULL), ('1633','磐石市','122','0','3',NULL), ('1634','舒兰市','122','0','3',NULL), ('1635','船营区','122','0','3',NULL), ('1636','蛟河市','122','0','3',NULL), ('1637','龙潭区','122','0','3',NULL), ('1638','伊通满族自治县','123','0','3',NULL), ('1639','公主岭市','123','0','3',NULL), ('1640','双辽市','123','0','3',NULL), ('1641','梨树县','123','0','3',NULL), ('1642','铁东区','123','0','3',NULL), ('1643','铁西区','123','0','3',NULL), ('1644','东丰县','124','0','3',NULL), ('1645','东辽县','124','0','3',NULL), ('1646','西安区','124','0','3',NULL), ('1647','龙山区','124','0','3',NULL), ('1648','东昌区','125','0','3',NULL), ('1649','二道江区','125','0','3',NULL), ('1650','柳河县','125','0','3',NULL), ('1651','梅河口市','125','0','3',NULL), ('1652','辉南县','125','0','3',NULL), ('1653','通化县','125','0','3',NULL), ('1654','集安市','125','0','3',NULL), ('1655','临江市','126','0','3',NULL), ('1656','八道江区','126','0','3',NULL), ('1657','抚松县','126','0','3',NULL), ('1658','江源区','126','0','3',NULL), ('1659','长白朝鲜族自治县','126','0','3',NULL), ('1660','靖宇县','126','0','3',NULL);
INSERT INTO `#__area` VALUES ('1661','干安县','127','0','3',NULL), ('1662','前郭尔罗斯蒙古族自治县','127','0','3',NULL), ('1663','宁江区','127','0','3',NULL), ('1664','扶余县','127','0','3',NULL), ('1665','长岭县','127','0','3',NULL), ('1666','大安市','128','0','3',NULL), ('1667','洮北区','128','0','3',NULL), ('1668','洮南市','128','0','3',NULL), ('1669','通榆县','128','0','3',NULL), ('1670','镇赉县','128','0','3',NULL), ('1671','和龙市','129','0','3',NULL), ('1672','图们市','129','0','3',NULL), ('1673','安图县','129','0','3',NULL), ('1674','延吉市','129','0','3',NULL), ('1675','敦化市','129','0','3',NULL), ('1676','汪清县','129','0','3',NULL), ('1677','珲春市','129','0','3',NULL), ('1678','龙井市','129','0','3',NULL), ('1679','五常市','130','0','3',NULL), ('1680','依兰县','130','0','3',NULL), ('1681','南岗区','130','0','3',NULL), ('1682','双城市','130','0','3',NULL), ('1683','呼兰区','130','0','3',NULL), ('1684','哈尔滨市道里区','130','0','3',NULL), ('1685','宾县','130','0','3',NULL), ('1686','尚志市','130','0','3',NULL), ('1687','巴彦县','130','0','3',NULL), ('1688','平房区','130','0','3',NULL), ('1689','延寿县','130','0','3',NULL), ('1690','方正县','130','0','3',NULL), ('1691','木兰县','130','0','3',NULL), ('1692','松北区','130','0','3',NULL), ('1693','通河县','130','0','3',NULL), ('1694','道外区','130','0','3',NULL), ('1695','阿城区','130','0','3',NULL), ('1696','香坊区','130','0','3',NULL), ('1697','依安县','131','0','3',NULL), ('1698','克东县','131','0','3',NULL), ('1699','克山县','131','0','3',NULL), ('1700','富拉尔基区','131','0','3',NULL), ('1701','富裕县','131','0','3',NULL), ('1702','建华区','131','0','3',NULL), ('1703','拜泉县','131','0','3',NULL), ('1704','昂昂溪区','131','0','3',NULL), ('1705','梅里斯达斡尔族区','131','0','3',NULL), ('1706','泰来县','131','0','3',NULL), ('1707','甘南县','131','0','3',NULL), ('1708','碾子山区','131','0','3',NULL), ('1709','讷河市','131','0','3',NULL), ('1710','铁锋区','131','0','3',NULL), ('1711','龙江县','131','0','3',NULL), ('1712','龙沙区','131','0','3',NULL), ('1713','城子河区','132','0','3',NULL), ('1714','密山市','132','0','3',NULL), ('1715','恒山区','132','0','3',NULL), ('1716','梨树区','132','0','3',NULL), ('1717','滴道区','132','0','3',NULL), ('1718','虎林市','132','0','3',NULL), ('1719','鸡东县','132','0','3',NULL), ('1720','鸡冠区','132','0','3',NULL), ('1721','麻山区','132','0','3',NULL), ('1722','东山区','133','0','3',NULL), ('1723','兴安区','133','0','3',NULL), ('1724','兴山区','133','0','3',NULL), ('1725','南山区','133','0','3',NULL), ('1726','向阳区','133','0','3',NULL), ('1727','工农区','133','0','3',NULL), ('1728','绥滨县','133','0','3',NULL), ('1729','萝北县','133','0','3',NULL), ('1730','友谊县','134','0','3',NULL), ('1731','四方台区','134','0','3',NULL), ('1732','宝山区','134','0','3',NULL), ('1733','宝清县','134','0','3',NULL), ('1734','尖山区','134','0','3',NULL), ('1735','岭东区','134','0','3',NULL), ('1736','集贤县','134','0','3',NULL), ('1737','饶河县','134','0','3',NULL), ('1738','大同区','135','0','3',NULL), ('1739','杜尔伯特蒙古族自治县','135','0','3',NULL), ('1740','林甸县','135','0','3',NULL), ('1741','红岗区','135','0','3',NULL), ('1742','肇州县','135','0','3',NULL), ('1743','肇源县','135','0','3',NULL), ('1744','胡路区','135','0','3',NULL), ('1745','萨尔图区','135','0','3',NULL), ('1746','龙凤区','135','0','3',NULL), ('1747','上甘岭区','136','0','3',NULL), ('1748','乌伊岭区','136','0','3',NULL), ('1749','乌马河区','136','0','3',NULL), ('1750','五营区','136','0','3',NULL), ('1751','伊春区','136','0','3',NULL), ('1752','南岔区','136','0','3',NULL), ('1753','友好区','136','0','3',NULL), ('1754','嘉荫县','136','0','3',NULL), ('1755','带岭区','136','0','3',NULL), ('1756','新青区','136','0','3',NULL), ('1757','汤旺河区','136','0','3',NULL), ('1758','红星区','136','0','3',NULL), ('1759','美溪区','136','0','3',NULL), ('1760','翠峦区','136','0','3',NULL);
INSERT INTO `#__area` VALUES ('1761','西林区','136','0','3',NULL), ('1762','金山屯区','136','0','3',NULL), ('1763','铁力市','136','0','3',NULL), ('1764','东风区','137','0','3',NULL), ('1765','前进区','137','0','3',NULL), ('1766','同江市','137','0','3',NULL), ('1767','向阳区','137','0','3',NULL), ('1768','富锦市','137','0','3',NULL), ('1769','抚远县','137','0','3',NULL), ('1770','桦南县','137','0','3',NULL), ('1771','桦川县','137','0','3',NULL), ('1772','汤原县','137','0','3',NULL), ('1773','郊区','137','0','3',NULL), ('1774','勃利县','138','0','3',NULL), ('1775','新兴区','138','0','3',NULL), ('1776','桃山区','138','0','3',NULL), ('1777','茄子河区','138','0','3',NULL), ('1778','东宁县','139','0','3',NULL), ('1779','东安区','139','0','3',NULL), ('1780','宁安市','139','0','3',NULL), ('1781','林口县','139','0','3',NULL), ('1782','海林市','139','0','3',NULL), ('1783','爱民区','139','0','3',NULL), ('1784','穆棱市','139','0','3',NULL), ('1785','绥芬河市','139','0','3',NULL), ('1786','西安区','139','0','3',NULL), ('1787','阳明区','139','0','3',NULL), ('1788','五大连池市','140','0','3',NULL), ('1789','北安市','140','0','3',NULL), ('1790','嫩江县','140','0','3',NULL), ('1791','孙吴县','140','0','3',NULL), ('1792','爱辉区','140','0','3',NULL), ('1793','车逊克县','140','0','3',NULL), ('1794','逊克县','140','0','3',NULL), ('1795','兰西县','141','0','3',NULL), ('1796','安达市','141','0','3',NULL), ('1797','庆安县','141','0','3',NULL), ('1798','明水县','141','0','3',NULL), ('1799','望奎县','141','0','3',NULL), ('1800','海伦市','141','0','3',NULL), ('1801','绥化市北林区','141','0','3',NULL), ('1802','绥棱县','141','0','3',NULL), ('1803','肇东市','141','0','3',NULL), ('1804','青冈县','141','0','3',NULL), ('1805','呼玛县','142','0','3',NULL), ('1806','塔河县','142','0','3',NULL), ('1807','大兴安岭地区加格达奇区','142','0','3',NULL), ('1808','大兴安岭地区呼中区','142','0','3',NULL), ('1809','大兴安岭地区新林区','142','0','3',NULL), ('1810','大兴安岭地区松岭区','142','0','3',NULL), ('1811','漠河县','142','0','3',NULL), ('2027','下关区','162','0','3',NULL), ('2028','六合区','162','0','3',NULL), ('2029','建邺区','162','0','3',NULL), ('2030','栖霞区','162','0','3',NULL), ('2031','江宁区','162','0','3',NULL), ('2032','浦口区','162','0','3',NULL), ('2033','溧水县','162','0','3',NULL), ('2034','玄武区','162','0','3',NULL), ('2035','白下区','162','0','3',NULL), ('2036','秦淮区','162','0','3',NULL), ('2037','雨花台区','162','0','3',NULL), ('2038','高淳县','162','0','3',NULL), ('2039','鼓楼区','162','0','3',NULL), ('2040','北塘区','163','0','3',NULL), ('2041','南长区','163','0','3',NULL), ('2042','宜兴市','163','0','3',NULL), ('2043','崇安区','163','0','3',NULL), ('2044','惠山区','163','0','3',NULL), ('2045','江阴市','163','0','3',NULL), ('2046','滨湖区','163','0','3',NULL), ('2047','锡山区','163','0','3',NULL), ('2048','丰县','164','0','3',NULL), ('2049','九里区','164','0','3',NULL), ('2050','云龙区','164','0','3',NULL), ('2051','新沂市','164','0','3',NULL), ('2052','沛县','164','0','3',NULL), ('2053','泉山区','164','0','3',NULL), ('2054','睢宁县','164','0','3',NULL), ('2055','贾汪区','164','0','3',NULL), ('2056','邳州市','164','0','3',NULL), ('2057','铜山县','164','0','3',NULL), ('2058','鼓楼区','164','0','3',NULL), ('2059','天宁区','165','0','3',NULL), ('2060','戚墅堰区','165','0','3',NULL), ('2061','新北区','165','0','3',NULL), ('2062','武进区','165','0','3',NULL), ('2063','溧阳市','165','0','3',NULL), ('2064','金坛市','165','0','3',NULL), ('2065','钟楼区','165','0','3',NULL), ('2066','吴中区','166','0','3',NULL), ('2067','吴江市','166','0','3',NULL), ('2068','太仓市','166','0','3',NULL), ('2069','常熟市','166','0','3',NULL), ('2070','平江区','166','0','3',NULL), ('2071','张家港市','166','0','3',NULL), ('2072','昆山市','166','0','3',NULL), ('2073','沧浪区','166','0','3',NULL), ('2074','相城区','166','0','3',NULL), ('2075','苏州工业园区','166','0','3',NULL);
INSERT INTO `#__area` VALUES ('2076','虎丘区','166','0','3',NULL), ('2077','金阊区','166','0','3',NULL), ('2078','启东市','167','0','3',NULL), ('2079','如东县','167','0','3',NULL), ('2080','如皋市','167','0','3',NULL), ('2081','崇川区','167','0','3',NULL), ('2082','海安县','167','0','3',NULL), ('2083','海门市','167','0','3',NULL), ('2084','港闸区','167','0','3',NULL), ('2085','通州市','167','0','3',NULL), ('2086','东海县','168','0','3',NULL), ('2087','新浦区','168','0','3',NULL), ('2088','海州区','168','0','3',NULL), ('2089','灌云县','168','0','3',NULL), ('2090','灌南县','168','0','3',NULL), ('2091','赣榆县','168','0','3',NULL), ('2092','连云区','168','0','3',NULL), ('2093','楚州区','169','0','3',NULL), ('2094','洪泽县','169','0','3',NULL), ('2095','涟水县','169','0','3',NULL), ('2096','淮阴区','169','0','3',NULL), ('2097','清河区','169','0','3',NULL), ('2098','清浦区','169','0','3',NULL), ('2099','盱眙县','169','0','3',NULL), ('2100','金湖县','169','0','3',NULL), ('2101','东台市','170','0','3',NULL), ('2102','亭湖区','170','0','3',NULL), ('2103','响水县','170','0','3',NULL), ('2104','大丰市','170','0','3',NULL), ('2105','射阳县','170','0','3',NULL), ('2106','建湖县','170','0','3',NULL), ('2107','滨海县','170','0','3',NULL), ('2108','盐都区','170','0','3',NULL), ('2109','阜宁县','170','0','3',NULL), ('2110','仪征市','171','0','3',NULL), ('2111','宝应县','171','0','3',NULL), ('2112','广陵区','171','0','3',NULL), ('2113','江都市','171','0','3',NULL), ('2114','维扬区','171','0','3',NULL), ('2115','邗江区','171','0','3',NULL), ('2116','高邮市','171','0','3',NULL), ('2117','丹徒区','172','0','3',NULL), ('2118','丹阳市','172','0','3',NULL), ('2119','京口区','172','0','3',NULL), ('2120','句容市','172','0','3',NULL), ('2121','扬中市','172','0','3',NULL), ('2122','润州区','172','0','3',NULL), ('2123','兴化市','173','0','3',NULL), ('2124','姜堰市','173','0','3',NULL), ('2125','泰兴市','173','0','3',NULL), ('2126','海陵区','173','0','3',NULL), ('2127','靖江市','173','0','3',NULL), ('2128','高港区','173','0','3',NULL), ('2129','宿城区','174','0','3',NULL), ('2130','宿豫区','174','0','3',NULL), ('2131','沭阳县','174','0','3',NULL), ('2132','泗洪县','174','0','3',NULL), ('2133','泗阳县','174','0','3',NULL), ('2134','上城区','175','0','3',NULL), ('2135','下城区','175','0','3',NULL), ('2136','临安市','175','0','3',NULL), ('2137','余杭区','175','0','3',NULL), ('2138','富阳市','175','0','3',NULL), ('2139','建德市','175','0','3',NULL), ('2140','拱墅区','175','0','3',NULL), ('2141','桐庐县','175','0','3',NULL), ('2142','江干区','175','0','3',NULL), ('2143','淳安县','175','0','3',NULL), ('2144','滨江区','175','0','3',NULL), ('2145','萧山区','175','0','3',NULL), ('2146','西湖区','175','0','3',NULL), ('2147','余姚市','176','0','3',NULL), ('2148','北仑区','176','0','3',NULL), ('2149','奉化市','176','0','3',NULL), ('2150','宁海县','176','0','3',NULL), ('2151','慈溪市','176','0','3',NULL), ('2152','江东区','176','0','3',NULL), ('2153','江北区','176','0','3',NULL), ('2154','海曙区','176','0','3',NULL), ('2155','象山县','176','0','3',NULL), ('2156','鄞州区','176','0','3',NULL), ('2157','镇海区','176','0','3',NULL), ('2158','乐清市','177','0','3',NULL), ('2159','平阳县','177','0','3',NULL), ('2160','文成县','177','0','3',NULL), ('2161','永嘉县','177','0','3',NULL), ('2162','泰顺县','177','0','3',NULL), ('2163','洞头县','177','0','3',NULL), ('2164','瑞安市','177','0','3',NULL), ('2165','瓯海区','177','0','3',NULL), ('2166','苍南县','177','0','3',NULL), ('2167','鹿城区','177','0','3',NULL), ('2168','龙湾区','177','0','3',NULL), ('2169','南湖区','178','0','3',NULL), ('2170','嘉善县','178','0','3',NULL), ('2171','平湖市','178','0','3',NULL), ('2172','桐乡市','178','0','3',NULL), ('2173','海宁市','178','0','3',NULL), ('2174','海盐县','178','0','3',NULL), ('2175','秀洲区','178','0','3',NULL);
INSERT INTO `#__area` VALUES ('2176','南浔区','179','0','3',NULL), ('2177','吴兴区','179','0','3',NULL), ('2178','安吉县','179','0','3',NULL), ('2179','德清县','179','0','3',NULL), ('2180','长兴县','179','0','3',NULL), ('2181','上虞市','180','0','3',NULL), ('2182','嵊州市','180','0','3',NULL), ('2183','新昌县','180','0','3',NULL), ('2184','绍兴县','180','0','3',NULL), ('2185','诸暨市','180','0','3',NULL), ('2186','越城区','180','0','3',NULL), ('2187','定海区','181','0','3',NULL), ('2188','岱山县','181','0','3',NULL), ('2189','嵊泗县','181','0','3',NULL), ('2190','普陀区','181','0','3',NULL), ('2191','常山县','182','0','3',NULL), ('2192','开化县','182','0','3',NULL), ('2193','柯城区','182','0','3',NULL), ('2194','江山市','182','0','3',NULL), ('2195','衢江区','182','0','3',NULL), ('2196','龙游县','182','0','3',NULL), ('2197','东阳市','183','0','3',NULL), ('2198','义乌市','183','0','3',NULL), ('2199','兰溪市','183','0','3',NULL), ('2200','婺城区','183','0','3',NULL), ('2201','武义县','183','0','3',NULL), ('2202','永康市','183','0','3',NULL), ('2203','浦江县','183','0','3',NULL), ('2204','磐安县','183','0','3',NULL), ('2205','金东区','183','0','3',NULL), ('2206','三门县','184','0','3',NULL), ('2207','临海市','184','0','3',NULL), ('2208','仙居县','184','0','3',NULL), ('2209','天台县','184','0','3',NULL), ('2210','椒江区','184','0','3',NULL), ('2211','温岭市','184','0','3',NULL), ('2212','玉环县','184','0','3',NULL), ('2213','路桥区','184','0','3',NULL), ('2214','黄岩区','184','0','3',NULL), ('2215','云和县','185','0','3',NULL), ('2216','庆元县','185','0','3',NULL), ('2217','景宁畲族自治县','185','0','3',NULL), ('2218','松阳县','185','0','3',NULL), ('2219','缙云县','185','0','3',NULL), ('2220','莲都区','185','0','3',NULL), ('2221','遂昌县','185','0','3',NULL), ('2222','青田县','185','0','3',NULL), ('2223','龙泉市','185','0','3',NULL), ('2224','包河区','186','0','3',NULL), ('2225','庐阳区','186','0','3',NULL), ('2226','瑶海区','186','0','3',NULL), ('2227','肥东县','186','0','3',NULL), ('2228','肥西县','186','0','3',NULL), ('2229','蜀山区','186','0','3',NULL), ('2230','长丰县','186','0','3',NULL), ('2231','三山区','187','0','3',NULL), ('2232','南陵县','187','0','3',NULL), ('2233','弋江区','187','0','3',NULL), ('2234','繁昌县','187','0','3',NULL), ('2235','芜湖县','187','0','3',NULL), ('2236','镜湖区','187','0','3',NULL), ('2237','鸠江区','187','0','3',NULL), ('2238','五河县','188','0','3',NULL), ('2239','固镇县','188','0','3',NULL), ('2240','怀远县','188','0','3',NULL), ('2241','淮上区','188','0','3',NULL), ('2242','禹会区','188','0','3',NULL), ('2243','蚌山区','188','0','3',NULL), ('2244','龙子湖区','188','0','3',NULL), ('2245','八公山区','189','0','3',NULL), ('2246','凤台县','189','0','3',NULL), ('2247','大通区','189','0','3',NULL), ('2248','潘集区','189','0','3',NULL), ('2249','田家庵区','189','0','3',NULL), ('2250','谢家集区','189','0','3',NULL), ('2251','当涂县','190','0','3',NULL), ('2252','花山区','190','0','3',NULL), ('2253','金家庄区','190','0','3',NULL), ('2254','雨山区','190','0','3',NULL), ('2255','杜集区','191','0','3',NULL), ('2256','濉溪县','191','0','3',NULL), ('2257','烈山区','191','0','3',NULL), ('2258','相山区','191','0','3',NULL), ('2259','狮子山区','192','0','3',NULL), ('2260','郊区','192','0','3',NULL), ('2261','铜官山区','192','0','3',NULL), ('2262','铜陵县','192','0','3',NULL), ('2263','大观区','193','0','3',NULL), ('2264','太湖县','193','0','3',NULL), ('2265','宜秀区','193','0','3',NULL), ('2266','宿松县','193','0','3',NULL), ('2267','岳西县','193','0','3',NULL), ('2268','怀宁县','193','0','3',NULL), ('2269','望江县','193','0','3',NULL), ('2270','枞阳县','193','0','3',NULL), ('2271','桐城市','193','0','3',NULL), ('2272','潜山县','193','0','3',NULL), ('2273','迎江区','193','0','3',NULL), ('2274','休宁县','194','0','3',NULL), ('2275','屯溪区','194','0','3',NULL);
INSERT INTO `#__area` VALUES ('2276','徽州区','194','0','3',NULL), ('2277','歙县','194','0','3',NULL), ('2278','祁门县','194','0','3',NULL), ('2279','黄山区','194','0','3',NULL), ('2280','黟县','194','0','3',NULL), ('2281','全椒县','195','0','3',NULL), ('2282','凤阳县','195','0','3',NULL), ('2283','南谯区','195','0','3',NULL), ('2284','天长市','195','0','3',NULL), ('2285','定远县','195','0','3',NULL), ('2286','明光市','195','0','3',NULL), ('2287','来安县','195','0','3',NULL), ('2288','琅玡区','195','0','3',NULL), ('2289','临泉县','196','0','3',NULL), ('2290','太和县','196','0','3',NULL), ('2291','界首市','196','0','3',NULL), ('2292','阜南县','196','0','3',NULL), ('2293','颍东区','196','0','3',NULL), ('2294','颍州区','196','0','3',NULL), ('2295','颍泉区','196','0','3',NULL), ('2296','颖上县','196','0','3',NULL), ('2297','埇桥区','197','0','3',NULL), ('2298','泗县辖','197','0','3',NULL), ('2299','灵璧县','197','0','3',NULL), ('2300','砀山县','197','0','3',NULL), ('2301','萧县','197','0','3',NULL), ('2302','含山县','198','0','3',NULL), ('2303','和县','198','0','3',NULL), ('2304','居巢区','198','0','3',NULL), ('2305','庐江县','198','0','3',NULL), ('2306','无为县','198','0','3',NULL), ('2307','寿县','199','0','3',NULL), ('2308','舒城县','199','0','3',NULL), ('2309','裕安区','199','0','3',NULL), ('2310','金安区','199','0','3',NULL), ('2311','金寨县','199','0','3',NULL), ('2312','霍山县','199','0','3',NULL), ('2313','霍邱县','199','0','3',NULL), ('2314','利辛县','200','0','3',NULL), ('2315','涡阳县','200','0','3',NULL), ('2316','蒙城县','200','0','3',NULL), ('2317','谯城区','200','0','3',NULL), ('2318','东至县','201','0','3',NULL), ('2319','石台县','201','0','3',NULL), ('2320','贵池区','201','0','3',NULL), ('2321','青阳县','201','0','3',NULL), ('2322','宁国市','202','0','3',NULL), ('2323','宣州区','202','0','3',NULL), ('2324','广德县','202','0','3',NULL), ('2325','旌德县','202','0','3',NULL), ('2326','泾县','202','0','3',NULL), ('2327','绩溪县','202','0','3',NULL), ('2328','郎溪县','202','0','3',NULL), ('2329','仓山区','203','0','3',NULL), ('2330','台江区','203','0','3',NULL), ('2331','平潭县','203','0','3',NULL), ('2332','晋安区','203','0','3',NULL), ('2333','永泰县','203','0','3',NULL), ('2334','福清市','203','0','3',NULL), ('2335','罗源县','203','0','3',NULL), ('2336','连江县','203','0','3',NULL), ('2337','长乐市','203','0','3',NULL), ('2338','闽侯县','203','0','3',NULL), ('2339','闽清县','203','0','3',NULL), ('2340','马尾区','203','0','3',NULL), ('2341','鼓楼区','203','0','3',NULL), ('2342','同安区','204','0','3',NULL), ('2343','思明区','204','0','3',NULL), ('2344','海沧区','204','0','3',NULL), ('2345','湖里区','204','0','3',NULL), ('2346','翔安区','204','0','3',NULL), ('2347','集美区','204','0','3',NULL), ('2348','仙游县','205','0','3',NULL), ('2349','城厢区','205','0','3',NULL), ('2350','涵江区','205','0','3',NULL), ('2351','秀屿区','205','0','3',NULL), ('2352','荔城区','205','0','3',NULL), ('2353','三元区','206','0','3',NULL), ('2354','大田县','206','0','3',NULL), ('2355','宁化县','206','0','3',NULL), ('2356','将乐县','206','0','3',NULL), ('2357','尤溪县','206','0','3',NULL), ('2358','建宁县','206','0','3',NULL), ('2359','明溪县','206','0','3',NULL), ('2360','梅列区','206','0','3',NULL), ('2361','永安市','206','0','3',NULL), ('2362','沙县','206','0','3',NULL), ('2363','泰宁县','206','0','3',NULL), ('2364','清流县','206','0','3',NULL), ('2365','丰泽区','207','0','3',NULL), ('2366','南安市','207','0','3',NULL), ('2367','安溪县','207','0','3',NULL), ('2368','德化县','207','0','3',NULL), ('2369','惠安县','207','0','3',NULL), ('2370','晋江市','207','0','3',NULL), ('2371','永春县','207','0','3',NULL), ('2372','泉港区','207','0','3',NULL), ('2373','洛江区','207','0','3',NULL), ('2374','石狮市','207','0','3',NULL), ('2375','金门县','207','0','3',NULL);
INSERT INTO `#__area` VALUES ('2376','鲤城区','207','0','3',NULL), ('2377','东山县','208','0','3',NULL), ('2378','云霄县','208','0','3',NULL), ('2379','华安县','208','0','3',NULL), ('2380','南靖县','208','0','3',NULL), ('2381','平和县','208','0','3',NULL), ('2382','漳浦县','208','0','3',NULL), ('2383','芗城区','208','0','3',NULL), ('2384','诏安县','208','0','3',NULL), ('2385','长泰县','208','0','3',NULL), ('2386','龙文区','208','0','3',NULL), ('2387','龙海市','208','0','3',NULL), ('2388','光泽县','209','0','3',NULL), ('2389','延平区','209','0','3',NULL), ('2390','建瓯市','209','0','3',NULL), ('2391','建阳市','209','0','3',NULL), ('2392','政和县','209','0','3',NULL), ('2393','松溪县','209','0','3',NULL), ('2394','武夷山市','209','0','3',NULL), ('2395','浦城县','209','0','3',NULL), ('2396','邵武市','209','0','3',NULL), ('2397','顺昌县','209','0','3',NULL), ('2398','上杭县','210','0','3',NULL), ('2399','新罗区','210','0','3',NULL), ('2400','武平县','210','0','3',NULL), ('2401','永定县','210','0','3',NULL), ('2402','漳平市','210','0','3',NULL), ('2403','连城县','210','0','3',NULL), ('2404','长汀县','210','0','3',NULL), ('2405','古田县','211','0','3',NULL), ('2406','周宁县','211','0','3',NULL), ('2407','寿宁县','211','0','3',NULL), ('2408','屏南县','211','0','3',NULL), ('2409','柘荣县','211','0','3',NULL), ('2410','福安市','211','0','3',NULL), ('2411','福鼎市','211','0','3',NULL), ('2412','蕉城区','211','0','3',NULL), ('2413','霞浦县','211','0','3',NULL), ('2414','东湖区','212','0','3',NULL), ('2415','南昌县','212','0','3',NULL), ('2416','安义县','212','0','3',NULL), ('2417','新建县','212','0','3',NULL), ('2418','湾里区','212','0','3',NULL), ('2419','西湖区','212','0','3',NULL), ('2420','进贤县','212','0','3',NULL), ('2421','青云谱区','212','0','3',NULL), ('2422','青山湖区','212','0','3',NULL), ('2423','乐平市','213','0','3',NULL), ('2424','昌江区','213','0','3',NULL), ('2425','浮梁县','213','0','3',NULL), ('2426','珠山区','213','0','3',NULL), ('2427','上栗县','214','0','3',NULL), ('2428','安源区','214','0','3',NULL), ('2429','湘东区','214','0','3',NULL), ('2430','芦溪县','214','0','3',NULL), ('2431','莲花县','214','0','3',NULL), ('2432','九江县','215','0','3',NULL), ('2433','修水县','215','0','3',NULL), ('2434','庐山区','215','0','3',NULL), ('2435','彭泽县','215','0','3',NULL), ('2436','德安县','215','0','3',NULL), ('2437','星子县','215','0','3',NULL), ('2438','武宁县','215','0','3',NULL), ('2439','永修县','215','0','3',NULL), ('2440','浔阳区','215','0','3',NULL), ('2441','湖口县','215','0','3',NULL), ('2442','瑞昌市','215','0','3',NULL), ('2443','都昌县','215','0','3',NULL), ('2444','分宜县','216','0','3',NULL), ('2445','渝水区','216','0','3',NULL), ('2446','余江县','217','0','3',NULL), ('2447','月湖区','217','0','3',NULL), ('2448','贵溪市','217','0','3',NULL), ('2449','上犹县','218','0','3',NULL), ('2450','于都县','218','0','3',NULL), ('2451','会昌县','218','0','3',NULL), ('2452','信丰县','218','0','3',NULL), ('2453','全南县','218','0','3',NULL), ('2454','兴国县','218','0','3',NULL), ('2455','南康市','218','0','3',NULL), ('2456','大余县','218','0','3',NULL), ('2457','宁都县','218','0','3',NULL), ('2458','安远县','218','0','3',NULL), ('2459','定南县','218','0','3',NULL), ('2460','寻乌县','218','0','3',NULL), ('2461','崇义县','218','0','3',NULL), ('2462','瑞金市','218','0','3',NULL), ('2463','石城县','218','0','3',NULL), ('2464','章贡区','218','0','3',NULL), ('2465','赣县','218','0','3',NULL), ('2466','龙南县','218','0','3',NULL), ('2467','万安县','219','0','3',NULL), ('2468','井冈山市','219','0','3',NULL), ('2469','吉安县','219','0','3',NULL), ('2470','吉州区','219','0','3',NULL), ('2471','吉水县','219','0','3',NULL), ('2472','安福县','219','0','3',NULL), ('2473','峡江县','219','0','3',NULL), ('2474','新干县','219','0','3',NULL), ('2475','永丰县','219','0','3',NULL);
INSERT INTO `#__area` VALUES ('2476','永新县','219','0','3',NULL), ('2477','泰和县','219','0','3',NULL), ('2478','遂川县','219','0','3',NULL), ('2479','青原区','219','0','3',NULL), ('2480','万载县','220','0','3',NULL), ('2481','上高县','220','0','3',NULL), ('2482','丰城市','220','0','3',NULL), ('2483','奉新县','220','0','3',NULL), ('2484','宜丰县','220','0','3',NULL), ('2485','樟树市','220','0','3',NULL), ('2486','袁州区','220','0','3',NULL), ('2487','铜鼓县','220','0','3',NULL), ('2488','靖安县','220','0','3',NULL), ('2489','高安市','220','0','3',NULL), ('2490','东乡县','221','0','3',NULL), ('2491','临川区','221','0','3',NULL), ('2492','乐安县','221','0','3',NULL), ('2493','南丰县','221','0','3',NULL), ('2494','南城县','221','0','3',NULL), ('2495','宜黄县','221','0','3',NULL), ('2496','崇仁县','221','0','3',NULL), ('2497','广昌县','221','0','3',NULL), ('2498','资溪县','221','0','3',NULL), ('2499','金溪县','221','0','3',NULL), ('2500','黎川县','221','0','3',NULL), ('2501','万年县','222','0','3',NULL), ('2502','上饶县','222','0','3',NULL), ('2503','余干县','222','0','3',NULL), ('2504','信州区','222','0','3',NULL), ('2505','婺源县','222','0','3',NULL), ('2506','广丰县','222','0','3',NULL), ('2507','弋阳县','222','0','3',NULL), ('2508','德兴市','222','0','3',NULL), ('2509','横峰县','222','0','3',NULL), ('2510','玉山县','222','0','3',NULL), ('2511','鄱阳县','222','0','3',NULL), ('2512','铅山县','222','0','3',NULL), ('2513','历下区','223','0','3',NULL), ('2514','历城区','223','0','3',NULL), ('2515','商河县','223','0','3',NULL), ('2516','天桥区','223','0','3',NULL), ('2517','市中区','223','0','3',NULL), ('2518','平阴县','223','0','3',NULL), ('2519','槐荫区','223','0','3',NULL), ('2520','济阳县','223','0','3',NULL), ('2521','章丘市','223','0','3',NULL), ('2522','长清区','223','0','3',NULL), ('2523','即墨市','224','0','3',NULL), ('2524','四方区','224','0','3',NULL), ('2525','城阳区','224','0','3',NULL), ('2526','崂山区','224','0','3',NULL), ('2527','市北区','224','0','3',NULL), ('2528','市南区','224','0','3',NULL), ('2529','平度市','224','0','3',NULL), ('2530','李沧区','224','0','3',NULL), ('2531','胶南市','224','0','3',NULL), ('2532','胶州市','224','0','3',NULL), ('2533','莱西市','224','0','3',NULL), ('2534','黄岛区','224','0','3',NULL), ('2535','临淄区','225','0','3',NULL), ('2536','博山区','225','0','3',NULL), ('2537','周村区','225','0','3',NULL), ('2538','张店区','225','0','3',NULL), ('2539','桓台县','225','0','3',NULL), ('2540','沂源县','225','0','3',NULL), ('2541','淄川区','225','0','3',NULL), ('2542','高青县','225','0','3',NULL), ('2543','台儿庄区','226','0','3',NULL), ('2544','山亭区','226','0','3',NULL), ('2545','峄城区','226','0','3',NULL), ('2546','市中区','226','0','3',NULL), ('2547','滕州市','226','0','3',NULL), ('2548','薛城区','226','0','3',NULL), ('2549','东营区','227','0','3',NULL), ('2550','利津县','227','0','3',NULL), ('2551','垦利县','227','0','3',NULL), ('2552','广饶县','227','0','3',NULL), ('2553','河口区','227','0','3',NULL), ('2554','招远市','228','0','3',NULL), ('2555','栖霞市','228','0','3',NULL), ('2556','海阳市','228','0','3',NULL), ('2557','牟平区','228','0','3',NULL), ('2558','福山区','228','0','3',NULL), ('2559','芝罘区','228','0','3',NULL), ('2560','莱山区','228','0','3',NULL), ('2561','莱州市','228','0','3',NULL), ('2562','莱阳市','228','0','3',NULL), ('2563','蓬莱市','228','0','3',NULL), ('2564','长岛县','228','0','3',NULL), ('2565','龙口市','228','0','3',NULL), ('2566','临朐县','229','0','3',NULL), ('2567','坊子区','229','0','3',NULL), ('2568','奎文区','229','0','3',NULL), ('2569','安丘市','229','0','3',NULL), ('2570','寒亭区','229','0','3',NULL), ('2571','寿光市','229','0','3',NULL), ('2572','昌乐县','229','0','3',NULL), ('2573','昌邑市','229','0','3',NULL), ('2574','潍城区','229','0','3',NULL), ('2575','诸城市','229','0','3',NULL);
INSERT INTO `#__area` VALUES ('2576','青州市','229','0','3',NULL), ('2577','高密市','229','0','3',NULL), ('2578','任城区','230','0','3',NULL), ('2579','兖州市','230','0','3',NULL), ('2580','嘉祥县','230','0','3',NULL), ('2581','市中区','230','0','3',NULL), ('2582','微山县','230','0','3',NULL), ('2583','曲阜市','230','0','3',NULL), ('2584','梁山县','230','0','3',NULL), ('2585','汶上县','230','0','3',NULL), ('2586','泗水县','230','0','3',NULL), ('2587','邹城市','230','0','3',NULL), ('2588','金乡县','230','0','3',NULL), ('2589','鱼台县','230','0','3',NULL), ('2590','东平县','231','0','3',NULL), ('2591','宁阳县','231','0','3',NULL), ('2592','岱岳区','231','0','3',NULL), ('2593','新泰市','231','0','3',NULL), ('2594','泰山区','231','0','3',NULL), ('2595','肥城市','231','0','3',NULL), ('2596','乳山市','232','0','3',NULL), ('2597','文登市','232','0','3',NULL), ('2598','环翠区','232','0','3',NULL), ('2599','荣成市','232','0','3',NULL), ('2600','东港区','233','0','3',NULL), ('2601','五莲县','233','0','3',NULL), ('2602','岚山区','233','0','3',NULL), ('2603','莒县','233','0','3',NULL), ('2604','莱城区','234','0','3',NULL), ('2605','钢城区','234','0','3',NULL), ('2606','临沭县','235','0','3',NULL), ('2607','兰山区','235','0','3',NULL), ('2608','平邑县','235','0','3',NULL), ('2609','沂南县','235','0','3',NULL), ('2610','沂水县','235','0','3',NULL), ('2611','河东区','235','0','3',NULL), ('2612','罗庄区','235','0','3',NULL), ('2613','苍山县','235','0','3',NULL), ('2614','莒南县','235','0','3',NULL), ('2615','蒙阴县','235','0','3',NULL), ('2616','费县','235','0','3',NULL), ('2617','郯城县','235','0','3',NULL), ('2618','临邑县','236','0','3',NULL), ('2619','乐陵市','236','0','3',NULL), ('2620','夏津县','236','0','3',NULL), ('2621','宁津县','236','0','3',NULL), ('2622','平原县','236','0','3',NULL), ('2623','庆云县','236','0','3',NULL), ('2624','德城区','236','0','3',NULL), ('2625','武城县','236','0','3',NULL), ('2626','禹城市','236','0','3',NULL), ('2627','陵县','236','0','3',NULL), ('2628','齐河县','236','0','3',NULL), ('2629','东昌府区','237','0','3',NULL), ('2630','东阿县','237','0','3',NULL), ('2631','临清市','237','0','3',NULL), ('2632','冠县','237','0','3',NULL), ('2633','茌平县','237','0','3',NULL), ('2634','莘县','237','0','3',NULL), ('2635','阳谷县','237','0','3',NULL), ('2636','高唐县','237','0','3',NULL), ('2637','博兴县','238','0','3',NULL), ('2638','惠民县','238','0','3',NULL), ('2639','无棣县','238','0','3',NULL), ('2640','沾化县','238','0','3',NULL), ('2641','滨城区','238','0','3',NULL), ('2642','邹平县','238','0','3',NULL), ('2643','阳信县','238','0','3',NULL), ('2644','东明县','239','0','3',NULL), ('2645','单县','239','0','3',NULL), ('2646','定陶县','239','0','3',NULL), ('2647','巨野县','239','0','3',NULL), ('2648','成武县','239','0','3',NULL), ('2649','曹县','239','0','3',NULL), ('2650','牡丹区','239','0','3',NULL), ('2651','郓城县','239','0','3',NULL), ('2652','鄄城县','239','0','3',NULL), ('2653','上街区','240','0','3',NULL), ('2654','中原区','240','0','3',NULL), ('2655','中牟县','240','0','3',NULL), ('2656','二七区','240','0','3',NULL), ('2657','巩义市','240','0','3',NULL), ('2658','惠济区','240','0','3',NULL), ('2659','新密市','240','0','3',NULL), ('2660','新郑市','240','0','3',NULL), ('2661','登封市','240','0','3',NULL), ('2662','管城回族区','240','0','3',NULL), ('2663','荥阳市','240','0','3',NULL), ('2664','金水区','240','0','3',NULL), ('2665','兰考县','241','0','3',NULL), ('2666','尉氏县','241','0','3',NULL), ('2667','开封县','241','0','3',NULL), ('2668','杞县','241','0','3',NULL), ('2669','禹王台区','241','0','3',NULL), ('2670','通许县','241','0','3',NULL), ('2671','金明区','241','0','3',NULL), ('2672','顺河回族区','241','0','3',NULL), ('2673','鼓楼区','241','0','3',NULL), ('2674','龙亭区','241','0','3',NULL), ('2675','伊川县','242','0','3',NULL);
INSERT INTO `#__area` VALUES ('2676','偃师市','242','0','3',NULL), ('2677','吉利区','242','0','3',NULL), ('2678','孟津县','242','0','3',NULL), ('2679','宜阳县','242','0','3',NULL), ('2680','嵩县','242','0','3',NULL), ('2681','新安县','242','0','3',NULL), ('2682','栾川县','242','0','3',NULL), ('2683','汝阳县','242','0','3',NULL), ('2684','洛宁县','242','0','3',NULL), ('2685','洛龙区','242','0','3',NULL), ('2686','涧西区','242','0','3',NULL), ('2687','瀍河回族区','242','0','3',NULL), ('2688','老城区','242','0','3',NULL), ('2689','西工区','242','0','3',NULL), ('2690','卫东区','243','0','3',NULL), ('2691','叶县','243','0','3',NULL), ('2692','宝丰县','243','0','3',NULL), ('2693','新华区','243','0','3',NULL), ('2694','汝州市','243','0','3',NULL), ('2695','湛河区','243','0','3',NULL), ('2696','石龙区','243','0','3',NULL), ('2697','舞钢市','243','0','3',NULL), ('2698','郏县','243','0','3',NULL), ('2699','鲁山县','243','0','3',NULL), ('2700','内黄县','244','0','3',NULL), ('2701','北关区','244','0','3',NULL), ('2702','安阳县','244','0','3',NULL), ('2703','文峰区','244','0','3',NULL), ('2704','林州市','244','0','3',NULL), ('2705','殷都区','244','0','3',NULL), ('2706','汤阴县','244','0','3',NULL), ('2707','滑县','244','0','3',NULL), ('2708','龙安区','244','0','3',NULL), ('2709','山城区','245','0','3',NULL), ('2710','浚县','245','0','3',NULL), ('2711','淇县','245','0','3',NULL), ('2712','淇滨区','245','0','3',NULL), ('2713','鹤山区','245','0','3',NULL), ('2714','凤泉区','246','0','3',NULL), ('2715','卫滨区','246','0','3',NULL), ('2716','卫辉市','246','0','3',NULL), ('2717','原阳县','246','0','3',NULL), ('2718','封丘县','246','0','3',NULL), ('2719','延津县','246','0','3',NULL), ('2720','新乡县','246','0','3',NULL), ('2721','牧野区','246','0','3',NULL), ('2722','红旗区','246','0','3',NULL), ('2723','获嘉县','246','0','3',NULL), ('2724','辉县市','246','0','3',NULL), ('2725','长垣县','246','0','3',NULL), ('2726','中站区','247','0','3',NULL), ('2727','修武县','247','0','3',NULL), ('2728','博爱县','247','0','3',NULL), ('2729','孟州市','247','0','3',NULL), ('2730','山阳区','247','0','3',NULL), ('2731','武陟县','247','0','3',NULL), ('2732','沁阳市','247','0','3',NULL), ('2733','温县','247','0','3',NULL), ('2734','解放区','247','0','3',NULL), ('2735','马村区','247','0','3',NULL), ('2736','华龙区','248','0','3',NULL), ('2737','南乐县','248','0','3',NULL), ('2738','台前县','248','0','3',NULL), ('2739','清丰县','248','0','3',NULL), ('2740','濮阳县','248','0','3',NULL), ('2741','范县','248','0','3',NULL), ('2742','禹州市','249','0','3',NULL), ('2743','襄城县','249','0','3',NULL), ('2744','许昌县','249','0','3',NULL), ('2745','鄢陵县','249','0','3',NULL), ('2746','长葛市','249','0','3',NULL), ('2747','魏都区','249','0','3',NULL), ('2748','临颍县','250','0','3',NULL), ('2749','召陵区','250','0','3',NULL), ('2750','源汇区','250','0','3',NULL), ('2751','舞阳县','250','0','3',NULL), ('2752','郾城区','250','0','3',NULL), ('2753','义马市','251','0','3',NULL), ('2754','卢氏县','251','0','3',NULL), ('2755','渑池县','251','0','3',NULL), ('2756','湖滨区','251','0','3',NULL), ('2757','灵宝市','251','0','3',NULL), ('2758','陕县','251','0','3',NULL), ('2759','内乡县','252','0','3',NULL), ('2760','南召县','252','0','3',NULL), ('2761','卧龙区','252','0','3',NULL), ('2762','唐河县','252','0','3',NULL), ('2763','宛城区','252','0','3',NULL), ('2764','新野县','252','0','3',NULL), ('2765','方城县','252','0','3',NULL), ('2766','桐柏县','252','0','3',NULL), ('2767','淅川县','252','0','3',NULL), ('2768','社旗县','252','0','3',NULL), ('2769','西峡县','252','0','3',NULL), ('2770','邓州市','252','0','3',NULL), ('2771','镇平县','252','0','3',NULL), ('2772','夏邑县','253','0','3',NULL), ('2773','宁陵县','253','0','3',NULL), ('2774','柘城县','253','0','3',NULL), ('2775','民权县','253','0','3',NULL);
INSERT INTO `#__area` VALUES ('2776','永城市','253','0','3',NULL), ('2777','睢县','253','0','3',NULL), ('2778','睢阳区','253','0','3',NULL), ('2779','粱园区','253','0','3',NULL), ('2780','虞城县','253','0','3',NULL), ('2781','光山县','254','0','3',NULL), ('2782','商城县','254','0','3',NULL), ('2783','固始县','254','0','3',NULL), ('2784','平桥区','254','0','3',NULL), ('2785','息县','254','0','3',NULL), ('2786','新县','254','0','3',NULL), ('2787','浉河区','254','0','3',NULL), ('2788','淮滨县','254','0','3',NULL), ('2789','潢川县','254','0','3',NULL), ('2790','罗山县','254','0','3',NULL), ('2791','商水县','255','0','3',NULL), ('2792','太康县','255','0','3',NULL), ('2793','川汇区','255','0','3',NULL), ('2794','扶沟县','255','0','3',NULL), ('2795','沈丘县','255','0','3',NULL), ('2796','淮阳县','255','0','3',NULL), ('2797','西华县','255','0','3',NULL), ('2798','郸城县','255','0','3',NULL), ('2799','项城市','255','0','3',NULL), ('2800','鹿邑县','255','0','3',NULL), ('2801','上蔡县','256','0','3',NULL), ('2802','平舆县','256','0','3',NULL), ('2803','新蔡县','256','0','3',NULL), ('2804','正阳县','256','0','3',NULL), ('2805','汝南县','256','0','3',NULL), ('2806','泌阳县','256','0','3',NULL), ('2807','确山县','256','0','3',NULL), ('2808','西平县','256','0','3',NULL), ('2809','遂平县','256','0','3',NULL), ('2810','驿城区','256','0','3',NULL), ('2811','济源市','257','0','3',NULL), ('2812','东西湖区','258','0','3',NULL), ('2813','新洲区','258','0','3',NULL), ('2814','武昌区','258','0','3',NULL), ('2815','汉南区','258','0','3',NULL), ('2816','汉阳区','258','0','3',NULL), ('2817','江夏区','258','0','3',NULL), ('2818','江岸区','258','0','3',NULL), ('2819','江汉区','258','0','3',NULL), ('2820','洪山区','258','0','3',NULL), ('2821','硚口区','258','0','3',NULL), ('2822','蔡甸区','258','0','3',NULL), ('2823','青山区','258','0','3',NULL), ('2824','黄陂区','258','0','3',NULL), ('2825','下陆区','259','0','3',NULL), ('2826','大冶市','259','0','3',NULL), ('2827','西塞山区','259','0','3',NULL), ('2828','铁山区','259','0','3',NULL), ('2829','阳新县','259','0','3',NULL), ('2830','黄石港区','259','0','3',NULL), ('2831','丹江口市','260','0','3',NULL), ('2832','张湾区','260','0','3',NULL), ('2833','房县','260','0','3',NULL), ('2834','竹山县','260','0','3',NULL), ('2835','竹溪县','260','0','3',NULL), ('2836','茅箭区','260','0','3',NULL), ('2837','郧县','260','0','3',NULL), ('2838','郧西县','260','0','3',NULL), ('2839','五峰土家族自治县','261','0','3',NULL), ('2840','伍家岗区','261','0','3',NULL), ('2841','兴山县','261','0','3',NULL), ('2842','夷陵区','261','0','3',NULL), ('2843','宜都市','261','0','3',NULL), ('2844','当阳市','261','0','3',NULL), ('2845','枝江市','261','0','3',NULL), ('2846','点军区','261','0','3',NULL), ('2847','秭归县','261','0','3',NULL), ('2848','虢亭区','261','0','3',NULL), ('2849','西陵区','261','0','3',NULL), ('2850','远安县','261','0','3',NULL), ('2851','长阳土家族自治县','261','0','3',NULL), ('2852','保康县','262','0','3',NULL), ('2853','南漳县','262','0','3',NULL), ('2854','宜城市','262','0','3',NULL), ('2855','枣阳市','262','0','3',NULL), ('2856','樊城区','262','0','3',NULL), ('2857','老河口市','262','0','3',NULL), ('2858','襄城区','262','0','3',NULL), ('2859','襄阳区','262','0','3',NULL), ('2860','谷城县','262','0','3',NULL), ('2861','华容区','263','0','3',NULL), ('2862','粱子湖','263','0','3',NULL), ('2863','鄂城区','263','0','3',NULL), ('2864','东宝区','264','0','3',NULL), ('2865','京山县','264','0','3',NULL), ('2866','掇刀区','264','0','3',NULL), ('2867','沙洋县','264','0','3',NULL), ('2868','钟祥市','264','0','3',NULL), ('2869','云梦县','265','0','3',NULL), ('2870','大悟县','265','0','3',NULL), ('2871','孝南区','265','0','3',NULL), ('2872','孝昌县','265','0','3',NULL), ('2873','安陆市','265','0','3',NULL), ('2874','应城市','265','0','3',NULL), ('2875','汉川市','265','0','3',NULL);
INSERT INTO `#__area` VALUES ('2876','公安县','266','0','3',NULL), ('2877','松滋市','266','0','3',NULL), ('2878','江陵县','266','0','3',NULL), ('2879','沙市区','266','0','3',NULL), ('2880','洪湖市','266','0','3',NULL), ('2881','监利县','266','0','3',NULL), ('2882','石首市','266','0','3',NULL), ('2883','荆州区','266','0','3',NULL), ('2884','团风县','267','0','3',NULL), ('2885','武穴市','267','0','3',NULL), ('2886','浠水县','267','0','3',NULL), ('2887','红安县','267','0','3',NULL), ('2888','罗田县','267','0','3',NULL), ('2889','英山县','267','0','3',NULL), ('2890','蕲春县','267','0','3',NULL), ('2891','麻城市','267','0','3',NULL), ('2892','黄州区','267','0','3',NULL), ('2893','黄梅县','267','0','3',NULL), ('2894','咸安区','268','0','3',NULL), ('2895','嘉鱼县','268','0','3',NULL), ('2896','崇阳县','268','0','3',NULL), ('2897','赤壁市','268','0','3',NULL), ('2898','通城县','268','0','3',NULL), ('2899','通山县','268','0','3',NULL), ('2900','广水市','269','0','3',NULL), ('2901','曾都区','269','0','3',NULL), ('2902','利川市','270','0','3',NULL), ('2903','咸丰县','270','0','3',NULL), ('2904','宣恩县','270','0','3',NULL), ('2905','巴东县','270','0','3',NULL), ('2906','建始县','270','0','3',NULL), ('2907','恩施市','270','0','3',NULL), ('2908','来凤县','270','0','3',NULL), ('2909','鹤峰县','270','0','3',NULL), ('2910','仙桃市','271','0','3',NULL), ('2911','潜江市','272','0','3',NULL), ('2912','天门市','273','0','3',NULL), ('2913','神农架林区','274','0','3',NULL), ('2914','天心区','275','0','3',NULL), ('2915','宁乡县','275','0','3',NULL), ('2916','岳麓区','275','0','3',NULL), ('2917','开福区','275','0','3',NULL), ('2918','望城县','275','0','3',NULL), ('2919','浏阳市','275','0','3',NULL), ('2920','芙蓉区','275','0','3',NULL), ('2921','长沙县','275','0','3',NULL), ('2922','雨花区','275','0','3',NULL), ('2923','天元区','276','0','3',NULL), ('2924','攸县','276','0','3',NULL), ('2925','株洲县','276','0','3',NULL), ('2926','炎陵县','276','0','3',NULL), ('2927','石峰区','276','0','3',NULL), ('2928','芦淞区','276','0','3',NULL), ('2929','茶陵县','276','0','3',NULL), ('2930','荷塘区','276','0','3',NULL), ('2931','醴陵市','276','0','3',NULL), ('2932','岳塘区','277','0','3',NULL), ('2933','湘乡市','277','0','3',NULL), ('2934','湘潭县','277','0','3',NULL), ('2935','雨湖区','277','0','3',NULL), ('2936','韶山市','277','0','3',NULL), ('2937','南岳区','278','0','3',NULL), ('2938','常宁市','278','0','3',NULL), ('2939','珠晖区','278','0','3',NULL), ('2940','石鼓区','278','0','3',NULL), ('2941','祁东县','278','0','3',NULL), ('2942','耒阳市','278','0','3',NULL), ('2943','蒸湘区','278','0','3',NULL), ('2944','衡东县','278','0','3',NULL), ('2945','衡南县','278','0','3',NULL), ('2946','衡山县','278','0','3',NULL), ('2947','衡阳县','278','0','3',NULL), ('2948','雁峰区','278','0','3',NULL), ('2949','北塔区','279','0','3',NULL), ('2950','双清区','279','0','3',NULL), ('2951','城步苗族自治县','279','0','3',NULL), ('2952','大祥区','279','0','3',NULL), ('2953','新宁县','279','0','3',NULL), ('2954','新邵县','279','0','3',NULL), ('2955','武冈市','279','0','3',NULL), ('2956','洞口县','279','0','3',NULL), ('2957','绥宁县','279','0','3',NULL), ('2958','邵东县','279','0','3',NULL), ('2959','邵阳县','279','0','3',NULL), ('2960','隆回县','279','0','3',NULL), ('2961','临湘市','280','0','3',NULL), ('2962','云溪区','280','0','3',NULL), ('2963','华容县','280','0','3',NULL), ('2964','君山区','280','0','3',NULL), ('2965','岳阳县','280','0','3',NULL), ('2966','岳阳楼区','280','0','3',NULL), ('2967','平江县','280','0','3',NULL), ('2968','汨罗市','280','0','3',NULL), ('2969','湘阴县','280','0','3',NULL), ('2970','临澧县','281','0','3',NULL), ('2971','安乡县','281','0','3',NULL), ('2972','桃源县','281','0','3',NULL), ('2973','武陵区','281','0','3',NULL), ('2974','汉寿县','281','0','3',NULL), ('2975','津市市','281','0','3',NULL);
INSERT INTO `#__area` VALUES ('2976','澧县','281','0','3',NULL), ('2977','石门县','281','0','3',NULL), ('2978','鼎城区','281','0','3',NULL), ('2979','慈利县','282','0','3',NULL), ('2980','桑植县','282','0','3',NULL), ('2981','武陵源区','282','0','3',NULL), ('2982','永定区','282','0','3',NULL), ('2983','南县','283','0','3',NULL), ('2984','安化县','283','0','3',NULL), ('2985','桃江县','283','0','3',NULL), ('2986','沅江市','283','0','3',NULL), ('2987','资阳区','283','0','3',NULL), ('2988','赫山区','283','0','3',NULL), ('2989','临武县','284','0','3',NULL), ('2990','北湖区','284','0','3',NULL), ('2991','嘉禾县','284','0','3',NULL), ('2992','安仁县','284','0','3',NULL), ('2993','宜章县','284','0','3',NULL), ('2994','桂东县','284','0','3',NULL), ('2995','桂阳县','284','0','3',NULL), ('2996','永兴县','284','0','3',NULL), ('2997','汝城县','284','0','3',NULL), ('2998','苏仙区','284','0','3',NULL), ('2999','资兴市','284','0','3',NULL), ('3000','东安县','285','0','3',NULL), ('3001','冷水滩区','285','0','3',NULL), ('3002','双牌县','285','0','3',NULL), ('3003','宁远县','285','0','3',NULL), ('3004','新田县','285','0','3',NULL), ('3005','江华瑶族自治县','285','0','3',NULL), ('3006','江永县','285','0','3',NULL), ('3007','祁阳县','285','0','3',NULL), ('3008','蓝山县','285','0','3',NULL), ('3009','道县','285','0','3',NULL), ('3010','零陵区','285','0','3',NULL), ('3011','中方县','286','0','3',NULL), ('3012','会同县','286','0','3',NULL), ('3013','新晃侗族自治县','286','0','3',NULL), ('3014','沅陵县','286','0','3',NULL), ('3015','洪江市/洪江区','286','0','3',NULL), ('3016','溆浦县','286','0','3',NULL), ('3017','芷江侗族自治县','286','0','3',NULL), ('3018','辰溪县','286','0','3',NULL), ('3019','通道侗族自治县','286','0','3',NULL), ('3020','靖州苗族侗族自治县','286','0','3',NULL), ('3021','鹤城区','286','0','3',NULL), ('3022','麻阳苗族自治县','286','0','3',NULL), ('3023','冷水江市','287','0','3',NULL), ('3024','双峰县','287','0','3',NULL), ('3025','娄星区','287','0','3',NULL), ('3026','新化县','287','0','3',NULL), ('3027','涟源市','287','0','3',NULL), ('3028','保靖县','288','0','3',NULL), ('3029','凤凰县','288','0','3',NULL), ('3030','古丈县','288','0','3',NULL), ('3031','吉首市','288','0','3',NULL), ('3032','永顺县','288','0','3',NULL), ('3033','泸溪县','288','0','3',NULL), ('3034','花垣县','288','0','3',NULL), ('3035','龙山县','288','0','3',NULL), ('3036','萝岗区','289','0','3',NULL), ('3037','南沙区','289','0','3',NULL), ('3038','从化市','289','0','3',NULL), ('3039','增城市','289','0','3',NULL), ('3040','天河区','289','0','3',NULL), ('3041','海珠区','289','0','3',NULL), ('3042','番禺区','289','0','3',NULL), ('3043','白云区','289','0','3',NULL), ('3044','花都区','289','0','3',NULL), ('3045','荔湾区','289','0','3',NULL), ('3046','越秀区','289','0','3',NULL), ('3047','黄埔区','289','0','3',NULL), ('3048','乐昌市','290','0','3',NULL), ('3049','乳源瑶族自治县','290','0','3',NULL), ('3050','仁化县','290','0','3',NULL), ('3051','南雄市','290','0','3',NULL), ('3052','始兴县','290','0','3',NULL), ('3053','新丰县','290','0','3',NULL), ('3054','曲江区','290','0','3',NULL), ('3055','武江区','290','0','3',NULL), ('3056','浈江区','290','0','3',NULL), ('3057','翁源县','290','0','3',NULL), ('3058','南山区','291','0','3',NULL), ('3059','宝安区','291','0','3',NULL), ('3060','盐田区','291','0','3',NULL), ('3061','福田区','291','0','3',NULL), ('3062','罗湖区','291','0','3',NULL), ('3063','龙岗区','291','0','3',NULL), ('3064','斗门区','292','0','3',NULL), ('3065','金湾区','292','0','3',NULL), ('3066','香洲区','292','0','3',NULL), ('3067','南澳县','293','0','3',NULL), ('3068','潮南区','293','0','3',NULL), ('3069','潮阳区','293','0','3',NULL), ('3070','澄海区','293','0','3',NULL), ('3071','濠江区','293','0','3',NULL), ('3072','金平区','293','0','3',NULL), ('3073','龙湖区','293','0','3',NULL), ('3074','三水区','294','0','3',NULL), ('3075','南海区','294','0','3',NULL);
INSERT INTO `#__area` VALUES ('3076','禅城区','294','0','3',NULL), ('3077','顺德区','294','0','3',NULL), ('3078','高明区','294','0','3',NULL), ('3079','台山市','295','0','3',NULL), ('3080','开平市','295','0','3',NULL), ('3081','恩平市','295','0','3',NULL), ('3082','新会区','295','0','3',NULL), ('3083','江海区','295','0','3',NULL), ('3084','蓬江区','295','0','3',NULL), ('3085','鹤山市','295','0','3',NULL), ('3086','吴川市','296','0','3',NULL), ('3087','坡头区','296','0','3',NULL), ('3088','廉江市','296','0','3',NULL), ('3089','徐闻县','296','0','3',NULL), ('3090','赤坎区','296','0','3',NULL), ('3091','遂溪县','296','0','3',NULL), ('3092','雷州市','296','0','3',NULL), ('3093','霞山区','296','0','3',NULL), ('3094','麻章区','296','0','3',NULL), ('3095','信宜市','297','0','3',NULL), ('3096','化州市','297','0','3',NULL), ('3097','电白县','297','0','3',NULL), ('3098','茂南区','297','0','3',NULL), ('3099','茂港区','297','0','3',NULL), ('3100','高州市','297','0','3',NULL), ('3101','四会市','298','0','3',NULL), ('3102','封开县','298','0','3',NULL), ('3103','广宁县','298','0','3',NULL), ('3104','德庆县','298','0','3',NULL), ('3105','怀集县','298','0','3',NULL), ('3106','端州区','298','0','3',NULL), ('3107','高要市','298','0','3',NULL), ('3108','鼎湖区','298','0','3',NULL), ('3109','博罗县','299','0','3',NULL), ('3110','惠东县','299','0','3',NULL), ('3111','惠城区','299','0','3',NULL), ('3112','惠阳区','299','0','3',NULL), ('3113','龙门县','299','0','3',NULL), ('3114','丰顺县','300','0','3',NULL), ('3115','五华县','300','0','3',NULL), ('3116','兴宁市','300','0','3',NULL), ('3117','大埔县','300','0','3',NULL), ('3118','平远县','300','0','3',NULL), ('3119','梅县','300','0','3',NULL), ('3120','梅江区','300','0','3',NULL), ('3121','蕉岭县','300','0','3',NULL), ('3122','城区','301','0','3',NULL), ('3123','海丰县','301','0','3',NULL), ('3124','陆丰市','301','0','3',NULL), ('3125','陆河县','301','0','3',NULL), ('3126','东源县','302','0','3',NULL), ('3127','和平县','302','0','3',NULL), ('3128','源城区','302','0','3',NULL), ('3129','紫金县','302','0','3',NULL), ('3130','连平县','302','0','3',NULL), ('3131','龙川县','302','0','3',NULL), ('3132','江城区','303','0','3',NULL), ('3133','阳东县','303','0','3',NULL), ('3134','阳春市','303','0','3',NULL), ('3135','阳西县','303','0','3',NULL), ('3136','佛冈县','304','0','3',NULL), ('3137','清城区','304','0','3',NULL), ('3138','清新县','304','0','3',NULL), ('3139','英德市','304','0','3',NULL), ('3140','连南瑶族自治县','304','0','3',NULL), ('3141','连山壮族瑶族自治县','304','0','3',NULL), ('3142','连州市','304','0','3',NULL), ('3143','阳山县','304','0','3',NULL), ('3144','东莞市','305','0','3',NULL), ('3145','中山市','306','0','3',NULL), ('3146','湘桥区','307','0','3',NULL), ('3147','潮安县','307','0','3',NULL), ('3148','饶平县','307','0','3',NULL), ('3149','惠来县','308','0','3',NULL), ('3150','揭东县','308','0','3',NULL), ('3151','揭西县','308','0','3',NULL), ('3152','普宁市','308','0','3',NULL), ('3153','榕城区','308','0','3',NULL), ('3154','云城区','309','0','3',NULL), ('3155','云安县','309','0','3',NULL), ('3156','新兴县','309','0','3',NULL), ('3157','罗定市','309','0','3',NULL), ('3158','郁南县','309','0','3',NULL), ('3159','上林县','310','0','3',NULL), ('3160','兴宁区','310','0','3',NULL), ('3161','宾阳县','310','0','3',NULL), ('3162','横县','310','0','3',NULL), ('3163','武鸣县','310','0','3',NULL), ('3164','江南区','310','0','3',NULL), ('3165','良庆区','310','0','3',NULL), ('3166','西乡塘区','310','0','3',NULL), ('3167','邕宁区','310','0','3',NULL), ('3168','隆安县','310','0','3',NULL), ('3169','青秀区','310','0','3',NULL), ('3170','马山县','310','0','3',NULL), ('3171','三江侗族自治县','311','0','3',NULL), ('3172','城中区','311','0','3',NULL), ('3173','柳北区','311','0','3',NULL), ('3174','柳南区','311','0','3',NULL), ('3175','柳城县','311','0','3',NULL);
INSERT INTO `#__area` VALUES ('3176','柳江县','311','0','3',NULL), ('3177','融安县','311','0','3',NULL), ('3178','融水苗族自治县','311','0','3',NULL), ('3179','鱼峰区','311','0','3',NULL), ('3180','鹿寨县','311','0','3',NULL), ('3181','七星区','312','0','3',NULL), ('3182','临桂县','312','0','3',NULL), ('3183','全州县','312','0','3',NULL), ('3184','兴安县','312','0','3',NULL), ('3185','叠彩区','312','0','3',NULL), ('3186','平乐县','312','0','3',NULL), ('3187','恭城瑶族自治县','312','0','3',NULL), ('3188','永福县','312','0','3',NULL), ('3189','灌阳县','312','0','3',NULL), ('3190','灵川县','312','0','3',NULL), ('3191','秀峰区','312','0','3',NULL), ('3192','荔浦县','312','0','3',NULL), ('3193','象山区','312','0','3',NULL), ('3194','资源县','312','0','3',NULL), ('3195','阳朔县','312','0','3',NULL), ('3196','雁山区','312','0','3',NULL), ('3197','龙胜各族自治县','312','0','3',NULL), ('3198','万秀区','313','0','3',NULL), ('3199','岑溪市','313','0','3',NULL), ('3200','苍梧县','313','0','3',NULL), ('3201','蒙山县','313','0','3',NULL), ('3202','藤县','313','0','3',NULL), ('3203','蝶山区','313','0','3',NULL), ('3204','长洲区','313','0','3',NULL), ('3205','合浦县','314','0','3',NULL), ('3206','海城区','314','0','3',NULL), ('3207','铁山港区','314','0','3',NULL), ('3208','银海区','314','0','3',NULL), ('3209','上思县','315','0','3',NULL), ('3210','东兴市','315','0','3',NULL), ('3211','港口区','315','0','3',NULL), ('3212','防城区','315','0','3',NULL), ('3213','浦北县','316','0','3',NULL), ('3214','灵山县','316','0','3',NULL), ('3215','钦北区','316','0','3',NULL), ('3216','钦南区','316','0','3',NULL), ('3217','平南县','317','0','3',NULL), ('3218','桂平市','317','0','3',NULL), ('3219','港北区','317','0','3',NULL), ('3220','港南区','317','0','3',NULL), ('3221','覃塘区','317','0','3',NULL), ('3222','兴业县','318','0','3',NULL), ('3223','北流市','318','0','3',NULL), ('3224','博白县','318','0','3',NULL), ('3225','容县','318','0','3',NULL), ('3226','玉州区','318','0','3',NULL), ('3227','陆川县','318','0','3',NULL), ('3228','乐业县','319','0','3',NULL), ('3229','凌云县','319','0','3',NULL), ('3230','右江区','319','0','3',NULL), ('3231','平果县','319','0','3',NULL), ('3232','德保县','319','0','3',NULL), ('3233','田东县','319','0','3',NULL), ('3234','田林县','319','0','3',NULL), ('3235','田阳县','319','0','3',NULL), ('3236','西林县','319','0','3',NULL), ('3237','那坡县','319','0','3',NULL), ('3238','隆林各族自治县','319','0','3',NULL), ('3239','靖西县','319','0','3',NULL), ('3240','八步区','320','0','3',NULL), ('3241','富川瑶族自治县','320','0','3',NULL), ('3242','昭平县','320','0','3',NULL), ('3243','钟山县','320','0','3',NULL), ('3244','东兰县','321','0','3',NULL), ('3245','凤山县','321','0','3',NULL), ('3246','南丹县','321','0','3',NULL), ('3247','大化瑶族自治县','321','0','3',NULL), ('3248','天峨县','321','0','3',NULL), ('3249','宜州市','321','0','3',NULL), ('3250','巴马瑶族自治县','321','0','3',NULL), ('3251','环江毛南族自治县','321','0','3',NULL), ('3252','罗城仫佬族自治县','321','0','3',NULL), ('3253','都安瑶族自治县','321','0','3',NULL), ('3254','金城江区','321','0','3',NULL), ('3255','兴宾区','322','0','3',NULL), ('3256','合山市','322','0','3',NULL), ('3257','忻城县','322','0','3',NULL), ('3258','武宣县','322','0','3',NULL), ('3259','象州县','322','0','3',NULL), ('3260','金秀瑶族自治县','322','0','3',NULL), ('3261','凭祥市','323','0','3',NULL), ('3262','大新县','323','0','3',NULL), ('3263','天等县','323','0','3',NULL), ('3264','宁明县','323','0','3',NULL), ('3265','扶绥县','323','0','3',NULL), ('3266','江州区','323','0','3',NULL), ('3267','龙州县','323','0','3',NULL), ('3268','琼山区','324','0','3',NULL), ('3269','秀英区','324','0','3',NULL), ('3270','美兰区','324','0','3',NULL), ('3271','龙华区','324','0','3',NULL), ('3272','三亚市','325','0','3',NULL), ('3273','五指山市','326','0','3',NULL), ('3274','琼海市','327','0','3',NULL), ('3275','儋州市','328','0','3',NULL);
INSERT INTO `#__area` VALUES ('3276','文昌市','329','0','3',NULL), ('3277','万宁市','330','0','3',NULL), ('3278','东方市','331','0','3',NULL), ('3279','定安县','332','0','3',NULL), ('3280','屯昌县','333','0','3',NULL), ('3281','澄迈县','334','0','3',NULL), ('3282','临高县','335','0','3',NULL), ('3283','白沙黎族自治县','336','0','3',NULL), ('3284','昌江黎族自治县','337','0','3',NULL), ('3285','乐东黎族自治县','338','0','3',NULL), ('3286','陵水黎族自治县','339','0','3',NULL), ('3287','保亭黎族苗族自治县','340','0','3',NULL), ('3288','琼中黎族苗族自治县','341','0','3',NULL), ('4209','双流县','385','0','3',NULL), ('4210','大邑县','385','0','3',NULL), ('4211','崇州市','385','0','3',NULL), ('4212','彭州市','385','0','3',NULL), ('4213','成华区','385','0','3',NULL), ('4214','新津县','385','0','3',NULL), ('4215','新都区','385','0','3',NULL), ('4216','武侯区','385','0','3',NULL), ('4217','温江区','385','0','3',NULL), ('4218','蒲江县','385','0','3',NULL), ('4219','邛崃市','385','0','3',NULL), ('4220','郫县','385','0','3',NULL), ('4221','都江堰市','385','0','3',NULL), ('4222','金堂县','385','0','3',NULL), ('4223','金牛区','385','0','3',NULL), ('4224','锦江区','385','0','3',NULL), ('4225','青白江区','385','0','3',NULL), ('4226','青羊区','385','0','3',NULL), ('4227','龙泉驿区','385','0','3',NULL), ('4228','大安区','386','0','3',NULL), ('4229','富顺县','386','0','3',NULL), ('4230','沿滩区','386','0','3',NULL), ('4231','自流井区','386','0','3',NULL), ('4232','荣县','386','0','3',NULL), ('4233','贡井区','386','0','3',NULL), ('4234','东区','387','0','3',NULL), ('4235','仁和区','387','0','3',NULL), ('4236','盐边县','387','0','3',NULL), ('4237','米易县','387','0','3',NULL), ('4238','西区','387','0','3',NULL), ('4239','叙永县','388','0','3',NULL), ('4240','古蔺县','388','0','3',NULL), ('4241','合江县','388','0','3',NULL), ('4242','江阳区','388','0','3',NULL), ('4243','泸县','388','0','3',NULL), ('4244','纳溪区','388','0','3',NULL), ('4245','龙马潭区','388','0','3',NULL), ('4246','中江县','389','0','3',NULL), ('4247','什邡市','389','0','3',NULL), ('4248','广汉市','389','0','3',NULL), ('4249','旌阳区','389','0','3',NULL), ('4250','绵竹市','389','0','3',NULL), ('4251','罗江县','389','0','3',NULL), ('4252','三台县','390','0','3',NULL), ('4253','北川羌族自治县','390','0','3',NULL), ('4254','安县','390','0','3',NULL), ('4255','平武县','390','0','3',NULL), ('4256','梓潼县','390','0','3',NULL), ('4257','江油市','390','0','3',NULL), ('4258','涪城区','390','0','3',NULL), ('4259','游仙区','390','0','3',NULL), ('4260','盐亭县','390','0','3',NULL), ('4261','元坝区','391','0','3',NULL), ('4262','利州区','391','0','3',NULL), ('4263','剑阁县','391','0','3',NULL), ('4264','旺苍县','391','0','3',NULL), ('4265','朝天区','391','0','3',NULL), ('4266','苍溪县','391','0','3',NULL), ('4267','青川县','391','0','3',NULL), ('4268','大英县','392','0','3',NULL), ('4269','安居区','392','0','3',NULL), ('4270','射洪县','392','0','3',NULL), ('4271','船山区','392','0','3',NULL), ('4272','蓬溪县','392','0','3',NULL), ('4273','东兴区','393','0','3',NULL), ('4274','威远县','393','0','3',NULL), ('4275','市中区','393','0','3',NULL), ('4276','资中县','393','0','3',NULL), ('4277','隆昌县','393','0','3',NULL), ('4278','五通桥区','394','0','3',NULL), ('4279','井研县','394','0','3',NULL), ('4280','夹江县','394','0','3',NULL), ('4281','峨眉山市','394','0','3',NULL), ('4282','峨边彝族自治县','394','0','3',NULL), ('4283','市中区','394','0','3',NULL), ('4284','沐川县','394','0','3',NULL), ('4285','沙湾区','394','0','3',NULL), ('4286','犍为县','394','0','3',NULL), ('4287','金口河区','394','0','3',NULL), ('4288','马边彝族自治县','394','0','3',NULL), ('4289','仪陇县','395','0','3',NULL), ('4290','南充市嘉陵区','395','0','3',NULL), ('4291','南部县','395','0','3',NULL), ('4292','嘉陵区','395','0','3',NULL), ('4293','营山县','395','0','3',NULL), ('4294','蓬安县','395','0','3',NULL), ('4295','西充县','395','0','3',NULL);
INSERT INTO `#__area` VALUES ('4296','阆中市','395','0','3',NULL), ('4297','顺庆区','395','0','3',NULL), ('4298','高坪区','395','0','3',NULL), ('4299','东坡区','396','0','3',NULL), ('4300','丹棱县','396','0','3',NULL), ('4301','仁寿县','396','0','3',NULL), ('4302','彭山县','396','0','3',NULL), ('4303','洪雅县','396','0','3',NULL), ('4304','青神县','396','0','3',NULL), ('4305','兴文县','397','0','3',NULL), ('4306','南溪县','397','0','3',NULL), ('4307','宜宾县','397','0','3',NULL), ('4308','屏山县','397','0','3',NULL), ('4309','江安县','397','0','3',NULL), ('4310','珙县','397','0','3',NULL), ('4311','筠连县','397','0','3',NULL), ('4312','翠屏区','397','0','3',NULL), ('4313','长宁县','397','0','3',NULL), ('4314','高县','397','0','3',NULL), ('4315','华蓥市','398','0','3',NULL), ('4316','岳池县','398','0','3',NULL), ('4317','广安区','398','0','3',NULL), ('4318','武胜县','398','0','3',NULL), ('4319','邻水县','398','0','3',NULL), ('4320','万源市','399','0','3',NULL), ('4321','大竹县','399','0','3',NULL), ('4322','宣汉县','399','0','3',NULL), ('4323','开江县','399','0','3',NULL), ('4324','渠县','399','0','3',NULL), ('4325','达县','399','0','3',NULL), ('4326','通川区','399','0','3',NULL), ('4327','名山县','400','0','3',NULL), ('4328','天全县','400','0','3',NULL), ('4329','宝兴县','400','0','3',NULL), ('4330','汉源县','400','0','3',NULL), ('4331','石棉县','400','0','3',NULL), ('4332','芦山县','400','0','3',NULL), ('4333','荥经县','400','0','3',NULL), ('4334','雨城区','400','0','3',NULL), ('4335','南江县','401','0','3',NULL), ('4336','巴州区','401','0','3',NULL), ('4337','平昌县','401','0','3',NULL), ('4338','通江县','401','0','3',NULL), ('4339','乐至县','402','0','3',NULL), ('4340','安岳县','402','0','3',NULL), ('4341','简阳市','402','0','3',NULL), ('4342','雁江区','402','0','3',NULL), ('4343','九寨沟县','403','0','3',NULL), ('4344','壤塘县','403','0','3',NULL), ('4345','小金县','403','0','3',NULL), ('4346','松潘县','403','0','3',NULL), ('4347','汶川县','403','0','3',NULL), ('4348','理县','403','0','3',NULL), ('4349','红原县','403','0','3',NULL), ('4350','若尔盖县','403','0','3',NULL), ('4351','茂县','403','0','3',NULL), ('4352','金川县','403','0','3',NULL), ('4353','阿坝县','403','0','3',NULL), ('4354','马尔康县','403','0','3',NULL), ('4355','黑水县','403','0','3',NULL), ('4356','丹巴县','404','0','3',NULL), ('4357','乡城县','404','0','3',NULL), ('4358','巴塘县','404','0','3',NULL), ('4359','康定县','404','0','3',NULL), ('4360','得荣县','404','0','3',NULL), ('4361','德格县','404','0','3',NULL), ('4362','新龙县','404','0','3',NULL), ('4363','泸定县','404','0','3',NULL), ('4364','炉霍县','404','0','3',NULL), ('4365','理塘县','404','0','3',NULL), ('4366','甘孜县','404','0','3',NULL), ('4367','白玉县','404','0','3',NULL), ('4368','石渠县','404','0','3',NULL), ('4369','稻城县','404','0','3',NULL), ('4370','色达县','404','0','3',NULL), ('4371','道孚县','404','0','3',NULL), ('4372','雅江县','404','0','3',NULL), ('4373','会东县','405','0','3',NULL), ('4374','会理县','405','0','3',NULL), ('4375','冕宁县','405','0','3',NULL), ('4376','喜德县','405','0','3',NULL), ('4377','宁南县','405','0','3',NULL), ('4378','布拖县','405','0','3',NULL), ('4379','德昌县','405','0','3',NULL), ('4380','昭觉县','405','0','3',NULL), ('4381','普格县','405','0','3',NULL), ('4382','木里藏族自治县','405','0','3',NULL), ('4383','甘洛县','405','0','3',NULL), ('4384','盐源县','405','0','3',NULL), ('4385','美姑县','405','0','3',NULL), ('4386','西昌','405','0','3',NULL), ('4387','越西县','405','0','3',NULL), ('4388','金阳县','405','0','3',NULL), ('4389','雷波县','405','0','3',NULL), ('4390','乌当区','406','0','3',NULL), ('4391','云岩区','406','0','3',NULL), ('4392','修文县','406','0','3',NULL), ('4393','南明区','406','0','3',NULL), ('4394','小河区','406','0','3',NULL), ('4395','开阳县','406','0','3',NULL);
INSERT INTO `#__area` VALUES ('4396','息烽县','406','0','3',NULL), ('4397','清镇市','406','0','3',NULL), ('4398','白云区','406','0','3',NULL), ('4399','花溪区','406','0','3',NULL), ('4400','六枝特区','407','0','3',NULL), ('4401','水城县','407','0','3',NULL), ('4402','盘县','407','0','3',NULL), ('4403','钟山区','407','0','3',NULL), ('4404','习水县','408','0','3',NULL), ('4405','仁怀市','408','0','3',NULL), ('4406','余庆县','408','0','3',NULL), ('4407','凤冈县','408','0','3',NULL), ('4408','务川仡佬族苗族自治县','408','0','3',NULL), ('4409','桐梓县','408','0','3',NULL), ('4410','正安县','408','0','3',NULL), ('4411','汇川区','408','0','3',NULL), ('4412','湄潭县','408','0','3',NULL), ('4413','红花岗区','408','0','3',NULL), ('4414','绥阳县','408','0','3',NULL), ('4415','赤水市','408','0','3',NULL), ('4416','道真仡佬族苗族自治县','408','0','3',NULL), ('4417','遵义县','408','0','3',NULL), ('4418','关岭布依族苗族自治县','409','0','3',NULL), ('4419','平坝县','409','0','3',NULL), ('4420','普定县','409','0','3',NULL), ('4421','紫云苗族布依族自治县','409','0','3',NULL), ('4422','西秀区','409','0','3',NULL), ('4423','镇宁布依族苗族自治县','409','0','3',NULL), ('4424','万山特区','410','0','3',NULL), ('4425','印江土家族苗族自治县','410','0','3',NULL), ('4426','德江县','410','0','3',NULL), ('4427','思南县','410','0','3',NULL), ('4428','松桃苗族自治县','410','0','3',NULL), ('4429','江口县','410','0','3',NULL), ('4430','沿河土家族自治县','410','0','3',NULL), ('4431','玉屏侗族自治县','410','0','3',NULL), ('4432','石阡县','410','0','3',NULL), ('4433','铜仁市','410','0','3',NULL), ('4434','兴义市','411','0','3',NULL), ('4435','兴仁县','411','0','3',NULL), ('4436','册亨县','411','0','3',NULL), ('4437','安龙县','411','0','3',NULL), ('4438','普安县','411','0','3',NULL), ('4439','晴隆县','411','0','3',NULL), ('4440','望谟县','411','0','3',NULL), ('4441','贞丰县','411','0','3',NULL), ('4442','大方县','412','0','3',NULL), ('4443','威宁彝族回族苗族自治县','412','0','3',NULL), ('4444','毕节市','412','0','3',NULL), ('4445','纳雍县','412','0','3',NULL), ('4446','织金县','412','0','3',NULL), ('4447','赫章县','412','0','3',NULL), ('4448','金沙县','412','0','3',NULL), ('4449','黔西县','412','0','3',NULL), ('4450','三穗县','413','0','3',NULL), ('4451','丹寨县','413','0','3',NULL), ('4452','从江县','413','0','3',NULL), ('4453','凯里市','413','0','3',NULL), ('4454','剑河县','413','0','3',NULL), ('4455','台江县','413','0','3',NULL), ('4456','天柱县','413','0','3',NULL), ('4457','岑巩县','413','0','3',NULL), ('4458','施秉县','413','0','3',NULL), ('4459','榕江县','413','0','3',NULL), ('4460','锦屏县','413','0','3',NULL), ('4461','镇远县','413','0','3',NULL), ('4462','雷山县','413','0','3',NULL), ('4463','麻江县','413','0','3',NULL), ('4464','黄平县','413','0','3',NULL), ('4465','黎平县','413','0','3',NULL), ('4466','三都水族自治县','414','0','3',NULL), ('4467','平塘县','414','0','3',NULL), ('4468','惠水县','414','0','3',NULL), ('4469','独山县','414','0','3',NULL), ('4470','瓮安县','414','0','3',NULL), ('4471','福泉市','414','0','3',NULL), ('4472','罗甸县','414','0','3',NULL), ('4473','荔波县','414','0','3',NULL), ('4474','贵定县','414','0','3',NULL), ('4475','都匀市','414','0','3',NULL), ('4476','长顺县','414','0','3',NULL), ('4477','龙里县','414','0','3',NULL), ('4478','东川区','415','0','3',NULL), ('4479','五华区','415','0','3',NULL), ('4480','呈贡县','415','0','3',NULL), ('4481','安宁市','415','0','3',NULL), ('4482','官渡区','415','0','3',NULL), ('4483','宜良县','415','0','3',NULL), ('4484','富民县','415','0','3',NULL), ('4485','寻甸回族彝族自治县','415','0','3',NULL), ('4486','嵩明县','415','0','3',NULL), ('4487','晋宁县','415','0','3',NULL), ('4488','盘龙区','415','0','3',NULL), ('4489','石林彝族自治县','415','0','3',NULL), ('4490','禄劝彝族苗族自治县','415','0','3',NULL), ('4491','西山区','415','0','3',NULL), ('4492','会泽县','416','0','3',NULL), ('4493','宣威市','416','0','3',NULL), ('4494','富源县','416','0','3',NULL), ('4495','师宗县','416','0','3',NULL);
INSERT INTO `#__area` VALUES ('4496','沾益县','416','0','3',NULL), ('4497','罗平县','416','0','3',NULL), ('4498','陆良县','416','0','3',NULL), ('4499','马龙县','416','0','3',NULL), ('4500','麒麟区','416','0','3',NULL), ('4501','元江哈尼族彝族傣族自治县','417','0','3',NULL), ('4502','华宁县','417','0','3',NULL), ('4503','峨山彝族自治县','417','0','3',NULL), ('4504','新平彝族傣族自治县','417','0','3',NULL), ('4505','易门县','417','0','3',NULL), ('4506','江川县','417','0','3',NULL), ('4507','澄江县','417','0','3',NULL), ('4508','红塔区','417','0','3',NULL), ('4509','通海县','417','0','3',NULL), ('4510','施甸县','418','0','3',NULL), ('4511','昌宁县','418','0','3',NULL), ('4512','腾冲县','418','0','3',NULL), ('4513','隆阳区','418','0','3',NULL), ('4514','龙陵县','418','0','3',NULL), ('4515','大关县','419','0','3',NULL), ('4516','威信县','419','0','3',NULL), ('4517','巧家县','419','0','3',NULL), ('4518','彝良县','419','0','3',NULL), ('4519','昭阳区','419','0','3',NULL), ('4520','水富县','419','0','3',NULL), ('4521','永善县','419','0','3',NULL), ('4522','盐津县','419','0','3',NULL), ('4523','绥江县','419','0','3',NULL), ('4524','镇雄县','419','0','3',NULL), ('4525','鲁甸县','419','0','3',NULL), ('4526','华坪县','420','0','3',NULL), ('4527','古城区','420','0','3',NULL), ('4528','宁蒗彝族自治县','420','0','3',NULL), ('4529','永胜县','420','0','3',NULL), ('4530','玉龙纳西族自治县','420','0','3',NULL), ('4531','临翔区','422','0','3',NULL), ('4532','云县','422','0','3',NULL), ('4533','凤庆县','422','0','3',NULL), ('4534','双江拉祜族佤族布朗族傣族自治县','422','0','3',NULL), ('4535','永德县','422','0','3',NULL), ('4536','沧源佤族自治县','422','0','3',NULL), ('4537','耿马傣族佤族自治县','422','0','3',NULL), ('4538','镇康县','422','0','3',NULL), ('4539','元谋县','423','0','3',NULL), ('4540','南华县','423','0','3',NULL), ('4541','双柏县','423','0','3',NULL), ('4542','大姚县','423','0','3',NULL), ('4543','姚安县','423','0','3',NULL), ('4544','楚雄市','423','0','3',NULL), ('4545','武定县','423','0','3',NULL), ('4546','永仁县','423','0','3',NULL), ('4547','牟定县','423','0','3',NULL), ('4548','禄丰县','423','0','3',NULL), ('4549','个旧市','424','0','3',NULL), ('4550','元阳县','424','0','3',NULL), ('4551','屏边苗族自治县','424','0','3',NULL), ('4552','建水县','424','0','3',NULL), ('4553','开远市','424','0','3',NULL), ('4554','弥勒县','424','0','3',NULL), ('4555','河口瑶族自治县','424','0','3',NULL), ('4556','泸西县','424','0','3',NULL), ('4557','石屏县','424','0','3',NULL), ('4558','红河县','424','0','3',NULL), ('4559','绿春县','424','0','3',NULL), ('4560','蒙自县','424','0','3',NULL), ('4561','金平苗族瑶族傣族自治县','424','0','3',NULL), ('4562','丘北县','425','0','3',NULL), ('4563','富宁县','425','0','3',NULL), ('4564','广南县','425','0','3',NULL), ('4565','文山县','425','0','3',NULL), ('4566','砚山县','425','0','3',NULL), ('4567','西畴县','425','0','3',NULL), ('4568','马关县','425','0','3',NULL), ('4569','麻栗坡县','425','0','3',NULL), ('4570','勐海县','426','0','3',NULL), ('4571','勐腊县','426','0','3',NULL), ('4572','景洪市','426','0','3',NULL), ('4573','云龙县','427','0','3',NULL), ('4574','剑川县','427','0','3',NULL), ('4575','南涧彝族自治县','427','0','3',NULL), ('4576','大理市','427','0','3',NULL), ('4577','宾川县','427','0','3',NULL), ('4578','巍山彝族回族自治县','427','0','3',NULL), ('4579','弥渡县','427','0','3',NULL), ('4580','永平县','427','0','3',NULL), ('4581','洱源县','427','0','3',NULL), ('4582','漾濞彝族自治县','427','0','3',NULL), ('4583','祥云县','427','0','3',NULL), ('4584','鹤庆县','427','0','3',NULL), ('4585','梁河县','428','0','3',NULL), ('4586','潞西市','428','0','3',NULL), ('4587','瑞丽市','428','0','3',NULL), ('4588','盈江县','428','0','3',NULL), ('4589','陇川县','428','0','3',NULL), ('4590','德钦县','430','0','3',NULL), ('4591','维西傈僳族自治县','430','0','3',NULL), ('4592','香格里拉县','430','0','3',NULL), ('4593','城关区','431','0','3',NULL), ('4594','堆龙德庆县','431','0','3',NULL), ('4595','墨竹工卡县','431','0','3',NULL);
INSERT INTO `#__area` VALUES ('4596','尼木县','431','0','3',NULL), ('4597','当雄县','431','0','3',NULL), ('4598','曲水县','431','0','3',NULL), ('4599','林周县','431','0','3',NULL), ('4600','达孜县','431','0','3',NULL), ('4601','丁青县','432','0','3',NULL), ('4602','八宿县','432','0','3',NULL), ('4603','察雅县','432','0','3',NULL), ('4604','左贡县','432','0','3',NULL), ('4605','昌都县','432','0','3',NULL), ('4606','江达县','432','0','3',NULL), ('4607','洛隆县','432','0','3',NULL), ('4608','类乌齐县','432','0','3',NULL), ('4609','芒康县','432','0','3',NULL), ('4610','贡觉县','432','0','3',NULL), ('4611','边坝县','432','0','3',NULL), ('4612','乃东县','433','0','3',NULL), ('4613','加查县','433','0','3',NULL), ('4614','扎囊县','433','0','3',NULL), ('4615','措美县','433','0','3',NULL), ('4616','曲松县','433','0','3',NULL), ('4617','桑日县','433','0','3',NULL), ('4618','洛扎县','433','0','3',NULL), ('4619','浪卡子县','433','0','3',NULL), ('4620','琼结县','433','0','3',NULL), ('4621','贡嘎县','433','0','3',NULL), ('4622','错那县','433','0','3',NULL), ('4623','隆子县','433','0','3',NULL), ('4624','亚东县','434','0','3',NULL), ('4625','仁布县','434','0','3',NULL), ('4626','仲巴县','434','0','3',NULL), ('4627','南木林县','434','0','3',NULL), ('4628','吉隆县','434','0','3',NULL), ('4629','定日县','434','0','3',NULL), ('4630','定结县','434','0','3',NULL), ('4631','岗巴县','434','0','3',NULL), ('4632','康马县','434','0','3',NULL), ('4633','拉孜县','434','0','3',NULL), ('4634','日喀则市','434','0','3',NULL), ('4635','昂仁县','434','0','3',NULL), ('4636','江孜县','434','0','3',NULL), ('4637','白朗县','434','0','3',NULL), ('4638','聂拉木县','434','0','3',NULL), ('4639','萨嘎县','434','0','3',NULL), ('4640','萨迦县','434','0','3',NULL), ('4641','谢通门县','434','0','3',NULL), ('4642','嘉黎县','435','0','3',NULL), ('4643','安多县','435','0','3',NULL), ('4644','尼玛县','435','0','3',NULL), ('4645','巴青县','435','0','3',NULL), ('4646','比如县','435','0','3',NULL), ('4647','班戈县','435','0','3',NULL), ('4648','申扎县','435','0','3',NULL), ('4649','索县','435','0','3',NULL), ('4650','聂荣县','435','0','3',NULL), ('4651','那曲县','435','0','3',NULL), ('4652','噶尔县','436','0','3',NULL), ('4653','措勤县','436','0','3',NULL), ('4654','改则县','436','0','3',NULL), ('4655','日土县','436','0','3',NULL), ('4656','普兰县','436','0','3',NULL), ('4657','札达县','436','0','3',NULL), ('4658','革吉县','436','0','3',NULL), ('4659','墨脱县','437','0','3',NULL), ('4660','察隅县','437','0','3',NULL), ('4661','工布江达县','437','0','3',NULL), ('4662','朗县','437','0','3',NULL), ('4663','林芝县','437','0','3',NULL), ('4664','波密县','437','0','3',NULL), ('4665','米林县','437','0','3',NULL), ('4666','临潼区','438','0','3',NULL), ('4667','周至县','438','0','3',NULL), ('4668','户县','438','0','3',NULL), ('4669','新城区','438','0','3',NULL), ('4670','未央区','438','0','3',NULL), ('4671','灞桥区','438','0','3',NULL), ('4672','碑林区','438','0','3',NULL), ('4673','莲湖区','438','0','3',NULL), ('4674','蓝田县','438','0','3',NULL), ('4675','长安区','438','0','3',NULL), ('4676','阎良区','438','0','3',NULL), ('4677','雁塔区','438','0','3',NULL), ('4678','高陵县','438','0','3',NULL), ('4679','印台区','439','0','3',NULL), ('4680','宜君县','439','0','3',NULL), ('4681','王益区','439','0','3',NULL), ('4682','耀州区','439','0','3',NULL), ('4683','凤县','440','0','3',NULL), ('4684','凤翔县','440','0','3',NULL), ('4685','千阳县','440','0','3',NULL), ('4686','太白县','440','0','3',NULL), ('4687','岐山县','440','0','3',NULL), ('4688','扶风县','440','0','3',NULL), ('4689','渭滨区','440','0','3',NULL), ('4690','眉县','440','0','3',NULL), ('4691','金台区','440','0','3',NULL), ('4692','陇县','440','0','3',NULL), ('4693','陈仓区','440','0','3',NULL), ('4694','麟游县','440','0','3',NULL), ('4695','三原县','441','0','3',NULL);
INSERT INTO `#__area` VALUES ('4696','干县','441','0','3',NULL), ('4697','兴平市','441','0','3',NULL), ('4698','彬县','441','0','3',NULL), ('4699','旬邑县','441','0','3',NULL), ('4700','杨陵区','441','0','3',NULL), ('4701','武功县','441','0','3',NULL), ('4702','永寿县','441','0','3',NULL), ('4703','泾阳县','441','0','3',NULL), ('4704','淳化县','441','0','3',NULL), ('4705','渭城区','441','0','3',NULL), ('4706','礼泉县','441','0','3',NULL), ('4707','秦都区','441','0','3',NULL), ('4708','长武县','441','0','3',NULL), ('4709','临渭区','442','0','3',NULL), ('4710','华县','442','0','3',NULL), ('4711','华阴市','442','0','3',NULL), ('4712','合阳县','442','0','3',NULL), ('4713','大荔县','442','0','3',NULL), ('4714','富平县','442','0','3',NULL), ('4715','潼关县','442','0','3',NULL), ('4716','澄城县','442','0','3',NULL), ('4717','白水县','442','0','3',NULL), ('4718','蒲城县','442','0','3',NULL), ('4719','韩城市','442','0','3',NULL), ('4720','吴起县','443','0','3',NULL), ('4721','子长县','443','0','3',NULL), ('4722','安塞县','443','0','3',NULL), ('4723','宜川县','443','0','3',NULL), ('4724','宝塔区','443','0','3',NULL), ('4725','富县','443','0','3',NULL), ('4726','延川县','443','0','3',NULL), ('4727','延长县','443','0','3',NULL), ('4728','志丹县','443','0','3',NULL), ('4729','洛川县','443','0','3',NULL), ('4730','甘泉县','443','0','3',NULL), ('4731','黄陵县','443','0','3',NULL), ('4732','黄龙县','443','0','3',NULL), ('4733','佛坪县','444','0','3',NULL), ('4734','勉县','444','0','3',NULL), ('4735','南郑县','444','0','3',NULL), ('4736','城固县','444','0','3',NULL), ('4737','宁强县','444','0','3',NULL), ('4738','汉台区','444','0','3',NULL), ('4739','洋县','444','0','3',NULL), ('4740','留坝县','444','0','3',NULL), ('4741','略阳县','444','0','3',NULL), ('4742','西乡县','444','0','3',NULL), ('4743','镇巴县','444','0','3',NULL), ('4744','佳县','445','0','3',NULL), ('4745','吴堡县','445','0','3',NULL), ('4746','子洲县','445','0','3',NULL), ('4747','定边县','445','0','3',NULL), ('4748','府谷县','445','0','3',NULL), ('4749','榆林市榆阳区','445','0','3',NULL), ('4750','横山县','445','0','3',NULL), ('4751','清涧县','445','0','3',NULL), ('4752','神木县','445','0','3',NULL), ('4753','米脂县','445','0','3',NULL), ('4754','绥德县','445','0','3',NULL), ('4755','靖边县','445','0','3',NULL), ('4756','宁陕县','446','0','3',NULL), ('4757','岚皋县','446','0','3',NULL), ('4758','平利县','446','0','3',NULL), ('4759','旬阳县','446','0','3',NULL), ('4760','汉滨区','446','0','3',NULL), ('4761','汉阴县','446','0','3',NULL), ('4762','白河县','446','0','3',NULL), ('4763','石泉县','446','0','3',NULL), ('4764','紫阳县','446','0','3',NULL), ('4765','镇坪县','446','0','3',NULL), ('4766','丹凤县','447','0','3',NULL), ('4767','商南县','447','0','3',NULL), ('4768','商州区','447','0','3',NULL), ('4769','山阳县','447','0','3',NULL), ('4770','柞水县','447','0','3',NULL), ('4771','洛南县','447','0','3',NULL), ('4772','镇安县','447','0','3',NULL), ('4773','七里河区','448','0','3',NULL), ('4774','城关区','448','0','3',NULL), ('4775','安宁区','448','0','3',NULL), ('4776','榆中县','448','0','3',NULL), ('4777','永登县','448','0','3',NULL), ('4778','皋兰县','448','0','3',NULL), ('4779','红古区','448','0','3',NULL), ('4780','西固区','448','0','3',NULL), ('4781','嘉峪关市','449','0','3',NULL), ('4782','永昌县','450','0','3',NULL), ('4783','金川区','450','0','3',NULL), ('4784','会宁县','451','0','3',NULL), ('4785','平川区','451','0','3',NULL), ('4786','景泰县','451','0','3',NULL), ('4787','白银区','451','0','3',NULL), ('4788','靖远县','451','0','3',NULL), ('4789','张家川回族自治县','452','0','3',NULL), ('4790','武山县','452','0','3',NULL), ('4791','清水县','452','0','3',NULL), ('4792','甘谷县','452','0','3',NULL), ('4793','秦安县','452','0','3',NULL), ('4794','秦州区','452','0','3',NULL), ('4795','麦积区','452','0','3',NULL);
INSERT INTO `#__area` VALUES ('4796','凉州区','453','0','3',NULL), ('4797','古浪县','453','0','3',NULL), ('4798','天祝藏族自治县','453','0','3',NULL), ('4799','民勤县','453','0','3',NULL), ('4800','临泽县','454','0','3',NULL), ('4801','山丹县','454','0','3',NULL), ('4802','民乐县','454','0','3',NULL), ('4803','甘州区','454','0','3',NULL), ('4804','肃南裕固族自治县','454','0','3',NULL), ('4805','高台县','454','0','3',NULL), ('4806','华亭县','455','0','3',NULL), ('4807','崆峒区','455','0','3',NULL), ('4808','崇信县','455','0','3',NULL), ('4809','庄浪县','455','0','3',NULL), ('4810','泾川县','455','0','3',NULL), ('4811','灵台县','455','0','3',NULL), ('4812','静宁县','455','0','3',NULL), ('4813','敦煌市','456','0','3',NULL), ('4814','玉门市','456','0','3',NULL), ('4815','瓜州县（原安西县）','456','0','3',NULL), ('4816','肃北蒙古族自治县','456','0','3',NULL), ('4817','肃州区','456','0','3',NULL), ('4818','金塔县','456','0','3',NULL), ('4819','阿克塞哈萨克族自治县','456','0','3',NULL), ('4820','华池县','457','0','3',NULL), ('4821','合水县','457','0','3',NULL), ('4822','宁县','457','0','3',NULL), ('4823','庆城县','457','0','3',NULL), ('4824','正宁县','457','0','3',NULL), ('4825','环县','457','0','3',NULL), ('4826','西峰区','457','0','3',NULL), ('4827','镇原县','457','0','3',NULL), ('4828','临洮县','458','0','3',NULL), ('4829','安定区','458','0','3',NULL), ('4830','岷县','458','0','3',NULL), ('4831','渭源县','458','0','3',NULL), ('4832','漳县','458','0','3',NULL), ('4833','通渭县','458','0','3',NULL), ('4834','陇西县','458','0','3',NULL), ('4835','两当县','459','0','3',NULL), ('4836','宕昌县','459','0','3',NULL), ('4837','康县','459','0','3',NULL), ('4838','徽县','459','0','3',NULL), ('4839','成县','459','0','3',NULL), ('4840','文县','459','0','3',NULL), ('4841','武都区','459','0','3',NULL), ('4842','礼县','459','0','3',NULL), ('4843','西和县','459','0','3',NULL), ('4844','东乡族自治县','460','0','3',NULL), ('4845','临夏县','460','0','3',NULL), ('4846','临夏市','460','0','3',NULL), ('4847','和政县','460','0','3',NULL), ('4848','广河县','460','0','3',NULL), ('4849','康乐县','460','0','3',NULL), ('4850','永靖县','460','0','3',NULL), ('4851','积石山保安族东乡族撒拉族自治县','460','0','3',NULL), ('4852','临潭县','461','0','3',NULL), ('4853','卓尼县','461','0','3',NULL), ('4854','合作市','461','0','3',NULL), ('4855','夏河县','461','0','3',NULL), ('4856','玛曲县','461','0','3',NULL), ('4857','碌曲县','461','0','3',NULL), ('4858','舟曲县','461','0','3',NULL), ('4859','迭部县','461','0','3',NULL), ('4860','城东区','462','0','3',NULL), ('4861','城中区','462','0','3',NULL), ('4862','城北区','462','0','3',NULL), ('4863','城西区','462','0','3',NULL), ('4864','大通回族土族自治县','462','0','3',NULL), ('4865','湟中县','462','0','3',NULL), ('4866','湟源县','462','0','3',NULL), ('4867','乐都县','463','0','3',NULL), ('4868','互助土族自治县','463','0','3',NULL), ('4869','化隆回族自治县','463','0','3',NULL), ('4870','平安县','463','0','3',NULL), ('4871','循化撒拉族自治县','463','0','3',NULL), ('4872','民和回族土族自治县','463','0','3',NULL), ('4873','刚察县','464','0','3',NULL), ('4874','海晏县','464','0','3',NULL), ('4875','祁连县','464','0','3',NULL), ('4876','门源回族自治县','464','0','3',NULL), ('4877','同仁县','465','0','3',NULL), ('4878','尖扎县','465','0','3',NULL), ('4879','河南蒙古族自治县','465','0','3',NULL), ('4880','泽库县','465','0','3',NULL), ('4881','共和县','466','0','3',NULL), ('4882','兴海县','466','0','3',NULL), ('4883','同德县','466','0','3',NULL), ('4884','贵南县','466','0','3',NULL), ('4885','贵德县','466','0','3',NULL), ('4886','久治县','467','0','3',NULL), ('4887','玛多县','467','0','3',NULL), ('4888','玛沁县','467','0','3',NULL), ('4889','班玛县','467','0','3',NULL), ('4890','甘德县','467','0','3',NULL), ('4891','达日县','467','0','3',NULL), ('4892','囊谦县','468','0','3',NULL), ('4893','曲麻莱县','468','0','3',NULL), ('4894','杂多县','468','0','3',NULL), ('4895','治多县','468','0','3',NULL);
INSERT INTO `#__area` VALUES ('4896','玉树县','468','0','3',NULL), ('4897','称多县','468','0','3',NULL), ('4898','乌兰县','469','0','3',NULL), ('4899','冷湖行委','469','0','3',NULL), ('4900','大柴旦行委','469','0','3',NULL), ('4901','天峻县','469','0','3',NULL), ('4902','德令哈市','469','0','3',NULL), ('4903','格尔木市','469','0','3',NULL), ('4904','茫崖行委','469','0','3',NULL), ('4905','都兰县','469','0','3',NULL), ('4906','兴庆区','470','0','3',NULL), ('4907','永宁县','470','0','3',NULL), ('4908','灵武市','470','0','3',NULL), ('4909','西夏区','470','0','3',NULL), ('4910','贺兰县','470','0','3',NULL), ('4911','金凤区','470','0','3',NULL), ('4912','大武口区','471','0','3',NULL), ('4913','平罗县','471','0','3',NULL), ('4914','惠农区','471','0','3',NULL), ('4915','利通区','472','0','3',NULL), ('4916','同心县','472','0','3',NULL), ('4917','盐池县','472','0','3',NULL), ('4918','青铜峡市','472','0','3',NULL), ('4919','原州区','473','0','3',NULL), ('4920','彭阳县','473','0','3',NULL), ('4921','泾源县','473','0','3',NULL), ('4922','西吉县','473','0','3',NULL), ('4923','隆德县','473','0','3',NULL), ('4924','中宁县','474','0','3',NULL), ('4925','沙坡头区','474','0','3',NULL), ('4926','海原县','474','0','3',NULL), ('4927','东山区','475','0','3',NULL), ('4928','乌鲁木齐县','475','0','3',NULL), ('4929','天山区','475','0','3',NULL), ('4930','头屯河区','475','0','3',NULL), ('4931','新市区','475','0','3',NULL), ('4932','水磨沟区','475','0','3',NULL), ('4933','沙依巴克区','475','0','3',NULL), ('4934','达坂城区','475','0','3',NULL), ('4935','乌尔禾区','476','0','3',NULL), ('4936','克拉玛依区','476','0','3',NULL), ('4937','独山子区','476','0','3',NULL), ('4938','白碱滩区','476','0','3',NULL), ('4939','吐鲁番市','477','0','3',NULL), ('4940','托克逊县','477','0','3',NULL), ('4941','鄯善县','477','0','3',NULL), ('4942','伊吾县','478','0','3',NULL), ('4943','哈密市','478','0','3',NULL), ('4944','巴里坤哈萨克自治县','478','0','3',NULL), ('4945','吉木萨尔县','479','0','3',NULL), ('4946','呼图壁县','479','0','3',NULL), ('4947','奇台县','479','0','3',NULL), ('4948','昌吉市','479','0','3',NULL), ('4949','木垒哈萨克自治县','479','0','3',NULL), ('4950','玛纳斯县','479','0','3',NULL), ('4951','米泉市','479','0','3',NULL), ('4952','阜康市','479','0','3',NULL), ('4953','博乐市','480','0','3',NULL), ('4954','温泉县','480','0','3',NULL), ('4955','精河县','480','0','3',NULL), ('4956','博湖县','481','0','3',NULL), ('4957','和硕县','481','0','3',NULL), ('4958','和静县','481','0','3',NULL), ('4959','尉犁县','481','0','3',NULL), ('4960','库尔勒市','481','0','3',NULL), ('4961','焉耆回族自治县','481','0','3',NULL), ('4962','若羌县','481','0','3',NULL), ('4963','轮台县','481','0','3',NULL), ('4964','乌什县','482','0','3',NULL), ('4965','库车县','482','0','3',NULL), ('4966','拜城县','482','0','3',NULL), ('4967','新和县','482','0','3',NULL), ('4968','柯坪县','482','0','3',NULL), ('4969','沙雅县','482','0','3',NULL), ('4970','温宿县','482','0','3',NULL), ('4971','阿克苏市','482','0','3',NULL), ('4972','阿瓦提县','482','0','3',NULL), ('4973','乌恰县','483','0','3',NULL), ('4974','阿克陶县','483','0','3',NULL), ('4975','阿合奇县','483','0','3',NULL), ('4976','阿图什市','483','0','3',NULL), ('4977','伽师县','484','0','3',NULL), ('4978','叶城县','484','0','3',NULL), ('4979','喀什市','484','0','3',NULL), ('4980','塔什库尔干塔吉克自治县','484','0','3',NULL), ('4981','岳普湖县','484','0','3',NULL), ('4982','巴楚县','484','0','3',NULL), ('4983','泽普县','484','0','3',NULL), ('4984','疏勒县','484','0','3',NULL), ('4985','疏附县','484','0','3',NULL), ('4986','英吉沙县','484','0','3',NULL), ('4987','莎车县','484','0','3',NULL), ('4988','麦盖提县','484','0','3',NULL), ('4989','于田县','485','0','3',NULL), ('4990','和田县','485','0','3',NULL), ('4991','和田市','485','0','3',NULL), ('4992','墨玉县','485','0','3',NULL), ('4993','民丰县','485','0','3',NULL), ('4994','洛浦县','485','0','3',NULL), ('4995','皮山县','485','0','3',NULL);
INSERT INTO `#__area` VALUES ('4996','策勒县','485','0','3',NULL), ('4997','伊宁县','486','0','3',NULL), ('4998','伊宁市','486','0','3',NULL), ('4999','奎屯市','486','0','3',NULL), ('5000','察布查尔锡伯自治县','486','0','3',NULL), ('5001','尼勒克县','486','0','3',NULL), ('5002','巩留县','486','0','3',NULL), ('5003','新源县','486','0','3',NULL), ('5004','昭苏县','486','0','3',NULL), ('5005','特克斯县','486','0','3',NULL), ('5006','霍城县','486','0','3',NULL), ('5007','乌苏市','487','0','3',NULL), ('5008','和布克赛尔蒙古自治县','487','0','3',NULL), ('5009','塔城市','487','0','3',NULL), ('5010','托里县','487','0','3',NULL), ('5011','沙湾县','487','0','3',NULL), ('5012','裕民县','487','0','3',NULL), ('5013','额敏县','487','0','3',NULL), ('5014','吉木乃县','488','0','3',NULL), ('5015','哈巴河县','488','0','3',NULL), ('5016','富蕴县','488','0','3',NULL), ('5017','布尔津县','488','0','3',NULL), ('5018','福海县','488','0','3',NULL), ('5019','阿勒泰市','488','0','3',NULL), ('5020','青河县','488','0','3',NULL), ('5021','石河子市','489','0','3',NULL), ('5022','阿拉尔市','490','0','3',NULL), ('5023','图木舒克市','491','0','3',NULL), ('5024','五家渠市','492','0','3',NULL), ('45055','海外','35','0','2',NULL);


INSERT INTO `#__config` (`id`, `code`, `value`, `remark`) VALUES
(1, 'site_name', '德尚商城', '商城名称'),
(2, 'site_phone', '3', '商城客服服务电话'),
(3, 'site_state', '1', '商城状态'),
(4, 'site_logo', 'site_logo.png', '商城logo图'),
(5, 'member_logo', 'member_logo.png', '默认会员图'),
(6, 'seller_center_logo', 'seller_center_logo.jpg', '默认卖家中心logo'),
(7, 'site_mobile_logo', 'site_mobile_logo.png', '默认商城手机端logo'),
(8, 'site_logowx', 'site_logowx.jpg', '微信商城二维码'),
(9, 'icp_number', '2', 'ICP备案号'),
(10, 'site_tel400', '40002541852', '解释,备注'),
(11, 'site_email', '858761000@qq.com', '电子邮件'),
(12, 'flow_static_code', 'Copyright © 2013-2063 德尚网络开源系统 版权所有 保留一切权利', '底部版权信息'),
(13, 'closed_reason', '商城暂时关闭', '商城关闭原因'),
(14, 'guest_comment', '1', '是否允许游客咨询'),
(15, 'captcha_status_login', '1', '会员登录是否需要验证码'),
(16, 'captcha_status_register', '1', '会员注册是否验证码'),
(17, 'captcha_status_goodsqa', '1', '商品咨询是否验证码'),
(18, 'captcha_status_storelogin', '1', '机构登录是否验证码'),
(19, 'cache_open', '0', '是否开启缓存'),
(21, 'default_goods_image', 'default_goods_image.jpg', '默认商品图'),
(22, 'default_store_logo', 'default_store_logo.jpg', '默认机构图'),
(23, 'default_store_avatar', 'default_store_avatar.png', '默认机构图像'),
(24, 'default_user_portrait', 'default_user_portrait.png', '默认会员图像'),
(25, 'fixed_suspension_state', '1', '是否开启首次访问悬浮'),
(26, 'fixed_suspension_img', 'fixed_suspension_img.png', '首次访问悬浮图像'),
(27, 'fixed_suspension_url', '#', '悬浮图像跳转地址'),
(31, 'store_joinin_pic', 'a:2:{s:3:"pic";a:3:{i:1;s:18:"store_joinin_1.jpg";i:2;s:18:"store_joinin_2.jpg";i:3;s:18:"store_joinin_3.jpg";}s:8:"show_txt";s:6:"测试";}', '解释,备注'),
(32, 'store_joinin_open', '1', '店铺入驻开启（0不开启1全部开启2仅企业3仅个人）'),
(40, 'smscf_wj_username', '', '短信平台账号'),
(41, 'smscf_wj_key', '', '短信平台密钥'),
(42, 'smscf_sign', '', '短信签名'),
(43, 'smscf_type', 'wj', '短信服务商'),
(44, 'smscf_ali_id', '', '阿里云短信AccessKeyId'),
(45, 'smscf_ali_secret', '', '阿里云短信AccessKeySecret'),
(46, 'smscf_ten_id', '', '腾讯云短信AccessKeyId'),
(47, 'smscf_ten_secret', '', '腾讯云短信AccessKeySecret'),
(51, 'email_host', 'smtp.126.com', '邮箱地址'),
(52, 'email_port', '25', '邮箱端口'),
(53, 'email_addr', '', '邮箱发件人地址'),
(54, 'email_id', '', '身份验证用户名'),
(55, 'email_pass', '', '用户名密码'),
(56, 'email_secure', '', '邮箱发送协议'),
(59, 'site_uniqid', '', '系统标识'),
(60, 'setup_date', '2015-01-01 00:00:00', '安装时间'),
(61, 'upload_type', 'local', '图片上传保存方式'),
(62, 'alioss_accessid', NULL, 'accessid'),
(63, 'alioss_accesssecret', NULL, 'oss_accesssecret'),
(64, 'alioss_bucket', NULL, 'oss_bucket'),
(65, 'alioss_endpoint', NULL, 'oss_endpoint'),
(66, 'aliendpoint_type', '0', 'aliendpoint_type'),
(67, 'upload_video_type', 'local', '视频上传保存方式'),
(92, 'order_auto_cancel_day', '3', '自动取消订单的天数'),
(93, 'order_refund_time', '1', '申请退款的有效天数,确认收货后不可申请退款'),
(94, 'malbum_max_sum', '10', '个人相册数量'),
(95, 'hot_search', '', '热门搜索关键字'),
(96, 'image_allow_ext', 'gif,jpg,jpeg,bmp,png,swf', '允许后缀'),
(97, 'image_max_filesize', '1024', '允许上传最大值'),

(101, 'qq_isuse', '1', '是否使用QQ互联'),
(102, 'qq_appid', '', 'qq互联id'),
(103, 'qq_appkey', '', 'qq秘钥'),
(111, 'sina_isuse', '1', '是的使用微博登录'),
(112, 'sina_wb_akey', '', '新浪id'),
(113, 'sina_wb_skey', '', '新浪秘钥'),

(121, 'weixin_pc_isuse', '', '是否微信PC登录'),
(122, 'weixin_pc_appid', '', '网站应用appid'),
(123, 'weixin_pc_secret', '', '网站应用appserite'),
(124, 'weixin_h5_isuse', '', '是否微信公众号登录'),
(125, 'weixin_h5_appid', '', '公众号appid'),
(126, 'weixin_h5_secret', '', '公众号appserite'),
(127, 'weixin_xcx_isuse', '', '是否微信小程序登录'),
(128, 'weixin_xcx_appid', '', '小程序appid'),
(129, 'weixin_xcx_secret', '', '小程序appserite'),
(130, 'weixin_app_isuse', '', '是否微信APP登录'),
(131, 'weixin_app_appid', '', '移动应用appid'),
(132, 'weixin_app_secret', '', '移动应用appserite'),

(141, 'sms_register', '0', '是否手机注册'),
(142, 'sms_login', '0', '是否手机登录'),
(143, 'sms_password', '0', '是否手机找回密码'),

(150, 'vod_tencent_secret_id', '', '腾讯Secret_id'),
(151, 'vod_tencent_secret_key', '', '腾讯Secret_key'),
(202, 'promotion_allow', '1', '商品促销'),
(204, 'points_isuse', '1', '开启积分机制'),
(205, 'pointshop_isuse', '1', '积分中心'),
(206, 'pointprod_isuse', '1', '积分兑换'),
(207, 'voucher_allow', '1', '代金券'),
(230, 'points_reg', '5', '注册赠送积分数'),
(231, 'points_login', '10', '登录赠送积分数'),
(232, 'points_comments', '10', '评论赠送积分数'),
(233, 'points_signin', '10', '会员签到赠送积分'),
(234, 'points_invite', '10', '邀请注册积分'),
(235, 'points_rebate', '1', '返佣比例'),
(236, 'points_orderrate', '4', '消费额与赠送积分比例'),
(237, 'points_ordermax', '4', '每单最多赠送积分'),
(238, 'points_signin_isuse', '1', '开启签到送积分'),
(239, 'points_signin_cycle', '7', '签到持续周期'),
(240, 'points_signin_reward', '7', '签到持续周期额外奖励'),
(267, 'promotion_voucher_price', '10', '代金券价格'),
(268, 'promotion_xianshi_price', '10', '限时促销价格'),
(270, 'voucher_storetimes_limit', '20', '代金券活动数'),
(271, 'voucher_buyertimes_limit', '20', '卖家最大领取数'),
(290, 'goods_verify', '0', '商品审核'),
(501, 'expset', 'a:4:{s:9:"login_exp";s:3:"151";s:11:"comment_exp";s:1:"1";s:7:"buy_exp";s:1:"1";s:11:"buy_exp_max";s:2:"50";}', '解释,备注'),
(502, 'exppoints_rule', 'a:4:{s:9:"exp_login";s:2:"20";s:12:"exp_comments";s:2:"10";s:13:"exp_orderrate";s:2:"10";s:12:"exp_ordermax";s:2:"10";}', '解释,备注'),
(601, 'member_grade', 'a:4:{i:1;a:3:{s:5:"level";i:1;s:10:"level_name";s:12:"青铜会员";s:9:"exppoints";i:0;}i:2;a:3:{s:5:"level";i:2;s:10:"level_name";s:12:"白银会员";s:9:"exppoints";i:150;}i:3;a:3:{s:5:"level";i:3;s:10:"level_name";s:12:"黄金会员";s:9:"exppoints";i:200;}i:4;a:3:{s:5:"level";i:4;s:10:"level_name";s:12:"白金会员";s:9:"exppoints";i:500;}}', '解释,备注'),
(701, 'inviter_back', 'inviter_back.png', '会员推广背景图片'),
(702, 'inviter_ratio_1', '30', '会员一级推广佣金比例'),
(703, 'inviter_ratio_2', '25', '会员二级推广佣金比例'),
(704, 'inviter_ratio_3', '15', '会员三级推广佣金比例'),
(706 ,  'inviter_open',  '1',  '推广开关'), 
(707 ,  'inviter_level',  '3',  '推广级别'), 
(708 ,  'inviter_show',  '1',  '详情页显示推广佣金'), 
(709 ,  'inviter_return',  '1',  '推广员返佣'), 
(710 ,  'inviter_view',  '0',  '推广员审核'),
(711 ,  'inviter_condition',  '0',  '推广员条件'), 
(712 ,  'inviter_condition_amount',  '0',  '成为推广员的消费门槛'),
(714 ,  'store_withdraw_cycle',  '1',  '机构提现周期（天）'), 
(715 ,  'store_withdraw_min',  '100',  '机构最小提现金额（元）'),
(716 ,  'store_withdraw_max',  '10000',  '机构最大提现金额（元）'),
(718 ,  'business_licence',  '',  '营业执照'),
(719 ,  'member_auth',  '0',  '实名认证'),
(729 ,  'h5_store_site_url',  'https://uniapp.store.dskms.csdeshang.com',  '手机端店铺地址'),
(730 ,  'h5_site_url',  'https://uniapp.dskms.csdeshang.com',  '手机端地址'),
(731 ,  'h5_force_redirect',  '0',  '强制跳转到手机端'),
(740 ,  'member_withdraw_cycle',  '1',  '用户提现周期（天）'), 
(741 ,  'member_withdraw_min',  '100',  '用户最小提现金额（元）'),
(742 ,  'member_withdraw_max',  '10000',  '用户最大提现金额（元）'),
(752 ,  'instant_message_verify',  '0',  '直播聊天审核'),
(753 ,  'instant_message_gateway_url',  '',  '直播聊天gateway地址'),
(754 ,  'instant_message_register_url',  '',  '直播聊天register地址'),
(755 ,  'live_push_domain',  '',  '推流域名'),
(756 ,  'live_push_key',  '',  '推流key'),
(757 ,  'live_play_domain',  '',  '拉流域名'),
(758 ,  'vod_tencent_appid',  '',  '腾讯云appid'),
(759 ,  'vod_tencent_play_domain',  '',  '点播域名'),
(760 ,  'vod_tencent_play_key',  '',  '点播key'),
(761 ,  'video_type',  'tencent',  '视频类型'),
(762 ,  'aliyun_user_id',  '',  '阿里云账号ID'),
(763 ,  'aliyun_access_key_id',  '',  '阿里云账号AccessKeyID'),
(764 ,  'aliyun_access_key_secret',  '',  '阿里云账号AccessKeySecret'),
(765 ,  'aliyun_live_push_key',  '',  '阿里云推流鉴权key'),
(766 ,  'aliyun_live_push_domain',  '',  '阿里云推流域名'),
(767 ,  'aliyun_live_play_domain',  '',  '阿里云播流域名'),
(768 ,  'aliyun_live_play_key',  '',  '阿里云播流鉴权key'),
(769 ,  'aliyun_vod_play_domain',  '',  '阿里云播点播域名'),
(770 ,  'aliyun_vod_play_key',  '',  '阿里云播点播鉴权key'),
(771 ,  'baidu_pan_id',  '',  '百度云盘应用ID'),
(772 ,  'baidu_pan_api_key',  '',  '百度云盘应用API Key'),
(773 ,  'baidu_pan_secret_key',  '',  '百度云盘应用Secret Key'),
(774 ,  'baidu_pan_app_name',  '',  '百度云盘应用名称'),
(775 ,  'live_type',  '0',  '直播类型（0小程序原生直播1小程序组件直播）'),
(780 ,  'wab_number',  '',  '网安备'),
(781 ,  'word_filter_open',  '0',  '是否开启敏感词过滤'),
(782 ,  'word_filter_appid',  '',  '百度应用appid'),
(783 ,  'word_filter_secret',  '',  '百度应用secret'),
(784 ,  'word_filter_access_token',  '',  '百度应用access_token'),
(785 ,  'word_filter_access_token_expire',  '',  '百度应用access_token过期时间'),
(790 ,  'member_normal_register',  '1',  '用户普通注册（0关闭1开启）');

INSERT INTO `#__articleclass` (`ac_id`, `ac_code`, `ac_name`, `ac_parent_id`, `ac_sort`) VALUES
  (1, 'notice', '商城公告', 0, 255),
  (2, 'member', '帮助中心', 0, 255),
  (3, 'store', '店主之家', 0, 255),
  (4, 'payment', '支付方式', 0, 255),
  (5, 'sold', '售后服务', 0, 255),
  (6, 'service', '客服中心', 0, 255),
  (7, 'about', '关于我们', 0, 255);

INSERT INTO `#__article` (`article_id`, `ac_id`, `article_url`, `article_show`, `article_sort`, `article_title`, `article_content`, `article_time`) VALUES (6, 2, '', 1, 255, '如何注册成为会员', '如何注册成为会员', 1435672310),
(7, 2, '', 1, 255, '如何搜索', '如何搜索', 1435672310),
(8, 2, '', 1, 255, '忘记密码', '忘记密码', 1435672310),
(9, 2, '', 1, 255, '我要买', '我要买', 1435672310),
(10, 2, '', 1, 255, '查看已购买商品', '查看已购买商品', 1435672310),
(11, 3, '', 1, 255, '如何管理机构', '如何管理机构', 1435672310),
(12, 3, '', 1, 255, '查看售出商品', '查看售出商品', 1435672310),
(13, 3, '', 1, 255, '如何发货', '如何发货', 1435672310),
(14, 3, '', 1, 255, '商城商品推荐', '商城商品推荐', 1435672310),
(15, 3, '', 1, 255, '如何申请开店', '如何申请开店', 1435672310),
(16, 4, '', 1, 255, '如何注册支付宝', '如何注册支付宝', 1435672310),
(17, 4, '', 1, 255, '在线支付', '在线支付', 1435672310),
(18, 6, '', 1, 255, '会员修改密码', '会员修改密码', 1435672310),
(19, 6, '', 1, 255, '会员修改个人资料', '会员修改个人资料', 1435672310),
(20, 6, '', 1, 255, '商品发布', '商品发布', 1435672310),
(21, 6, '', 1, 255, '修改收货地址', '修改收货地址', 1435672310),
(22, 7, '', 1, 255, '关于我们', '关于我们', 1435672310),
(23, 7, '', 1, 255, '联系我们', '联系我们', 1435672310),
(24, 7, '', 1, 255, '招聘英才', '招聘英才', 1435672310),
(25, 7, '', 1, 255, '合作及洽谈', '合作及洽谈', 1435672310),
(26, 5, '', 1, 255, '联系卖家', '联系卖家', 1435672310),
(28, 4, '', 1, 255, '分期付款', '分期付款<br />', 1435672310),
(29, 4, '', 1, 255, '邮局汇款', '邮局汇款<br />', 1435672310),
(30, 4, '', 1, 255, '公司转账', '公司转账<br />', 1435672310),
(31, 5, '', 1, 255, '退换货政策', '退换货政策', 1435672310),
(32, 5, '', 1, 255, '退换货流程', '退换货流程', 1435672310),
(33, 5, '', 1, 255, '返修/退换货', '返修/退换货', 1435672310),
(34, 5, '', 1, 255, '退款申请', '退款申请', 1435672310),
(35, 1, 'http://www.csdeshang.com/', 1, 1, '火爆销售中', '火爆销售中<br />', 1435672310),
(36, 1, '', 1, 255, '管理功能说明', '管理功能说明', 1435672310),
(37, 1, '', 1, 255, '如何扩充水印字体库', '如何扩充水印字体库', 1435672310),
(38, 1, '', 1, 255, '提示信息', '提示信息', 1435672310),
(39, 2, '', 1, 255, '积分细则', '积分细则积分细则', 1435672310),
(40, 2, '', 1, 255, '积分兑换说明', '积分兑换说明', 1435672310),
(41, 1, '', 1, 255, '功能使用说明', '功能使用说明', 1435672310);

INSERT INTO `#__consulttype` (`consulttype_id`, `consulttype_name`, `consulttype_introduce`, `consulttype_sort`) VALUES
(1, '商品咨询', '后台->交易->咨询管理->咨询类型->编辑', 1),
(2, '支付问题', '后台->交易->咨询管理->咨询类型->编辑', 2),
(3, '发票及保修', '后台->交易->咨询管理->咨询类型->编辑', 3),
(4, '促销及赠品', '后台->交易->咨询管理->咨询类型->编辑', 4);

INSERT INTO `#__document` (`document_id`, `document_code`, `document_title`, `document_content`, `document_time`) VALUES
(1, 'agreement', '用户服务协议', '&lt;p&gt;欢迎您注册、使用德尚网络账号！&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;《德尚网络账号使用协议》（如下简称“本协议”）系您与德尚网络科技有限责任公司及其关联公司（如下简称“德尚网络”或“我们”）就德尚网络账号的注册和使用而订立的协议。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;在您使用德尚网络账号的注册和使用服务（如下简称“本服务”）之前，我们在此特别提醒您，请您认真阅读、充分理解本协议各条款，特别是涉及服务费用条款，免除或者限制德尚网络责任的条款，对您的权利限制条款，争议解决和法律适用条款等，此类条款将以加粗的形式提示您注意。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;您通过网络页面点击同意和/或注册和使用德尚网络账号的行为，即视为您已阅读、理解并同意接受本协议，并同意接受本协议各项条款的约束。若您不同意本协议，则您有充分且完全的权利退出本服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;一、 概述&lt;/h2&gt;&lt;p&gt;1.1 德尚网络账号：是指德尚网络通过本协议授权您注册、登录或使用相关德尚网络服务的标识，是德尚网络服务的通行凭证。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1.2 协议范围：本协议内容同时包括德尚网络可能不断发布的关于本服务的相关协议、服务声明、业务规则及公告提示等内容（以下统称为“本协议”）。上述内容一经正式发布，即为本协议不可分割的组成部分，您同样应当遵守。此外，您在使用德尚网络为您提供的任何单项服务时，应认真阅读各单项服务的相关协议规则，并同意受本协议及单项协议规则的约束。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1.3 账号基本功能：您可通过德尚网络账号使用德尚网络提供的各类产品或服务，在您使用德尚网络网、应用商店、云服务、浏览器、德尚网络社区、米家等产品或服务，以及德尚网络终端类设备和相关合作伙伴服务时，为您提供账号注册、账号登录与安全、实名认证、家人服务等功能。为了在您使用德尚网络产品或服务时提供安全、便捷和统一的账号登录体验，德尚网络账号将为您开启自动登录功能。您亦可通过德尚网络账号向德尚网络购买米币以便您享受德尚网络的各种虚拟产品和增值服务（如游戏、主题、电子书等）。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1.4 德尚网络授予您一项有限的、非排他性、不可转让、不可转授及免费使用（除本协议另有声明外）的许可，以供您使用本服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;二、账号使用规范&lt;/h2&gt;&lt;h3&gt;2.1 账号注册&lt;/h3&gt;&lt;p&gt;2.1.1 您确认，在您独立注册和使用德尚网络账号前，您已经具备中华人民共和国法律所规定的完全民事行为能力。若您不具备前述的完全民事行为能力，则您及您的监护人应依照法律规定承担因此而导致的一切后果。如果您是未满十八周岁的未成年人，若您想使用我们的服务，请在您的父母或其他监护人的同意下为您创建未成年人账号后，您方可使用本服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.1.2 您保证在注册时所提交的所有资料（包括但不限于手机号码、邮箱等）真实、准确、合法、有效且系您本人的资料，否则可能无法使用本服务，或在使用过程中受到限制。因您提供的注册信息不真实、不准确或不完整而引发的相关责任或损失， 应由您自行承担。您应当通过真实身份信息认证注册账号，且您提交的账号名称、头像等注册信息中不得出现违法和不良信息，经德尚网络审核，如存在上述情况，德尚网络将不予注册；在您注册后，如发现您以虚假信息骗取账号或账号名称注册，或您的账号头像、简介等注册信息存在违法和不良信息的，德尚网络有权不经通知单方采取限期改正、暂停使用、终止账号等措施。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.1.3 您在使用德尚网络账号的过程中，所提供的信息发生变动的，应当及时进行变更。您可以通过德尚网络账号设置页面查询、更正您的信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;2.2 账号使用&lt;/h3&gt;&lt;p&gt;2.2.1 德尚网络账号系您有权使用我们提供的服务的凭证。德尚网络账号的所有权属于德尚网络，您作为初始申请注册人仅拥有您所注册的德尚网络账号的使用权。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.2.2 您应对您账号项下的所有行为结果（包括但不限于在线签署各类单项条款、发布信息、购买商品及服务及披露信息等）负责。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.2.3 您可在本协议约定的范围内使用德尚网络账号，您不得恶意注册德尚网络账号，未经德尚网络书面同意，您不得赠与、出借、出租、有偿或无偿转让或售卖德尚网络账号或者以其他方式许可非初始申请注册人使用德尚网络账号。德尚网络有权对上述行为进行独立判断并处理，您应当自行承担由此产生的任何责任，同时德尚网络保留追究上述行为人法律责任的权利。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.2.4 由于您的德尚网络账号关联您的个人信息及德尚网络商业信息，您的德尚网络账号仅限您本人使用。未经德尚网络同意，您直接或间接授权第三方使用您德尚网络账号或获取您账号项下信息的行为无效。如德尚网络根据德尚网络各平台规则中约定的违约认定程序及标准判断您德尚网络账号的使用可能危及您的账号安全及或德尚网络信息安全的，德尚网络可拒绝提供相应服务或终止本协议。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.2.5 如您违反上述账号使用规范，德尚网络有权根据法律法规对您采取以下限制措施：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;（1）如您违反我国相关法律法规、德尚网络各单项条款或业务规则的规定，德尚网络有权进行独立判断并随时采取限期改正、暂停使用、终止您对德尚网络账号的使用等措施，且根据实际情况决定是否恢复您对德尚网络账号的继续使用。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;（2）如果德尚网络发现您并非该账号初始申请注册人，德尚网络有权在未经通知的情况下终止您使用该账号。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;（3）德尚网络按照本协议或相关法律法规，限期改正、暂停使用或终止您对德尚网络账号的使用，而由此给您带来的损失（包括但不限于通信中断，用户资料、邮件和相关数据等的清空等），由您自行承担。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.2.6 若您选择第三方账号登录德尚网络的服务，第三方账号将与您的德尚网络账号相关联，德尚网络将根据您授权第三方提供的信息而使用您的信息（如头像）。您后续使用该德尚网络账号的行为受本协议约束。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;2.3 账号保管和安全&lt;/h3&gt;&lt;p&gt;2.3.1 德尚网络账号包括账号名称和密码，您的账号和密码均由您自行设置并保管，您可使用设置的账号名称（包括账号ID、手机号和邮箱）和密码登录并使用相关德尚网络服务。德尚网络任何时候均不会主动要求您提供您的账号密码。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.3.2 您应妥善保管好您的账号信息及密码，建议您采取特定措施保护您的账号安全，包括但不限于采取定期更改密码等相关措施。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.3.3 如果您当前使用的德尚网络账号并非您初始申请注册的或是通过非德尚网络提供的其他途径获得的，但您却知悉该德尚网络账号当前的密码，您不得用该德尚网络账号登录或进行任何操作，并请您在第一时间通知德尚网络或者该德尚网络账号的初始申请注册人。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.3.4 因您个人原因导致的账号信息遗失，如您需找回德尚网络账号信息，请按照德尚网络账号找回流程提供相应的信息，并确保提供的信息合法真实有效，若提供的信息不符合要求，无法通过德尚网络安全验证，德尚网络有权拒绝提供账号找回服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.3.5 因您主动泄露或因您遭受他人攻击、诈骗，以及丢失手机等智能设备等行为而产生的与德尚网络账号相关的损失及后果，德尚网络并不承担责任，您应通过司法、行政等救济途径向侵权行为人追偿。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2.4 账号回收&lt;/p&gt;&lt;p&gt;为了防止德尚网络账号资源被恶意占用，如您连续24个月未使用您的德尚网络账号或未通过德尚网络认可的其他方式登录过您的账号，德尚网络有权终止您对该账号的使用，您将不能再通过该账号登录或使用相关德尚网络服务，德尚网络保留删除该账号相关的所有数据信息以及回收该账号的权利。如该账号有关联的待处理交易或余额等，德尚网络将通过合适的方式（如短信或邮件等）通知您并尽最大努力在合理范围内协助您处理，请您按照德尚网络提示的方式进行操作。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;三、第三方服务&lt;/h2&gt;&lt;p&gt;3.1 如您使用德尚网络账号登录我们的关联公司或其他合作伙伴独立运营的第三方软件或平台（如下简称“第三方服务提供商”），并使用相关产品或服务时，除遵守本协议外，您还需仔细阅读并遵守上述第三方服务提供商的用户协议及隐私政策。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3.2 德尚网络无权也没有义务控制或审查第三方服务提供商所提供产品或服务的内容，对于您在使用此类第三方产品与服务时遭受的权益侵犯，德尚网络不承担任何责任。德尚网络不对任何第三方服务提供商的产品或服务的可用性、可靠性、安全性负责，也不为第三方在本服务中提供的任何内容、广告、产品、服务或其他信息作出任何形式的承诺、保证或担保。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3.3 在第三方服务提供商向您收集用户信息或记录您的行为信息时，您应当自行查看相关协议或警示等，我们对此不承担任何提示、审查义务，并不对因此对您可能承担的风险、损失负责，您应当与第三方服务提供商自行协商解决。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;四、用户权利和义务&lt;/h2&gt;&lt;p&gt;4.1 为顺利使用本服务，您需要具备一台或多台与本服务兼容的终端设备，并完成移动数据服务或WLAN网络连接，您应自行支付前述设备和网络连接服务的相关费用。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4.2 您在使用本服务时，必须遵守《网络安全法》、《互联网新闻信息服务管理规定》、《网络信息内容生态治理规定》《互联网用户账号信息管理规定》《移动互联网应用程序信息服务管理规定》等中华人民共和国相关法律法规的规定，您同意不会利用本服务进行任何违法犯罪行为或不正当的活动，包括但不限于下列行为:&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4.2.1 上载、展示、张贴、传播或以其他方式传送含有下列内容之一的信息：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;反对宪法所确定的基本原则的；&lt;/p&gt;&lt;p&gt;危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；&lt;/p&gt;&lt;p&gt;损害国家荣誉和利益的；&lt;/p&gt;&lt;p&gt;煽动民族仇恨、民族歧视、破坏民族团结的；&lt;/p&gt;&lt;p&gt;破坏国家宗教政策，宣扬邪教和封建迷信的；&lt;/p&gt;&lt;p&gt;散布谣言，扰乱社会秩序，破坏社会稳定的；&lt;/p&gt;&lt;p&gt;散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；&lt;/p&gt;&lt;p&gt;侮辱或者诽谤他人，侵害他人合法权利的；&lt;/p&gt;&lt;p&gt;含有虚假、有害、胁迫、侵害他人隐私、骚扰、侵害、中伤、粗俗、猥亵、或其他道德上令人反感的内容；&lt;/p&gt;&lt;p&gt;含有中国法律、法规、规章、条例以及任何具有法律效力之规范所限制或禁止的其他内容的。&lt;/p&gt;&lt;p&gt;4.2.2 不得利用本服务从事以下活动：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;未经允许，进入计算机信息网络或者使用计算机信息网络资源的；&lt;/p&gt;&lt;p&gt;未经允许，对计算机信息网络功能进行删除、修改或者增加的；&lt;/p&gt;&lt;p&gt;未经允许，对进入计算机信息网络中存储、处理或者传输的数据和应用程序进行删除、修改或者增加的；&lt;/p&gt;&lt;p&gt;故意制作、传播计算机病毒等破坏性程序的；&lt;/p&gt;&lt;p&gt;进行任何诸如发布广告、销售商品的商业行为，或者进行任何非法的侵害德尚网络利益的行为，如贩卖可币、游戏币、外挂、道具等；&lt;/p&gt;&lt;p&gt;其他危害计算机信息网络安全的行为。&lt;/p&gt;&lt;p&gt;4.3 您违反本协议或其他单项服务条款的规定，导致或产生的第三方向德尚网络与合作公司、关联公司主张的任何索赔、要求或损失，包括合理的律师费，您同意赔偿德尚网络与合作公司、关联公司，并使之免受损害。同时，德尚网络有权视您的行为性质，采取包括但不限于删除发布信息内容、暂停使用许可、终止服务、限制使用德尚网络账号、追究法律责任等措施。同时，德尚网络会视司法部门的要求，协助调查。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4.4 您不得对本服务任何部分或本服务之使用或获得，进行复制、拷贝、出售、转售或用于任何其他商业目的。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4.5 您须对自己在使用本服务过程中的行为承担法律责任。您承担法律责任的形式包括但不限于对受到侵害者进行赔偿，以及在德尚网络首先承担了因您的行为导致的行政处罚或侵权损害赔偿责任后，您应给予德尚网络等额的赔偿。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;五、免责声明&lt;/h2&gt;&lt;p&gt;5.1 本服务仅按照“现状”和“现有”提供。在法律允许的最大范围内，我们不承担任何明示或默示的保证、条件或其他协定，也未就此作出任何相保、承诺、声明或保证，包括对本服务及相关单项服务或功能的完整性、准确性、及时性或可靠性等。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2 在法律允许的最大范围内，对于您或其他任何人因下列原因无法访问或使用本服务导致的任何损失，德尚网络不承担任何责任：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2.1 任何由于黑客攻击，电脑病毒的侵入，非法内容信息、骚扰信息的屏蔽，政府管制以及其他任何网络、技术、通信线路、信息安全管理措施等原因造成的服务中断、延迟、受阻等情形；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2.2 因第三方如运营商的通讯线路故障、技术问题、网络、电脑故障、系统不稳定及其他原因等造成的损失的情形；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2.3 德尚网络对系统或软硬件进行维护或升级导致本服务中止或终止；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2.4 因台风、地震、停电、洪水、战争、恐怖袭击等不可抗力因素导致的服务中断；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.2.5 由于法律法规的变更、司法机关及行政机关等的命令、裁定等原因而导致的本服务中断、延迟或终止等情形。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.3 本服务仅供您个人使用，未经德尚网络同意，您直接或间接授权第三方使用您德尚网络账号或获取您账号项下信息导致的任何损失，均由您自行承担，德尚网络不承担任何责任。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.4 您可以选择使用您已有的第三方账号关联德尚网络账号，德尚网络对于因第三方原因对您造成的损失不承担任何责任。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.5 我们依法律、诉讼、司法机关或政府机关的要求或其他合理必要的情况下，可能访问、使用、保存、和/或披露您的账号信息和内容。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.6 由于您的使用不当或其他自身原因而导致任何与您相关的个人信息的泄露，德尚网络对此不承担任何责任。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.7 使用德尚网络产品、程序及服务可能存在的来自他人匿名或冒名的含有威胁、诽谤、令人反感或非法内容的信息而招致的风险，或用户之间通过本服务与其他用户交流，因受误导或欺骗而导致或可能导致的任何心理、生理上的伤害以及经济上的损失，均由侵权方承担所有责任，德尚网络对此不承担任何责任。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.8 您在利用德尚网络账号所发布的任何内容并不代表和反映德尚网络的任何观点或政策，德尚网络对此不承担任何责任。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;5.9 在任何情况下，德尚网络均不对任何间接性、后果性、惩罚性、偶然性、特殊性或刑罚性的损害，包括因您使用德尚网络服务而遭受的利润损失，承担责任。您同意，我们对您承担的全部责任，无论因何原因或何种行为方式，始终不超过您在协议有效期内因使用德尚网络服务而支付给德尚网络的费用（如有）。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;六、知识产权&lt;/h2&gt;&lt;p&gt;6.1 您使用德尚网络账号在任何平台发布的信息不得侵犯任何第三人的知识产权，未经具有相关所有权人之事先书面同意，您不得以任何方式上传、发布、修改、传播或复制任何受著作权保护的材料、商标或属于其他人的专有信息。如果收到任何著作权人或其合法代表发给德尚网络的适当通知后，我们将在审查的基础上移除该等侵犯他人著作权的内容。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;6.2 您在使用本服务时利用德尚网络账号发表、上传的文字、图片、视频、软件以及表演等原创信息的知识产权归您所有，但是您确认您对该等信息的发表、上传行为视同为对德尚网络非独占地、永久地、不可撤销地授予该等信息相关全部知识产权的使用、复制等权利，并且您同意德尚网络可转授权上述权利。您同意您不会向德尚网络提供任何您视为保密或专有的信息或资料。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;七、修订&lt;/h2&gt;&lt;p&gt;7.1 鉴于互联网相关技术、市场、业务模式等发展迅速，您同意，德尚网络有权随时根据中华人民共和国有关法律、法规的变化、互联网的发展以及公司经营状况和经营策略的调整等不定时地制定、修改本协议及各类规则。更新后的协议、规则经合理的公示期后即生效，并代替原来的协议、规则。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;7.2 我们将以合适的方式通知您阅读新的协议，您可以在相关服务页面查阅最新版本的协议条款。若修订后的协议、规则会严重影响或限制您继续使用本服务的，我们将通过弹窗、网页显著位置公示、短信等方式获取您的同意。如您不同意修订后的相关条款，则请您立即停止使用本协议下各项服务。您继续登录德尚网络账号或继续使用德尚网络各单项服务的行为，视为您认可并接受经修订的协议、规则。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;7.3 我们可能会依据法律的规定，保留您使用德尚网络服务、或者德尚网络账号的权利；无论是否通知，我们将在任何时间以任何原因终止本协议，包括出于善意地相信您违反了我们可接受使用政策或本协议的其他规定。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;7.4 不受前款规定所限，如果您侵犯第三人的著作权且德尚网络接到著作权人或著作权人的合法代理人的通知后，德尚网络保留终止本协议的权利。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;八、服务终止&lt;/h2&gt;&lt;p&gt;8.1 如果您存在违反法律法规规定及本协议约定情形的，我们有权中止、限制或终止该账号的使用，停止向您提供服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.2 我们有权根据自身经营的实际需要，在合理期限内暂时或永久终止德尚网络账号服务（或任何一部分），您应及时备份该账号中存储的所有数据及信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.3 我们可能会依法律规定、诉讼裁决、司法机关或政府机关的要求终止为您提供德尚网络账号服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4 若属于上述条款中服务终止的情形，我们会以平台公告、短信、邮件或其他方式尽可能通知您。但存在以下情形时，我们可能在不事先通知您的情况下立即中止、限制或终止您访问全部或部分本服务：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4.1 您违反本协议或德尚网络有理由认为您即将违反本协议；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4.2 您或代表您行事的任何人进行欺诈或违法活动，或向德尚网络提供虚假或误导信息；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4.3 依照有效法律程序响应执法部门或政府机关提出的要求；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4.4 执行紧急的系统或硬件维护更新工作；&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.4.5 因无法预料的技术、安全或业务原因。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;8.5 如果您的德尚网络账号发布的信息内容存在侵犯第三人的知识产权的情形，且德尚网络接到知识产权人或知识产权人的合法代理人的通知，德尚网络保留终止本协议的权利。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;九、个人信息保护&lt;/h2&gt;&lt;p&gt;9.1 尊重用户隐私并保护您的个人信息安全是我们的一贯态度，德尚网络将会采取合理的措施保护您的个人信息与隐私。我们承诺，除非获得用户同意，德尚网络不会收集、使用其提供服务所必需以外的用户个人信息或者将信息用于提供服务之外的目的。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;9.2 我们将采取法律法规以及约定的必要安全措施，以尽到信息安全保护义务。但请您知悉并理解，我们无法对相关第三方的信息安全保护能力或其处理行为做超出能力范围的保证。如您对相关第三方收集、提供、存储、使用您的个人信息有任何疑问，或决定行使法律法规规定的权利，您可以直接联系该第三方处理。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;9.3 在遵守本协议项下特别约定的个人信息保护与隐私政策外，我们希望您认真并完整阅读《德尚网络账号隐私政策》，这将更有助于保障您的个人信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;十、适用法律与争议解决&lt;/h2&gt;&lt;p&gt;10.1 本协议适用中华人民共和国法律。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;10.2 如果双方发生纠纷，应本着友好的原则协商解决；如协商不成，应向北京市海淀区人民法院提起诉讼。若单项条款与本协议在管辖约定内容上存在冲突，则在单项条款约束范围内应以单项条款为准。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;十一、其他&lt;/h2&gt;&lt;p&gt;11.1 协议生效&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;本协议自您接受之日起生效，在您使用本服务的过程中持续有效，直至依据本协议终止。如果您实际使用本服务的时间早于您接受本协议的时间，您在此知晓或应当知晓并同意本协议于您第一次使用本服务时生效，除非依据本协议提前终止。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;11.2 通知&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;在注册德尚网络账号时，您应该向德尚网络提供真实有效的联系方式（包括您的电子邮件地址、手机号码等），对于联系方式发生变更的，您有义务及时更新有关信息，并保持可被联系的状态。德尚网络将向您的上述联系方式的其中之一或其中若干送达各类通知，而此类通知的内容可能对您的权利义务产生重大的有利或不利影响，请您务必及时关注。您所提供的电子邮件地址或手机号码无法使用或者因任何原因我们无法将通知送达给您而产生的风险，由您自行承担。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;11.3 独立性&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;如本协议中的任一条款被认定为或视为废止、无效、不可执行，其余条款仍具有法律效力，对双方均具有约束力。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;11.4 权利义务的转让&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;未经我们事先书面许可，您不得转让或以其它方式转移您在本协议下的任何权利或义务。任何未受许可的转让或转移都将视为无效。出于业务升级或运营主体发生变化等情况，德尚网络有权转让、分包或更替德尚网络在本协议下的权利和义务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1435672310),
(2, 'privacy', '用户隐私政策', '&lt;p&gt;请花一些时间熟悉我们的隐私政策，如果您有任何问题，请联系我们。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;引言&lt;/h2&gt;&lt;p&gt;德尚网络账号是由德尚网络科技有限责任公司及其关联公司（以下简称“德尚网络”或“我们”）为您提供的一项账号注册、登录和管理服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;我们非常重视您的个人信息保护及隐私安全。《德尚网络账号隐私政策》（以下简称“本隐私政策”）是德尚网络针对德尚网络公司德尚网络账号做出的隐私方面的陈述与承诺，属于《德尚网络隐私政策》的重要组成部分。涉及到德尚网络账号特殊约定的，若与《德尚网络隐私政策》之间存在不一致的，以本隐私政策为准；若本隐私政策未约定的，以《德尚网络隐私政策》为准。特别是关于“安全措施、跨境传输”等部分的约定，建议您详细阅读《德尚网络隐私政策》。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;本隐私政策在制定时充分考虑到您的需求，您全面了解我们的个人信息收集和使用惯例，同时确保您最终能控制提供给我们的个人信息，这一点至关重要。本隐私政策规定我们如何收集、使用、披露、处理和存储您使用我们的德尚网络账号时提供给我们的信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;注意，您登录德尚网络账号后使用对应产品或服务的个人信息收集使用规则请查看该产品或服务的隐私政策。本隐私政策下“个人信息”指通过信息本身或通过关联其他信息后能够识别特定个人的信息数据。我们将严格遵守本隐私政策来处理使用这些信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;最后，我们希望为用户带来最好的体验。如果您对本隐私政策中描述的个人信息处理实践有任何疑问，请联系我们，以便我们处理您的特殊需求。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;一、我们收集哪些信息以及如何使用信息&lt;/p&gt;&lt;p&gt;（一）您须授权我们收集和使用您个人信息的情形&lt;/p&gt;&lt;p&gt;收集个人信息的目的在于向您提供适用于德尚网络账号的产品和/或服务，并且保证我们遵守适用的相关法律、法规及其他规范性文件。您有权自行选择是否提供该信息，但多数情况下，如果您不提供，我们可能无法向您提供相应的服务，也无法回应您遇到的问题。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;德尚网络仅会出于本隐私政策所述目的收集和使用您提供的个人信息。我们出于提供如下服务功能目的时，可能会以相关特定方式处理您的相关个人信息，这些功能包括：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;1、提供账号基础功能&lt;/h2&gt;&lt;h3&gt;（1）注册或登录&lt;/h3&gt;&lt;p&gt;您在注册时需要向我们提供手机号码或邮箱地址作为您的德尚网络账号名称。同时，您还可以为您的德尚网络账号设置密码。您的账号和密码会将被加密保存在服务器，建议您妥善保管好您的密码信息，谨防您的德尚网络账号被他人盗用。&lt;/p&gt;&lt;p&gt;在您成功注册德尚网络账号后，您可以随时使用德尚网络账号登录德尚网络各类系统应用或服务。为了实现同一设备上所有支持德尚网络账号登录的系统应用或服务能够准确接入德尚网络账号服务，判断德尚网络系统应用或服务的账号登录状态，我们需要获取已安装的应用列表信息。&lt;/p&gt;&lt;p&gt;当您登录德尚网络账号后，德尚网络账号在您使用德尚网络系统应用或服务时开启了自动登录功能，以为您提供流畅的服务体验，这些应用或服务会收集您的德尚网络账号信息以为您提供登录服务。&lt;/p&gt;&lt;p&gt;为提供便捷的登录体验，在注册或登录时，您可以选择自动填写您的手机号码。为此我们需要获取您的SIM卡IMSI、ICCID、设备型号、系统版本信息、联网状态。上述功能可能需要您在您的设备中向我们开启您的网络权限、WIFI状态权限以实现这些功能所涉及的信息的收集和使用。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（2）完善账号信息&lt;/h3&gt;&lt;p&gt;您在使用德尚网络提供的各类服务过程中，您可以通过完善头像、昵称、性别等基础信息，以及设置密保问题，来获得更好的服务与体验，如不提供也不会影响德尚网络账号的基本功能。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;2、保障注册、登录安全所必须的功能&lt;/h3&gt;&lt;p&gt;为提高您使用我们的产品与/或服务时系统的安全性，更准确地预防钓鱼网站欺诈和保护账号安全，我们还会通过短信、邮箱、密码、密保来验证您的身份。&lt;/p&gt;&lt;p&gt;出于风控的目的，我们还会收集您的注册/登录时间、设备相关信息（如设备IMEI/（在Android Q上的）OAID、设备型号、操作系统版本）、设备传感器信息（如手机角速度、手机加速度）、蓝牙地址、网络信息（如Wi-Fi SSID、常用登录IP地址），为此我们需要获取您的设备信息权限，收集您的设备IMEI/OAID，以保障您注册或登录环境的安全。此外，我们还会将您的手机号码提供给第三方（如手机号码风险等级库、运营商的二次号检查服务）进行查验，以保障您的账号安全。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（二）您可选择是否授权我们收集和使用您的个人信息的情形&lt;/h3&gt;&lt;p&gt;为提升您使用德尚网络账号的体验，我们的以下附加功能中可能会收集和使用您的个人信息。如果您不提供这些个人信息，您依然可以使用德尚网络账号，但您可能无法使用这些附加功能。这些附加功能包括：&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1、家人服务&lt;/p&gt;&lt;p&gt;您可以通过家人服务功能邀请您的家人、朋友共享德尚网络的服务、内容及购买项目。为此，我们会收集您的德尚网络ID、被邀请人的德尚网络ID/手机号码。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2、未成年人账号&lt;/p&gt;&lt;p&gt;您可以为您的未成年子女创建专属的德尚网络未成年人账号，您需要授权我们获取您的未成年子女的账号昵称、出生日期信息；我们会记录您的未成年子女的出生日期，在您的未成年子女年满十八周岁时及时更正账号状态。此外，您还需要提供给我们您用来绑定未成年人账号的手机号码（未注册过德尚网络账号）信息，以实现后续的登录、账号安全与验证等操作。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（三）您充分知晓，以下情形中，我们收集、使用个人信息无需征得您的同意：&lt;/h3&gt;&lt;p&gt;1、与国家安全、国防安全有关的；&lt;/p&gt;&lt;p&gt;2、与公共安全、公共卫生、重大公共利益有关的；&lt;/p&gt;&lt;p&gt;3、与犯罪侦查、起诉、审判和判决执行等有关的；&lt;/p&gt;&lt;p&gt;4、出于维护个人信息主体或其他个人的生命、财产等重大合法权益但又很难得到本人同意的；&lt;/p&gt;&lt;p&gt;5、所收集的个人信息是个人信息主体自行向社会公众公开的；&lt;/p&gt;&lt;p&gt;6、从合法公开披露的信息中收集的您的个人信息的，如合法的新闻报道、政府信息公开等渠道；&lt;/p&gt;&lt;p&gt;7、根据您的要求签订合同所必需的；&lt;/p&gt;&lt;p&gt;8、用于维护所提供的产品与/或服务的安全稳定运行所必需的，例如发现、处置产品与/或服务的故障；&lt;/p&gt;&lt;p&gt;9、为合法的新闻报道所必需的；&lt;/p&gt;&lt;p&gt;10、学术研究机构基于公共利益开展统计或学术研究所必要，且对外提供学术研究或描述的结果时，对结果中所包含的个人信息进行去标识化处理的。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（四）我们从第三方获得您个人信息的情形&lt;/h3&gt;&lt;p&gt;在一些法律允许的情况下，我们可能从第三方处获得您的个人信息。例如，您授权第三方账号绑定德尚网络账号并登录使用德尚网络的服务或授权导入的您在第三方平台的信息如头像、昵称；出于风控的目的，我们会通过第三方（如手机号码风险等级库、运营商的二次号检查服务）来查验您手机号码的安全情况，以了解您当前注册/登录环境是否安全；在您使用一键登录服务时从运营商处获取您的手机号码；您的家人、朋友邀请您使用共享相关的服务时输入您的德尚网络ID和手机号码。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（五）非个人信息&lt;/h3&gt;&lt;p&gt;我们还可能收集其他无法识别到特定个人的信息（即不属于个人信息的信息），例如您使用特定服务时产生的统计类数据，如您使用德尚网络账号服务时的登录/退出记录、交互记录、错误记录。收集此类信息的目的在于改善我们向您提供的服务。所收集信息的类别和数量取决于您如何使用我们产品和/或服务。我们会将此类信息汇总。就本隐私政策而言，汇总数据被视为非个人信息。如果我们将非个人信息与个人信息结合使用，则在结合使用期间，此类信息将被视为个人信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;二、我们如何共享、转让、公开披露您的个人信息&lt;/h2&gt;&lt;h3&gt;（一）共享：&lt;/h3&gt;&lt;p&gt;我们不会将任何个人信息出售给第三方。&lt;/p&gt;&lt;p&gt;我们有时可能会向第三方（定义见下文）共享您的个人信息，以便提供或改进我们的产品或服务，包括根据您的要求提供产品或服务。如果您不再希望允许我们共享这些信息，请联系我们。&lt;/p&gt;&lt;p&gt;1、与国家安全、国防安全有关的； 您主动选择的共享&lt;/p&gt;&lt;p&gt;在获得您的明确同意或主动选择的情况下，向您指定的第三方共享您授权范围内的信息。&lt;/p&gt;&lt;p&gt;2、与我们集团共享信息&lt;/p&gt;&lt;p&gt;为了顺利地从事商业经营，以向您提供产品或服务的全部功能，我们可能不时向其他的德尚网络关联公司共享您的个人信息。&lt;/p&gt;&lt;p&gt;3、与第三方服务提供商共享您的个人信息&lt;/p&gt;&lt;p&gt;当您在第三方网站/应用使用德尚网络账号登录时，我们会获取您的授权，授权页面上会展示具体的授权对象以及授权信息类型，在您授权同意后，我们会将您的授权信息（取决于该第三方的服务和产品性质）传给该第三方。若您不同意该第三方获得您的个人信息，请您不要同意授权。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;本业务中部分服务内容由第三方服务提供商提供，为此我们需要向第三方服务提供商提供您的部分个人信息，[点击查看]我们向关联方、合作伙伴及第三方共享您的个人信息的情形。如我们与这些第三方分享您的个人信息，我们将采取加密等手段保障您的信息安全。对我们与之共享个人信息的公司、组织，我们会对其数据安全环境进行合理审查，并与其签署严格的数据处理协议，要求第三方对您的信息采取足够的保护措施，严格遵守相关法律法规与监管要求。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（二）转让&lt;/h3&gt;&lt;p&gt;我们不会将您的个人信息转让给任何公司、组织和个人，但以下情况除外：&lt;/p&gt;&lt;p&gt;1、在获取明确同意的情况下转让：获得您的明确同意后，我们会向其他方转让您的个人信息。&lt;/p&gt;&lt;p&gt;2、在涉及合并、分立、解散、被宣告破产、收购等原因需要转移您的个人信息时，我们将向您告知接收方的名称和联系方式。我们将要求接收方继续履行个人信息处理者的义务。接收方变更本隐私政策约定的处理目的、处理方式的，应当依照规定重新取得您的个人同意。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h3&gt;（三）公开披露&lt;/h3&gt;&lt;p&gt;我们仅会在以下情况下，公开披露您的个人信息：&lt;/p&gt;&lt;p&gt;1、获得您明确同意后；&lt;/p&gt;&lt;p&gt;2、基于法律的披露：在法律、法律程序、诉讼或政府主管部门强制性要求的情况下，我们可能会对上述监管部门披露您的个人信息。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;四、保留政策&lt;/h2&gt;&lt;p&gt;我们基于本隐私政策中所述的信息收集的目的所必需的期间，或者遵守适用的相关法律要求保留个人信息。个人信息在完成收集目的，或在我们确认您的删除或注销申请后，或我们终止运营相应产品或服务后，我们将停止保留，并做删除或匿名化处理。如果是出于公众利益、科学、历史研究或统计的目的，我们将基于适用的法律继续保留相关数据，即使进一步的数据处理与原有的收集目的无关。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;五、我们如何保护未成年人的个人信息安全&lt;/h2&gt;&lt;p&gt;我们重视并关注未成年人的个人信息保护。我们不会在明知且未经授权的情况下收集未成年人个人信息，如果我们发现在未事先获得可证实的父母或监护人同意的情况下收集了未成年人的个人信息，则会设法尽快删除相关信息。但请您知悉并同意，在某些特定场景和功能中（如创建未成年人账号），您需要授权我们获取您的未成年子女的部分个人信息。如果您的未成年子女为14周岁以下的儿童，关于德尚网络对于儿童个人信息的保护规则，请您点击查阅《德尚网络账号儿童信息保护规则》。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;六、第三方服务说明&lt;/h2&gt;&lt;p&gt;德尚网络的产品或服务中可能含有第三方网站、产品与服务的链接。德尚网络的产品或服务也可能使用或提供来自第三方的产品或服务，如德尚网络应用商店内内上架的第三方应用。所有含有第三方网站、产品与服务的链接仅为方便用户而提供。请您注意，该第三方可能会处理您的相关信息，建议您在使用第三方服务前，仔细阅读、充分理解其相关的个人信息保护规则后再使用第三方的产品或服务。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;h2&gt;七、本隐私政策如何更新&lt;/h2&gt;&lt;p&gt;我们会根据业务和技术的变更，根据适用法律，依循良好做法，定期审核隐私政策，并可能更新本隐私政策。如果我们对本隐私政策进行重大变更，我们将通过系统内弹窗等合理的方式通知您，这样您可以了解我们收集的信息以及我们如何使用这些信息。此类隐私政策将从规定的生效日期开始适用。我们建议您定期查阅本页面以获取我们个人信息处理实践的最新信息。您继续使用德尚网络账号并登录德尚网络相关系统应用或服务的行为，将被视为接受更新的隐私政策。在我们向您收集更多的个人信息或我们因为新的目的使用或披露您的个人信息时，我们会根据适用法律要求，再次征得您的同意。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1435672310),
(3, 'open_store', '开店协议', '&lt;h2 style=&quot;padding: 0px; margin: 0px; font-size: 16px; font-weight: normal; color: rgb(51, 51, 51); line-height: 30px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;1.本协议的订立&lt;/span&gt;&lt;/h2&gt;&lt;p class=&quot;cont&quot; style=&quot;padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 28px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;在本网站（&lt;/span&gt;&lt;a target=&quot;_blank&quot; href=&quot;http://www.ecmoban.com/&quot; class=&quot;link&quot; style=&quot;padding: 0px; margin: 0px; text-decoration: underline; color: rgb(85, 85, 85); font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;dsmall.csdeshang.com&lt;/span&gt;&lt;/a&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;）依据《德尚商城网站用户注册协议》登记注册，且符合本网站 商家入驻标准的用户（以下简称&amp;quot;商 家&amp;quot;），在同意本协议全部条款后，方有资格使用&amp;quot;德尚商城商家在线入驻系统&amp;quot;（以 下简称&amp;quot;入驻系统&amp;quot;）申请入驻。一经商家点击&amp;quot;同意以上协议，下一步&amp;quot;按键， 即意味着商家同意与本网站签订本协议并同意受本协议约束。&lt;/span&gt;&lt;/p&gt;&lt;h2 style=&quot;padding: 0px; margin: 0px; font-size: 16px; font-weight: normal; color: rgb(51, 51, 51); line-height: 30px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;2.入驻系统使用说明&lt;/span&gt;&lt;/h2&gt;&lt;p class=&quot;cont&quot; style=&quot;padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 28px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;2.1 商家通过入驻系统提出入驻申请，并按照要求填写商家信息、提供商家资质资料后，由 本网站审核并与有合作意向的商家联系协商合作相关事宜，经双方协商一致线下签订书面《开放平台 供应商合作运营协议》（以下简称&amp;quot;运营协议&amp;quot;），且商家按照&amp;quot;运营协议&amp;quot;约定 支付相应平台使用费及保证金等必要费用后，商家正式入驻本网站。本网站将为入驻商家开通商家后 台系统，商家可通过商家后台系统在本网站运营 自己的入驻店铺。&lt;/span&gt;&lt;/p&gt;&lt;p class=&quot;cont&quot; style=&quot;padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 28px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;2.2 商家以及本网站通过入驻系统做出的申请、资料提交及确认等各类沟通，仅为双方合作 的意向以及本网站对商家资格审核的必备程序，除遵守本协议各项约定外，对双方不产生法律约束力 。双方间最终合作事宜及运营规则均以&amp;quot;运营协议&amp;quot;的约定及商家后台系统公示的各项规则 为准。&lt;/span&gt;&lt;/p&gt;&lt;h2 style=&quot;padding: 0px; margin: 0px; font-size: 16px; font-weight: normal; color: rgb(51, 51, 51); line-height: 30px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;3.商家权利义务&lt;/span&gt;&lt;/h2&gt;&lt;p style=&quot;padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 28px;&quot;&gt;&lt;span style=&quot;font-family: 宋体, SimSun; font-size: 12px;&quot;&gt;用户使用&amp;quot;德尚商城商家在线入驻系统&amp;quot;前请认真阅读并理解本协议内容，本协议 内容中以加粗方式显著标识的条款，请用户着重阅读、慎重考虑。&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1435672310);

INSERT INTO `#__help` (`help_id`, `help_sort`, `help_title`, `help_info`, `help_url`, `help_updatetime`, `helptype_id`, `page_show`) VALUES
(96, 1, '招商方向', '后台->机构->开店首页->入驻指南->编辑内容', '', 1399284217, 1, 1),
(97, 2, '招商标准', '后台->机构->开店首页->入驻指南->编辑内容', '', 1399281053, 1, 1),
(98, 3, '资质要求', '后台->机构->开店首页->入驻指南->编辑内容', '', 1399282350, 1, 1),
(99, 4, '资费标准', '后台->机构->开店首页->入驻指南->编辑内容', '', 1399282379, 1, 1),
(101, 155, '签署入驻协议', '签署入驻协议(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392949932, 99, 1),
(102, 156, '机构信息提交', '机构信息提交(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392949961, 99, 1),
(103, 157, '平台审核资质', '平台审核资质(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392949991, 99, 1),
(104, 158, '机构缴纳费用', '机构缴纳费用(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950031, 99, 1),
(105, 159, '机构开通', '机构开通(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950076, 99, 1),
(106, 255, '商品发布规则', '商品发布规则(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950396, 91, 1),
(107, 255, '商品规格及属性', '商品规格及属性(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950481, 92, 1),
(108, 255, '限时折扣说明', '限时折扣说明(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950523, 93, 1),
(109, 255, '订单商品退款', '订单商品退款(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950670, 94, 1),
(110, 255, '续约流程及费用', '续约流程及费用(后台->机构->机构帮助->帮助内容->编辑内容)', '', 1392950739, 95, 1);

INSERT INTO `#__helptype` (`helptype_id`, `helptype_name`, `helptype_sort`, `helptype_code`, `helptype_show`, `page_show`) VALUES
  (91, '规则体系', 91, 'sys_rules', 1, 1),
  (92, '商品管理', 92, 'sys_goods', 1, 1),
  (93, '促销方式', 93, 'sys_sales', 1, 1),
  (94, '订单及售后', 94, 'sys_order', 1, 1),
  (95, '机构续约', 95, 'sys_renew', 1, 1),
  (99, '入驻流程', 99, 'store_in', 1, 1);

INSERT INTO `#__informsubject` (`informsubject_id`, `informsubject_content`, `informsubject_type_id`, `informsubject_type_name`, `informsubject_state`) VALUES (1, '管制刀具、弓弩类、其他武器等', 1, '出售禁售品', 1),
(2, '赌博用具类', 1, '出售禁售品', 1),
(3, '枪支弹药', 1, '出售禁售品', 1),
(4, '毒品及吸毒工具', 1, '出售禁售品', 1),
(5, '色差大，质量差。', 2, '产品质量问题', 1);

INSERT INTO `#__informsubjecttype` (`informtype_id`, `informtype_name`, `informtype_desc`, `informtype_state`) VALUES (1, '出售禁售品', '销售商城禁止和限制交易规则下所规定的所有商品。', 1),
(2, '产品质量问题', '产品质量差，与描述严重不相符。', 1);

INSERT INTO `#__mailmsgtemlates` (`mailmt_name`, `mailmt_title`, `mailmt_code`, `mailmt_content`) VALUES
('<strong>[用户]</strong>账号注册通知', '账号注册通知 - ${site_name}', 'register', '您正在申请注册会员，动态码：${code}。'),
('<strong>[用户]</strong>账号登录通知', '账号登录通知 - ${site_name}', 'login', '您正在申请登录，动态码：${code}。'),
('<strong>[用户]</strong>重置密码通知', '重置密码通知 - ${site_name}', 'reset_password', '您正在申请重置登录密码，动态码：${code}。'),
('<strong>[用户]</strong>身份验证通知', '账户安全认证 - ${site_name}', 'authenticate', '您正在提交账户安全验证，验证码是：${code}。'),
('<strong>[用户]</strong>邮箱验证通知', '邮箱验证通知 - ${site_name}', 'bind_email', '<p>您好！</p>\r\n<p>请在24小时内点击以下链接完成邮箱验证</p>\r\n<p><a href="${verify_url}" target="_blank">马上验证</a></p>\r\n<p>如果您不能点击上面链接，还可以将以下链接复制到浏览器地址栏中访问</p>\r\n<p>${verify_url}</p>'),
('<strong>[用户]</strong>手机验证通知', '手机验证通知 - ${site_name}', 'modify_mobile', '您正在申请绑定手机号，验证码是：${code}。');

INSERT INTO `#__mallconsulttype` (`mallconsulttype_id`, `mallconsulttype_name`, `mallconsulttype_introduce`, `mallconsulttype_sort`) VALUES
(1, '商品咨询', '&lt;p&gt;\r\n	请写明商品链接，或平台货号。\r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	如果您对商品规格、介绍等有疑问，可以在商品详情页“购买咨询”处发起咨询，会得到及时专业的回复。\r\n&lt;/p&gt;', 255),
(2, '订单咨询', '&lt;p&gt;\r\n	请写明要咨询的订单编号。\r\n&lt;/p&gt;\r\n&lt;p&gt;\r\n	如需处理交易中产生的纠纷，请选择投诉。\r\n&lt;/p&gt;', 255);

INSERT INTO `#__membermsgtpl` (`membermt_code`, `membermt_name`, `membermt_message_switch`, `membermt_message_content`, `membermt_short_switch`, `membermt_short_content`, `membermt_mail_switch`, `membermt_mail_subject`, `membermt_mail_content`) VALUES
('consult_goods_reply', '商品咨询回复提醒', 1, '您关于商品 “${goods_name}”的咨询，机构已经回复。<a href="${consult_url}" target="_blank">点击查看回复</a>', 0, '您关于商品 “${goods_name}” 的咨询，机构已经回复。', 0, '${site_name}提醒：您关于商品 “${goods_name}”的咨询，机构已经回复。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您关注的商品“${goods_name}” 已经到货。\r\n</p>\r\n<p>\r\n	<a href="${consult_url}" target="_blank">点击查看回复</a> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<br />\r\n<div class="firebugResetStyles firebugBlockBackgroundColor">\r\n	<div style="background-color:transparent ! important;" class="firebugResetStyles">\r\n	</div>\r\n</div>'),
('consult_mall_reply', '平台客服回复提醒', 1, '您的平台客服咨询已经回复。<a href="${consult_url}" target="_blank">点击查看回复</a>', 0, '您的平台客服咨询已经回复。', 0, '${site_name}提醒：您的平台客服咨询已经回复。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的平台客服咨询已经回复。\r\n</p>\r\n<p>\r\n	<a href="${consult_url}" target="_blank">点击查看回复</a> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>'),
('order_deliver_success', '商品出库提醒', 1, '您的订单已经出库。<a href="${order_url}" target="_blank">点击查看订单</a>', 1, '您的订单已经出库。订单编号 ${order_sn}。', 0, '${site_name}提醒：您的订单已经出库。订单编号 ${order_sn}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的订单已经出库。订单编号 ${order_sn}。<br />\r\n<a href="${order_url}" target="_blank">点击查看订单</a>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<br />'),
('order_payment_success', '付款成功提醒', 1, '关于订单：${order_sn}的款项已经收到，请留意出库通知。<a href="${order_url}" target="_blank">点击查看订单详情</a>', 1, '${order_sn}的款项已经收到，请留意出库通知。', 0, '${site_name}提醒：${order_sn}的款项已经收到，请留意出库通知。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	${order_sn}的款项已经收到，请留意出库通知。\r\n</p>\r\n<p>\r\n	<a href="${order_url}" target="_blank">点击查看订单详情</a>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<br />'),
('predeposit_change', '余额变动提醒', 1, '你的账户于 ${time} 账户资金有变化，描述：${desc}，可用金额变化 ：${av_amount}元，冻结金额变化：${freeze_amount}元。<a href="${pd_url}" target="_blank">点击查看余额</a>', 0, '你的账户于 ${time} 账户资金有变化，描述：${desc}，可用金额变化： ${av_amount}元，冻结金额变化：${freeze_amount}元。', 0, '${site_name}提醒：你的账户于 ${time} 账户资金有变化，描述：${desc}，可用金额变化： ${av_amount}元，冻结金额变化：${freeze_amount}元。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	你的账户于 ${time} 账户资金有变化，描述：${desc}，可用金额变化：${av_amount}元，冻结金额变化：${freeze_amount}元。\r\n</p>\r\n<p>\r\n	<a href="${pd_url}" target="_blank">点击查看余额</a> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>'),
('recharge_card_balance_change', '充值卡余额变动提醒', 1, '你的账户于 ${time} 充值卡余额有变化，描述：${description}，可用充值卡余额变化 ：${available_amount}元，冻结充值卡余额变化：${freeze_amount}元。<a href="${url}" target="_blank">点击查看充值卡余额</a>', 0, '你的账户于 ${time} 充值卡余额有变化，描述：${description}，可用充值卡余额变化： ${available_amount}元，冻结充值卡余额变化：${freeze_amount}元。', 0, '${site_name}提醒：你的账户于 ${time} 充值卡余额有变化，描述：${description}，可用充值卡余额变化： ${available_amount}元，冻结充值卡余额变化：${freeze_amount}元。', '<p>\r\n    ${site_name}提醒：\r\n</p>\r\n<p>\r\n  你的账户于 ${time} 充值卡余额有变化，描述：${description}，可用充值卡余额变化：${available_amount}元，冻结充值卡余额变化：${freeze_amount}元。\r\n</p>\r\n<p>\r\n  <a href="${url}" target="_blank">点击查看余额</a> \r\n</p>\r\n<p>\r\n  <br />\r\n</p>\r\n<p>\r\n   <br />\r\n</p>\r\n<p>\r\n   <br />\r\n</p>\r\n<p style="text-align:right;">\r\n ${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n   ${mail_send_time}\r\n</p>'),
('refund_return_notice', '退款退货提醒', 1, '您的退款退货单有了变化。<a href="${refund_url}" target="_blank">点击查看</a>', 1, '您的退款退货单有了变化。退款退货单编号：${refund_sn}。', 0, '${site_name}提醒：您的退款退货单有了变化。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的退款退货单有了变化。退款退货单编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	&lt;a href="${refund_url}" target="_blank"&gt;点击查看&lt;/a&gt;\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<br />'),
('voucher_use', '代金券使用提醒', 1, '您有代金券已经使用，代金券编号：${voucher_code}。<a href="${voucher_url}" target="_blank">点击查看</a>', 0, '您有代金券已经使用，代金券编号：${voucher_code}。', 0, '${site_name}提醒：您有代金券已经使用，代金券编号：${voucher_code}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有代金券已经使用，代金券编号：${voucher_code}。\r\n</p>\r\n<p>\r\n	&lt;a href="${voucher_url}" target="_blank"&gt;点击查看&lt;/a&gt;\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>'),
('voucher_will_expire', '代金券即将到期提醒', 1, '您有一个代金券即将在${indate}过期，请记得使用。<a href="${voucher_url}" target="_blank">点击查看</a>', 0, '您有一个代金券即将在${indate}过期，请记得使用。', 0, '${site_name}提醒：您有一个代金券即将在${indate}过期，请记得使用。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有一个代金券即将在${indate}过期，请记得使用。\r\n</p>\r\n<p>\r\n	<a href="${voucher_url}" target="_blank">点击查看</a> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>');

INSERT INTO `#__seo` (`seo_id`, `seo_title`, `seo_keywords`, `seo_description`, `seo_type`) VALUES (1, '{sitename} - 程序来源于德尚网络', 'DSKMS,PHP商城系统,DSKMS商城系统,多用户商城系统,电商ERP,电商CRM,电子商务解决方案', '', 'index'),
(2, '{sitename} - 抢购', 'DSKMS,{sitename}', '', 'group'),
(3, '{sitename} - {name}', 'DSKMS,{name},{sitename}', 'DSKMS,{name},{sitename}', 'group_content'),
(7, '{sitename} - {name}', 'DSKMS,{sitename},{name}', 'DSKMS,{sitename},{name}', 'coupon_content'),
(8, '{sitename} - 积分商城', 'DSKMS,{sitename}', 'DSKMS,{sitename}', 'point'),
(9, '{sitename} - {name}', 'DSKMS,{sitename},{key}', 'DSKMS,{sitename},{description}', 'point_content'),
(10, '{sitename} - {article_class}', 'DSKMS,{sitename}', 'DSKMS,{sitename}', 'article'),
(11, '{sitename} - {name}', 'DSKMS,{sitename},{key}', 'DSKMS,{sitename},{description}', 'article_content'),
(12, '{sitename} - {shopname}', 'DSKMS,{sitename},{key}', 'DSKMS,{sitename},{description}', 'shop'),
(13, '{name} - {sitename}', 'DSKMS,{sitename},{key}', 'DSKMS,{sitename},{description}', 'product'),
(14, '看{name}怎么淘到好的宝贝-{sitename}', 'DSKMS,{sitename},{name}', 'DSKMS,{sitename},{name}', 'sns');

INSERT INTO `#__storeclass` (`storeclass_id`, `storeclass_name`, `storeclass_bail`, `storeclass_sort`) VALUES
(1, '珠宝/首饰', 0, 8),
(2, '服装鞋包', 0, 1),
(3, '3C数码', 0, 2),
(4, '美容护理', 0, 3),
(5, '家居用品', 0, 4),
(6, '食品/保健', 0, 5),
(7, '母婴用品', 0, 6),
(8, '文体/汽车', 0, 7),
(9, '收藏/爱好', 0, 9),
(10, '生活/服务', 0, 10);

INSERT INTO `#__storemsgtpl` (`storemt_code`, `storemt_name`, `storemt_message_switch`, `storemt_message_content`, `storemt_message_forced`, `storemt_short_switch`, `storemt_short_content`, `smt_short_forced`, `storemt_mail_switch`, `storemt_mail_subject`, `storemt_mail_content`, `storemt_mail_forced`) VALUES
('goods_verify', '商品审核失败提醒', 1, '您的商品没有通过管理员审核，原因：“${remark}”。平台货号：${goods_id}。', 1, 0, '您的商品没有通过管理员审核，原因：“${remark}”。平台货号：${goods_id}。', 0, 0, '${site_name}提醒：您的商品没有通过管理员审核。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的商品没有通过管理员审核，原因：“${remark}”。平台货号：${goods_id}。\r\n	</p><p>\r\n		<br />\r\n	</p>\r\n	<p>\r\n		<br />\r\n	</p>\r\n	<p style="text-align:right;">\r\n		${site_name}\r\n	</p>\r\n	<p style="text-align:right;">\r\n		${mail_send_time}\r\n	</p>', 0),
('goods_violation', '商品违规被下架', 1, '您的商品被违规下架，原因：“${remark}”。平台货号：${goods_id}。', 1, 0, '您的商品被违规下架，原因：“${remark}”。平台货号：${goods_id}。', 0, 0, '${site_name}提醒：您的商品被违规下架。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的商品被违规下架。，原因：“${remark}”。平台货号：${goods_id}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 0),
('new_order', '新订单提醒', 1, '您有订单需要处理，订单编号：${order_sn}。', 1, 1, '您有订单需要处理，订单编号：${order_sn}。', 1, 0, '${site_name}提醒：您有订单需要处理。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有订单需要处理，订单编号：${order_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<br />', 0),
('refund', '退款提醒', 1, '您有一个${type}退款单需要处理，退款编号：${refund_sn}。', 1, 0, '您有一个${type}退款单需要处理，退款编号：${refund_sn}。', 1, 0, '${site_name}提醒：您有一个${type}退款单需要处理，退款编号：${refund_sn}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有一个${type}退款单需要处理，退款编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 1),
('refund_auto_process', '退款自动处理提醒', 1, '您的${type}退款单超期未处理，已自动同意买家退款申请。退款单编号：${refund_sn}。', 1, 0, '您的${type}退款单超期未处理，已自动同意买家退款申请。退款单编号：${refund_sn}。', 0, 0, '{site_name}提醒：您的${type}退款单超期未处理，已自动同意买家退款申请。退款单编号：${refund_sn}。', '<p>\r\n	{site_name}提醒：\r\n</p>\r\n<p>\r\n	您的${type}退款单超期未处理，已自动同意买家退款申请。退款单编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 0),
('return', '退货提醒', 1, '您有一个${type}退货单需要处理。退货编号：${refund_sn}。', 1, 0, '您有一个${type}退货单需要处理。退货编号：${refund_sn}。', 0, 0, '${site_name}提醒：您有一个${type}退货单需要处理。退货编号：${refund_sn}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有一个${type}退货单需要处理。退货编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<br />', 0),
('return_auto_process', '退货自动处理提醒', 1, '您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。', 1, 0, '您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。', 0, 0, '${site_name}提醒：您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>', 0),
('return_auto_receipt', '退货未收货自动处理提醒', 1, '您的${type}退货单不处理收货超期未处理，已自动按弃货处理。退货单编号：${refund_sn}。', 1, 0, '您的${type}退货单不处理收货超期未处理，已自动按弃货处理。退货单编号：${refund_sn}。', 0, 0, '${site_name}提醒：您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您的${type}退货单超期未处理，已自动同意买家退货申请（弃货）。退货单编号：${refund_sn}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 0),
('store_cost', '机构消费提醒', 1, '您有一条新的机构消费记录，金额：${price}，操作人：${seller_name}，备注：${remark}。', 1, 1, '您有一条新的机构消费记录，金额：${price}，操作人：${seller_name}，备注：${remark}。', 1, 0, '${site_name}提醒：您有一条新的机构消费记录。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您有一条新的机构消费记录，金额：${price}，操作人：${seller_name}，备注：${remark}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>', 0),
('store_expire', '机构到期提醒', 1, '你的机构即将到期，请及时续期。', 1, 0, '你的机构即将到期，请及时续期。', 0, 0, '${site_name}提醒：你的机构即将到期，请及时续期。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	你的机构即将到期，请及时续期。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 0),
('live_apply_verify', '直播审核失败提醒', 1, '您申请的直播没有通过管理员审核，原因：“${remark}”。直播号：${live_apply_id}。', 1, 0, '您申请的直播没有通过管理员审核，原因：“${remark}”。直播号：${live_apply_id}。', 0, 0, '${site_name}提醒：您申请的直播没有通过管理员审核。', '<p>\r\n	${site_name}提醒：\r\n</p>\r\n<p>\r\n	您申请的直播没有通过管理员审核，原因：“${remark}”。直播号：${live_apply_id}。\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p style="text-align:right;">\r\n	${site_name}\r\n</p>\r\n<p style="text-align:right;">\r\n	${mail_send_time}\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 0);

INSERT INTO `#__storegrade` (`storegrade_id`, `storegrade_name`, `storegrade_goods_limit`, `storegrade_album_limit`, `storegrade_space_limit`, `storegrade_template_number`, `storegrade_template`, `storegrade_price`, `storegrade_description`, `storegrade_sort`) VALUES (1, '系统默认', 100, 500, 100, 6, 'default|style1|style2|style3|style4|style5', '100', '用户选择“默认等级”，可以立即开通。', 0),
(2, '白金机构', 200, 1000, 100, 6, 'default|style1|style2|style3|style4|style5', '200', '享受更多特权', 2),
(3, '钻石机构', 0, 1000, 100, 6, 'default|style1|style2|style3|style4|style5', '1000', '', 100);

INSERT INTO `#__albumclass` VALUES ('1','默认相册','1','','255','','1506020826','1');

INSERT INTO `#__advposition` (`ap_id`, `ap_name`, `ap_intro`, `ap_isuse`, `ap_width`, `ap_height`) VALUES
(1, '首页-轮播图', '', 1, 1920, 400);

INSERT INTO `#__adv` (`adv_id`, `ap_id`, `adv_title`, `adv_link`, `adv_code`, `adv_startdate`, `adv_enddate`, `adv_sort`, `adv_enabled`, `adv_clicknum`, `adv_bgcolor`) VALUES
(1, 1, '首页轮播图1', '', '5a4341aeb0372.jpg', 1199116800, 1830268800, 0, 1, 0, NULL),
(2, 1, '首页轮播图2', '', '5a4341c011073.jpg', 1199116800, 1830268800, 0, 1, 0, NULL);

INSERT INTO `#__appadvposition` (`ap_id`, `ap_name`, `ap_intro`, `ap_isuse`, `ap_width`, `ap_height`) VALUES
(1, '首页轮播图', '首页轮播图', 1, 720, 350),
(2, '首页促销', '首页促销', 1, 375, 160),
(3, '首页导航图', '', 1, 60, 60),
(4, '首页横图广告', '', 1, 375, 115);

INSERT INTO `#__appadv` (`adv_id`, `ap_id`, `adv_title`, `adv_type`, `adv_typedate`, `adv_code`, `adv_startdate`, `adv_enddate`, `adv_sort`, `adv_enabled`) VALUES
(1, 1, '首页轮播图', NULL, NULL, '6188c5ec94011.png', 1514476800, 1902412800, 255, 1),
(2, 1, '首页轮播图', NULL, NULL, '6188c5f5bc6a6.png', 1514476800, 1902412800, 255, 1),
(3, 1, '首页轮播图', NULL, NULL, '6188c5fcd1c13.png', 1514476800, 1902412800, 255, 1),
(4, 2, '首页促销左(320X260)', NULL, NULL, '5a44c828b411b.jpg', 1514476800, 1902412800, 255, 1),
(5, 2, '首页促销右上(320X130)', NULL, NULL, '5a44c84ace6ca.jpg', 1514476800, 1902412800, 255, 1),
(6, 2, '首页促销右下(320X130)', NULL, NULL, '5a44c85ca3b46.jpg', 1514476800, 1902412800, 255, 1),
(7, 4, '首页横图广告1', 'store', 1, '5cb590f2ae5c6.gif', 1555344000, 1902412800, 1, 1),
(8, 4, '首页横图广告2', 'store', 1, '5cb5980bbac26.jpg', 1555344000, 1902412800, 2, 1),
(9, 4, '首页横图广告3', 'store', 1, '5cb59d7e78073.gif', 1555344000, 1902412800, 255, 1);


