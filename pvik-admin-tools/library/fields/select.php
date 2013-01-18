<?php
namespace PvikAdminTools\Library\Fields;
use \Pvik\Database\Generic\ModelTable;
/**
 * Displays a select field.
 */
class Select extends Base {
    /**
     * Returns the preset value.
     * @param string $FieldName
     * @param string $Value
     * @return string 
     */
    protected function GetSelectPresetValue($FieldName, $Value){
        if($this->IsPOST($FieldName)){
            if($this->GetPOST($FieldName)==$Value){
                return 'selected="selected"';
            }
            return '';
        }
        elseif(!$this->IsNewEntity()){
            if($this->Entity->$FieldName == $Value){
                return 'selected="selected"';
            }
            return '';
        }
        elseif($this->Preset!=''&&$this->Preset==$Value){
            return 'selected="selected"';
        }
        elseif($this->ConfigurationHelper->HasValueField($this->FieldName, 'Preset') &&
                ($this->ConfigurationHelper->GetValue($this->FieldName, 'Preset')==$Value)){
            return 'selected="selected"';
        }
        else {
            return '';
        }
    }
    
    /**
     * Returns the html for the field.
     * @return string 
     */
    protected function AddHtmlSingleControl(){
        if($this->FieldDefinitionHelper->IsTypeForeignObject($this->FieldName)){
          

            $ModelTableName = $this->FieldDefinitionHelper->GetModelTableNameForForeignObject($this->FieldName);
            $ForeignKeyFieldName = $this->FieldDefinitionHelper->GetForeignKeyFieldName($this->FieldName);
            $Field = $this->ConfigurationHelper->GetField($this->FieldName);
            $UseField = $Field['UseField'];
            $EntityArray = ModelTable::Get($ModelTableName)->LoadAll();

            $this->Html .= '<select class="span8" name="'. strtolower($ForeignKeyFieldName) .'">';
            if($this->ConfigurationHelper->IsNullable($this->FieldName)||!$this->IsNewEntity){
                 $this->Html .= '<option value="">(none)</option>';
            }
            foreach($EntityArray as $Entity){
                $this->Html .= '<option value="' . $Entity->GetPrimaryKey() .'" '.$this->GetSelectPresetValue($ForeignKeyFieldName, $Entity->GetPrimaryKey()).'>';
                $this->Html .= $Entity->$UseField;
                $this->Html .= '</option>';
            }


            $this->Html .= '</select>';
        }
        else {
            $this->Html .= 'Error';
        }
    }
    
    /**
     * Returns the html for the overview.
     * @return string 
     */
    public function HtmlOverview() {
        $this->Html = '';
        $FieldName = $this->FieldName;
        $Field = $this->ConfigurationHelper->GetField($this->FieldName);
        $UseField = $Field['UseField'];
        if($this->Entity->$FieldName!=null){
             $this->Html = $this->Entity->$FieldName->$UseField;
        }
        return $this->Html;
    }
    
    /**
     * Updates a entity.
     */
    public function Update(){
        $ForeignKeyFieldName = $this->FieldDefinitionHelper->GetForeignKeyFieldName($this->FieldName);
        $this->Entity->$ForeignKeyFieldName = $this->GetPOST($ForeignKeyFieldName);
    }
    
    /**
     * Validates the field value.
     * @return ValidationState 
     */
    public function Validation() {
      $Message = '';
      $ForeignKeyFieldName = $this->FieldDefinitionHelper->GetForeignKeyFieldName($this->FieldName);
      if(!$this->ConfigurationHelper->IsNullable($this->FieldName)&&$this->GetPOST($ForeignKeyFieldName)==""){
          $this->ValidationState->SetError($this->FieldName, 'Can not be empty');
      }
      return $this->ValidationState;
    }
}
