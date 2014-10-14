<?php

namespace Addons\SubmitInformation;
use Common\Controller\Addon;

/**
 * 完善个人信息插件
 * @author 周峰
 */

    class SubmitInformationAddon extends Addon{

        public $info = array(
            'name'=>'SubmitInformation',
            'title'=>'完善个人信息',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'周峰',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/SubmitInformation/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/SubmitInformation/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }