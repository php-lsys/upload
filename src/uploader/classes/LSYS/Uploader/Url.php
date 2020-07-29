<?php
namespace LSYS\Uploader;
interface Url extends Handler{
    /**
     * 检查并保存
     * @return string  $url
     * @throws Exception
     */
    public function saveUrl($url,&$name=null);
    /**
     * 只检查不保存,用在不需要保存的上传
     * @param string $url
     * @throws Exception
     */
    public function checkUrl($url);
}