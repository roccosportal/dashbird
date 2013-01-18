<?php
require "lessc.inc.php";

chdir('../');
require_once ("./library/pvik/core/core.php");
    $Core = new Pvik\Core\Core();
    $Core->Init()
        ->LoadConfig();

$less = new lessc;
$compiled = $less->compileFile(Pvik\Core\Path::RealPath("~/application/less/dashboard.less"));



$Version = Pvik\Core\Config::$Config['Version'];

file_put_contents(Pvik\Core\Path::RealPath('~/css/dashbird-' . $Version .'.css'), $compiled);