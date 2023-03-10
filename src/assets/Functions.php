<?php

namespace Pachel\EasyFrameWork;

class Functions
{
    public const
        ERROR_NOT_CONFIGURED = "The app is not configured!";

    private static $internalEncoding = 'UTF-8';
    /**
     * @param string $dir
     * @return mixed|string
     */
    public static function checkSlash($dir) {
        if (mb_substr($dir, strlen($dir) - 1, 1) == "/") {
            return $dir;
        }
        return $dir . "/";
    }
    public static function checkSlash2($dir) {
        if (mb_substr($dir, strlen($dir) - 1, 1) == "/") {
            return mb_substr($dir,0,mb_strlen($dir)-1);
        }
        return $dir;
    }
    public static function detract_last_dir($dir)
    {
        if(!$dirs = mb_strrpos($dir,"/")){
            return "";
        }
        return mb_substr($dir,0,$dirs+1);

    }
    public static function HTTPStatus($num) {
        $http = array(
            100 => 'HTTP/1.1 100 Continue',
            101 => 'HTTP/1.1 101 Switching Protocols',
            200 => 'HTTP/1.1 200 OK',
            201 => 'HTTP/1.1 201 Created',
            202 => 'HTTP/1.1 202 Accepted',
            203 => 'HTTP/1.1 203 Non-Authoritative Information',
            204 => 'HTTP/1.1 204 No Content',
            205 => 'HTTP/1.1 205 Reset Content',
            206 => 'HTTP/1.1 206 Partial Content',
            300 => 'HTTP/1.1 300 Multiple Choices',
            301 => 'HTTP/1.1 301 Moved Permanently',
            302 => 'HTTP/1.1 302 Found',
            303 => 'HTTP/1.1 303 See Other',
            304 => 'HTTP/1.1 304 Not Modified',
            305 => 'HTTP/1.1 305 Use Proxy',
            307 => 'HTTP/1.1 307 Temporary Redirect',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            402 => 'HTTP/1.1 402 Payment Required',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            405 => 'HTTP/1.1 405 Method Not Allowed',
            406 => 'HTTP/1.1 406 Not Acceptable',
            407 => 'HTTP/1.1 407 Proxy Authentication Required',
            408 => 'HTTP/1.1 408 Request Time-out',
            409 => 'HTTP/1.1 409 Conflict',
            410 => 'HTTP/1.1 410 Gone',
            411 => 'HTTP/1.1 411 Length Required',
            412 => 'HTTP/1.1 412 Precondition Failed',
            413 => 'HTTP/1.1 413 Request Entity Too Large',
            414 => 'HTTP/1.1 414 Request-URI Too Large',
            415 => 'HTTP/1.1 415 Unsupported Media Type',
            416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
            417 => 'HTTP/1.1 417 Expectation Failed',
            500 => 'HTTP/1.1 500 Internal Server Error',
            501 => 'HTTP/1.1 501 Not Implemented',
            502 => 'HTTP/1.1 502 Bad Gateway',
            503 => 'HTTP/1.1 503 Service Unavailable',
            504 => 'HTTP/1.1 504 Gateway Time-out',
            505 => 'HTTP/1.1 505 HTTP Version Not Supported',
        );
        return
            array(
                'code' => $num,
                'error' => $http[$num],
            );
    }
    public static function mb_substr($s, $start, $length = null, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return (string) substr($s, $start, null === $length ? 2147483647 : $length);
        }

        if ($start < 0) {
            $start = iconv_strlen($s, $encoding) + $start;
            if ($start < 0) {
                $start = 0;
            }
        }

        if (null === $length) {
            $length = 2147483647;
        } elseif ($length < 0) {
            $length = iconv_strlen($s, $encoding) + $length - $start;
            if ($length < 0) {
                return '';
            }
        }

        return (string) iconv_substr($s, $start, $length, $encoding);
    }
    private static function getEncoding($encoding)
    {
        if (null === $encoding) {
            return self::$internalEncoding;
        }

        if ('UTF-8' === $encoding) {
            return 'UTF-8';
        }

        $encoding = strtoupper($encoding);

        if ('8BIT' === $encoding || 'BINARY' === $encoding) {
            return 'CP850';
        }

        if ('UTF8' === $encoding) {
            return 'UTF-8';
        }

        return $encoding;
    }
    public static function get_random_string($count = 10, $extra = false, $chars = "qwertzuioplkjhgfdsayxcvbnm0123456789QWERTZUIOPLKJHGFDSAYXCVBNM")
    {
        if(is_string($extra)){

        }
        elseif($extra){
            $extra = "$.:-_*+|!%=";
        }
        else{
            $extra = "";
        }
        $string = "";
        for ($x = 0; $x < $count; $x++) {
            $string .= substr($chars.$extra, mt_rand(0, strlen($chars.$extra)), 1);
        }
        return $string;
    }
}