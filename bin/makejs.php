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
    '~/Application/js/simple-js-lib/base-object.js',
    '~/Application/js/simple-js-lib/event-handler.js',
    '~/Application/js/simple-js-lib/single-request-queue.js',
    '~/Application/js/simple-js-lib/observable.js',
    '~/Application/js/shared/ajax.js',
    '~/Application/js/shared/ajax-response.js',
    '~/Application/js/shared/user.js',
    '~/Application/js/posts/utils.js',
    '~/Application/js/posts/posts.js',
    '~/Application/js/posts/post.js',
    '~/Application/js/posts/post-html-layer.js',
    '~/Application/js/posts/commands/base.js',
    '~/Application/js/posts/commands/comment.js',
    '~/Application/js/posts/commands/edit.js',
    '~/Application/js/posts/commands/remove.js',
    '~/Application/js/posts/commands/share.js',
    '~/Application/js/posts/bbcode/bold.js',
    '~/Application/js/posts/bbcode/image.js',
    '~/Application/js/posts/bbcode/link.js',
    '~/Application/js/posts/bbcode/video.js',
    '~/Application/js/posts/stack.js',
    '~/Application/js/posts/modal.js',
    '~/Application/js/posts/new-post.js',
    '~/Application/js/posts/search.js',
    '~/Application/js/posts/templates.js',
    '~/Application/js/posts/general.js',
    '~/Application/js/posts/init.js',
);

$Javascript = '';
foreach($Files as $File){
    $Javascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$Javascript = str_replace("\n\n", "\n", $Javascript);
file_put_contents(\Pvik\Core\Path::RealPath('~/js/dashbird-developer-' . $Version .'.js'),$Javascript);


$Files = array (
    '~/Application/js/simple-js-lib/base-object.js',
    '~/Application/js/shared/ajax.js',
    '~/Application/js/shared/ajax-response.js',
    '~/Application/js/shared/user.js',
    '~/Application/js/settings/user-settings.js',
    '~/Application/js/settings/settings.js',
    '~/Application/js/settings/init.js',
);

$Javascript = '';
foreach($Files as $File){
    $Javascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$Javascript = str_replace("\n\n", "\n", $Javascript);
file_put_contents(\Pvik\Core\Path::RealPath('~/js/dashbird-developer-settings-' . $Version .'.js'),$Javascript);

