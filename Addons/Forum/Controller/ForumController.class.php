<?php

namespace Addons\Forum\Controller;
use Home\Controller\AddonsController;

class ForumController extends AddonsController{

	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _ACTION );
		
		$res ['title'] = '帖子管理';
		$res ['url'] = addons_url ( 'Forum://Forum/lists' );
		$res ['class'] = $controller == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '评论管理';
		$res ['url'] = addons_url ( 'Forum://Forum/comment' );
		$res ['class'] = $controller == 'comment' ? 'current' : '';
		$nav [] = $res;

		$res ['title'] = '消息管理';
		$res ['url'] = addons_url ( 'Forum://Forum/message' );
		$res ['class'] = $controller == 'message' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '论坛配置';
		$res ['url'] = addons_url ( 'Forum://Forum/config' );
		$res ['class'] = $controller == 'config' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '预览微论坛';
		$res ['url'] = addons_url ( 'Forum://Index/index', array(wecha_id=>get_mid(),token=>get_token()));
		$res ['class'] = $controller == 'config' ? 'current' : '';
		$nav [] = $res;
				
		$this->assign ( 'nav', $nav );
	}
	
	
	//帖子管理
	public function lists(){
		$this->assign ( 'checkTopics_button', true );
		$this->assign ( 'add_button', false );
		is_array ( $model ) || $model = $this->getModel ( 'forum_topics' );
		$templateFile = $this->getAddonTemplate ( $model ['template_list'] );
		parent::common_lists ( $model, $page, 'lists' );
	}
	
	//评论管理
	public function comment(){
		$this->assign ( 'checkTopics_button', true );
		$this->assign ( 'add_button', false );
		is_array ( $model ) || $model = $this->getModel ( 'forum_comment' );
		$templateFile = $this->getAddonTemplate ( $model ['template_list'] );
		parent::common_lists ( $model, $page, 'lists' );
	}
	
	//消息管理
	public function message(){
		$this->assign ( 'checkTopics_button', false );
		$this->assign ( 'add_button', false );
		is_array ( $model ) || $model = $this->getModel ( 'forum_message' );
		$templateFile = $this->getAddonTemplate ( $model ['template_list'] );
		parent::common_lists ( $model, $page, 'lists' );
	}
	//审核帖子
	//审核评论

	
	//增加模型
	function add() {
		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_add'] );
		
		parent::common_add ( $model, $templateFile );
	}

	//编辑模型
	public function edit() {
		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_edit'] );
		parent::common_edit ( $model, $id, $templateFile );
	}
	
	//删除模型
	public function del($model = null, $ids = null) {
		$mod =  I('get.model');
		switch ($mod) {
            case 'topics':
                $model = 'forum_topics';
                break;
            case 'comment':
                $model = 'forum_comment';
                break;
            case 'message':
                $model = 'forum_message';
                break;
            case 'lists':
                $model = 'forum_topics';
                break;
            default:
        }
		parent::common_del ( $model, $ids );
	}
	
	//审核帖子
	public function checkTopics(){
	
		$id = I('id');
		$ids = I('ids');
		
		$token = get_token();
				
		if(empty($id) && empty($ids)){
			$this->error('请勾选要通过审核的内容');
		}
		
		if(is_array($ids)){
			$id = $ids;
		
			$id = implode(',',$id);
			$where = "token = '$token' AND id in($id)";
			
		}else{	
			$where = "token = '$token' AND id = $id";
		}
		$mod =  I('get.model');
		switch ($mod) {
            case 'topics':
                $model = 'forum_topics';
                break;
            case 'comment':
                $model = 'forum_comment';
                break;
            case 'lists':
                $model = 'forum_topics';
                break;
            default:
        }
		$result = M( $model )->where( $where )->setField('status',1);
		if($result !== false){
			$this->success('审核成功');
		}else{
			$this->error('审核失败');
		}
	
	}
}
