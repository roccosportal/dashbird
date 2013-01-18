<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a textarea.
 */
class Textarea extends Base {
    
    /**
     * Returns the html for the field.
     * @return type 
     */
    protected function AddHtmlSingleControl(){
        $this->Html .= '<textarea class="span8" name="'. $this->GetLowerFieldName() .'" cols="50" rows="15" >'.htmlentities($this->GetPresetValue()) .'</textarea>';
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