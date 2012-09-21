<?php

namespace Pvik\Web;

use Pvik\Core\Config;
use Pvik\Core\Log;
use Pvik\Web\ControllerManager;
use Pvik\Core\Path;

/**
 * Trys to find a controller via the current url
 */
class RouteManager {

    /**
     * Starts the route manager
     * @throws \Pvik\Web\NoRouteFoundException
     */
    public function Start() {
        if (Config::$Config['UnderConstruction']['Enabled'] == true) {
            $this->ExecuteUnderConstruction(Path::RealPath(Config::$Config['UnderConstruction']['Path']));
        } else {
            //Request::GetInstance()->FetchUrl();
            $Request = $this->FindRoute();
            if ($Request != null) {
                // start output buffering
                ob_start();
                $Route = $Request->GetRoute();
                // execute controller
                ControllerManager::ExecuteController($Route['Controller'], $Route['Action'], $Request);
                // end output buffering and output the buffer
                echo ob_get_clean();
            } else {
                throw new \Pvik\Web\NoRouteFoundException('No route found for ' . $this->Url);
            }
        }
    }

    /**
     * Returns the route for the current url.
     * @return Request. 
     */
    protected function FindRoute() {
        if (!isset(Config::$Config['Routes'])) {
            throw new \Exception('No Routes found in config. Probably misconfigured config.');
        }

        $Routes = Config::$Config['Routes'];
        $Url = $this->FetchUrl();
        foreach ($Routes as $Route) {
            $Request = $this->UrlIsMatching($Url, $Route);
            if ($Request != null) {
                return $Request;
            }
        }

        return null;
    }

    /**
     * Checks if a url matches with an route. 
     * @param string $OrignalUrl
     * @param array $Route
     * @return bool
     */
    protected function UrlIsMatching($OrignalUrl, $Route) {
        $RouteUrl = $Route['Url'];
        if ($RouteUrl == '*' || strtolower($OrignalUrl) == $RouteUrl) {
            $Request = new Request();
            $Request->SetRoute($Route);
            $Request->SetUrl($OrignalUrl);

            return $Request;
        } elseif (strpos($RouteUrl, '{') !== false && strpos($RouteUrl, '}') !== false) { // contains a variable
            $RouteUrlParts = explode('/', $RouteUrl);
            $OrignalUrlParts = explode('/', $OrignalUrl);
            // have the same part length
            if (count($RouteUrlParts) == count($OrignalUrlParts)) {
                for ($Index = 0; $Index < count($RouteUrlParts); $Index++) {
                    if (strlen($RouteUrlParts[$Index]) >= 3 && $RouteUrlParts[$Index][0] == '{') { // it's a variable 
                        $Key = substr($RouteUrlParts[$Index], 1, -1);
                        if (isset($Route['Parameters'][$Key]) && !preg_match($Route['Parameters'][$Key], $OrignalUrlParts[$Index])) {
                            return null;
                        }
                    } else if (strtolower($RouteUrlParts[$Index]) != $OrignalUrlParts[$Index]) {
                        // not matching
                        return null;
                    }
                }
                Log::WriteLine('Route matching: ' . $Route['Url']);
                $Request = new Request();
                $Request->SetRoute($Route);
                $Request->SetUrl($OrignalUrl);
                // matching successfull
                // save url parameter
                for ($Index = 0; $Index < count($RouteUrlParts); $Index++) {
                    if (strlen($RouteUrlParts[$Index]) >= 3 && $RouteUrlParts[$Index][0] == '{') { // it's a variable
                        // the key is the name between the brakets
                        $Key = substr($RouteUrlParts[$Index], 1, -1);
                        // add to url parameters
                        $Request->GetParameters()->Add($Key, $OrignalUrlParts[$Index]);
                        if (isset($Route['Parameters'][$Key])) {
                            Log::WriteLine('Url parameter: ' . $Key . ' -> ' . $Route['Parameters'][$Key] . ' -> ' . $OrignalUrlParts[$Index]);
                        } else {
                            Log::WriteLine('Url parameter: ' . $Key . ' -> ' . $OrignalUrlParts[$Index]);
                        }
                    }
                }
                return $Request;
            }
        }
        return null;
    }

    /**
     * Fetches the current url and converts it to a pretty url
     * @return string
     */
    protected function FetchUrl() {
        // get the file base
        $RequestUri = $_SERVER['REQUEST_URI'];
        // Delete Parameters
        $QueryStringPos = strpos($RequestUri, '?');
        If ($QueryStringPos !== false) {
            $RequestUri = substr($RequestUri, 0, $QueryStringPos);
        }
        // urldecode for example cyrillic charset
        $RequestUri = urldecode($RequestUri);
        $Url = substr($RequestUri, strlen(Path::GetRelativeFileBase()));
        if (strlen($Url) != 0) {
            // add a / at the start if not already has
            if ($Url[0] != '/')
                $Url = '/' . $Url;

            // add a / at the end if not already has
            if ($Url[strlen($Url) - 1] != '/')
                $Url = $Url . '/';
        }
        else {
            $Url = '/';
        }

        $this->Url = $Url;
        return $this->Url;
    }

    /**
     * Executes the under construction file.
     * @param type $File 
     */
    protected function ExecuteUnderConstruction($File) {
        require($File);
    }

}