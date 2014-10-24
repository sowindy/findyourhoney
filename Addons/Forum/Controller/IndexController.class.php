<?php

namespace Addons\Forum\Controller;
use Home\Controller\AddonsController;

class IndexController extends AddonsController{

	function _initialize() {
		parent::_initialize();
		$token = get_token();
		// 获取后台插件的配置参数	
        $config = getAddonConfig ( 'Forum' );
		$this->assign ( $config );
		
		if($config['isopen'] == 0){
			$this->error('请联系官方开启微论坛');
		}

		$this->assign('bgurl',$config['bgurl']);

		$config = getAddonConfig ( 'Forum' );
		$this->assign ( $config );
	}
	
	//论坛首页
	function index(){	
	
		$config = getAddonConfig ( 'Forum' );
		$token = get_token();
		$map = array('status'=>'1','token'=>$token);
		$model = $this->getModel ( 'forum_topics' );
		$count = M('forum_topics')->where( $map )->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
		$wecha_id = get_mid();
		$list = M('forum_topics')->where( $map )->order('createtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$wecha_id' AND status = 1 AND isread = 1")->count();
		
		//$nickname = M('Follow')->where("token = '$token'")->field('nickname')->select();

		foreach($list as $k=>$v){
			$list["$k"]["content"] = mb_substr(strip_tags($v['content']),0,200,'utf-8');
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1")->select();
			$list["$k"]["cnum"] = count($comment);
			if($v['photos'] != ''){
				$list["$k"]["photoArr"] = explode(',',$v['photos']);
			}
			$list["$k"]["uinfo"] = $this->uinfo($v['uid'],$token);
		}
		//增加浏览量
		if(!cookie('pv')){
			$config['pv'] += 1;
			D ( 'Common/AddonConfig' )->set ( _ADDONS, $config );
			cookie('pv','1',24*60*60);
		}

		$this->assign('messageNum',$messageNum);
		$this->assign('show',$show);
		$this->assign('count',$count);
		$this->assign('list',$list);
		
		$this->assign('wecha_id',$wecha_id);
		$this->display ();
	
	}

	//首页ajax加载
	public function moreList(){
		
		
		$token = get_token();	
		$forum = M('Forum_topics');
		$where = array('status'=>'1','token'=>$token);
		$count      = $forum->where( $where )->count();
        $Page = new \Think\Page($count,10);
		$wecha_id = get_mid();

		$pageNum = I('post.pageNum');
		$pagetotal = ceil($count/10);
		if($pageNum > $pagetotal){
				exit;
		}
		
		$list2 = $forum->where( $where )->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

		if(!empty($list2)){
			foreach($list2 as $k=>$v){
				$list2["$k"]["content"] = mb_substr(strip_tags($v['content']),0,200,'utf-8');
				$id = $v['id'];
				$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
				$v["cnum"] = count($comment);
				if($v['photos'] != ''){
					$v["photoArr"] = explode(',',$v['photos']);
				}
				
				$uinfo = $this->uinfo($v['uid'],$token);

				$tpl .='<article><header><ul class="tbox"><li><a href="#" class="head_img"><img src="'.get_cover_url($uinfo['headimgurl']).'" onerror="this.src='.ADDON_PUBLIC_PATH.'/images/face.png'.';" /></a></li><li><h5>'.$v['uname'].'</h5><p>'.date('Y-m-d H:i:s',$v['createtime']).'</p></li></ul></header><section>';
		
				if($v['photoArr'] != NULL){
						$photonum = count($v['photoArr']);
						$tpl .='<figure data-count="'.$photonum.'张图片"><div>';
										
						for($i=0;$i<$photonum;$i++){
							$tpl .='<img src="'.get_cover_url($v['photoArr'][$i]).'" data-src="'.get_cover_url($v['photoArr'][$i]).'" data-gid="g7" onload="preViewImg(this, event);"/>';
						}
				$tpl .= '</div></figure>';
						}
				
				$tpl .='<div style="clear:both"></div><div><h5>'.$v['title'].'</h5><div>'.htmlspecialchars_decode($list2["$k"]["content"],ENT_QUOTES).'</div></div><a href="'.U('comment',array('tid'=>$list2["$k"]["id"],'wecha_id'=>get_mid(),'token'=>$list2["$k"]["token"])).'">查看全文</a></section><footer><ul class="box"><li>';
											
											
				if(in_array($wecha_id,explode(',',$v['likeid']))){
										
					$tpl .= '<a href="javascript:;" class="a_collect on" onclick="collectTrends('.$v['id'].',\''.$wecha_id.'\')" ><span class="icons icons_collect" >&nbsp;</span><label>';
											
						}else{
									
					$tpl .= '<a href="javascript:;" class="a_collect" onclick="collectTrends('.$v['id'].',\''.$wecha_id.'\')" ><span class="icons icons_collect" >&nbsp;</span>';
						}

					if($v['likeid'] == NULL){
						$tpl .= '<label>0</label></a>';
												
					}else{
												
						$tpl .= '<label>'.count(explode(',',$v['likeid'])).'</label></a>';
													
					}	
											
										
				$tpl .= '</li><li><a href="'.U('comment',array('tid'=>$v['id'],'wecha_id'=>get_mid(),'token'=>$token)).'" class="a_comment"><span class="icons icons_comment" >&nbsp;</span><label>'.$v['cnum'].'</label></a></li><li>';
										
					if(in_array($wecha_id,explode(',',$v['favourid']))){

						$tpl .= '<a href="javascript:;" class="a_like on" onclick="praiseTrends('.$v['id'].',\''.$wecha_id.'\')"><span class="icons icons_like">&nbsp;</span><label>';
												
					}else{
						$tpl .= '<a href="javascript:;" class="a_like" onclick="praiseTrends('.$v['id'].',\''.$wecha_id.'\')"><span class="icons icons_like">&nbsp;</span>';
					}
		
					if($v['favourid'] == NULL){
											
						$tpl .= '<label>0</label></a>';
												
					}else{
												
						$tpl .= '<label>'.count(explode(',',$v['favourid'])).'</label></a>';
												
					}
											
						$tpl .= '</li></ul></footer></article>';
			}
			
				echo $tpl;
			}else{
				echo 0;
			}
	}	
	
	//发布新帖子
	public function checkAdd(){
		$data = array();
		$data['uid'] = get_mid();
		
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['title'] = I('post.title');
		$data['content'] = stripslashes(I('post.form_article'));
		
		$wecha_id = $data['uid'];	
		$userinfo = M('Follow')->field('nickname')->where( "id = '$wecha_id'" )->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$data['token'] = get_token();
		$data['createtime'] = time();
		
		$token = $data['token'];
		
		$config = getAddonConfig ( 'Forum' );

		if($config['ischeck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
							
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
				$this->dealFiles($_FILES),
				C('PICTURE_UPLOAD'),
				C('PICTURE_UPLOAD_DRIVER'),
				C("UPLOAD_{$pic_driver}_CONFIG")
		); 
		
        if($info){
			foreach ($info as $key => $value) {
				$photos[] = $value['id'];
			}
        } else {
            $return = $Picture->getError();
        }
		
		//dump( $info );
		//dump( $photos );exit;		
		
		foreach($photos as $k=>$v){
		
			if($v == ''){
				unset($photos[$k]);
			}
		}
		
		$data['photos'] = implode(',',$photos);		
		
		//添加记录
		$forum = M('Forum_topics');
	
		if($forum->create()){
			if($forum->add($data)){
				if($config['ischeck'] == 1){
					$this->success('管理员审核后您的帖子才可以显示',U('myContent',array('wecha_id'=>$data['uid'],'token'=>$data['token'])));
				}else{
					$this->success('发表成功！',U('index',array('wecha_id'=>$data['uid'],'token'=>$data['token'])));
				}
				
			}
		}else{
			$this->error('系统错误');
		}

	}	

	//喜欢
	public function likeAjax(){
		$uid = I('post.uid');
		
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = I('post.tid');
		
		$info = M('Forum_topics')->field('likeid')->where("id = $id AND status = 1")->find();

		if($info['likeid'] == ''){
		
			$data['likeid'] = I('post.uid');
			$boo = M('Forum_topics')->where("id = $id")->setField($data);
			
		}else{
		
			$likeid = explode(',',$info['likeid']);
			if(in_array(I('post.uid'),$likeid)){
			
				unset($likeid[array_search(I('post.uid'),$likeid)]);
				$data['likeid'] = implode(',',$likeid);
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
				
			}else{
		
				$data['likeid'] = $info['likeid'].','.I('post.uid');
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
			}
		}
		if($boo){
		
			echo 1;
		}else{
			echo 0;
		}

	}

	//赞
	public function favourAjax(){
		$uid = I('post.uid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = I('post.tid');
		
		$info = M('Forum_topics')->field('favourid')->where("id = $id")->find();

		if($info['favourid'] == ''){
		
			$data['favourid'] = I('post.uid');
			$boo = M('Forum_topics')->where("id = $id")->setField($data);

		}else{
		
			$favourid = explode(',',$info['favourid']);
			if(in_array(I('post.uid'),$favourid)){
			
				unset($favourid[array_search(I('post.uid'),$favourid)]);
				$data['favourid'] = implode(',',$favourid);
				$boo = M('Forum_topics')->where("id = $id")->setField($data);

			}else{
			
				$data['favourid'] = $info['favourid'].','.I('post.uid');
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
			}
		}

		if($boo){
		
			echo 1;
		}else{
			echo 0;
		}
	}
	
	//帖子和评论详情页面
	public function comment(){
		
		$id = I('get.tid');
		$token = I('get.token');
		$topics = M('Forum_topics')->where("id = $id AND status = 1 AND token = '$token'")->find();
		$topics['content'] = htmlspecialchars_decode($topics['content']);
		$wecha_id = get_mid();

		//load comment
		
		$comment = M('Forum_comment')->order('createtime DESC')->where("tid = $id AND status = 1")->select();
		$cnum = count($comment);
		foreach($comment as $k=>$v){
			$comment["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$comment["$k"]["uinfo"] = $this->uinfo($v['uid'],$token);
			if($v['replyid'] != NULL){
				$reuid = $v['replyid'];
				$userinfo = M('Userinfo')->field('nickname')->where("id = '$reuid'")->find();
				$comment[$k]['reuname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
				
			}
		}

		$this->assign('wecha_id',$wecha_id);
		$this->assign('cnum',$cnum);
		$this->assign('comment',$comment);
		$this->assign('topics',$topics);
		$this->display();
	
	}
	
	//评论提交页面
	public function add(){
		$this->display(add);
	}
	
	//发表评论
	public function commentAdd(){
		$this->display(commentAdd);
	}
	
	//评论提交处理
	public function checkCommentAdd(){
		
		$data['uid'] = I('post.wecha_id');
		
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['tid'] = I('post.tid');
		
		$wecha_id = $data['uid'];
		$userinfo = M('Follow')->field('nickname')->where("id = '$wecha_id'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		
		$data['content'] = stripslashes(I('post.form_article'));
		$data['token'] = I('post.token');
		$token = I('post.token');
		$data['createtime'] = time();
		
		$config = getAddonConfig ( 'Forum' );
		if($config['comcheck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
				
		$comment = M('Forum_comment');

			if($comment->add($data)){
				$tid = $data['tid'];
				$token = $data['token'];
				$uid = M('Forum_topics')->where("token = '$token' AND id = $tid AND status = 1")->field('uid')->find();
				if($config['comcheck'] == 1){
					$message['content'] = '<a href="'.U('comment',array('tid'=>$data['tid'],'wecha_id'=>$uid['uid'],'token'=>$data['token'])).'">'.$data['uname'].'评论了您的帖子,该评论需要等待管理员审核后才能显示</a>';
				}else{
					$message['content'] = '<a href="'.U('comment',array('tid'=>$data['tid'],'wecha_id'=>$uid['uid'],'token'=>$data['token'])).'">'.$data['uname'].'评论了您的帖子</a>';
				}
				
				$message['createtime'] = time();
				$message['fromuid'] = $data['uid'];
				$message['token'] = $data['token'];
				$message['touid'] = $uid['uid'];
				$message['tid'] = $data['tid'];
				$message['cid'] = NULL;
				$message['fromuname'] = $data['uname'];
				
			$touid = $uid['uid'];
			$userinfo = M('Follow')->field('nickname')->where("id = '$touid'")->find();
			$message['touname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';

				
				M('Forum_message')->add($message);
				
				if($config['comcheck'] == 1){
					$this->success('等待管理员审核后您的评论才可以显示',U('comment',array('wecha_id'=>$data['uid'],'tid'=>$tid,'token'=>$data['token'])));
				}else{
					$this->success('评论成功',U("comment",array('tid'=>$data['tid'],'wecha_id'=>$data['uid'],'token'=>$data['token'])));
				}
				
				
			}else{
				$this->error('评论失败');
			}
	
	}
	
	//赞评论
	public function commentFavourAjax(){
		$uid = I('post.uid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = I('post.id');
		
		
		$comment = M('Forum_comment');

		$fav = $comment->field('favourid')->where("id = $id")->find();
		
		if($fav['favourid'] == NULL){
			
			
			$boo = $comment->where("id = $id")->setField(array('favourid'=>$uid));
			
		}else{
			
			$favArray = explode(',',$fav['favourid']);
			if(in_array($uid,$favArray)){
				
				unset($favArray[array_search($uid,$favArray)]);
				$res['favourid'] = implode(',',$favArray);
				$boo = $comment->where("id = $id")->setField($res);
				
			}else{
				$boo = $comment->where("id = $id")->setField(array('favourid'=>$fav['favourid'].','.$uid));
			}
		}

		if($boo){
			echo 1;
		}else{
			echo 0;
		}
	
	}	

	//回复评论页面
	public function recomment(){
		$uid = I('get.wecha_id');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$uid = I('get.reid');
		
		$data = M('Forum_comment')->where("uid = '$uid'")->field('uname')->find();
		$uname = $data['uname'];

		
		$this->assign('uname',$uname);
		$this->display();
	}	
	
	//回复评论提交处理
	public function checkRecomment(){
		$data['uid'] = I('post.wecha_id');
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['tid'] = I('post.tid');
		$data['replyid'] = I('post.reid');
		$data['token'] = I('post.token');
		$token = $data['token'];
		$wecha_id = $data['uid'];
		$userinfo = M('Follow')->field('nickname')->where("id = '$wecha_id'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		
		
		$data['content'] = stripslashes(I('post.form_article'));
		$data['createtime'] = time();
		
		$config = getAddonConfig ( 'Forum' );
		if($config['comcheck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
		
		$comment = M('Forum_comment');
		if($comment->create()){
		
			if($comment->add($data)){
				if($config['comcheck'] == 1){
					$message['content'] = '<a href="'.U('comment',array('tid'=>$data['tid'],'wecha_id'=>$data['replyid'],'token'=>$data['token'])).'">'.$data['uname'].'回复了您的评论，该评论在管理员审核后才能显示</a>';
				}else{
					$message['content'] = '<a href="'.U('comment',array('tid'=>$data['tid'],'wecha_id'=>$data['replyid'],'token'=>$data['token'])).'">'.$data['uname'].'回复了您的评论。</a>';
				}
				
				$message['createtime'] = time();
				$message['fromuid'] = $data['uid'];
				$message['token'] = $data['token'];
				$message['touid'] = $data['replyid'];
				$message['tid'] = $data['tid'];
				$message['cid'] = I('post.cid');
				$message['fromuname'] = $data['uname'];
				
				$touid = $message['touid'];
				$userinfo = M('Follow')->field('nickname')->where("id = '$touid'")->find();
				$message['touname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
				

				M('Forum_message')->add($message);
				if($config['comcheck'] == 1){
					$this->success('等待管理员审核后您的评论才可以显示',U('comment',array('wecha_id'=>$data['uid'],'tid'=>$data['tid'],'token'=>$data['token'])));
				}else{
					$this->success('评论成功',U("comment",array('tid'=>$data['tid'],'wecha_id'=>$data['uid'],'token'=>$data['token'])));
				}

			}
		}else{
			$this->error('系统错误');
		}	
	}

	//我发表的帖子页面
	public function myContent(){
		
		$uid = get_mid();
		
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$token = I('get.token');

		
		$userinfo = M('Follow')->field('nickname')->where("id = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
 
		$list = M('Forum_topics')->order('createtime DESC')->where("uid = '$uid' AND status != 0 AND token = '$token'")->select();

		$mylikenum = M('Forum_topics')->field('id')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->count();
		$mymessagenum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1")->count();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->count();
		foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
			$list["$k"]["cnum"] = count($comment);
		}

		$fans = $this->uinfo($uid,$token);
		$this->assign('fans',$fans); 
		$this->assign('mymessagenum',$mymessagenum); 
		$this->assign('messageNum',$messageNum); 
		$this->assign('mylikenum',$mylikenum); 
		$this->assign('uname',$uname); 
		$this->assign('list',$list); 
		$this->display();
	}

	//我的消息页面
	public function myMessage(){
		
		$uid = get_mid();
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$token = I('get.token');
		
		$userinfo = M('Follow')->field('nickname')->where("id = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		
		$list = M('Forum_message')->order('createtime DESC')->where("touid = '$uid' AND token = '$token' AND status = 1")->select();
		
		foreach($list as $k=>$v){
		
			$list["$k"]['uinfo'] = $this->uinfo($v['fromuid'],$token);
		}
		
		$mylikenum = M('Forum_topics')->field('id')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->count();
		$mytopicsnum = M('Forum_topics')->field('id')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->count();

		M('Forum_message')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->setField('isread',0);
		
		$fans = $this->uinfo($uid,$token);
		$this->assign('fans',$fans); 
		$this->assign('list',$list);
		$this->assign('mylikenum',$mylikenum);
		$this->assign('uname',$uname);
		$this->assign('mytopicsnum',$mytopicsnum);
		$this->display();
	
	}

	//其他用户页面
	public function otherUser(){
		$wecha_id = I('get.wecha_id');	
		
		if($wecha_id == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$uid = I('get.uid');
		$token = I('get.token');

		
		$userinfo = M('Follow')->field('nickname,headimgurl')->where("id = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		if($userinfo['headimgurl'] == ''){
			$headimgurl = ADDON_PUBLIC_PATH.'/images/face.png';
		}else{
			$headimgurl = $userinfo['headimgurl'];
		}
		$list = M('Forum_topics')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->select();


		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$wecha_id' AND status = 1 AND isread = 1")->count();
		foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
			$list["$k"]["cnum"] = count($comment);
		}
		

		$this->assign('messageNum',$messageNum); 
		
		$this->assign('wecha_id',$wecha_id);
		$this->assign('uname',$uname); 
		$this->assign('headimgurl',$headimgurl); 
		$this->assign('list',$list); 
		$this->display();
	}

	//我喜欢过的帖子页面
	public function myLike(){
		
		$uid = get_mid();
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$token = I('get.token');
		$list = M('Forum_topics')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->select();
		
		$userinfo = M('Follow')->field('nickname')->where("id = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		
		$mytopicsnum = M('Forum_topics')->field('id')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->count();
		$mymessagenum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1")->count();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->count();
		foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1")->select();
			$list["$k"]["cnum"] = count($comment);
			$list["$k"]["uinfo"] = $this->uinfo($v['uid'],$token);
		}
		$wecha_id = I('get.wecha_id');
		$fans = $this->uinfo($uid,$token);
		$this->assign('fans',$fans); 
		$this->assign('mytopicsnum',$mytopicsnum);
		$this->assign('mymessagenum',$mymessagenum);
		$this->assign('messageNum',$messageNum);
		$this->assign('wecha_id',$wecha_id);
		$this->assign('list',$list); 
		$this->assign('uname',$uname); 
		$this->display();	
		
	}

	//编辑我的帖子页面
	public function myContentEdit(){
		$wecha_id = I('get.wecha_id');	
		if($wecha_id == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$tid = I('get.tid');
		$wecha_id = I('get.wecha_id');
		$token = I('get.token');
		$data = M('Forum_topics')->where("id = $tid AND token = '$token' AND uid = '$wecha_id'")->find();
		$data['photoArr'] = explode(',',$data['photos']);
		$data['content'] = htmlspecialchars_decode($data['content']);

		$this->assign('data',$data);
		$this->display();
	}	

	//更新我的帖子提交处理
	public function myContentUpdate(){
		
		
		$data = array();
		$data['uid'] = I('post.wecha_id');
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$topics = M('Forum_topics');
		$data['title'] = I('post.title');
		$data['content'] = stripslashes(I('post.form_article'));
		
		
		$wecha_id = $data['uid'];
		$userinfo = M('Follow')->field('nickname')->where("id = '$wecha_id'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		

		$data['token'] = I('post.token');
		$data['updatetime'] = time();
		
		$token = $data['token'];

		$tid = I('post.tid');
		
		$tinfo = $topics->field('photos')->where("token = '$token' AND uid = '$wecha_id' AND status = 1 AND id = $tid")->find();
		
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
				$this->dealFiles($_FILES),
				C('PICTURE_UPLOAD'),
				C('PICTURE_UPLOAD_DRIVER'),
				C("UPLOAD_{$pic_driver}_CONFIG")
		); 
		
        if($info){
			foreach ($info as $key => $value) {
				$photos[] = $value['id'];
			}
        } else {
            $return = $Picture->getError();
        }		

		foreach($photos as $k=>$v){
		
			if($v == ''){
				unset($photos[$k]);
			}
		}
		$photos[] = $tinfo['photos'];
		$data['photos'] = implode(',',$photos);

		if($topics->create()){
		
			if($topics->where("id = $tid AND token = '$token' AND uid = '$wecha_id' AND status = 1")->setField($data)){
				$this->success('修改成功',U("myContent",array('wecha_id'=>$data['uid'],'token'=>$data['token'])));
			}
		}else{
			$this->error('系统错误');
		}
		
	}	
	
	//删除帖子
	public function delTopics(){
		$uid = I('post.wecha_id');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$id = I('post.tid');
		
		$token = I('post.token');
		
		if(M('Forum_topics')->where("id = $id AND token = '$token' AND uid = '$uid' AND status = 1")->setField('status',0)){
				echo 1;
		}else{	
				echo 0;
		}
	}
	
	//删除评论
	public function delComment(){
		$uid = I('get.wecha_id');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		
		$cid = I('post.cid');
		$token = I('get.token');
		
		
		if(M('Forum_comment')->where("id = $cid AND token = '$token' AND uid = '$uid' AND status = 1")->setField('status',0)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	//获取头像
	public function uinfo($wid='',$to=''){
	
		$uinfo = M('Follow')->field('headimgurl,nickname')->where("id = '$wid' AND token = '$to'")->find();
		if($uinfo['headimgurl'] == ''){
			$uinfo['headimgurl'] = ADDON_PUBLIC_PATH.'/images/face.png';
		}
		return $uinfo;
	}


    //转换上传文件数组变量为正确的方式
    public function dealFiles($files) {
        $fileArray = array();
        $n = 0;
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key) {
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            } else {
                $fileArray[$key] = $file;
            }
        }
        return $fileArray;
    }
	
}
