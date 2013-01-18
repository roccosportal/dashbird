<?php

namespace Pvik\Web;

use Pvik\Utils\KeyValueArray;
use Pvik\Core\Log;
use Pvik\Core\Config;

/**
 * This static class starts a controller.
 */
class ControllerManager {

    /**
     * Execute a action from a controller.
     * @param string $ControllerName
     * @param string $ActionName
     * @param KeyValueArray $Parameters 
     */
    public static function ExecuteController($ControllerName, $ActionName, Request $Request) {
        $ControllerClassName = $ControllerName;
        if ($ControllerClassName[0] !== '\\') {
            $ControllerClassName = Config::$Config['DefaultNamespace'] . Config::$Config['DefaultNamespaceControllers'] . '\\' . $ControllerClassName;
        }

        $ControllerInstance = new $ControllerClassName($Request, $ControllerName);
        /* @var $ControllerInstance \Pvik\Web\Controller */
        $ActionFunctionName = $ActionName . 'Action';
        if (method_exists($ControllerInstance, $ActionFunctionName)) {
            Log::WriteLine('Executing action: ' . $ActionFunctionName);
            $ControllerInstance->SetCurrentActionName($ActionName);
            // execute action    
            $ControllerInstance->$ActionFunctionName();
        } else {
            throw new \Exception('Action doesn\'t exists: ' . $ControllerClassName . '->' . $ActionFunctionName);
        }
    }

}