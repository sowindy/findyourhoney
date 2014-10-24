<?php
return array(
	'keywords'=>array(
		'title'=>'关键词:',
		'tip'=>'微论坛 —— 当用户输入该关键词时，将会触发此回复。'
	),
	'forumname'=>array(          //配置在表单中的键名 ,这个会是config[forumname]
		'title'=>'微论坛名称:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'WEIPHP官方微论坛',		 //表单的默认值
		'tip'=>''
	),
	'picurl'=>array(
		'title'=>'回复图片:',//表单的文字
		'type'=>'picture',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'',			 //表单的默认值
		'tip'=>''
	),
	'logo'=>array(
		'title'=>'微论坛logo:',//表单的文字
		'type'=>'picture',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'',			 //表单的默认值
		'tip'=>''
	),
	'bgurl'=>array(
		'title'=>'自定义背景:',//表单的文字
		'type'=>'picture',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'',			 //表单的默认值
		'tip'=>''
	),
	'intro'=>array(
		'title'=>'图文回复介绍:',//表单的文字
		'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'微论坛 - 点击进入',			 //表单的默认值
		'tip'=>''
	),
	'ischeck'=>array(
		'title'=>'是否需要审核新帖:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(
			    '0'=>'不需要',
				'1'=>'需要',
		),
		'value'=>'1',			 //表单的默认值
		'tip'=>''
	),
	'comcheck'=>array(
		'title'=>'是否需要审核新评论:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(
			    '0'=>'不需要',
				'1'=>'需要',
		),
		'value'=>'1',			 //表单的默认值
		'tip'=>''
	),
	'isopen'=>array(
		'title'=>'开启/关闭微论坛:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(
			    '1'=>'开启',
				'0'=>'关闭',
		),
		'value'=>'0',			 //表单的默认值
		'tip'=>''
	),
	'pv'=>array(
		'title'=>'微论坛访问量:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'0',			 //表单的默认值
		'tip'=>''
	),
);
					