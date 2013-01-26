<?php
self::$Config['Log']['On'] = false;
self::$Config['Log']['UseOneFile'] = true;
self::$Config['UnderConstruction']['Enabled'] = false;

self::$Config['ErrorPages'] = array (
    'Default' => '~/Application/Views/ErrorPages/Master.php'
);

self::$Config['DefaultNamespace'] = '\\MyProject';
self::$Config['DefaultNamespaceControllers'] = '\\Controllers';
self::$Config['DefaultNamespaceEntity'] = '\\Model\\Entities'; 
self::$Config['DefaultNamespaceModelTable'] = '\\Model\\ModelTables';
self::$Config['DefaultViewsFolder'] = '~/Application/Views/';
self::$Config['UnderConstruction']['Path'] = self::$Config['DefaultViewsFolder'] . 'Other/UnderConstruction.php';
?>