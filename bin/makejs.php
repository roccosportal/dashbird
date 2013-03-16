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
    '~/Application/js/simple-js-lib/mapping-array.js',
    '~/Application/js/dashbird/_.js',
    '~/Application/js/dashbird/controllers/_.js',
    '~/Application/js/dashbird/controllers/post.js',
    '~/Application/js/dashbird/controllers/posts.js',
    '~/Application/js/dashbird/controllers/user.js',
    '~/Application/js/dashbird/controllers/utils/_.js',
    '~/Application/js/dashbird/controllers/utils/ajax.js',
    '~/Application/js/dashbird/controllers/utils/ajax-response.js',
    '~/Application/js/dashbird/models/_.js',
    '~/Application/js/dashbird/models/comment.js',
    '~/Application/js/dashbird/models/comments.js',
    '~/Application/js/dashbird/models/post.js',
    '~/Application/js/dashbird/view-models/_.js',
    '~/Application/js/dashbird/view-models/activity-feed.js',
    '~/Application/js/dashbird/view-models/comment-feed.js',
    '~/Application/js/dashbird/view-models/comment.js',
    '~/Application/js/dashbird/view-models/comments.js',
    '~/Application/js/dashbird/view-models/post-feed.js',
    '~/Application/js/dashbird/view-models/post.js',
    '~/Application/js/dashbird/view-models/bbcode/_.js',
    '~/Application/js/dashbird/view-models/bbcode/bold.js',
    '~/Application/js/dashbird/view-models/bbcode/image.js',
    '~/Application/js/dashbird/view-models/bbcode/link.js',
    '~/Application/js/dashbird/view-models/bbcode/video.js',
    '~/Application/js/dashbird/view-models/commands/_.js',
    '~/Application/js/dashbird/view-models/commands/base.js',
    '~/Application/js/dashbird/view-models/commands/comment.js',
    '~/Application/js/dashbird/view-models/commands/edit.js',
    '~/Application/js/dashbird/view-models/commands/remove.js',
    '~/Application/js/dashbird/view-models/commands/share.js',
    '~/Application/js/dashbird/view-models/utils/_.js',
    '~/Application/js/dashbird/view-models/utils/drawing-manager.js',
    '~/Application/js/dashbird/views/_.js',
    '~/Application/js/dashbird/views/board/_.js',
    '~/Application/js/dashbird/views/board/feed.js',
    '~/Application/js/dashbird/views/board/latest.js',
    '~/Application/js/dashbird/views/board/new-post.js',
    '~/Application/js/dashbird/views/board/notification.js',
    '~/Application/js/dashbird/views/board/search.js',
    '~/Application/js/dashbird/views/board/single-view.js',
    '~/Application/js/dashbird/views/board/stack.js',
    '~/Application/js/dashbird/views/utils/_.js',
    '~/Application/js/dashbird/views/utils/modal.js',
    '~/Application/js/dashbird/views/utils/view-model-posts-manager.js',
    '~/Application/js/dashbird/views/utils/templates.js',
    '~/Application/js/dashbird/utils.js',
    '~/Application/js/dashbird/views/board/bootstrap.js',
);

$PostsJavascript = '';
foreach($Files as $File){
    $PostsJavascript .= file_get_contents(\Pvik\Core\Path::RealPath($File)) . "\n";
}

// delete empty lines
$PostsJavascript = str_replace("\n\n", "\n", $PostsJavascript);






$Files = array (
    '~/Application/js/simple-js-lib/base-object.js',
    '~/Application/js/dashbird/_.js',
    '~/Application/js/dashbird/controllers/_.js',
    '~/Application/js/dashbird/controllers/user.js',
    '~/Application/js/dashbird/controllers/user-settings.js',
    '~/Application/js/dashbird/controllers/utils/_.js',
    '~/Application/js/dashbird/controllers/utils/ajax.js',
    '~/Application/js/dashbird/controllers/utils/ajax-response.js',
    '~/Application/js/dashbird/views/_.js',
    '~/Application/js/dashbird/views/settings/_.js',
    '~/Application/js/dashbird/views/settings/settings.js',
    '~/Application/js/dashbird/views/settings/bootstrap.js',
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


