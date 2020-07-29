<?php
namespace LSYS\Uploader;
class Exception extends \LSYS\Exception{
    protected $_errors;
    /**
     * @param string[] $messages
     * @param \Exception $previous
     */
    public function __construct($messages,\Exception $previous = NULL)
    {
        $this->_errors=is_array($messages)?$messages:[$messages];
        if ($previous)$code=$previous->getCode();
        else $code=10086;
        parent::__construct(implode(",", $this->_errors), $code, $previous);
    }
    public function getErrors(){
        return $this->_errors;
    }
}