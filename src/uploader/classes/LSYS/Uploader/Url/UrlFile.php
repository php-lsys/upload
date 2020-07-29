<?php
namespace LSYS\Uploader\Url;
use LSYS\Uploader\Url;
use LSYS\Uploader\Exception;
use LSYS\Uploader\HandlerTrait;
class UrlFile implements Url{
    use HandlerTrait;
    /**
     * @return string
     */
    public function saveUrl($url,&$name=null){
        $upload=new \LSYS\Remote($url,$this->config());
        $file=$upload->save($name,$this->chmod());
        if (!$file) throw new Exception($upload->errors());
        return $file;
    }
    public function checkUrl($url){
        $upload=new \LSYS\Remote($url,$this->config());
        $file=$upload->check();
        if (!$file) throw new Exception($upload->errors());
    }
}