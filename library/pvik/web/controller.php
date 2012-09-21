<?php

namespace Pvik\Web;

use Pvik\Utils\KeyValueArray;
use Pvik\Web\ControllerManager;
use Pvik\Core\Config;
use Pvik\Core\Path;

/**
 * This class contains the logic for a web site.
 */
class Controller {

    /**
     * Contains the data for the view.
     * @var KeyValueArray 
     */
    protected $ViewData = null;

    /**
     * 
     * @var Request 
     */
    protected $Request = null;

    /**
     * Name of the controller.
     * @var string 
     */
    protected $ControllerName = null;

    /**
     * Name of the current executed action.
     * @var type 
     */
    protected $CurrentActionName = null;

    /**
     * 
     * @param \Pvik\Web\Request $Request
     * @param string $ControllerName
     */
    public function __construct(Request $Request, $ControllerName) {
        $this->Request = $Request;
        $this->ControllerName = $ControllerName;
        $this->ViewData = new KeyValueArray();
    }

    /**
     * Sets the name of the current executed action
     * @param string $Name
     */
    public function SetCurrentActionName($Name) {
        $this->CurrentActionName = $Name;
    }

    /**
     * Returns the data for the view
     * @return KeyValueArray;
     */
    public function GetViewData() {
        return $this->ViewData;
    }

    /**
     * Execute a view.
     * @param string $ActionName
     * @param string $Folder
     */
    protected function ExecuteViewByAction($ActionName, $Folder = null) {
        if ($Folder == null) {
            $Folder = Config::$Config['DefaultViewsFolder'];
        }
        //$ViewPath = ControllerManager::GetViewPathByAction($ActionName, $Folder);
        $ViewPath = Path::RealPath($Folder . Path::ConvertNameToPath($this->ControllerName) . '/' . Path::ConvertNameToPath($ActionName) . '.php');
        \Pvik\Core\Log::WriteLine('Executing view: ' . $ViewPath);
        $View = new View($ViewPath, $this);
    }

    /**
     * Execute a view.
     * @param string $Folder
     */
    protected function ExecuteView($Folder = null) {
        $this->ExecuteViewByAction($this->CurrentActionName, $Folder);
    }

    /**
     * Redirects to another controllers action with passing the original parameters.
     * @param string $ControllerName
     * @param string $ActionName 
     */
    protected function RedirectToController($ControllerName, $ActionName, Request $Request = null) {
        if ($Request == null) {
            $Request = $this->Request;
        }
        Log::WriteLine('Redirecting to controller: ' . $ControllerName);
        Log::WriteLine('Redirecting to action: ' . $ActionName);

        ControllerManager::ExecuteController($ControllerName, $ActionName, $Request);
    }

    /**
     * Redirect to a url via setting the location in the header.
     * @param string $Path 
     */
    protected function RedirectToPath($Path) {
        $RelativePath = Path::RelativePath($Path);
        header("Location: " . $RelativePath);
    }

}