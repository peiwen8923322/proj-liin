<?php
    /*
     測試版common.php
     */
    session_start();
    date_default_timezone_set('Asia/Taipei'); //設定時區
    // set to the user defined error handler
    // $old_error_handler = set_error_handler("myErrorHandler");

    //常數定義
    define("WWWROOT", "http://192.168.204.28/liin-testing/public/login.php");
    define("INDEXPAGE", "http://192.168.204.28/liin-testing/Apps/index/index.php");

    //變數定義

    //require_once
    require_once "../../models/cls_form.php";
    require_once "../../models/cls_models.php";

    /*
    //DB Connetcion config
    $host = 'localhost';
    $db = 'liin';
    $user = 'liin';
    $paword = '/eg9XQbo(K4.*25e';
    $chrs = 'utf8mb4';
    $dsn = "mysql:host={$host};dbname={$db};charset={$chrs}";
    $opts = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false];

    //Open Connection
    try{
        $obj_PDO = new PDO($dsn, $user, $paword, $opts);
    }
    catch(PDOException $e){
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
     */

    // error handler function
    function myErrorHandler($errNo, $errMsg, $errFile, $errLine)
    {
        //Begin
        /* 
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
        */

        // $errstr may need to be escaped:
        $errMsg = htmlspecialchars($errMsg);

        $_SESSION['error']['errNo'] = $errNo; //$errNo
        $_SESSION['error']['errMsg'] = "[錯誤訊息]: $errMsg"; //$errMsg
        $_SESSION['error']['errFile'] = $errFile; //$errFile
        $_SESSION['error']['errLine'] = $errLine; //$errLine

        /* 
        switch ($errno) {
            case E_USER_ERROR:
                $_SESSION['error'] = "<b>My ERROR</b> [$errno] $errstr<br />";
                $_SESSION['error'] .=  "  Fatal error on line $errline in file $errfile";
                $_SESSION['error'] .=   ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />";
                $_SESSION['error'] .=   "Aborting...<br />";
                exit(1);

            case E_USER_WARNING:
                $_SESSION['error'] = "<b>My WARNING</b> [$errno] $errstr<br />";
                break;

            case E_USER_NOTICE:
                $_SESSION['error'] = "<b>My NOTICE</b> [$errno] $errstr<br />";
                break;

            default:
                $_SESSION['error'] = "[$errno] $errstr";
                break;
        }
        */

        /* Don't execute PHP internal error handler */
        return true;
        //End
    }

    //權限管理
?>