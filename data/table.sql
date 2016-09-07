use manager;
set names utf8;
-- ACL相关 --
-- 用户表 --
DROP TABLE IF EXISTS `users`; 
CREATE TABLE `users` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `roleid` varchar(100) NOT NULL DEFAULT '1',
    `mobile` char(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` char(32) NOT NULL,
    `salt` char(6) NOT NULL,
    `duties` int(10),
    `gender` char(1) NOT NULL DEFAULT 1,
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `operator` int(10) NOT NULL DEFAULT '1',
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`),
    UNIQUE KEY (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users(`roleid`,`mobile`,`name`,`email`,`password`,`salt`,`duties`,`status`, `operator`, `created`, `gender`) VALUES('1','13353001101','徐会娥','missxu@miss.com','4f648330f3dcb79dca1415acc7e14ec2',':CCF%U','1', 1, 1, 1471405721, '女'), ('2','13810729964','杨小状','hele@hak.com','4f648330f3dcb79dca1415acc7e14ec2',':CCF%U','2', 1, 1, 1471405721, '男');

-- 角色表 --
DROP TABLE IF EXISTS `roles`; 
CREATE TABLE `roles` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `resourceid` varchar(512) NOT NULL,
    `operator` int(10) NOT NULL DEFAULT '1',
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO roles (`name`, `resourceid`, `created`, `operator`) VALUES ('系统管理员', '1', 1471405721, 1), ('助理律师', '1001,1002,1003', 1471405721, 1);
-- 职位表 --
DROP TABLE IF EXISTS `duties`; 
CREATE TABLE `duties` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `operator` int(10) NOT NULL DEFAULT '1',
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 资源表 --
DROP TABLE IF EXISTS `resources`; 
/*
CREATE TABLE `resources` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `parentid` int(10) NOT NULL default 0, 
    `accessid` int(4) NOT NULL default 0, 
    `name` varchar(240) NOT NULL default '', 
    `operator` int(10) NOT NULL DEFAULT '1',
    `created` int(10) unsigned NOT NULL default 1, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
-- 字典表 --
-- 案件类型表 --
DROP TABLE IF EXISTS `cases_catrgory`; 
CREATE TABLE `cases_catrgory` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `parentid` int(4) NOT NULL default 0, 
    `operator` int(10) NOT NULL DEFAULT '1',
    `level` tinyint(1) NOT NULL default 1, 
    `name` varchar(100) NOT NULL default '', 
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 案件阶段状态 --
DROP TABLE IF EXISTS `cases_flag`; 
CREATE TABLE `cases_flag` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `name` varchar(100) NOT NULL default '', 
    `files` text NOT NULL default '', 
    `operator` int(10) NOT NULL DEFAULT '1',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 消息通知 --
-- 全站公告 --
DROP TABLE IF EXISTS `proclamation`; 
CREATE TABLE `proclamation` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `title` varchar(200) NOT NULL , 
    `content` varchar(1024) NOT NULL , 
    `operator` int(10) NOT NULL DEFAULT '1',
    `status` tinyint(1) NOT NULL DEFAULT 0,
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 公告读取状态 --
DROP TABLE IF EXISTS `proclamation_user`; 
CREATE TABLE `proclamation_user` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `proclamation_id` int(10) unsigned NOT NULL, 
    `user_id` int(10) unsigned NOT NULL, 
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 个人信箱,用于标记消息读取状态 --
DROP TABLE IF EXISTS `msg_box`; 
CREATE TABLE `msg_box` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `user_id` int(10) unsigned NOT NULL default 0,
    `cases_id` int(1) NOT NULL default 0,
    `content` varchar(1024) NOT NULL , 
    `status` tinyint(1) unsigned NOT NULL default 0,
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 案件信息 --
DROP TABLE IF EXISTS `cases_extra`;
CREATE TABLE `cases_extra` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `cases_id` int(1) NOT NULL default 0,
    `cases_flag` int(10) unsigned NOT NULL COMMENT "案件阶段",
    `files` text COMMENT "案件相关文件",
    `operator` text COMMENT "受理机关/案件经办人员",
    `timeline` text COMMENT "时间计划",
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
    `user_id` int(10) unsigned NOT NULL COMMENT "创建人", 
    `title` varchar(200) NOT NULL COMMENT "案件标题", 
    `consignor` varchar(100) NOT NULL COMMENT "委托人",
    `consignor_idcode` varchar(100) NOT NULL COMMENT "身份证",
    `consignor_regcode` varchar(100) NOT NULL COMMENT "企业登记号码",
    `party` varchar(100) NOT NULL COMMENT "对方当事人",
    `party_idcode` varchar(100) NOT NULL COMMENT "身份证",
    `party_regcode` varchar(100) NOT NULL COMMENT "企业登记号码",
    `third` varchar(100) NOT NULL COMMENT "第三人",
    `third_idcode` varchar(100) NOT NULL COMMENT "身份证",
    `third_regcode` varchar(100) NOT NULL COMMENT "企业登记号码",
    `cases_typeid` varchar(100) NOT NULL COMMENT "案件种类",
    `cases_reason` varchar(200) NOT NULL COMMENT "案由",
    `cases_flag` int(10) unsigned NOT NULL COMMENT "案件阶段",
    `guide_user` int(10) unsigned COMMENT "经办律师，指导律师",
    `handle_user` int(10) unsigned COMMENT "经办律师，指导律师",
    `times` text NOT NULL COMMENT "案件相关时间",
    `created` int(10) unsigned NOT NULL default 1, 
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`),
    INDEX idx_title(`title`),
    INDEX idx_userid(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
