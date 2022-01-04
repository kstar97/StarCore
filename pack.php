<?php

use StarCore\Core\Pack;
use StarCore\Core\UnPack;
use StarCore\Exception\MainException;


define("ROOT_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR);
const LOG_PATH = ROOT_PATH . 'Log' . DIRECTORY_SEPARATOR;

include_once "vendor/autoload.php";

const DEBUG = true;

//异常捕获
MainException::Start(DEBUG, LOG_PATH . 'Exception' . DIRECTORY_SEPARATOR);

$pack = new Pack();
$pack->writeString("hello");
$pack->writeInt(100);
$pack->writeShort(50);
$data = $pack->getBinary();
var_dump($data);

$unPack = new UnPack($data);
$reData = $unPack->readString(strlen("hello"));
var_dump($reData);
$reData = $unPack->readInt();
var_dump($reData);
$reData = $unPack->readShort();
var_dump($reData);