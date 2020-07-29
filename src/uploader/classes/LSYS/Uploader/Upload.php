<?php
namespace LSYS\Uploader;
interface Upload extends Handler{
    /**
     * 检查并保存
     * @return string
     * @throws Exception
     */
    public function saveFile(array $file,&$name=null);
    /**
     * 只检查不保存,用在不需要保存的上传
     * @param array $file
     * @throws Exception
     */
    public function checkFile(array $file);
}