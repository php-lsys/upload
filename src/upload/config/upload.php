<?php
/**
 * lsys upload
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
return array(
	'dome'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(dirname(__DIR__)))."/../assets/".date("Y-m-d")."/",//不能为空
		'exts'=>true,//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
	),
	'dome_image'=>array(
		//保存文件配置
	    'dir'=>dirname(dirname(dirname(__DIR__)))."/../assets/".date("Y-m-d")."/",//不能为空
		'exts'=>array("jpg","jpeg","png",'gif'),//空表示所有扩展名均可
		'size'=>'6M',//空表示不限制文件大小
		'width'=>array("max"=>10240,"min"=>10),//宽度限制
		'height'=>array("max"=>10240,"min"=>10),//高度限制
	),
);