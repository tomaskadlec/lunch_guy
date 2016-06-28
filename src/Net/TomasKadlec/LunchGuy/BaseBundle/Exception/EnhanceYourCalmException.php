<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * An exception indicating HTTP status
 *
 * 420 Enhance your calm
 * Rate is beeing limited
 *
 * Class EnhanceYourCalmException
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Exception
 */
class EnhanceYourCalmException extends HttpException
{
    public function __construct($statusCode = 420, $message = "Enhance your calm", \Exception $previous = null, array $headers = array(), $code = 420)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

}