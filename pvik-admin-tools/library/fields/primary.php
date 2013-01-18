<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a text field.
 */
class Primary extends Normal{
    
    
    public function __construct($FieldName, \Pvik\Database\Generic\Entity $Entity, \Pvik\Web\Request $Request, \Pvik\Utils\ValidationState $ValidationState) {
        parent::__construct($FieldName, $Entity, $Request, $ValidationState);
        $this->ConfigurationHelper->SetValue($FieldName, 'Disabled', true);
        $this->ConfigurationHelper->SetValue($FieldName, 'Nullable', true);
    }
 
    
    
    
    public function Update() {
       // do nothing
    }
    
}
