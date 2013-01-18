<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a text field.
 */
class Normal extends Base{
    /**
     * Returns the html.
     * @return string 
     */
    public function HtmlSingle(){
        $this->Html = '';
        $this->Html .= '<div class="control-group ';
        
        $Message = $this->ValidationState->GetError($this->FieldName);
        if($Message!=''){
            $this->Html .= 'error';
        }
        $this->Html .= '">';
        $this->AddHtmlLabel();
        $this->Html .= '<div class="controls">';
        $this->AddHtmlSingleControl();
        $this->AddHtmlValidationField();
        $this->Html .= '</div>';
        $this->Html .= '</div>';
       
        return $this->Html;
    }
    
    protected function AddHtmlSingleControl(){
        $Disabled = '';
        if($this->ConfigurationHelper->FieldExists($this->FieldName)
                && $this->ConfigurationHelper->IsDisabled($this->FieldName)){
            $Disabled = 'disabled="disabled"';
        }
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
    
    
    
}
