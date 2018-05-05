DROP TABLE IF EXISTS `platform`;
CREATE TABLE `platform` (
  `p_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '平台id',
  `p_name` varchar(50) NOT NULL DEFAULT '' COMMENT '平台名称',
  `p_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据记录状态，0为废弃，1为有效',
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='steam第三方交易平台';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `p_id` int(10) COMMENT '平台id',
  `c_id` varchar(50) NOT NULL DEFAULT '' COMMENT '分类id',
  `c_code` varchar(50) NOT NULL DEFAULT '' COMMENT '分类代码',
  `c_name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类';






