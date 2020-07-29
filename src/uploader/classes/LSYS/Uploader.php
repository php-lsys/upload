<?php
namespace LSYS;
interface Uploader{
    /**
	 * return uploader handler
     * @param string $name
     * @return \LSYS\Uploader\Handler|null
     */
    public function find($name);
	/**
     * return all uploader handler
     * [$key]=>\LSYS\Uploader\Handler
     * @return \LSYS\Uploader\Handler[]
     */
    public function findAll();
}
