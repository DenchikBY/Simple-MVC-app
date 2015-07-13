<?php namespace System;

class ErrorHandler
{

    public static function setHandlers()
    {
        ini_set('display_errors', 0);
        set_error_handler([new self, 'handle']);
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error != null) {
                self::handle($error['type'], $error['message'], $error['file'], $error['line']);
            }
        });
    }

    public static function handle($type, $message = '', $file = '', $line = 0)
    {

        echo '
        <style>
        table.error_info {
            border: #ff0000 1px solid;
            margin: 10px;
        }
        table.error_info td {
            border: #ff0000 1px solid;
            padding: 3px;
        }
        </style>
        <table class="error_info">
            <tr>
                <td>Error type</td>
                <td>' . self::stringErrorType($type) . '</td>
            </tr>
            <tr>
                <td>Error message</td>
                <td>' . $message . '</td>
            </tr>
            <tr>
                <td>File</td>
                <td>' . $file . '</td>
            </tr>
            <tr>
                <td>Error string</td>
                <td>' . self::getErrorString($file, $line) . '</td>
            </tr>
            <tr>
                <td>Stack trace</td>
                <td>' . self::stackTrace() . '</td>
            </tr>
        </table>';
    }

    private static function getErrorString($file, $line)
    {
        $lines = explode("\n", file_get_contents($file));
        $return = '';
        for ($i = $line - 1; $i < $line + 2; ++$i) {
            $return .= $i . ': ' . htmlspecialchars($lines[$i - 1]) . '<br>';
        }
        return $return;
    }

    private static function stringErrorType($type)
    {
        switch($type)
        {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }

    private static function stackTrace()
    {
        $stack = debug_backtrace();
        $count = count($stack);
        $return = '<table>';
        foreach ($stack as $key => $value) {
            $return .= '<tr><td>' . ($count - $key) . '</td><td>' . $value['class'] . $value['type'] . $value['function'] . '</td><td>' . (isset($value['file']) ? $value['file'] : '') . '</td></tr>';
        }
        $return .= '</table>';
        return $return;
    }

}
