<?php
self::$Config['Log']['On'] = false;
self::$Config['Log']['UseOneFile'] = true;
self::$Config['UnderConstruction']['Enabled'] = false;

self::$Config['ErrorPages'] = array (
    'Default' => '~/application/views/error-pages/master.php'
);
self::$Config['NamespaceAssociations'] = array ();
self::$Config['NamespaceAssociations']['\\Pvik'] = '~/library/pvik/';

self::$Config['DefaultNamespace'] = '\\MyProject';
self::$Config['DefaultNamespaceControllers'] = '\\Controllers';
self::$Config['DefaultNamespaceEntity'] = '\\Model\\Entities'; 
self::$Config['DefaultNamespaceModelTable'] = '\\Model\\ModelTables';
self::$Config['DefaultViewsFolder'] = '~/application/views/';
self::$Config['UnderConstruction']['Path'] = self::$Config['DefaultViewsFolder'] . 'other/under-construction.php';
?>