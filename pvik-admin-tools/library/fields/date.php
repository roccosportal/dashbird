<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a date field.
 */
class Date extends Base{
   
     protected function AddHtmlSingleControl(){
        $Disabled = '';
        if($this->ConfigurationHelper->FieldExists($this->FieldName)
                && $this->ConfigurationHelper->IsDisabled($this->FieldName)){
            $Disabled = 'disabled="disabled"';
        }
        $this->Html .= '<input class="span8" name="'. $this->GetLowerFieldName() .'" type="text" value="' . $this->GetPresetValue().'" '. $Disabled .' />';
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
     * Validates if the field value is a date.
     * @return ValidationState 
     */
    public function Validation() {
        parent::Validation();
        if($this->ValidationState->GetError($this->FieldName)==null && !$this->IsEnglishDate($this->GetPost($this->FieldName))){
              $this->ValidationState->SetError($this->FieldName, 'Not a date.');
        }
        return $this->ValidationState;
    }
    
    protected function IsEnglishDate($Date){
        $DateArray = explode('-', $Date);
        if(count($DateArray)==3){
            if(is_numeric($DateArray[0])&&is_numeric($DateArray[1])&&is_numeric($DateArray[2])){
                return checkdate($DateArray[1],$DateArray[2],$DateArray[1]);
            }
        }
        return false;
    }
    
    /**
     * Returns the preset type for the date field.
     * @return string 
     */
    protected function GetPresetValue(){
        $FieldName = $this->FieldName;
        if($this->IsPOST($FieldName)){
            return $this->GetPOST();
        }
        elseif(!$this->IsNewEntity()){
            return $this->Entity->$FieldName;
        }
        elseif($this->ConfigurationHelper->HasValueField($FieldName, 'Preset')){
            return $this->ConfigurationHelper->GetValue($FieldName, 'Preset');
        }
        else {
            return date('Y-m-d');
        }
    }
    
}