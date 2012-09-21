<?php

namespace Pvik\Web;

/**
 * Contains data of the current web request.
 */
class Request {

    /**
     * Contains the current url.
     * @var string 
     */
    protected $Url;

    /**
     * Contains the parameters from the current url
     * @var \Pvik\Utils\KeyValueArray 
     */
    protected $Parameters;

    /**
     * Contains the current route.
     * @var array 
     */
    protected $Route;

    /**
     * 
     */
    public function __construct() {
        $this->Parameters = new \Pvik\Utils\KeyValueArray();
    }

    /**
     * Returns the current url
     * @return string
     */
    public function GetUrl() {
        return $this->Url;
    }

    /**
     * Sets the current url
     * @param string $Url
     */
    public function SetUrl($Url) {
        $this->Url = $Url;
    }

    /**
     * Sets the current route.
     * @param type $Route
     */
    public function SetRoute(array $Route) {
        $this->Route = $Route;
    }

    /**
     * Returns the current route
     * @return array
     */
    public function GetRoute() {
        return $this->Route;
    }

    /**
     * Returns the current parameters from the url
     * @return \Pvik\Utils\KeyValueArray
     */
    public function GetParameters() {
        return $this->Parameters;
    }

    /**
     * Returns a $_POST value or null.
     * @param string $Key
     * @return string 
     */
    public function GetPOST($Key) {
        if ($this->IsPOST($Key)) {
            return $_POST[$Key];
        }
        return null;
    }

    /**
     * Checks if a $_POST value is set.
     * @param string $Key
     * @return bool 
     */
    public function IsPOST($Key) {
        return isset($_POST[$Key]);
    }

    /**
     * Checks if a $_GET value is set.
     * @param string $Key
     * @return bool 
     */
    public function IsGET($Key) {
        return isset($_GET[$Key]);
    }

    /**
     * Returns a $_GET value or null.
     * @param string $Key
     * @return string 
     */
    public function GetGET($Key) {
        if ($this->IsGET($Key)) {
            return $_GET[$Key];
        }
        return null;
    }

    /**
     * Is set to true if a sessions was started.
     * @var bool 
     */
    protected static $SessionStarted = false;

    /**
     * Starts a session if not already started.
     * Use this function to prevent multiple session starts.
     */
    public function SessionStart() {
        if (!self::$SessionStarted) {
            session_start();
            self::$SessionStarted = true;
        }
    }

}
