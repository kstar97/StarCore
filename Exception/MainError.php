<?php


namespace StarCore\Exception;

use StarCore\Common\Tool\GlobalFun;
use StarCore\Core\File;

class MainError
{
    private static bool $debug;
    private static string $logPath;
    private static array $errorType;

    /**
     * @param bool $debug 是否调试模式，调试模式：异常直接输出，非调试模式：异常记录文件
     * @param string $logPath 非调试模式时，错误记录路径
     */
    public static function Start(bool $debug, string $logPath)
    {
        self::$debug = $debug;
        self::$logPath = $logPath;
        self::$errorType = [
            E_ERROR => "E_ERROR",
            E_WARNING => "E_WARNING",
            E_PARSE => "E_PARSE",
            E_NOTICE => "E_NOTICE",
            E_CORE_ERROR => "E_CORE_ERROR",
            E_CORE_WARNING => "E_CORE_WARNING",
            E_COMPILE_ERROR => "E_COMPILE_ERROR",
            E_COMPILE_WARNING => "E_COMPILE_WARNING",
            E_USER_ERROR => "E_USER_ERROR",
            E_USER_WARNING => "E_USER_WARNING",
            E_USER_NOTICE => "E_USER_NOTICE",
            E_STRICT => "E_STRICT",
            E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
            E_DEPRECATED => "E_DEPRECATED",
            E_USER_DEPRECATED => "E_USER_DEPRECATED",
        ];
        set_error_handler(callback: array("StarCore\\Exception\\MainError", "ErrorHandler"));
    }

    public static function ErrorHandler(int $errorType, string $errorStr, string $errorFile, int $errorLine)
    {
        $content = '[' . date("Y-m-d H:i:s") . ']' . PHP_EOL;
        $content .= self::$errorType[$errorType] . ": {$errorStr} in {$errorFile}:{$errorLine}" . PHP_EOL;
        if (self::$debug) {
            GlobalFun::echoHtml($content);
            exit;
        } else {
            $date = date("Ymd");
            $file = new File(self::$logPath  . "Error_" . $date);
            $file->writeAppend($content);
        }
    }
}