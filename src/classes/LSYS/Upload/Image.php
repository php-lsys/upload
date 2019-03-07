<?php
/**
 * lsys upload
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Upload;
use LSYS\Upload;

class Image extends Upload{
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Upload::check()
	 */
	public function check(){
		if(!parent::check())return false;
		$this->checkImage();
		return count($this->_error)==0;
	}
	/**
	 * @return  boolean
	 */
	public function checkImage()
	{
		$file=$this->_file;
		if (!isset($file['tmp_name']))return false;
		try
		{
			list($w, $h) = getimagesize($file['tmp_name']);
		}
		catch (\ErrorException $e)
		{
			$this->_error[]=__("parse image fail: :msg",array("msg"=>$e->getMessage()));
			return false;
		}
		$config=$this->_config;
		if ($config==null)return true;
		$width=(array)$config->get("width",array());
		$height=(array)$config->get("height",array());
		$max_w=isset($width['max'])?$width['max']:0;
		if ($max_w&&$w>$max_w){
			$this->_error[]=__("file width max limit is :max,you are :ymax",
				array("max"=>$max_w,"ymax"=>$w)
			);
			return false;
		}
		$max_h=isset($height['max'])?$height['max']:0;
		if ($max_h&&$h>$max_h){
			$this->_error[]=__("file height max limit is :max,you are :ymax",
				array("max"=>$max_h,"ymax"=>$h)
			);
			return false;
		}
		$min_w=isset($width['min'])?$width['min']:0;
		if ($min_w&&$w<$min_w){
			$this->_error[]=__("file width min limit is :min,you are :ymin",
				array("min"=>$min_w,"ymin"=>$w)
			);
			return false;
		}
		$min_h=isset($height['min'])?$height['min']:0;
		if ($min_h&&$h<$min_h){
			$this->_error[]=__("file height min limit is :max,you are :ymax",
				array("min"=>$min_h,"ymin"=>$h)
			);
			return false;
		}
		return true;
	}
	
}