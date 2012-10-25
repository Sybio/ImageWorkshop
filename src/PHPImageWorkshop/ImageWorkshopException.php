<?php

namespace PHPImageWorkshop;

/**
 * ImageWorkshopException
 * 
 * Manage ImageWorkshop exceptions
 */
class ImageWorkshopException extends \Exception
{
    /**
     * Constructor
     * 
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * __toString method
     */
    public function __toString()
    {
        return __CLASS__.": [{$this->code}]: {$this->message}\n";
    }
}