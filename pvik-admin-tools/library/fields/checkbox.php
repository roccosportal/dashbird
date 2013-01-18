<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a checkbox.
 */
class Checkbox extends Base {
    

   protected function AddHtmlSingleControl(){
        $Disabled = '';
        if($this->ConfigurationHelper->FieldExists($this->FieldName)
                && $this->ConfigurationHelper->IsDisabled($this->FieldName)){
            $Disabled = 'disabled="disabled"';
        }
        $this->Html .= '<input class="span8" name="'. $this->GetLowerFieldName() .'" type="checkbox" '. $this->GetPresetValue() .' value="checked" '. $Disabled .' />';
    }
    
    
    /**
     * Returns the preset value for the checkbox
     * @return string 
     */
    public function GetPresetValue() {
        $FieldName = $this->FieldName;
        if($this->IsPOST($FieldName)){
            if($this->GetPOST()=='checked'){
               return 'checked="checked"';
            }
            else {
                return '';
            }
        }
        elseif(!$this->IsNewEntity()){
            if($this->Entity->$FieldName){
                return 'checked="checked"';
            }
            else {
                return '';
            }
        }
        elseif($this->ConfigurationHelper->HasValueField($FieldName, 'Preset') &&
            $this->ConfigurationHelper->GetValue($FieldName, 'Preset')=='checked'){
            return 'checked="checked"';
        }
        else {
            return '';
        }
    }
    
    /**
     * Validates the checkbox.
     * @return ValidationState 
     */
    public function Validation() {
        // ignore validation
        return $this->ValidationState;
    }
    
    /**
     * Returns the html for the overview.
     * @return string 
     */
    public function HtmlOverview() {
        $FieldName = $this->FieldName;
        $this->Html = '';
        $Checked = '';
        if($this->Entity->$FieldName){
           $Checked = 'checked="checked"';
        }
        $this->Html .= '<input class="input-field" type="checkbox" '. $Checked .' disabled="disabled" />';
        return  $this->Html;
    }
    
    /**
     * Updates the model.
     */
    public function Update(){
        $FieldName = $this->FieldName;
        $Value = $this->GetPost();
        if($Value == 'checked'){
            $this->Entity->$FieldName = true;
        }
        else {
            $this->Entity->$FieldName = false;
        }
    }
}
