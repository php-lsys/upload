<?php
/**
 * lsys remote
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use function LSYS\Remote\__;
class Remote{
	/**
	 * @var  string  default upload directory
	 */
	public static $default_directory = NULL;
	/**
	 * @var Config
	 */
	protected $_config;
	/**
	 * @var string
	 */
	protected $_file;
	/**
	 * @var array
	 */
	protected $_error=array();
	protected $_ext;
	protected $_ch;
	/**
	 * @var string
	 */
	protected $_save_ch;
	//写入的文件名
	protected $_save_file;
	//已写入文件大小
	protected $_save_size=0;
	/**
	 * @param array $file
	 * @param Config $config
	 */
	public function __construct($file_url,Config $config=null){
		$this->_file=$file_url;
		$this->_config=$config;
		$ch=$this->_ch=curl_init($file_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // a true curl_exec return content
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_config->get("timeout",60)); // 60 second
		curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // don't check certificate
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // don't check certificate
		curl_setopt($ch, CURLOPT_HEADER, false); // true Return the HTTP headers in string, no good with CURLOPT_HEADERFUNCTION
		//curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192); // 8192 8k
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this,'_writeFn')); // callad every CURLOPT_BUFFERSIZE
		$this->_ext='.'.pathinfo($this->_file, PATHINFO_EXTENSION);
	}
	/**
	 * 回调写入文件
	 * @param mixed $ch
	 * @param mixed $str
	 * @return boolean|number
	 */
	public function _writeFn($ch, $str){
		$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if ($httpCode==0||($httpCode>=300)){
			$this->_error[]=__("remote file not find");
			return false;
		}
		$len = strlen($str);
		$this->_save_size+=$len;
		if ($this->_max()!==true&&$this->_save_size>$this->_max()){
			$this->_error[]=__("remote file limit size :size",array(":size"=>$this->_config->get("size")));
			return false;
		}
		$this->_save_ch&&fwrite($this->_save_ch, $str);
		return $len;
	}
	/**
	 * 结束清理
	 * @param string $clear
	 */
	protected function _endCurl($clear=true){
		$this->_save_ch&&fclose($this->_save_ch);
		@curl_close($this->_ch); // close curl resource
		if ($clear){
			if (is_file($this->_save_file))@unlink($this->_save_file);
		}
		$this->_ch=null;
	}
	/**
	 * get upload file ext
	 */
	public function getExt(){
		return $this->_ext;
	}
	/**
	 * save file as $filename
	 * @param string $filename
	 * @param number $chmod
	 * @throws Exception
	 * @return boolean|string
	 */
	public function save(&$filename = NULL,$chmod = 0644)
	{
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
			$this->_endCurl();
			throw new Exception(__('Directory :dir must be writable',array(":dir"=>$directory)));
		}
		// Make the filename into a complete path
		$this->_save_file=$_save_file = realpath($directory).DIRECTORY_SEPARATOR.$filename;
		$this->_save_ch = fopen($_save_file, "w+b");
		
		if ( ! curl_exec($this->_ch) ) {
			$msg=curl_error($this->_ch);
			$no=curl_errno($this->_ch);
			$this->_endCurl();
			if ($no==23&&count($this->_error)>0){
				return false;
			}
			$this->_error[]=$msg;
			return false;
		}
		//move file
		if ($chmod !== FALSE)
		{
			// Set permissions on filename
			@chmod($_save_file, $chmod);
		}
		$this->_endCurl(false);
		// Return new file path
		return $_save_file;
	}
	public function __destruct(){
		if ($this->_ch)$this->_endCurl();
	}
	/**
	 * create upload save directory
	 * @param string $path
	 * @throws Exception
	 */
	protected function _makeDir($dir){
		$is_linux=false;
		$dir=str_replace(array('\\','//'),'/',$dir);
		if(substr($dir, 0,1)=='/')$is_linux=true;
		$dir=explode("/",$dir);
		if(empty($dir)) return ;
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
			if(!@mkdir($t_dir,0777)){
				$this->_endCurl();
				throw new Exception(__("can't create directory :dir",array(":dir"=>$t_dir)));
			}
			@chmod($t_dir, 0777);
		}
		return true;
	}
	/**
	 * check upload file
	 * @throws Exception
	 */
	public function check(){
	    $this->checkType();
	    if ( ! curl_exec($this->_ch) ) {
	        $msg=curl_error($this->_ch);
	        $no=curl_errno($this->_ch);
	        if ($no==23&&count($this->_error)>0){
	            return false;
	        }
	        $this->_error[]=$msg;
	    }
		return count($this->_error)==0;
	}
	/**
	 * get check error message
	 */
	public function errors(){
		return $this->_error;
	}
	/**
	 * check upload file type
	 * @return boolean
	 */
	public function checkType()
	{
		$config=$this->_config;
		if ($config==null)return true;
		$exts=(array)$config->get("exts",array());
		if (count($exts)==0)return  true;
		$ext = $this->getExt();
		if(in_array($ext, $exts))return true;
		$this->_error[]=__("remote file ext allowed is :exts",array(":exts"=>implode(",",$exts)));
	}
	/**
	 * 得到最大的文件限制
	 * @return boolean|number
	 */
	protected function _max(){
		static $size;
		if ($size)return $size;
		$config=$this->_config;
		if ($config==null){
			$size=true;
			return true;
		}
		$size=$config->get("size");
		if (empty($size)){
			$size=true;
			return true;
		};
		$size=self::_bytes($size);
		return $size;
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
