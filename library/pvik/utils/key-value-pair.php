<?php

namespace Pvik\Utils;

/**
 * A class that assigns a key to a value.
 */
Class KeyValuePair {

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

    public function __construct($Key, $Value) {
        $this->Key = $Key;
        $this->Value = $Value;
    }

    /**
     * Returns the key.
     * @return string 
     */
    public function GetKey() {
        return $this->Key;
    }

    /**
     * Sets the value.
     * @param mixed $Value 
     */
    public function SetValue($Value) {
        $this->Value = $Value;
    }

    /**
     * Returns the value.
     * @return mixed 
     */
    public function GetValue() {
        return $this->Value;
    }

    /**
     * Returns the value.
     * @return mixed. 
     */
    public function __toString() {
        return $this->Value;
    }

}