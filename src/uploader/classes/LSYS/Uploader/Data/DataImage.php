<?php
namespace LSYS\Uploader\Data;
use LSYS\Uploader\Exception;
use LSYS\Uploader\HandlerTrait;
use function LSYS\Uploader\__;
class DataImage extends DataFile{
    use HandlerTrait;
    /**
     * @return string
     */
    public function saveData($file_data,&$name=null){
        $typeCode=$this->_checkType($file_data);
        if (empty($name))$name=uniqid();
        $name = $name.'.'.$typeCode;
        return parent::saveData($file_data,$name);
    }
    public function checkData($file_data){
        $this->_checkType($file_data);
    }
    protected function _checkType($file_data){
        $strInfo = @unpack("C2chars", $file_data);
        if(!isset($strInfo['chars1'])||!isset($strInfo['chars2'])){
            throw new Exception(__('file data not parse'));
        }
        $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
        switch ($typeCode) {
            case 255216: $typeCode= 'jpg'; break;
            case 7173: $typeCode= 'gif'; break;
            case 6677: $typeCode= 'bmp'; break;
            case 13780: $typeCode= 'png'; break;
            default:throw new Exception(__('file data not support image'));
        }
        $config=$this->config();
        if ($config!=null){
            $exts=(array)$config->get("exts",array());
            if (count($exts)>0&&!in_array($typeCode, $exts)){
                throw new Exception(__("remote file ext allowed is :exts",array(":exts"=>implode(",",$exts))));
            }
        }
        return $typeCode;
    }
}