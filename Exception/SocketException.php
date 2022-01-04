<?php


namespace StarCore\Exception;

class SocketException extends \Exception
{
    public function Render()
    {
        MainException::Render($this);
    }
}