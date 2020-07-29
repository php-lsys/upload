<?php
namespace LSYS\Uploader;
trait HandlerTrait{
    protected $_config;
    protected $_chmod;
    public function __construct(\LSYS\Config $config,$chmod=0644){
        $this->_config=$config;
        $this->_chmod=$chmod;
    }
    public function chmod(){
        return $this->_chmod;
    }
    /**
     * @return \LSYS\Config
     */
    public function config(){
        return $this->_config;
    }
}
