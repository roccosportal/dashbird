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
    '~/Application/js/posts/latest.js',
    '~/Application/js/posts/feed.js',
    '~/Application/js/posts/notification.js',
    '~/Application/js/posts/post-html-layers-manager.js',
    '~/Application/js/posts/posts.js',
    '~/Application/js/posts/comments.js',
    '~/Application/js/posts/comment.js',
    '~/Application/js/posts/single-view.js',
    '~/Application/js/posts/post.js',
    '~/Application/js/posts/drawing-manager.js',
    '~/Application/js/posts/post-html-layer.js',
    '~/Application/js/posts/post-feed-html-layer.js',
    '~/Application/js/posts/activity-feed-layer.js',
    '~/Application/js/posts/comment-feed-layer.js',
    '~/Application/js/posts/comments-layer.js',
    '~/Application/js/posts/comment-layer.js',
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
    '~/Application/js/posts/init.js',
);

$PostsJavascript = '';
foreach($Files as $File){
    $PostsJavascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$PostsJavascript = str_replace("\n\n", "\n", $PostsJavascript);






$Files = array (
    '~/Application/js/simple-js-lib/base-object.js',
    '~/Application/js/shared/ajax.js',
    '~/Application/js/shared/ajax-response.js',
    '~/Application/js/shared/user.js',
    '~/Application/js/settings/user-settings.js',
    '~/Application/js/settings/settings.js',
    '~/Application/js/settings/init.js',
);

$SettingsJavascript = '';
foreach($Files as $File){
    $SettingsJavascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$SettingsJavascript = str_replace("\n\n", "\n", $SettingsJavascript);


// delete previous files
$regex = '/^dashbird-developer-/';
if ($fileHandle = opendir(\Pvik\Core\Path::RealPath('~/js/'))) {
    while (false !== ($file = readdir($fileHandle))) {
        if(preg_match($regex , $file)){
            unlink(\Pvik\Core\Path::RealPath('~/js/' . $file));
        }
    }
    closedir($fileHandle);
}

// write new files
file_put_contents(\Pvik\Core\Path::RealPath('~/js/dashbird-developer-' . $Version .'.js'),$PostsJavascript);
file_put_contents(\Pvik\Core\Path::RealPath('~/js/dashbird-developer-settings-' . $Version .'.js'),$SettingsJavascript);


