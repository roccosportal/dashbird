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
$compiled = $less->compileFile(Pvik\Core\Path::RealPath("~/Application/less/dashboard.less"));



$Version = Pvik\Core\Config::$Config['Version'];

file_put_contents(Pvik\Core\Path::RealPath('~/css/dashbird-' . $Version .'.css'), $compiled);