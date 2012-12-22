<?php
require "lessc.inc.php";

class Config {
    public static $Config;
    
    public static function Load(){
        require '../application/configs/config.php';
    }
}
Config::Load();

$less = new lessc;
$compiled = $less->compileFile("../application/less/dashboard.less");



$Version = Config::$Config['Version'];

file_put_contents('../css/dashbird-' . $Version .'.css', $compiled);