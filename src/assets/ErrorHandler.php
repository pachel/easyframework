<?php

namespace Pachel\EasyFrameWork;

function errorHandler($errno, $errstr, $errfile, $errline, $contex) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

    switch ($errno) {
        case E_USER_ERROR:

            echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";

            break;

        case E_USER_WARNING:
            echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
            break;

        case E_USER_NOTICE:
            echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
            break;

        default:
            echo "Unknown error type: [$errno] $errstr<br />\n";
            echo "Info: $errline in file $errfile";
            break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
function exceptionHandler($exception) {

    // these are our templates
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

    // alter your trace as you please, here
    $trace = $exception->getTrace();

    foreach ($trace as $key => $stackPoint) {
        // I'm converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        if(is_array($trace[$key]['args'])) {
            $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
        }
    }

    // build your tracelines
    $result = array();
    foreach ($trace as $key => $stackPoint) {
        $result[] = sprintf(
            $traceline,
            $key,
            $stackPoint['file'],
            $stackPoint['line'],
            $stackPoint['function'],

            (!empty($stackPoint['args'])?implode(', ', $stackPoint['args']):"")
        );
    }
    // trace always ends with {main}
    $result[] = '#' . ++$key . ' {main}';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("\n", $result),
        $exception->getFile(),
        $exception->getLine()
    );
    $msg = str_pad("",150,"-")."\n\n".$msg."\n";
    if(Base::instance()->env("APP.TEST") || Base::instance()->env("APP.LOGS")==""){
        echo "<pre>";
        print_r($msg);
        echo "</pre>";
    }
    else{
        _log($msg);
        Base::instance()->send_error(500);
    }
}
if (!function_exists("_log")) {
    function _log($text) {
        if(!is_dir(Base::instance()->env("APP.LOGS"))){
            return false;
        }
        $logfile = Base::instance()->env("APP.LOGS")."/error.log";
        if (!file_exists($logfile)) {
            touch($logfile);
           // shell_exec("chmod -R 0777 \"".$logfile."\"");
        }
        $logsize = filesize($logfile);
        if ($logsize > (1024*1024* 1)) {
            rename($logfile, $logfile . "." . date("Ymd") . ".log");
            touch($logfile);
            shell_exec("chmod -R 0777 \"".$logfile."\"");
        }
        file_put_contents($logfile, "[" . date("Y-m-d H:i:s") . "]" . $text . "\n".file_get_contents($logfile));
        return true;
    }

}