<?php
/**
 * lsys upload
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use function LSYS\Upload\__;

class Upload {
	/**
	 * @var  string  default upload directory
	 */
	public static $default_directory = NULL;
	/**
	 * @var Config
	 */
	protected $_config;
	/**
	 * @var array
	 */
	protected $_file;
	/**
	 * @var array
	 */
	protected $_error=array();
	/**
	 * @param array $file
	 * @param Config $config
	 */
	public function __construct(array $file,Config $config=null){
		$this->_file=$file;
		$this->_config=$config;
	}
	/**
	 * get upload file ext
	 */
	public function getExt():string{
		return '.'.pathinfo($this->_file['name'], PATHINFO_EXTENSION);
	}
	/**
	 * save file as $filename
	 * @param string $filename
	 * @param number $chmod
	 * @throws Exception
	 * @return boolean|string
	 */
	public function save(?string &$filename = NULL,$chmod = 0644)
	{
		if (!$this->check())return false;
		if ($filename === NULL)
		{
			if ($filename==null){
				$ext=$this->getExt();
			}else{
			    $ext=".".pathinfo($filename, PATHINFO_EXTENSION);
			}
			$filename = uniqid().$ext;
		}
		if ($this->_config==null){
			if (self::$default_directory==null){
				$dir=__DIR__."/../../assets/";
				self::$default_directory=$dir;
			}
			$directory = self::$default_directory;
		}
		else $directory = $this->_config->get("dir",self::$default_directory);
		
		$this->_makeDir($directory);
		if (!is_writable(realpath($directory))){
			throw new Exception(__('Directory :dir must be writable',array(":dir"=>$directory)));
		}
		// Make the filename into a complete path
		$_filename = realpath($directory).DIRECTORY_SEPARATOR.$filename;
		$this->_save($_filename);
		if ($chmod !== FALSE)
		{
			// Set permissions on filename
		    @chmod($_filename, $chmod);
		}
		// Return new file path
		return $_filename;
	}
	//save file
	protected function _save($filename){
		//move file
		if (!@move_uploaded_file($this->_file['tmp_name'], $filename)){
			throw new Exception(__('move :tmp_file to :file fail',array(":file"=>$filename,':tmp_file'=>$this->_file['tmp_name'])));
		}
	}
	/**
	 * create upload save directory
	 * @param string $path
	 * @throws Exception
	 */
	protected function _makeDir(string $dir):bool{
		$is_linux=false;
		$dir=str_replace(array('\\','//'),'/',$dir);
		if(substr($dir, 0,1)=='/')$is_linux=true;
		$dir=explode("/",$dir);
		if(empty($dir)) return false;
		$t_dir='';
		$one=true;
		$sdir=ini_get("open_basedir");
		if ($sdir){
			$sdir=explode(":", $sdir);
			$sdir=array_map('realpath', $sdir);
		}
		foreach($dir as $v){
			if(empty($v)) continue;
			if($one&&!$is_linux){
				$t_dir.=$v;
				$one=false;
			}else $t_dir.=DIRECTORY_SEPARATOR.$v;
			$t_dir=str_replace("\\", "/", $t_dir);
			if(is_array($sdir))foreach ($sdir as $vv){
				if (strpos($vv,$t_dir)===0)continue 2;
			}
			if(is_dir($t_dir))continue;
			if (!is_writable($t_dir))throw new Exception(__("can't write directory :dir",array(":dir"=>$t_dir)));
			if(!@mkdir($t_dir,0777)) throw new Exception(__("can't create directory :dir",array(":dir"=>$t_dir)));
			@chmod($t_dir, 0777);
		}
		return true;
	}
	/**
	 * check upload file
	 * @throws Exception
	 */
	public function check():bool{
		if(!$this->valid())return false;
		$this->checkSize();
		$this->checkEmpty();
		$this->checkType();
		return count($this->_error)==0;
	}
	/**
	 * get check error message
	 */
	public function errors(){
		return $this->_error;
	}
	/**
	 * Tests if upload data is valid, even if no file was uploaded. If you
	 * _do_ require a file to be uploaded, add the [Upload::not_empty] rule
	 * before this rule.
	 *
	 *     $array->rule('file', 'Upload::valid')
	 *
	 * @param   array   $file   $_FILES item
	 * @return  bool
	 */
	public function valid()
	{
		$file=$this->_file;
		$status=(isset($file['error'])
			AND isset($file['name'])
			AND isset($file['type'])
			AND isset($file['tmp_name'])
			AND isset($file['size']));
		if (!$status){
			$this->_error[]=__("file array is not valid");//文件数组无效
		}
		return $status;
	}
	/**
	 * check upload file is empty
	 * @return boolean
	 */
	public function checkEmpty():bool
	{
		$file=$this->_file;
		$status= (isset($file['error'])
			AND isset($file['tmp_name'])
			AND $file['error'] === UPLOAD_ERR_OK
			AND is_uploaded_file($file['tmp_name']));
		if ($this->_file['error']!=UPLOAD_ERR_INI_SIZE&&!$status){
			$this->_error[]=__("upload file is empty");//上传文件为空
		}
		return $status;
	}
	/**
	 * check upload file type
	 * @return boolean
	 */
	public function checkType():bool
	{
		$file=$this->_file;
		if (!isset($file['name']))return false;
		$config=$this->_config;
		if ($config==null)return true;
		$exts=(array)$config->get("exts",array());
		if (count($exts)==0)return  true;
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(in_array($ext, $exts))return true;
		$this->_error[]=__("upload file ext allowed is :exts",array(":exts"=>implode(",",$exts)));
		return false;
	}
	/**
	 * check upload file size
	 * @return boolean
	 */
	public function checkSize():bool
	{
		if (!isset($this->_file['error'])||!isset($this->_file['size']))return false;
		switch ($this->_file['error']) {
			case UPLOAD_ERR_INI_SIZE:
				$upload_max=ini_get("upload_max_filesize");
				$error=__("The uploaded file exceeds the upload_max_filesize[:size] directive in php.ini",
					array(":size"=>$upload_max)
				);
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$error=__("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
				break;
			case UPLOAD_ERR_PARTIAL:
				$error=__("The uploaded file was only partially uploaded");
				break;
			case UPLOAD_ERR_NO_FILE:
				$error=__("No file was uploaded");
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$error=__("Missing a temporary folder");
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$error=__("Failed to write file to disk");
				break;
			case UPLOAD_ERR_EXTENSION:
				$error=__("File upload stopped by extension");
			break;
		}
		if (!isset($error)&&$this->_file['error'] !== UPLOAD_ERR_OK)
		{
			$error=__("The upload failed");
		}
		if (isset($error)){
			$this->_error[]=$error;
			return false;
		}
		$config=$this->_config;
		if ($config==null)return true;
		$size=$config->get("size",ini_get("upload_max_filesize"));
		if (empty($size))return true;
		if($this->_file['size'] > self::_bytes($size)){
			$error=__("upload file limit size :size",array(":size"=>$size));
		}
		if (isset($error)){
			$this->_error[]=$error;
			return false;
		}
		return true;
	}
	protected static function _bytes($size)
	{
		$byte_units = array
		(
			'B'   => 0,
			'K'   => 10,
			'Ki'  => 10,
			'KB'  => 10,
			'KiB' => 10,
			'M'   => 20,
			'Mi'  => 20,
			'MB'  => 20,
			'MiB' => 20,
			'G'   => 30,
			'Gi'  => 30,
			'GB'  => 30,
			'GiB' => 30,
			'T'   => 40,
			'Ti'  => 40,
			'TB'  => 40,
			'TiB' => 40,
			'P'   => 50,
			'Pi'  => 50,
			'PB'  => 50,
			'PiB' => 50,
			'E'   => 60,
			'Ei'  => 60,
			'EB'  => 60,
			'EiB' => 60,
			'Z'   => 70,
			'Zi'  => 70,
			'ZB'  => 70,
			'ZiB' => 70,
			'Y'   => 80,
			'Yi'  => 80,
			'YB'  => 80,
			'YiB' => 80,
		);
		$size = trim( (string) $size);
		$accepted = implode('|', array_keys($byte_units));
		$pattern = '/^([0-9]+(?:\.[0-9]+)?)('.$accepted.')?$/Di';
		$matches=null;
		if ( ! preg_match($pattern, $size, $matches))return 0;
		$size = (float) $matches[1];
		$unit = isset($matches[2])?$matches[2]:'B';
		$bytes = $size * pow(2, $byte_units[$unit]);
		return $bytes;
	}
	
}
