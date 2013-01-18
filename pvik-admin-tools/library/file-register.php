<?php
namespace PvikAdminTools\Library;
/**
 * A class that handles registeres files.
 */
class FileRegister {
    /**
     * Contains the file paths.
     * @var array 
     */
    protected static $Files;
    
    /**
     * Registers a path.
     * @param string $Path 
     */
    public static function RegisterFile($Path){
        if(!is_array(self::$Files)){
            self::$Files = array();
        }
        array_push(self::$Files, $Path);
    }
    
    /**
     * checks if a file is already registerd.
     * @param string $Path
     * @return bool 
     */
    public static function IsFileRegisterd($Path){
        if(!is_array(self::$Files)){
            self::$Files = array();
        }
        
        if(in_array($Path, self::$Files)){
            return true;
        }
        else {
            return false;
        }
    }
}
