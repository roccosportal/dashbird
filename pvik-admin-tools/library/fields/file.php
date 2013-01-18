<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a normal text field for a file path.
 */
class File extends Normal{
    
    /**
     * Checks if the value is a file.
     * @return ValidationState 
     */
    public function Validation() {
        parent::Validation();
        if($this->ValidationState->GetError($this->FieldName)==null && !is_file(\Pvik\Core\Path::RealPath($this->GetPOST()))){
              $this->ValidationState->SetError($this->FieldName, 'Must be a valid file.');
        }
        return $this->ValidationState;
    }
    
}

