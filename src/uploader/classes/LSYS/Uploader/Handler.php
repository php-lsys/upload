<?php
namespace LSYS\Uploader;
interface Handler{
    /**
     * @return int
     */
    public function chmod();
    /**
     * @return \LSYS\Config
     */
    public function config();
}