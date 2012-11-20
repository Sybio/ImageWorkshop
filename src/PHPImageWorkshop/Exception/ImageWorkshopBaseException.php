<?php

namespace PHPImageWorkshop\Exception;

/**
 * ImageWorkshopBaseException
 * 
 * The inherited exception class
 * 
 * @link http://phpimageworkshop.com
 * @author Bjørn Børresen | Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshopBaseException extends \Exception
{
    /**
     * Constructor
     * 
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * __toString method
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__.": [{$this->code}]: {$this->message}\n";
    }
}