<?php
namespace LSYS\Uploader;
use LSYS\Uploader;
class FixedUploader implements Uploader{
    /**
     * @var \LSYS\Uploader\Upload[]
     */
    protected $_upload=[];
    /**
     * @param string $name
     * @param Upload|callable $upload
     * @return $this
     */
    public function set($name,$upload){
		if(!is_callable($upload))assert($upload instanceof \LSYS\Uploader\Upload);
        $this->_upload[$name]=$upload;
        return $this;
    }
    /**
     * @return array
     */
    public function allName(){
        return array_keys($this->_upload);
    }
    /**
     * @param string $name
     * @return \LSYS\Uploader\Upload|null
     */
    public function find($name){
        foreach ($this->_upload as $k=>$v){
            if ($name==$k){
				if(is_callable($v)){
					$v=call_user_func($v);
					assert($v instanceof \LSYS\Uploader\Upload);
					$this->_upload[$k]=$v;
				}
				return $v;
			}
        }
        return null;
    }
    public function findAll(){
		foreach ($this->_upload as $k=>$v){
			if(!is_callable($v))continue;
			$v=call_user_func($v);
			assert($v instanceof \LSYS\Uploader\Upload);
			$this->_upload[$k]=$v;
        }
		return $this->_upload;
	}
}
