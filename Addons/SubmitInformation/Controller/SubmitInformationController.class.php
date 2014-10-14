<?php

namespace Addons\SubmitInformation\Controller;
use Home\Controller\AddonsController;

class SubmitInformationController extends AddonsController{
	function info(){
		if(IS_POST){
			$map['uid'] = $this->mid;//存储在wp_member中的uid
			$map['nickname'] = I('nickname');
			$map['sex'] = I('sex');
			$map['birthday'] = I('birthday');
			$map['grade'] = I('grade'];
			$map['zhuanye'] = I('zhuanye');
			$map['hobby'] = I('hobby');//兴趣爱好有分词功能，提示用户用‘，’来进行分词
			$map['jiaoyouxuanyan'] = I('jiaoyouxuanyan');//交友宣言
			$map['cTime'] = time();
			$map['token'] = get_token();
			$res = M('member_info')->add($map);
			if($res){
				$this->success('您的信息已经更新成功，您的信息审核后，就会立马放在推荐名单中哦');
			}else{
				$this->error('位置错误，导致您的信息添加失败，我们表示非常抱歉');
			}
		}else{
			$this->display();
		}
		
	}
}
