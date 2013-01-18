<?php
self::$Config['PvikAdminTools'] = array ();



// gets and sets the base path of PvikAdminTools
self::$Config['PvikAdminTools']['BasePath'] = '~' . str_replace('load.php', '', str_replace(getcwd(), '',  realpath ( __FILE__ )));

self::$Config['NamespaceAssociations']['\\PvikAdminTools'] = self::$Config['PvikAdminTools']['BasePath'];
//include pvik-admin-tools config
require(\Pvik\Core\Path::RealPath(self::$Config['PvikAdminTools']['BasePath'] .'configs/config.php'));

// add routes
if(!isset(self::$Config['Routes'])){
    self::$Config['Routes'] = array();
}
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] , 'Controller' => '\\PvikAdminTools\\Controllers\\Tables', 'Action' => 'Index'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'tables/', 'Controller' => '\\PvikAdminTools\\Controllers\\Tables', 'Action' => 'Index'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'tables/{parameters}/', 'Controller' => '\\PvikAdminTools\\Controllers\\Tables', 'Action' => 'IndexWithParameters'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'tables/{parameters}/{preset-values}/', 'Controller' => '\\PvikAdminTools\\Controllers\\Tables', 'Action' => 'IndexWithParameters'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'files/upload/', 'Controller' => '\\PvikAdminTools\\Controllers\\Files', 'Action' => 'UploadFile'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'login/', 'Controller' => '\\PvikAdminTools\\Controllers\\Account', 'Action' => 'Login'));
array_push(self::$Config['Routes'], array ('Url' => self::$Config['PvikAdminTools']['Url'] . 'logout/', 'Controller' => '\\PvikAdminTools\\Controllers\\Account', 'Action' => 'Logout'));

