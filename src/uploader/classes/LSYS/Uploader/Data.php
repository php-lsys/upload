<?php
namespace LSYS\Uploader;
interface Data extends Handler{
    /**
     * 检查并保存
     * @return string $data_string
     * @throws Exception
     */
    public function saveData($data_string,&$name=null);
    /**
     * 只检查不保存,用在不需要保存的上传
     * @return string $data_string
     * @throws Exception
     */
    public function checkData($data_string);
}