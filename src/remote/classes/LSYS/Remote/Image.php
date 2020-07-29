<?php
/**
 * lsys remote
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Remote;
use LSYS\Remote;
class Image extends Remote{
	/**
	 * 重置的扩展名
	 * @var string
	 */
	protected $_reext=false;
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Upload::check()
	 */
	public function check(){
		if(!parent::check())return false;
		return count($this->_error)==0;
	}
	/**
	 * check upload file type
	 * @return boolean
	 */
	public function checkType()
	{
		return true;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Remote::_writeFn()
	 */
	public function _writeFn($ch, $str){
		$len=parent::_writeFn($ch, $str);
		if ($this->_save_size==strlen($str)){
			$strInfo = @unpack("C2chars", $str);
			$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
			$fileType = '';
			switch ($typeCode) {
				case 255216: $fileType = 'jpg'; break;
				case 7173: $fileType = 'gif'; break;
				case 6677: $fileType = 'bmp'; break;
				case 13780: $fileType = 'png'; break;
			}
			$this->_ext=$this->_reext='.'.$fileType;
			$config=$this->_config;
			if ($config!=null){
				$exts=(array)$config->get("exts",array());
				if (count($exts)>0&&!in_array($fileType, $exts)){
					$this->_error[]=__("remote file ext allowed is :exts",array(":exts"=>implode(",",$exts)));
					return false;
				}
			}
		}
		return $len;
	}
	/**
	 * {@inheritDoc}
	 * 扩展根据获取到的实际文件名会重置
	 * @see \LSYS\Remote::save()
	 */
	public function save(&$filename = NULL,$chmod = 0644){
		$filename=parent::save($filename,$chmod );
		$ext=".".pathinfo($filename, PATHINFO_EXTENSION);
		if ($this->_reext&&$ext!=$this->_reext){
			$newname=substr($filename, 0,strlen($filename)-strlen($this->_ext)).$this->_reext;
			rename($filename, $newname);
		}
		return isset($newname)?$newname:$filename;
	}
}