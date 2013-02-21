<?php
require "lessc.inc.php";

chdir('../');
// set up path mapper
    require './Library/Pvik/Core/Path.php';
    \Pvik\Core\Path::Init();
    
    // set up class loader
    require \Pvik\Core\Path::RealPath('~/Library/Pvik/Core/ClassLoader.php');
    $ClassLoader = new \Pvik\Core\ClassLoader();
    $ClassLoader->SetNamespaceAssociation('\\Pvik', '~/Library/Pvik/');
    $ClassLoader->SetNamespaceAssociation('\\Dashbird', '~/Application/');
    $ClassLoader->Init();
    
    $Core = new \Pvik\Core\Core();
    $Core->LoadConfig(array(
			'~/Application/Configs/DefaultConfig.php',
			'~/Application/Configs/Config.php')
			);

$less = new lessc;
$css = $less->compileFile(Pvik\Core\Path::RealPath("~/Application/less/dashboard.less"));



// delete previous files
$regex = '/^dashbird-/';
if ($fileHandle = opendir(\Pvik\Core\Path::RealPath('~/css/'))) {
    while (false !== ($file = readdir($fileHandle))) {
        if(preg_match($regex , $file)){
            unlink(\Pvik\Core\Path::RealPath('~/css/' . $file));
        }
    }
    closedir($fileHandle);
}


$Version = Pvik\Core\Config::$Config['Version'];

file_put_contents(Pvik\Core\Path::RealPath('~/css/dashbird-' . $Version .'.css'), $css);