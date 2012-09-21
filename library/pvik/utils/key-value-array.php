<?php

namespace Pvik\Utils;

/**
 * A simple associative array
 */
Class KeyValueArray {

    /**
     * Array of KeyValuePairs.
     * @var array 
     */
    protected $KeyValuePairs = null;

    /**
     * 
     */
    public function __construct() {
        $this->KeyValuePairs = array();
    }

    /**
     * Add a value to a key if the key doesn't exists.
     * @param string $Key
     * @param mixed $Value 
     */
    public function Add($Key, $Value) {
        if (!$this->ContainsKey($Key)) {
            $KeyValuePair = new KeyValuePair($Key, $Value);
            array_push($this->KeyValuePairs, $KeyValuePair);
        } else {
            throw new \Exception('The key already exists: ' . $Key);
        }
    }

    /**
     * Set a value to a key even when the key already exists.
     * @param string $Key
     * @param mixed $Value 
     */
    public function Set($Key, $Value) {
        if ($this->ContainsKey($Key)) {
            $Pair = $this->GetPair($Key);
            if ($Pair != null) {
                $Pair->SetValue($Value);
            } else {
                throw new \Exception('Unexpected error caused.');
            }
        } else {
            $this->Add($Key, $Value);
        }
    }

    /**
     * Removes a key from the array.
     * @param type $Key 
     */
    public function Remove($Key) {
        if ($this->ContainsKey($Key)) {
            unset($this->KeyValuePairs[$Key]);
        } else {
            throw new \Exception('The key doesn\'t exists: ' . $Key);
        }
    }

    /**
     * Returns the KeyValuePair.
     * @param string $Key
     * @return KeyValuePair 
     */
    public function GetPair($Key) {
        foreach ($this->KeyValuePairs as $KeyValuePair) {
            if ($KeyValuePair->GetKey() == $Key) {
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
    public function Get($Key) {
        foreach ($this->KeyValuePairs as $KeyValuePair) {
            if ($KeyValuePair->GetKey() == $Key) {
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
    public function ContainsKey($Key) {
        foreach ($this->KeyValuePairs as $KeyValuePair) {
            if ($KeyValuePair->GetKey() == $Key) {
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
    public function IsNotEmpty($Key) {
        foreach ($this->KeyValuePairs as $KeyValuePair) {
            if ($KeyValuePair->GetKey() == $Key) {
                $Value = $KeyValuePair->GetValue();
                if (!empty($Value)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

}

