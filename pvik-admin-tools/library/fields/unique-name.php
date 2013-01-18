<?php
namespace PvikAdminTools\Library\Fields;
/**
 * Displays a text field that creates a unique name from a other field.
 */
class UniqueName extends Base{
    /**
     * Returns the html for the field.
     * @return type 
     */
    protected function AddHtmlSingleControl(){
       
       
        $Disabled = 'disabled="disabled"';
        
        $this->Html .= '<input class="span8" name="'. $this->GetLowerFieldName() .'" type="text" value="'. $this->GetPresetValue() .'" ' . $Disabled . ' />';
    }
    
    /**
     * Returns the  html for the overview.
     * @return type 
     */
    public function HtmlOverview() {
        $this->Html = '';
        $FieldName = $this->FieldName;
        return $this->Entity->$FieldName;
    }
    
    /**
     * Checks if the field value is valid.
     * @return ValidationState. 
     */
    public function Validation() {
        // ignore 
        return $this->ValidationState;
    }
    
    public function Update(){
        $Field = $this->ConfigurationHelper->GetField($this->FieldName);
        // wrong 'UseField' configuration
        if(!isset($Field['UseField'])||!$this->FieldDefinitionHelper->FieldExists($Field['UseField'])){
            throw new \Exception('PvikAdminTools: UseField for '. $FieldName . ' is not set up correctly. UseField is missing or it the stated field does not exists.');
        }
        $UseField = $Field['UseField'];
        $FieldName = $this->FieldName;
        $this->Entity->$FieldName = $this->CreateUniqueName( $UseField);
    }
    
    protected function CreateUniqueName($UseField){
        $Name = $this->GetPOST($UseField);
        $IsValid = false;
        $UrlSafeName = \PvikAdminTools\Library\Help::MakeUrlSafe($Name);
        $UniqueName = $UrlSafeName;
        if($this->CheckIfUniqueName($UniqueName,  $UseField)){
            $IsValid = true;;
        }
        // add numbers till name is unique
        $i = 1;
        while(!$IsValid){
           $UniqueName = $UrlSafeName . '-' . $i;
           if($this->CheckIfUniqueName($UniqueName, $UseField)){
            $IsValid = true;;
           }
           $i++;
         }
        return $UniqueName;
    }
    
    protected function CheckIfUniqueName($Name, $UseField){
        $PrimaryKey = $this->Entity->GetPrimaryKey();
        $EntityArray = $this->Entity->GetModelTable()->LoadAll()
            ->FilterEquals($UseField, $Name);

        if($PrimaryKey!=null&&$PrimaryKey!=''){         
            $EntityArray = $EntityArray->FilterNotEquals($this->Entity->GetModelTable()->GetPrimaryKeyName(), $PrimaryKey);
        }
            
        // if query return an empty object, than no other entry has this name
        if($EntityArray->IsEmpty()){
            return true;
        }
        else {
            return false;
        }

    }
    
    
}