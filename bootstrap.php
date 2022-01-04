<?php

use StarCore\Common\DBPool;
use StarCore\Exception\MainException;

//自动加载
include "vendor/autoload.php";

/**
 * 是否开启调试模式
 * 调试模式下异常会直接输出
 * TODO关联到异常处理类
 */
define("DEBUG", true);

define("ROOT_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR);
define("CONFIG_PATH", ROOT_PATH . 'Config' . DIRECTORY_SEPARATOR);
define("LOG_PATH", ROOT_PATH . 'Log' . DIRECTORY_SEPARATOR);

//异常捕获
MainException::Start(DEBUG, LOG_PATH . 'Exception' . DIRECTORY_SEPARATOR);


//test
DBPool::GetInstance()->Init(CONFIG_PATH . 'DBConfig' . DIRECTORY_SEPARATOR);
$db = DBPool::GetInstance()->GetDB('key');

//查
$data = $db->table('user')->where('id', '=', 1)->selectOne();
var_dump($data);

//改
$tel = isset($data['tel']) && $data['tel'] >= 0 ? $data['tel'] + 1 : 0;
$upData = [
    'tel' => $tel,
];
$rows = $db->table('user')->where('id', '=', 1)->update($upData);
var_dump($rows);

//增
$inData = [
    'name' => 'testtesttest',
    'home' => 'testtesttest',
];
$rows = $db->table('user')->insert($inData);
var_dump($rows);

//删
$rows = $db->table('user')->where('name', '=', 'testtesttest')->delete();
var_dump($rows);