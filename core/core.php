<?php
error_reporting(E_ALL ^ E_NOTICE);
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");
Class Core {
    /**
     * Contains the current url.
     * @var type 
     */
    public static $Url = null;
    /**
     * Contains the parameters that are passed via the url.
     * @var KeyValueArray 
     */
    public static $UrlParameters = null;
    /**
     * Contains the config in an associative array loaded from default-config.php and config.php.
     * @var array 
     */
    public static $Config = null;
    /**
     * Contains the relative file base.
     * Example: /sub-folder/
     * @var type 
     */
    protected static $RelativeFileBase;
    /**
     * Contains the absoulte file base.
     * Example: /var/www/sub-folder/
     * @var type
     */
    protected static $AbsoluteFileBase;
    /**
     * Contains the names of already included files.
     * @var ArrayObject 
     */
    protected static $IncludedFiles;
    public function __construct() {
        try {
            self::$IncludedFiles = new ArrayObject();
            date_default_timezone_set('UTC');
            $RelativeFileBase = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ;

            self::$RelativeFileBase = $RelativeFileBase;
            self::$AbsoluteFileBase =  $_SERVER['DOCUMENT_ROOT'] . $RelativeFileBase;

            // load the config
            $this->LoadConfig();
          
            Log::WriteLine('Relative file base: ' . self::$RelativeFileBase);
            Log::WriteLine('Absolute file base: ' . self::$AbsoluteFileBase);
            if(self::$Config['UnderConstruction']['Enabled']==true){
                $this->ExecuteUnderConstruction(Core::RealPath(self::$Config['UnderConstruction']['Path']));
            }
            else {

                // include files
                $this->IncludeFolders();

                $Route = $this->GetRoute();
                if ($Route != null) {
                    // start output buffering
                    ob_start();
                    // execute controller
                    ControllerManager::ExecuteController($Route['Controller'], $Route['Action'], self::$UrlParameters);
                    // end output buffering and output the buffer
                    echo ob_get_clean();

                }
                else {
                    throw new NoRouteFoundException('No route found for '. self::$Url);
                }
            }



        }
        catch (Exception $Exception) { // unhandled exception
            // delete output buffer and ignore it
            ob_get_clean();
            $this->ErrorPage($Exception);
        }
    }
    /**
     * Tries to show an error page for an exception.
     * @param Exception $Exception 
     */
    protected function ErrorPage(Exception $Exception){
        try {
            $ExceptionClass = get_class($Exception);
            $ErrorPages = self::$Config['ErrorPages'];
            if(isset($ErrorPages[$ExceptionClass])){
                $File = Core::RealPath($ErrorPages[$ExceptionClass]);
                if(file_exists($File)){
                    $this->ExecuteErrorPage($Exception, $File);
                }
                else {
                    throw new Exception('Erropage '. $File. ' not found');
                }
            }
            else {
                $File = Core::RealPath($ErrorPages['Default']);
                if(file_exists($File)){
                    $this->ExecuteErrorPage($Exception, $File);
                }
                else {
                    throw new Exception('Erropage '. $File. ' not found');
                }
            }
            
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
    
    /**
     * Executes the error page file.
     * @param Exception $Exception
     * @param type $File 
     */
    protected function ExecuteErrorPage(Exception $Exception, $File){
        require($File);
    }

    /**
     * Executes the under construction file.
     * @param Exception $Exception
     * @param type $File 
     */
    protected function ExecuteUnderConstruction($File){
        require($File);
    }

    /**
     * Returns the route for the current url.
     * @return array. 
     */
    protected function GetRoute() {
        $RouteMatch = null;
         // get the file base
        $RequestUri = $_SERVER['REQUEST_URI'];
        // Delete Parameters
        $QueryStringPos = strpos($RequestUri, '?');
        If($QueryStringPos!== false){
            $RequestUri = substr($RequestUri,0,  $QueryStringPos);
        }
        // urldecode for example cyrillic charset
        $RequestUri = urldecode($RequestUri);

        $Url = substr($RequestUri, strlen(self::$RelativeFileBase));

        if(strlen($Url)!=0){
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
       
        self::$Url = $Url;
        Log::WriteLine('Url: ' . $Url);

        // set to array because if a route matches the variable automatically get filled
        self::$UrlParameters = new KeyValueArray();
        if(!isset(self::$Config['Routes'])){
            throw new Exception('No Routes found in config. Probably misconfigured config.');
        }
        else {
        $Routes = self::$Config['Routes'];
        foreach ($Routes as $Route) {
            if ($this->UrlIsMatching($Url,$Route) ) {
                Log::WriteLine('Route matching: ' . $Route['Url']);
                $RouteMatch = $Route;
                break;
            }
        }
        }
        return $RouteMatch;
    }
    /**
     * Checks if a url matches with an route. 
     * @param string $OrignalUrl
     * @param array $Route
     * @return bool
     */
    protected function UrlIsMatching($OrignalUrl, $Route){
        $RouteUrl = $Route['Url'];
        $IsMatching = false;
        if($RouteUrl=='*'){
            return true;
        }
        elseif(strtolower($OrignalUrl) == $RouteUrl){
            return true;
        }
        elseif (strpos($RouteUrl, '{') !== false && strpos($RouteUrl, '}') !== false) // contains a variable
        {
            $RouteUrlParts = explode('/', $RouteUrl);
            $OrignalUrlParts = explode('/', $OrignalUrl);
            // have the same part length
            if(count($RouteUrlParts) == count($OrignalUrlParts)){
               for ($Index = 0; $Index < count($RouteUrlParts); $Index++) {
                   if(strlen($RouteUrlParts[$Index]) >= 3 && $RouteUrlParts[$Index][0]=='{'){ // it's a variable 
                        $Key = substr($RouteUrlParts[$Index], 1, -1);
                        if(isset($Route['Parameters'][$Key]) &&  !preg_match($Route['Parameters'][$Key], $OrignalUrlParts[$Index])){
                            return false;
                        }
             
                   }
                   else if(strtolower($RouteUrlParts[$Index]) != $OrignalUrlParts[$Index]) {
                       // not matching
                       return false;
                   }
               }
               // matching successfull
               // save url parameter
               for ($Index = 0; $Index < count($RouteUrlParts); $Index++) {
                   if(strlen($RouteUrlParts[$Index]) >= 3 && $RouteUrlParts[$Index][0]=='{'){ // it's a variable
                        // the key is the name between the brakets
                        $Key = substr($RouteUrlParts[$Index], 1, -1);
                        // add to url parameters
                        self::$UrlParameters->add($Key ,$OrignalUrlParts[$Index]);
                        if(isset($Route['Parameters'][$Key])){
                            Log::WriteLine('Url parameter: '. $Key. ' -> ' . $Route['Parameters'][$Key] . ' -> ' .  $OrignalUrlParts[$Index]);
                        }
                        else {
                            Log::WriteLine('Url parameter: '. $Key. ' -> ' . $OrignalUrlParts[$Index]);
                        }
                   }
               }
               return true;
            }
        }
        return false;
    }
    
    /**
     * Loads first first the default config and then the config.
     */
    protected function LoadConfig() {
        self::$Config = array();

        // load at first default values
        require(Core::RealPath('~/configs/default-config.php'));

        // overwrite them
        require(Core::RealPath('~/configs/config.php'));
        Log::WriteLine('Loaded: config.php');
    }


    /**
     * Includes the folders set up in the config.
     */
    protected function IncludeFolders() {
        foreach (self::$Config['IncludeFolders'] as $FolderPath) {
            $RealFolderPath = Core::RealPath($FolderPath);
            $this->IncludeFolder($RealFolderPath, 0);
        }
    }
    /**
     * Includes a folder.
     * @param string $RealFolderPath 
     */
    public function IncludeFolder($RealFolderPath) {
        
        if ($Handle = opendir($RealFolderPath)) {
            while (false !== ($File = readdir($Handle))) {
                $Extension = '';
                $Path = $RealFolderPath . $File;
                If (strlen($File) > 4) {
                    // get extension from file
                    $Extension = substr($File, -4);
                }
                if ($Extension == '.php') {
                    if(!in_array($Path,(array)self::$IncludedFiles)){
                        include_once($Path);
                        Log::WriteLine('Included: ' . $Path );
                        self::$IncludedFiles->append($Path);
                    }
                   
                } elseif ($File != '.' && $File != '..' && is_dir($Path)) {
                    // it's a sub folder
                    $this->IncludeFolder($Path . '/');
                }
            }
            closedir($Handle);
        }
    }
    
    /**
     * Includes a file on that the the caller file depends if not already included.
     * If ClassB extends ClassA it depends on ClassA and so the file of ClassA must be included first.
     * @param type $FilePath 
     */
    public static function Depends($FilePath){
        $FilePath = self::RealPath($FilePath);
        if(!in_array($FilePath,(array)self::$IncludedFiles)){
           
            include_once($FilePath);
            Log::WriteLine('Included: ' . $FilePath);
            self::$IncludedFiles->append($FilePath);            
        }
    }
    
    /**
     * Returns an absolute path.
     * Resolves the ~/ symbol.
     * Example /var/www/sub-folder/something.php
     * @param string $Path
     * @return string 
     */
    public static function RealPath($Path) {
        $NewFilePath = str_replace('~/', self::$AbsoluteFileBase, $Path);
        return $NewFilePath;
    }
    
    /**
     * Returns a relative path.
     * Resolves the ~/ symbol.
     * Example /sub-folder/something.js
     * @param strubg $Path
     * @return type
     */
    public static function RelativePath($Path) {
        $NewPath = str_replace('~/', self::$RelativeFileBase, $Path);
        return $NewPath;
    }
    
    /**
     * Returns a $_POST value or null.
     * @param string $Key
     * @return string 
     */
    public static function GetPOST($Key){
        $Data = null;
        if(self::IsPOST($Key)){
            $Data = $_POST[$Key];
        }
        return $Data;
    }
    
    /**
     * Checks if a $_POST value is set.
     * @param string $Key
     * @return string 
     */
    public static function IsPOST($Key){
        if(isset($_POST[$Key])){
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Returns a $_GET value or null.
     * @param string $Key
     * @return string 
     */
    public static function GetGET($Key){
        $Data = null;
        if(isset($_GET[$Key])){
            $Data = $_GET[$Key];
        }
        return $Data;
    }
    
    /**
     * Creates an guid.
     * @return string 
     */
    public static function CreateGuid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $CharId = strtoupper(md5(uniqid(rand(), true)));
            $Hyphen = chr(45);// "-"
            $Uuid = chr(123)// "{"
                    .substr($CharId, 0, 8).$Hyphen
                    .substr($CharId, 8, 4).$Hyphen
                    .substr($CharId,12, 4).$Hyphen
                    .substr($CharId,16, 4).$Hyphen
                    .substr($CharId,20,12)
                    .chr(125);// "}"
            return $Uuid;
        }
    }
    
    /**
     * Converts a name to a safe path name. Converts ThisIsAnExample to this-is-an-example.
     * @param string $Name
     * @return string 
     */
    public static function ConvertNameToPath($Name){
        $ProcessingName = preg_replace("/([a-z])([A-Z][A-Za-z0-9])/", '${1}-${2}', $Name);
        return strtolower($ProcessingName);
    }
    /**
     * Is set to true if a sessions was started.
     * @var type 
     */
    protected static $SessionStarted = false;
    /**
     * Starts a session if not already started.
     * Use this function to prevent multiple session starts.
     */
    public static function SessionStart(){
        if(!self::$SessionStarted){
            session_start();
            self::$SessionStarted = true;
        }
    }


}
/**
 * A class to handle the log.
 * Source code must be in this file because very basic core functions insists on that.
 */
class Log {
    /**
     * Contains the instance of an Log obejcet.
     * @var Log 
     */
    protected static $Log = null;
    /**
     * Contains an array of the log lines.
     * @var array
     */
    protected $LogTrace;
    
    /**
     * Contains the handle of the current log file.
     * @var type 
     */
    protected $LogFileHandle = null;

    
    public function __construct() {
        $this->LogTrace = array();
        $date = new DateTime();
        if (Core::$Config['Log']['UseOneFile'] == false) {
            $GeneratedFilePath = Core::RealPath('~/logs') . '/log-' . $date->getTimestamp() . '.txt';
        } else {
            $GeneratedFilePath = Core::RealPath('~/logs/log-file.txt');
        }

        $this->LogFileHandle = fopen($GeneratedFilePath, 'w');
    }
    
    /**
     * Writes a line in the log file and saves it into the log trace.
     * @param string $Message 
     */
    public function Write($Message) {
        If ($this->LogFileHandle != null){
            fwrite($this->LogFileHandle, $Message);
        }
        array_push($this->LogTrace, $Message);
    }

    /**
     * Writes a line in the log file and saves it into the log trace if logging is turned on.
     * @param string $Message 
     */
    public static function WriteLine($Message) {
        if (Core::$Config['Log']['On'] == true) {
            If (self::$Log == null) {
                self::$Log = new Log();
            }
            self::$Log->Write($Message . "\n");
        }
    }
    
    /**
     * Returns a log trace.
     * Can be used for a exception page.
     * @return string 
     */
    public static function GetTrace(){
        $TraceString = "";
        If (self::$Log != null) {
             $TraceString = self::$Log->GetTraceString();
        }
        return $TraceString;
    }

    /**
     * Returns a log trace.
     * Can be used for a exception page.
     * @return string 
     */
    public function GetTraceString(){
        $TraceString = "";
        $Max = count($this->LogTrace);
        if ($Max > 0){
            for ($i = 0; $i < $Max; $i++) {
                if(($i + 1)< 10){
                    $TraceString .= '#0' . ($i+ 1) . ' ' . $this->LogTrace[$i];
                }
                else {
                    $TraceString .= '#' . ($i + 1). ' ' . $this->LogTrace[$i];
                }
            }
        }
        return $TraceString;
    }
    /**
     * Destroys the log file handle when finished.
     */
    public function __destruct() {
        if ($this->LogFileHandle != null) {
            fclose($this->LogFileHandle);
        }
    }

}
/**
 * A simple associative array
 */
Class KeyValueArray {
    /**
     * Array of KeyValuePairs.
     * @var array 
     */
    protected $KeyValuePairs = null;


    public function  __construct() {
        $this->KeyValuePairs = array();
    }

    /**
     * Add a value to a key if the key doesn't exists.
     * @param string $Key
     * @param mixed $Value 
     */
    public function Add($Key, $Value){
        if(!$this->ContainsKey($Key)){
            $KeyValuePair = new KeyValuePair($Key, $Value);
            array_push($this->KeyValuePairs, $KeyValuePair);
        }
        else {
            throw new Exception('The key already exists: '. $Key);
        }
    }
    
    /**
     * Set a value to a key even when the key already exists.
     * @param string $Key
     * @param mixed $Value 
     */
    public function Set($Key, $Value){
        if($this->ContainsKey($Key)){
            $Pair = $this->GetPair($Key);
            if($Pair!=null){
                $Pair->SetValue($Value);
            }
            else {
                 throw new Exception('Unexpected error caused.');
            }
        }
        else {
            $this->Add($Key, $Value);
        }

    }

    /**
     * Removes a key from the array.
     * @param type $Key 
     */
    public function Remove($Key){
        if($this->ContainsKey($Key)){
            unset($this->KeyValuePairs[$Key]);
        }
        else {
            throw new Exception('The key doesn\'t exists: '. $Key);
        }
    }

    /**
     * Returns the KeyValuePair.
     * @param string $Key
     * @return KeyValuePair 
     */
    public function GetPair($Key){
        foreach( $this->KeyValuePairs as $KeyValuePair){
            if($KeyValuePair->GetKey()==$Key){
                return $KeyValuePair;
            }
        }
        return null;
    }

    /**
     * Returns the value.
     * @param string $Key
     * @return mixed 
     */
    public function Get($Key){
        foreach( $this->KeyValuePairs as $KeyValuePair){
            if($KeyValuePair->GetKey()==$Key){
                return $KeyValuePair->GetValue();
            }
        }
        return null;
    }

    /**
     * Checks if a key exists,
     * @param string $Key
     * @return bool 
     */
    public function ContainsKey($Key){
        foreach($this->KeyValuePairs as $KeyValuePair ){
            if($KeyValuePair->GetKey()==$Key){
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the value to a key is not empty.
     * @param string $Key
     * @return bool 
     */
    public function IsNotEmpty($Key){
        foreach($this->KeyValuePairs as $KeyValuePair ){
            if($KeyValuePair->GetKey()==$Key){
                $Value = $KeyValuePair->GetValue();
                if(!empty($Value)){
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        return false;
    }
    

}

/**
 * A class that assigns a key to a value.
 */
Class KeyValuePair{
    /**
     *
     * @var string 
     */
    protected $Key = null;
    /**
     *
     * @var mixed 
     */
    protected $Value = null;

    public function __construct($Key, $Value){
        $this->Key = $Key;
        $this->Value = $Value;
    }
    
    /**
     * Returns the key.
     * @return string 
     */
    public function GetKey(){
        return $this->Key;
    }

    /**
     * Sets the value.
     * @param mixed $Value 
     */
    public function SetValue($Value){
        $this->Value = $Value;
    }

    /**
     * Returns the value.
     * @return mixed 
     */
    public function GetValue(){
        return $this->Value;
    }

    /**
     * Returns the value.
     * @return mixed. 
     */
    public function  __toString() {
        return $this->Value;
    }
}
/**
 * Exception when no route was found.
 */
class NoRouteFoundException extends Exception { }
