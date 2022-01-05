<?php


namespace StarCore\Common;

use StarCore\Core\DB;
use StarCore\Exception\DBException;

/**
 * 数据库连接池
 * 数据库配置在Config/DBConfig目录中，文件名DBConfig.key.php
 * 取链接的时候传：key
 * 用完放回池里
 */
class DBPool
{
    /**
     * 单例
     */
    protected function __construct()
    {
    }

    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private string $configPath = "";
    private array $pool = [];

    /**
     * @param string $configPath 数据库配置文件路径
     */
    public function init(string $configPath): void
    {
        $this->configPath = $configPath;
    }

    /**
     * @param string $key
     * @return DB
     * 从池里取一个链接
     * @throws DBException
     */
    public function getDB(string $key): DB
    {
        if (empty($this->pool[$key])) {
            //池里没有，创建一个
            $config = $this->getConfig($key);
            $db = new DB($config['host'], $config['port'], $config['dbname'], $config['charset'], $config['user'], $config['passwd']);
        } else {
            $db = array_pop($this->pool[$key]);
        }
        return $db;
    }

    public function putDB(string $key, DB $db): void
    {
        if (empty($this->pool[$key])) {
            $this->pool[$key] = [];
        }
        $this->pool[$key][] = $db;
    }

    /**
     * @param string $key
     * @return array
     * @throws DBException
     */
    private function getConfig(string $key): array
    {
        $file = sprintf('%sDBConfig.%s.php', $this->configPath, $key);
        if (!is_file($file)) {
            throw new DBException("【{$key}】配置文件不存在：$file");
        }
        $config = include $file;
        if (!isset($config['host'])) {
            throw new DBException("【host】未配置");
        }
        if (!isset($config['port'])) {
            throw new DBException("【port】未配置");
        }
        if (!isset($config['dbname'])) {
            throw new DBException("【dbname】未配置");
        }
        if (!isset($config['charset'])) {
            throw new DBException("【charset】未配置");
        }
        if (!isset($config['user'])) {
            throw new DBException("【user】未配置");
        }
        if (!isset($config['passwd'])) {
            throw new DBException("【passwd】未配置");
        }
        return $config;
    }
}