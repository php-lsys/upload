<?php
namespace LSYS\Uploader\Data;
use LSYS\Uploader\Data;
use LSYS\Uploader\Exception;
use LSYS\Uploader\HandlerTrait;
class DataFile implements Data{
    use HandlerTrait;
    /**
     * @return string
     */
    public function saveData($file_data,&$name=null){
        if (empty($name))$name=uniqid();
        $directory = realpath($this->config()->get("dir"));
        if (empty($directory)||!is_writable(($directory))){
            throw new Exception(__('Directory :dir must be writable',array(":dir"=>$directory)));
        }
        // Make the filename into a complete path
        $_name = $directory.DIRECTORY_SEPARATOR.$name;
        if (!@file_put_contents($_name,$file_data)){
            throw new Exception(__("can't save data to [:file]",array(":file"=>$_name)));
        }
        @chmod($_name, $this->chmod());
        return $_name;
    }
    public function checkData($file_data){
        return true;
    }
}