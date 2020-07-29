<?php
use LSYS\Remote\Image;
use LSYS\Config\File;
include __DIR__."/Bootstarp.php";
$file="http://git.oschina.net/lonely/lremote/afsd";
$config= new File("remote.dome_image");
$upload = new Image($file,$config);
$name="gg.jpg";
$filename=$upload->save($name);
if ($filename===false){
	print_r($upload->errors());
}else{
	var_dump($filename);
}
