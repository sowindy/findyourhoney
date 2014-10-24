<?php

namespace Addons\Forum;
use Common\Controller\Addon;

/**
 * 微论坛插件
 * @author ylweb
 */

    class ForumAddon extends Addon{

        public $info = array(
            'name'=>'Forum',
            'title'=>'微论坛',
            'description'=>'weiphp的论坛插件',
            'status'=>1,
            'author'=>'ylweb',
            'version'=>'1.0',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Forum/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Forum/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }