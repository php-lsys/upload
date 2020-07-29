<?php
use LSYS\Uploader\DI;
use LSYS\Uploader\Upload\UploadFile;
use LSYS\Uploader\Upload\UploadImage;
include_once __DIR__."/../vendor/autoload.php";
LSYS\Config\File::dirs(array(
	__DIR__."/config",
));

$uploader=DI::get()->uploader();
$uploader->set("a", new UploadFile(\LSYS\Config\DI::get()->config("upload.dome")));
$uploader->set("b", new UploadImage(\LSYS\Config\DI::get()->config("upload.dome_image")));


try{
    $uploader->find("a")->saveFile((array)$_FILES['file']??[]);
}catch (\LSYS\Uploader\Exception $e){
    print_r($e->getMessage());
}
