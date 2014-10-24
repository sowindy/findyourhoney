<?php
        	
namespace Addons\Forum\Model;
use Home\Model\WeixinModel;
        	
/**
 * Forum的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Forum' ); // 获取后台插件的配置参数	
		//dump($config);
		$param ['wecha_id'] = get_mid ();
		$param ['token'] = get_token ();
		$url = addons_url ( 'Forum://Index/index', $param );
		if($config['picurl']){
		$picurl = get_cover_url($config['picurl']);
		}else {
		$picurl = SITE_URL . '/Addons/Forum/View/default/Public/images/forum.png';
		}
		
		
		// 组装微信需要的图文数据，格式是固定的
		$articles [0] = array (
				'Title' => $config ['forumname'],
				'Description' => $config ['intro'],
				'PicUrl' => $picurl,
				'Url' => $url 
		);
		$this->replyNews ( $articles );
	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	