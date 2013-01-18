<?php

namespace Pvik\Utils;

/**
 * Class that can contains the validation state and errors if occured.
 */
class ValidationState {

    /**
     * Indicates if this class has errors.
     * @var bool 
     */
    protected $Valid;

    /**
     * Contains the errors.
     * @var KeyValueArray 
     */
    protected $Errors;

    /**
     * 
     */
    public function __construct() {
        $this->Valid = true;
        $this->Errors = new KeyValueArray();
    }

    /**
     * Set an error for a field.
     * @param string $Field
     * @param string $Message 
     */
    public function SetError($Field, $Message) {
        $this->Valid = false;
        $this->Errors->Set($Field, $Message);
    }

    /**
     * Gets an error or null.
     * @param string $Field
     * @return string 
     */
    public function GetError($Field) {
        return $this->Errors->Get($Field);
    }

    /**
     * Checks if this objects contains errors.
     * @return bool 
     */
    public function IsValid() {
        return $this->Valid;
    }

}