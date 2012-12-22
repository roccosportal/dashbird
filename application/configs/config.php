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
    array ('Url' => '/', 'Controller' => 'Dashboard', 'Action' => 'Index'),
    array ('Url' => '/ajax/auth/login/', 'Controller' => 'Auth', 'Action' => 'AjaxLogin'),
    array ('Url' => '/ajax/auth/logout/', 'Controller' => 'Auth', 'Action' => 'AjaxLogout'),
    array ('Url' => '/ajax/auth/is/logged/in/', 'Controller' => 'Auth', 'Action' => 'AjaxIsLoggedIn'),
    array ('Url' => '/ajax/entries/load/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGetDashboardEntries'),
    array ('Url' => '/ajax/entries/get/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGetMultiple'),
    array ('Url' => '/ajax/entries/hashes/get/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGetHashes'),
    array ('Url' => '/ajax/user/shares/get/', 'Controller' => 'User', 'Action' => 'AjaxGetUserShares'),
    array ('Url' => '/ajax/user/shares/add/', 'Controller' => 'User', 'Action' => 'AjaxAddUserShare'),
    array ('Url' => '/ajax/user/password/change/', 'Controller' => 'User', 'Action' => 'AjaxChangePassword'),
    array ('Url' => '/ajax/entry/get/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGet'),
    array ('Url' => '/ajax/entry/add/', 'Controller' => 'Dashboard', 'Action' => 'AjaxAdd'),
    array ('Url' => '/ajax/entry/delete/', 'Controller' => 'Dashboard', 'Action' => 'AjaxDelete'),
    array ('Url' => '/ajax/entry/edit/', 'Controller' => 'Dashboard', 'Action' => 'AjaxEdit'),
    array ('Url' => '/ajax/entry/hash/get/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGetHash'),
    array ('Url' => '/ajax/entry/comment/add/', 'Controller' => 'Comment', 'Action' => 'AjaxAddComment'),
    array ('Url' => '/ajax/entry/comment/delete/', 'Controller' => 'Comment', 'Action' => 'AjaxDeleteComment'),
    array ('Url' => '/ajax/entry/shares/set/', 'Controller' => 'Dashboard', 'Action' => 'AjaxSetEntryShares'),
    array ('Url' => '/ajax/plugin/data/get/', 'Controller' => 'PluginData', 'Action' => 'AjaxGet'),
    array ('Url' => '/ajax/plugin/data/save/', 'Controller' => 'PluginData', 'Action' => 'AjaxSave')
   
);

self::$Config['Version'] = '4.0.0-alpha';
?>
