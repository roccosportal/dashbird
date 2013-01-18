<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a field that contains the date of the last update date.
 */
class UpdateDate extends Base{
    /**
     * Returns the html for the field.
     * @return string 
     */
     protected function AddHtmlSingleControl(){
       
        $Disabled = 'disabled="disabled"';
       
        $this->Html .= '<input class="span8" name="'. $this->GetLowerFieldName() .'" type="text" value="'. $this->GetPresetValue() .'" ' . $Disabled . ' />';
        
       
    }
    
    /**
     * Returns the html for the overview.
     * @return type 
     */
    public function HtmlOverview() {
        $this->Html = '';
        $FieldName = $this->FieldName;
        return  $this->Entity->$FieldName;
    }
    
    /**
     * Checks if the field value is valid.
     * @return ValidationState 
     */
    public function Validation() {
        // ignore 
        return $this->ValidationState;
    }
    
    /**
     *  Updates the model field.
     */
    public function Update(){
        $FieldName = $this->FieldName;
        $this->Entity->$FieldName =date('Y-m-d');
    }
    
    
    
}