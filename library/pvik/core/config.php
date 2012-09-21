<?php

namespace Pvik\Core;

/**
 * A placeholder for the config file
 */
class Config {

    /**
     * Contains the config values
     * @var array 
     */
    public static $Config;

    /**
     * Loads a file into the config
     * @param string $Path 
     */
    public static function Load($Path) {
        require($Path);
    }

}