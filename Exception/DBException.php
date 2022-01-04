<?php


namespace StarCore\Exception;

class DBException extends \Exception
{
    public function Render()
    {
        MainException::Render($this);
    }
}