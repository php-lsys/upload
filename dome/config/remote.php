<?php
/**
 * lsys remote
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	'dome'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(__DIR__))."/assets/",//不能为空
		'exts'=>true,//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
		'timeout'=>60,//连接超时
	),
	'dome_image'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(__DIR__))."/assets/",//不能为空
		'exts'=>array("jpg","jpeg","png",'gif'),//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
		'timeout'=>60,//连接超时
	),
);