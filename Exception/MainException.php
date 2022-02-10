<?php


namespace StarCore\Exception;

use StarCore\Common\Tool\GlobalFun;
use StarCore\Core\File;
use Throwable;

class MainException
{
    private static bool $debug;
    private static string $logPath;

    /**
     * @param bool $debug 是否调试模式，调试模式：异常直接输出，非调试模式：异常记录文件
     * @param string $logPath 非调试模式时，错误记录路径
     */
    public static function Start(bool $debug, string $logPath)
    {
        self::$debug = $debug;
        self::$logPath = $logPath;
        set_exception_handler(callback: array("StarCore\Exception\MainException", "ExceptionHandler"));
    }

    public static function ExceptionHandler(Throwable $exception)
    {
        if (method_exists($exception, "Render")) {
            //自定义类
            $exception->Render();
        } else {
            //系统类
            self::Render($exception);
        }
    }

    public static function Render(Throwable $exception, string $errorInfo = "", string $file = "Exception")
    {
        $content = '[' . date("Y-m-d H:i:s") . ']' . PHP_EOL;
        $content .= get_class($exception) . ": {$errorInfo} {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()} ErrorCode:{$exception->getCode()}" . PHP_EOL;
        $content .= "Stack trace:" . PHP_EOL;
        foreach ($exception->getTrace() as $trace) {
            $args = isset($trace['args']) ? json_encode($trace['args']) : "";
            $class = $trace['class'] ?? "";
            $type = $trace['type'] ?? " ";
            $content .= "    {$trace['file']}({$trace['line']}): {$class}{$type}{$trace['function']}({$args})" . PHP_EOL;
        }
        if (self::$debug) {
            GlobalFun::echoHtml($content);
            exit;
        } else {
            $date = date("Ymd");
            $file = new File(self::$logPath . $file . "_" . $date);
            $file->writeAppend($content);
        }
    }
}