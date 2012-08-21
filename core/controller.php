<?php

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
     * Contains the parameters passed by the url
     * @var KeyValueArray 
     */
    protected $Parameters = null;

    /**
     *
     * @param KeyValueArray $UrlParameters 
     */
    public function __construct(KeyValueArray $UrlParameters = null) {
        $this->Parameters = $UrlParameters;
        if ($this->Parameters == null) {
            $this->Parameters = new KeyValueArray();
        }
        $this->ViewData = new KeyValueArray();
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
    protected function ExecuteViewByAction($ActionName, $Folder = '~/views/') {
        Log::WriteLine('Redirecting to action view: ' . $ActionName);
        $ViewPath = ControllerManager::GetViewPathByAction($ActionName, $Folder);
        if ($ViewPath == "")
            throw new Exception('No view found in ' . $Folder . '*/' . ControllerManager::GetControllerPathName() . '/' . Core::ConvertNameToPath($ActionName) . '.php');

        ViewManager::ExecuteView($ViewPath, $this);
    }

    /**
     * Execute a view.
     * @param string $Folder
     */
    protected function ExecuteView($Folder = '~/views/') {
        $ViewPath = ControllerManager::GetViewPath($Folder);
        if ($ViewPath == "")
            throw new Exception('No view found in ' . $Folder . '*/' . ControllerManager::GetControllerPathName() . '/' . ControllerManager::GetActionPathName() . '.php');

        ViewManager::ExecuteView($ViewPath, $this);
    }

    /**
     * Redirects to another controllers action with passing the original parameters.
     * @param string $ControllerName
     * @param string $ActionName 
     */
    protected function RedirectToController($ControllerName, $ActionName) {
        Log::WriteLine('Redirecting to controller: ' . $ControllerName);
        Log::WriteLine('Redirecting to action: ' . $ActionName);
        $this->RedirectToControllerWithParameters($ControllerName, $ActionName, $this->Parameters);
    }

    /**
     * Redirects to another controllers action.
     * @param string $ControllerName
     * @param string $ActionName
     * @param KeyValueArray $Parameters 
     */
    protected function RedirectToControllerWithParameters($ControllerName, $ActionName, KeyValueArray $Parameters = null) {
        ControllerManager::ExecuteController($ControllerName, $ActionName, $Parameters);
    }
    
    /**
     * Redirect to a url via setting the location in the header.
     * @param string $Path 
     */
    protected function RedirectToPath($Path) {
        $RelativePath = Core::RelativePath($Path);
        header("Location: " . $RelativePath);
    }

}