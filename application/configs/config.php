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
    array ('Url' => '/ajax/link/add/', 'Controller' => 'Link', 'Action' => 'AjaxAdd'),
    array ('Url' => '/ajax/link/delete/', 'Controller' => 'Link', 'Action' => 'AjaxDelete'),
    array ('Url' => '/ajax/link/edit/', 'Controller' => 'Link', 'Action' => 'AjaxEdit'),
    array ('Url' => '/ajax/note/add/', 'Controller' => 'Note', 'Action' => 'AjaxAdd'),
    array ('Url' => '/ajax/note/delete/', 'Controller' => 'Note', 'Action' => 'AjaxDelete'),
    array ('Url' => '/ajax/note/edit/', 'Controller' => 'Note', 'Action' => 'AjaxEdit'),
    array ('Url' => '/ajax/todo/add/', 'Controller' => 'Todo', 'Action' => 'AjaxAdd'),
    array ('Url' => '/ajax/todo/delete/', 'Controller' => 'Todo', 'Action' => 'AjaxDelete'),
    array ('Url' => '/ajax/todo/edit/is/done/', 'Controller' => 'Todo', 'Action' => 'AjaxEditIsDone'),
    array ('Url' => '/ajax/todo/edit/', 'Controller' => 'Todo', 'Action' => 'AjaxEdit'),
    array ('Url' => '/test/', 'Controller' => 'Test', 'Action' => 'Test')
);

self::$Config['Version'] = '1.0.1';
?>
