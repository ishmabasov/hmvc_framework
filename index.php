<?php

define("BASEPATH", $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR);
define("APPPATH",  BASEPATH . "application" . DIRECTORY_SEPARATOR);

function custom_log($message) {
    $logDirectory = BASEPATH . "logs" . DIRECTORY_SEPARATOR;

    $year  = date('Y');
    $month = date('m');
    $day   = date('Y-m-d');

    $logPath = $logDirectory . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day . '.php';

    $logDir = dirname($logPath);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    if (!file_exists($logPath)) {
       $logMessage  = "<?php\n";
       $logMessage .= "defined('BASEPATH') OR exit('No direct scripts allowed');\n";
    } else {
       $logMessage  = "";
    }

    $timestamp   = date('Y-m-d H:i:s');
    $logMessage .= "[$timestamp] $message\n";

    file_put_contents($logPath, $logMessage, FILE_APPEND);
}

function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $errorMessage = "Error [$errno]: $errstr in $errfile on line $errline";

    custom_log($errorMessage);

    return false;
}

set_error_handler('custom_error_handler');

function custom_exception_handler($exception) {
    $exceptionMessage = "Uncaught exception: " . $exception->getMessage() .
                        " in " . $exception->getFile() .
                        " on line " . $exception->getLine();

    custom_log($exceptionMessage);
}


set_exception_handler('custom_exception_handler');

require_once BASEPATH . "core" . DIRECTORY_SEPARATOR . "Maga.php";
