<?php
namespace YiiThrift;

/**
 * Interface ThriftServer
 * @package YiiThrift
 */
interface ThriftServerInterface{
    /**
     * @return string the class name of the processor class.
     */
    public function getProcessorClass();
}
