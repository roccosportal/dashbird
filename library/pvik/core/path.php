<?php

namespace Pvik\Core;

/**
 * Static class with usefull functions for handling paths.
 */
class Path {

    /**
     * Contains the absolute file base (server path)
     * Example: /var/www/sub-folder/
     * @var string
     */
    protected static $AbsoluteFileBase;

    /**
     * Contains the relative file path (http path)
     * @var string 
     */
    protected static $RelativeFileBase;

    /**
     * Initalizes the absolute and relative file path. 
     */
    public static function Init() {
        self::$AbsoluteFileBase = getcwd() . '/';
        self::$RelativeFileBase = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    }

    /**
     * Returns the absolute file base (server path)
     * @return string
     */
    public static function GetAbsoluteFileBase() {
        return self::$AbsoluteFileBase;
    }

    /**
     * Returns the relative file base (http path)
     * @return string
     */
    public static function GetRelativeFileBase() {
        return self::$RelativeFileBase;
    }

    /**
     * Returns an absolute path (server path).
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
     * Returns a relative path (http path).
     * Resolves the ~/ symbol.
     * Example /sub-folder/something.js
     * @param string $Path
     * @return string
     */
    public static function RelativePath($Path) {
        $Path = str_replace('~/', self::$RelativeFileBase, $Path);
        return $Path;
    }

    /**
     * Converts a name to a safe path name. Converts ThisIsAnExample to this-is-an-example.
     * @param string $Name
     * @return string 
     */
    public static function ConvertNameToPath($Name) {
        $Name = preg_replace("/([a-z])([A-Z][A-Za-z0-9])/", '${1}-${2}', $Name);
        $Name = str_replace('\\', '/', $Name);
        return strtolower($Name);
    }

}
