<?php


namespace StarCore\Common\Tool;


use Exception;

class GlobalFun
{
    /**
     * @param string $content
     */
    public static function echoHtml(string $content)
    {
        echo "<pre>{$content}</pre>";
    }

    /**
     * @param int $length
     * @return string
     * 生成随机字符串
     */
    public static function getRandStr(int $length = 8): string
    {
        $rand = "0123456789abcdefghijklmnopqrstuvwxyz";
        $randLength = strlen($rand);
        $randStr = "";
        for ($i = 0; $i < $length; $i++) {
            $randStr .= substr($rand, rand(0, $randLength - 1), 1);
        }
        return $randStr;
    }

    /**
     * @param string $paramName
     * @return string
     * 根据参数名获取网络数据（Get、Post）
     */
    public static function getParam(string $paramName): string
    {
        $param = "";
        if (!isset($_REQUEST[$paramName])) {
            return $param;
        }
        //去掉空格与换行
        $param = trim($_REQUEST[$paramName]);
        //转义特殊字符
        $param = addslashes($param);
        //转换编码
        $encode = mb_detect_encoding($param, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
        return mb_convert_encoding($param, 'UTF-8', $encode);
    }

    /**
     * @return array
     * 获取所有网络数据
     */
    public static function getAllParam(): array
    {
        $AllParam = array();
        foreach ($_REQUEST as $key => $value) {
            $AllParam[$key] = self::getParam($key);
        }
        return $AllParam;
    }

    /**
     * @return array
     * 获取所有网络数据（无过滤）
     */
    public static function getAllParamNoFilter(): array
    {
        return $_REQUEST;
    }

    /**
     * @param $url
     * @return bool|string
     * 发送请求
     * TODO 支持请求类型，可选GET和POST
     */
    public static function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36 Edg/94.0.992.38');
        $tmp_sources = curl_exec($ch);
        //捕抓异常
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);
        }
        curl_close($ch);
        return $tmp_sources;
    }

    /**
     * @param string $file
     * @return void
     * 检查文件是否存在
     * @throws Exception
     */
    public static function checkFile(string $file)
    {
        if (!is_file($file)) {
            throw new Exception("文件不存在：" . $file);
        }
    }
}