<?php

use StarCore\Core\Socket\SocketServer;
use StarCore\Exception\MainException;


define("ROOT_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR);
const LOG_PATH = ROOT_PATH . 'Log' . DIRECTORY_SEPARATOR;

include_once "vendor/autoload.php";

const DEBUG = true;

//异常捕获
MainException::Start(DEBUG, LOG_PATH . 'Exception' . DIRECTORY_SEPARATOR);

$server = new SocketServer('127.0.0.1', 8888);