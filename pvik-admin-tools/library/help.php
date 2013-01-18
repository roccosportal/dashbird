<?php
namespace PvikAdminTools\Library;
/**
 * A helper class for mixed functions.
 */
class Help {
    /**
     * Returns a url safe text.
     * @param string $Text
     * @return string 
     */
    public static function MakeUrlSafe($Text){
        $String = str_replace('-', '', $Text);
        $String = str_replace(' ', '-', $String);
        $String = str_replace(':', '', $String);
        $String = str_replace('.', '', $String);
        $String = str_replace(',', '', $String);
        $String = str_replace('!', '', $String);
        $String = str_replace('?', '', $String);
        $String = str_replace("'", '', $String);
        $String = str_replace("&", '', $String);
         $String = str_replace('--', '-', $String);
        $String = str_replace('--', '-', $String);
        $String = strtolower($String);
        return $String;
    }
    
    /**
     * Returns a relative file path with the PvikAdminTools base path.
     * @param string $Path
     * @return string 
     */
    public static function FileRelativePath($Path){
        return \Pvik\Core\Path::RelativePath(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . $Path);
    }
}
