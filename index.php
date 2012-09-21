<?php

    error_reporting(E_ALL ^ E_NOTICE);
    
    require_once ("./library/pvik/core/core.php");
    $Core = new Pvik\Core\Core();
    $Core->Init()
        ->LoadConfig()
        ->StartWeb();
?>