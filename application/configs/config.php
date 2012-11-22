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
    array ('Url' => '/ajax/login/', 'Controller' => 'Auth', 'Action' => 'AjaxLogin'),
    array ('Url' => '/ajax/logout/', 'Controller' => 'Auth', 'Action' => 'AjaxLogout'),
    array ('Url' => '/ajax/is/logged/in/', 'Controller' => 'Auth', 'Action' => 'AjaxIsLoggedIn'),
    array ('Url' => '/ajax/get/dashboard/entries/', 'Controller' => 'Dashboard', 'Action' => 'AjaxGetDashboardEntries'),
    array ('Url' => '/ajax/get/user/shares/', 'Controller' => 'User', 'Action' => 'AjaxGetUserShares'),
    array ('Url' => '/ajax/add/user/share/', 'Controller' => 'User', 'Action' => 'AjaxAddUserShare'),
    array ('Url' => '/ajax/set/entry/shares/', 'Controller' => 'Dashboard', 'Action' => 'AjaxSetEntryShares'),
    array ('Url' => '/ajax/entry/add/', 'Controller' => 'Dashboard', 'Action' => 'AjaxAdd'),
    array ('Url' => '/ajax/entry/delete/', 'Controller' => 'Dashboard', 'Action' => 'AjaxDelete'),
    array ('Url' => '/ajax/entry/edit/', 'Controller' => 'Dashboard', 'Action' => 'AjaxEdit'),
    array ('Url' => '/ajax/add/comment/', 'Controller' => 'Comment', 'Action' => 'AjaxAddComment'),
    array ('Url' => '/ajax/delete/comment/', 'Controller' => 'Comment', 'Action' => 'AjaxDeleteComment')
);

self::$Config['Version'] = '3.0.3-alpha';
?>
