<?php

class Config {
    public static $Config;
    
    public static function Load(){
        require '../application/configs/config.php';
    }
}
Config::Load();
$Version = Config::$Config['Version'];

$Files = array (
    '../application/js/simple-js-lib/event-handler.js',
    '../application/js/simple-js-lib/single-request-queue.js',
    '../application/js/constants.js',
    '../application/js/dashboard-entry.js',
    '../application/js/commands/base.js',
    '../application/js/commands/comment.js',
    '../application/js/commands/edit.js',
    '../application/js/commands/remove.js',
    '../application/js/commands/share.js',
    '../application/js/bbcode/bold.js',
    '../application/js/bbcode/image.js',
    '../application/js/bbcode/link.js',
    '../application/js/bbcode/video.js',
    '../application/js/dashboard.js',
    '../application/js/login-box.js',
    '../application/js/modal.js',
    '../application/js/new-entry.js',
    '../application/js/plugin-manager.js',
    '../application/js/plugins/notifications.js',
    '../application/js/search.js',
    '../application/js/settings.js',
    '../application/js/user.js',
    '../application/js/templates.js',
    '../application/js/general.js',
    '../application/js/init.js',
);

$Javascript = '';
foreach($Files as $File){
    $Javascript .= file_get_contents($File) . "\n";
}

// delete empty lines
$Javascript = str_replace("\n\n", "\n", $Javascript);

file_put_contents('../js/dashbird-developer-' . $Version .'.js',$Javascript);


