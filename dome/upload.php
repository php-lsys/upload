<html>
<body>
<form action="./upload.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
<br />
<input type="submit" name="submit" value="Submit" />
</form>
</body>
</html>
<?php
// die('check dome,plase remore this line.');//测试示例,移除此行
use LSYS\Config\File;
use LSYS\Upload\Image;
include __DIR__."/Bootstarp.php";
if (!isset($_FILES['file']))die();
$file=$_FILES['file'];
$config= new File("upload.dome_image");
$upload = new Image($file,$config);
$name="ddd".$upload->getExt();
$filename=$upload->save($name);
if ($filename===false){
	print_r($upload->errors());
}else{
	var_dump($filename);
}
