<?php
/**
 * This static class can run a controller and contains useful funtions for a controller.
 */
class ControllerManager {
    /**
     * Contains the name of the current executed action.
     * @var string 
     */
    protected static $ActionName = null;
    /**
     * Contains the name of the current executed controller.
     * @var string 
     */
    protected static $ControllerName = null;

    /**
     * Returns the name of the current executed action.
     * @return string 
     */
    public static function GetActionName(){
        return self::$ActionName;
    }
    
    /**
     * Converts the name of the current executed action to a safe path name.
     * Converts ThisIsAnExample to this-is-an-example.
     * @return string 
     */
    public static function GetActionPathName(){
        return Core::ConvertNameToPath(self::GetActionName());
    }
    
    /**
     * Returns the name of the current executed controller.
     * @return string 
     */
    public static function GetControllerName(){
        return self::$ControllerName;
    }
   
     /**
     * Converts the name of the current executed controller to a safe path name.
     * Converts ThisIsAnExample to this-is-an-example.
     * @return string 
     */
    public static function GetControllerPathName(){
        return Core::ConvertNameToPath(self::GetControllerName());
    }

    /**
     * Returns the complete path for a view.
     * Example: /var/www/pvik/views/blog/overview.php.
     * @param string $Folder
     * @return string 
     */
    public static function GetViewPath($Folder = '~/views/'){
        return self::GetViewPathByAction(self::$ActionName, $Folder);
    }
    
    /**
     * Returns the complete path for a view.
     * Example: /var/www/pvik/views/blog/overview.php.
     * @param string $ActionName
     * @param string $Folder
     * @return string 
     */
    public static function GetViewPathByAction($ActionName, $Folder = '~/views/'){
        $FolderPath = Core::RealPath($Folder);
        $Path = self::SearchForView($FolderPath, Core::ConvertNameToPath(self::$ControllerName), Core::ConvertNameToPath($ActionName) .'.php');
        return $Path;
    }
    
    /**
     * Returns the complete path for a view.
     * Example: /var/www/pvik/views/blog/overview.php.
     * @param string $FolderPath
     * @param string $ControllerFolderName
     * @param string $ActionFileName
     * @return string 
     */
    public static function SearchForView($FolderPath, $ControllerFolderName, $ActionFileName){
         $Path = '';
         if ($Handle = opendir($FolderPath)) { // e.g. /views/
            while (false !== ($SubFolder = readdir($Handle))) {	
                 // it's a sub folder
                $SubFolderPath = $FolderPath  . $SubFolder;
                Log::WriteLine($SubFolderPath);
                if($SubFolder != '.' && $SubFolder != '..' && is_dir($SubFolderPath)){
                    // it is the controller folder
                    if($SubFolder==$ControllerFolderName){
                        $Path = $FolderPath  .$ControllerFolderName . '/' . $ActionFileName;
                        // break search
                        Break;
                    }
                    else {
                        
                        $Search = self::SearchForView($SubFolderPath .'/', $ControllerFolderName, $ActionFileName);
                        if($Search!=""){
                            // search found something
                            $Path = $Search;
                            Break;
                        }
                    }

                }
            }
        }
        return $Path;
    }
    
    /**
     * Execute a action from a controller.
     * @param string $ControllerName
     * @param string $ActionName
     * @param KeyValueArray $Parameters 
     */
    public static function ExecuteController($ControllerName, $ActionName,KeyValueArray $Parameters = null){
        $ControllerClassName = $ControllerName . 'Controller';
        // save controller und action name
        self::$ActionName = $ActionName;
        self::$ControllerName = $ControllerName;

        Log::WriteLine('Executing controller: '. $ControllerName .', using '. $ControllerClassName . ' as class');
        
        if(class_exists($ControllerClassName)){
            // create a new instance
            $ControllerInstance = new $ControllerClassName($Parameters);
            if(method_exists($ControllerInstance, $ActionName)){
                Log::WriteLine('Executing action: '.$ActionName );
                // execute action
                $ControllerInstance->$ActionName();
            }
            else {
                throw new Exception('Action doesn\'t exists: '. $ControllerClassName . '->' . $ActionName);
            }
        }
        else {
            throw new Exception('Controller class doesn\'t exists: ' .$ControllerClassName);
        }
             
    }
}