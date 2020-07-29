<?php
namespace LSYS\Uploader\Upload;
use LSYS\Uploader\Upload;
use LSYS\Uploader\Exception;
use LSYS\Uploader\HandlerTrait;
class UploadFile implements Upload{
    use HandlerTrait;
    /**
     * @return string
     */
    public function saveFile(array $file,&$name=null){
        $upload=new \LSYS\Upload($file,$this->config());
        $file=$upload->save($name,$this->chmod());
        if (!$file) throw new Exception($upload->errors());
        return $file;
    }
    public function checkFile(array $file){
        $upload=new \LSYS\Upload($file,$this->config());
        $file=$upload->check();
        if (!$file) throw new Exception($upload->errors());
    }
}