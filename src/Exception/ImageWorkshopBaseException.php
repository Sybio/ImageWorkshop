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
class ImageWorkshopBaseException extends \Exception implements \Stringable
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return self::class.": [{$this->code}]: {$this->message}\n";
    }
}
