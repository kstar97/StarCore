<?php


namespace StarCore\Core;

/*
 * 文件操作类
 */
class File
{
    //文件名
    private string $fileName;

    function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $content
     * 向文件末尾追加内容
     */
    public function writeAppend(string $content): void
    {
        $handle = fopen($this->fileName, "a");
        flock($handle, LOCK_EX);
        fwrite($handle, $content);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    /**
     * @param int $line
     * @return array
     * 从文件取走N行数据（会修改文件）
     * $line：取走行数，默认1
     */
    public function getDataByLine(int $line = 1): array
    {
        $contentArr = array();
        $content = "";
        $handle = fopen($this->fileName, "r+");
        flock($handle, LOCK_EX);
        //读取内容
        for ($i = 0; $i < $line; $i++) {
            $content = fgets($handle);
            if (empty($content)) {
                //文件已读完
                break;
            }
            $contentArr[] = $content;
        }
        //剩下的输出到缓存区
        ob_start();
        fpassthru($handle);
        //清空原文件
        rewind($handle);
        ftruncate($handle, 0);
        //保存缓存区到文件
        fwrite($handle, ob_get_clean());
        flock($handle, LOCK_UN);
        fclose($handle);

        return $contentArr;
    }

    public function __destruct()
    {
        unset($this->fileName);
    }
}