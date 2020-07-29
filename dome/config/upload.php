<?php
return array(
	'dome'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(__DIR__))."/assets/",//不能为空
		'exts'=>true,//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
	),
	'dome_image'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(__DIR__))."/assets/",//不能为空
		'exts'=>array("jpg","jpeg","png",'gif'),//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
		'width'=>array("max"=>10240,"min"=>10),//宽度限制
		'height'=>array("max"=>10240,"min"=>10),//高度限制
	),
);