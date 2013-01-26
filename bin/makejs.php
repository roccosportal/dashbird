<?php
chdir('../');
 require './Library/Pvik/Core/Path.php';
    \Pvik\Core\Path::Init();
    
    // set up class loader
    require \Pvik\Core\Path::RealPath('~/Library/Pvik/Core/ClassLoader.php');
    $ClassLoader = new \Pvik\Core\ClassLoader();
    $ClassLoader->SetNamespaceAssociation('\\Pvik', '~/Library/Pvik/');
    $ClassLoader->Init();
    
    $Core = new \Pvik\Core\Core();
    $Core->LoadConfig(array(
			'~/Application/Configs/DefaultConfig.php',
			'~/Application/Configs/Config.php')
			);

    
$Version = \Pvik\Core\Config::$Config['Version'];

$Files = array (
    '~/Application/js/simple-js-lib/event-handler.js',
    '~/Application/js/simple-js-lib/single-request-queue.js',
    '~/Application/js/constants.js',
    '~/Application/js/dashboard-entry.js',
    '~/Application/js/commands/base.js',
    '~/Application/js/commands/comment.js',
    '~/Application/js/commands/edit.js',
    '~/Application/js/commands/remove.js',
    '~/Application/js/commands/share.js',
    '~/Application/js/bbcode/bold.js',
    '~/Application/js/bbcode/image.js',
    '~/Application/js/bbcode/link.js',
    '~/Application/js/bbcode/video.js',
    '~/Application/js/dashboard.js',
    '~/Application/js/login-box.js',
    '~/Application/js/modal.js',
    '~/Application/js/new-entry.js',
    '~/Application/js/plugin-manager.js',
    '~/Application/js/plugins/notifications.js',
    '~/Application/js/search.js',
    '~/Application/js/settings.js',
    '~/Application/js/user.js',
    '~/Application/js/templates.js',
    '~/Application/js/general.js',
    '~/Application/js/init.js',
);

$Javascript = '';
foreach($Files as $File){
    $Javascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$Javascript = str_replace("\n\n", "\n", $Javascript);
file_put_contents(\Pvik\Core\Path::RealPath('~/js/dashbird-developer-' . $Version .'.js'),$Javascript);


