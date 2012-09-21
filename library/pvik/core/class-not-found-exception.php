<?php

namespace Pvik\Core;

/**
 * Exception when no class was found.
 */
class ClassNotFoundException extends \Exception {

    /**
     * The class that was looked for 
     * @var string 
     */
    protected $Class;

    /**
     * The path that was looked in
     * @var string 
     */
    protected $SearchedFor;

    /**
     * 
     * @param string $Class The class that was looked for
     * @param string $SearchedFor The path that was looked in
     */
    public function __construct($Class, $SearchedFor) {
        $this->Class = $Class;
        $this->SearchedFor = $SearchedFor;
        $Message = 'Class not found: ' . $Class . "\n" . 'Searched for:' . $SearchedFor;
        parent::__construct($Message);
    }

    /**
     * Returns the class that was looked for
     * @return string
     */
    public function GetClass() {
        return $this->Class;
    }

    /**
     * Returns the path that was looked in
     * @return type
     */
    public function GetSearchedFor() {
        return $this->SearchedFor;
    }

}