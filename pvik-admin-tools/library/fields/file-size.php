<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a disabled file size field that gets the file size of a file.
 */
class FileSize extends Normal{
   
    
    protected function AddHtmlSingleControl(){
        $Disabled = 'disabled="disabled"';
      
        $this->Html .= '<input class="span8" name="'. $this->GetLowerFieldName() .'" type="text" value="'. $this->GetPresetValue() .'" ' . $Disabled . ' />';    
    
    }
    /**
     * Validates the field value.
     * @return ValidationState 
     */
    public function Validation() {
        // ignore 
        return $this->ValidationState;
    }
    
    /**
     * Updates the model.
     */
    public function Update(){
        $FieldName = $this->FieldName;
        $Field = $this->ConfigurationHelper->GetField($FieldName);
        // wrong 'UseField' configuration
        if(!isset($Field['UseField'])||!$this->FieldDefinitionHelper->FieldExists($Field['UseField'])){
            throw new \Exception('PvikAdminTools: UseField for '. $FieldName . ' is not set up correctly. UseField is missing or it the stated field does not exists.');
        }
        $UseField = $Field['UseField'];
        
        $this->Model->$FieldName = filesize(\Pvik\Core\Path::RealPath($this->GetPOST($UseField)));
    }
}

