# 上传简单封装
使用示例:
```
//参考:dome/upload.php
use LSYS\Config\File;
use LSYS\Upload\Image;
include __DIR__."/Bootstarp.php";
if (!isset($_FILES['file']))die();
$file=$_FILES['file'];
$config= new File("upload.dome_image");
$upload = new Image($file,$config);

$filename=$upload->save("ddd".$upload->get_ext());
if ($filename===false){
	print_r($upload->errors());
}else{
	var_dump($filename);
}
```