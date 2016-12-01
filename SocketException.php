<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/11/29
 * Time: 下午6:48
 */
namespace ga\socket;
use Exception;
class SocketException extends Exception{

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}