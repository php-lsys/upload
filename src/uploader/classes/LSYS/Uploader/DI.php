<?php
namespace LSYS\Uploader;
/**
 * @method \LSYS\Uploader uploader()
 */
class DI extends \LSYS\DI{
    /**
     * @return DI
     */
    public static function get(){
        $di=parent::get();
        !isset($di->uploader)&&$di->uploader(new \LSYS\DI\SingletonCallback(function(){
            return new \LSYS\Uploader\FixedUploader();
        }));
        return $di;
    }
}