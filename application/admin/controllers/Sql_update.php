<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 官网 数据库结构变更; 方便上线
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-09-14T14:12:12+0800
*/
class Sql_update extends ACDM_Controller {

    public function __construct(){
        parent::__construct();
        $pwd = $this->I("pwd",FALSE);
        $pwd !== "*.magenjie.*" && $this->json_exit();
    }

    public function index(){
        $sql_array = $this->get_sql_version();
        $error = array();
        $success = 0;
        foreach ($sql_array as $sql) {
            if ($this->db->simple_query($sql)){
                ++$success;
            }else{
                $error[] = $sql;
            }
        }
        $msg = empty($error) ? " Success: 共{$success}条sql执行成功" : "Query failed：".var_export($error,TRUE)." Success: 共{$success}条sql执行成功";
        $this->json_exit(array('status'=>TRUE,'msg'=>$msg));
    }
    
    public function get_sql_version($is_all = FALSE){
        $query = array(
            "2017-10-10" => array( //上线日期;
                // "INSERT INTO `acdm_admin_auth` (`id`, `name`, `uri`, `sort`, `pid`, `status`, `auth_limit`, `is_left_nav`, `parent_node`, `related_node`) VALUES
                // (53, '上传首页幻灯图片', 'system_setting/upload_pic', 255, 7, 1, 1, 0, '0', '0'),
                // (54, '机场列表', 'acdm/index', 255, 16, 1, 1, 1, '0', '0'),
                // (55, '版本列表', 'acdm/version_list', 255, 17, 1, 1, 1, '0', '0'),
                // (56, '添加机场', 'acdm/add_airport', 255, 16, 1, 1, 1, '0', '0'),
                // (57, '编辑机场', 'acdm/edit_airport', 255, 16, 1, 1, 0, '0', '54'),
                // (58, '添加版本', 'acdm/add_version', 255, 17, 1, 1, 1, '0', '0'),
                // (59, '编辑版本', 'acdm/edit_version', 255, 17, 1, 1, 0, '0', '55'),
                // (60, '删除机场', 'acdm/delete_airport', 255, 16, 1, 1, 0, '0', '54'),
                // (61, '禁/启机场', 'acdm/disabled_airport', 255, 16, 1, 1, 0, '54', '0'),
                // (62, '删除版本', 'acdm/delete_version', 255, 17, 1, 1, 0, '0', '55'),
                // (63, '禁/启版本', 'acdm/disabled_version', 255, 17, 1, 1, 0, '54', '0'),
                // (64, '类型列表', 'acdm/app_list', 255, 17, 1, 1, 1, '0', '0'),
                // (65, '添加app类型', 'acdm/update_app_type', 255, 17, 1, 1, 0, '64', '0'),
                // (66, '删除APP类型', 'acdm/delete_app_type', 255, 17, 1, 1, 0, '0', '64'),
                // (67, '根据三字码获取机场信息', 'acdm/get_airport_info_by_iata', 255, 16, 1, 1, 0, '56,57', '0');",
                // //表的结构 `acdm_app_version`
                // "CREATE TABLE IF NOT EXISTS `acdm_app_version` (
                //     `id` int(11) NOT NULL AUTO_INCREMENT,
                //     `airport_iata` char(3) NOT NULL COMMENT '三字码',
                //     `type` tinyint(4) NOT NULL COMMENT '客户端类型 WGS_IOS WGS_Android ACDM_IOS ACDM_Android',
                //     `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '当前版本是否生效',
                //     `name` varchar(50) NOT NULL,
                //     `code` tinyint(4) NOT NULL COMMENT '版本编号 数字 android 使用',
                //     `is_must` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为必更版本',
                //     `description` varchar(255) NOT NULL,
                //     `url` varchar(50) NOT NULL COMMENT 'app 下载地址；',
                //     `update_time` int(11) NOT NULL,
                //     PRIMARY KEY (`id`),
                //     KEY `iata_app_type` (`airport_iata`,`type`)
                // ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='acdm app版本列表；' AUTO_INCREMENT=0;",

                // // acdm_member_airport
                // "CREATE TABLE IF NOT EXISTS `acdm_member_airport` (
                //     `member_airport_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                //     `airport_iata` varchar(3) NOT NULL COMMENT '机场三字码',
                //     `airport_icao` varchar(4) NOT NULL DEFAULT '' COMMENT '机场四字码',
                //     `cn_name` varchar(255) NOT NULL DEFAULT '' COMMENT '中文名',
                //     `cn_name_short` varchar(128) NOT NULL DEFAULT '' COMMENT '中文名简写',
                //     `runway` varchar(255) NOT NULL DEFAULT '',
                //     `system_name` varchar(255) NOT NULL DEFAULT '',
                //     `api_public_key` varchar(128) NOT NULL DEFAULT '',
                //     `system_prefix` char(3) NOT NULL DEFAULT '' COMMENT '集团默认机场',
                //     `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0,1 是否有效;',
                //     `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
                //     `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
                //     PRIMARY KEY (`member_airport_id`),
                //     KEY `airport_iata` (`airport_iata`)
                // ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='ACDM成员机场表' AUTO_INCREMENT=0 ;",
                
                // 导航数据
                // "INSERT INTO `acdm_admin_nav` (`id`, `name`, `status`, `icon`, `sort`, `pid`) VALUES
                // (15, 'ACDM管理', 1, 'fa-buysellads', 3, 0),
                // (16, 'ACDM机场', 1, 'fa-adn', 255, 15),
                // (17, 'APP版本', 1, 'fa-bullseye', 255, 15);"
                
                // 新增字段;
                "ALTER TABLE `acdm_member_airport` ADD COLUMN `airport_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '机场名称' AFTER `airport_icao`;"
            ),
        );
        $last_date = max(array_keys($query));
        if ($last_date >= date("Y-m-d")) {
            return $is_all ? $query : $query[max(array_keys($query))];    
        }else{
            return array();
        }
    }

    // 执行sql
    public function query_sql(){
        $pwd = $this->I("pwd",FALSE);
        $pwd !== '.*magenjie*.' && $this->json_exit();
        $sql = "";
        empty($sql) && $this->json_exit();
        $result = $this->db->simple_query($sql);
        if ($result) {
            var_export($result);
        }else{
            echo "Query failed!";
        }
    }
}