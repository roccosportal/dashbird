<?php
self::$Config['UnderConstruction']['Enabled'] = false;
self::$Config['Log']['On'] = true;
self::$Config['Log']['UseOneFile'] = true;

self::$Config['DefaultNamespace'] = '\\Dashbird';
self::$Config['NamespaceAssociations']['\\Dashbird'] = '~/application/';

self::$Config['MySQL']['Server'] = 'localhost';
self::$Config['MySQL']['Username'] = 'root';
self::$Config['MySQL']['Password'] = 'root';
self::$Config['MySQL']['Database'] = 'dashbird';


self::$Config['Routes'] = array (
    array ('Url' => '/', 'Controller' => 'Posts', 'Action' => 'Index'),
    array ('Url' => '/api/auth/login/', 'Controller' => 'Auth', 'Action' => 'ApiAuthLogin'),
    array ('Url' => '/api/auth/logout/', 'Controller' => 'Auth', 'Action' => 'ApiAuthLogout'),
    array ('Url' => '/api/auth/is/logged/in/', 'Controller' => 'Auth', 'Action' => 'ApiAuthIsLoggedIn'),
    array ('Url' => '/api/posts/load/', 'Controller' => 'Posts', 'Action' => 'ApiPostsLoad'),
    array ('Url' => '/api/posts/get/', 'Controller' => 'Posts', 'Action' => 'ApiPostsGet'),
    array ('Url' => '/api/posts/updated/get/', 'Controller' => 'Posts', 'Action' => 'ApiPostsUpdatedGet'),
    array ('Url' => '/api/user/shares/get/', 'Controller' => 'User', 'Action' => 'ApiUserSharesGet'),
    array ('Url' => '/api/user/shares/add/', 'Controller' => 'User', 'Action' => 'ApiUserSharesAdd'),
    array ('Url' => '/api/user/password/change/', 'Controller' => 'User', 'Action' => 'ApiUserChangePassword'),
    array ('Url' => '/api/post/get/', 'Controller' => 'Posts', 'Action' => 'ApiPostGet'),
    array ('Url' => '/api/post/add/', 'Controller' => 'Posts', 'Action' => 'ApiPostAdd'),
    array ('Url' => '/api/post/delete/', 'Controller' => 'Posts', 'Action' => 'ApiPostDelete'),
    array ('Url' => '/api/post/edit/', 'Controller' => 'Posts', 'Action' => 'ApiPostEdit'),
    array ('Url' => '/api/post/hash/get/', 'Controller' => 'Posts', 'Action' => 'ApiPostHashGet'),
    array ('Url' => '/api/post/comment/add/', 'Controller' => 'Comment', 'Action' => 'ApiPostCommentAdd'),
    array ('Url' => '/api/post/comment/delete/', 'Controller' => 'Comment', 'Action' => 'ApiPostCommentDelete'),
    array ('Url' => '/api/post/shares/set/', 'Controller' => 'Posts', 'Action' => 'ApiPostSharesSet'),
    array ('Url' => '/api/plugin/data/get/', 'Controller' => 'PluginData', 'Action' => 'ApiPluginDataGet'),
    array ('Url' => '/api/plugin/data/save/', 'Controller' => 'PluginData', 'Action' => 'ApiPluginDataSave'),
    array ('Url' => '/login/', 'Controller' => 'Auth', 'Action' => 'Login'),
    array ('Url' => '/logout/', 'Controller' => 'Auth', 'Action' => 'Logout'),
    array ('Url' => '/settings/', 'Controller' => 'User', 'Action' => 'Settings')
    
   
);

self::$Config['Version'] = '4.1.1-alpha';




