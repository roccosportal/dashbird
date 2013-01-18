<?php
namespace PvikAdminTools\Library\Fields;
use \PvikAdminTools\Library\ConfigurationHelper;
use \Pvik\Database\Generic\FieldDefinitionHelper;
use \Pvik\Database\Generic\ModelTable;
use \Pvik\Database\Generic\Entity;
use \Pvik\Utils\ValidationState;
use \Pvik\Web\Request;
/**
 * A class that contains the basic methods for a html table field 
 */
abstract class Base {
    /**
     * The current entity
     * @var \Pvik\Database\Generic\Entity
     */
    protected $Entity;
    /**
     * Indicates if the entity is a new entity.
     * @var bool 
     */
    protected $IsNewEntity;
    /**
     * Contains the html output.
     * @var type 
     */
    protected $Html;
    /**
     * Contains the helper class for the configuration.
     * @var ConfigurationHelper 
     */
    protected $ConfigurationHelper;
    /**
     * Contains the helper class for the model table field definition.
     * @var FieldDefinitionHelper
     */
    protected $FieldDefinitionHelper;
    /**
     * Contains the name of the field.
     * @var string 
     */
    protected $FieldName;
    /**
     * Contains the validation state of the current entry.
     * @var ValidationState
     */
    protected $ValidationState;
    /**
     * Contains the model table of the current entry.
     * @var ModelTable 
     */
    protected $ModelTable;
    
    /**
     * Contains a preset value for the field.
     * @var string 
     */
    protected $Preset;
    
    /**
     * 
     * @var Request 
     */
    protected $Request;
    
    /**
     *
     * @param string $FieldName
     * @param Entity $Entity
     * @param ValidationState $ValidationState 
     */
    public function __construct($FieldName, Entity $Entity, Request $Request, ValidationState $ValidationState){
        $this->FieldName = $FieldName;
        $this->Entity = $Entity;
        $this->ModelTable = $this->Entity->GetModelTable();
        $this->Request = $Request;
        $this->FieldDefinitionHelper = $this->ModelTable->GetFieldDefinitionHelper();
        $this->ConfigurationHelper = new \PvikAdminTools\Library\ConfigurationHelper();
        $this->ConfigurationHelper->SetCurrentTable($this->ModelTable->GetTableName());
        $this->ValidationState = $ValidationState;
        if($this->Entity->GetPrimaryKey()==null||$this->Entity->GetPrimaryKey()==''){
            $this->IsNewEntity = true;
        }
        else {
            $this->IsNewEntity = false;
        }
        $this->Preset = '';
    }
    
    /**
     * Sets the preset value of the current field.
     * @param string $Preset 
     */
    public function SetPreset($Preset){
        $this->Preset = $Preset;
    }
    
    /**
     * Checks if the model is a new model.
     * @return bool 
     */
    protected function IsNewEntity(){
        return $this->IsNewEntity;
    }
    
    /**
     * Returns the $_POST value of the current field if no field name given.
     * @param string $FieldName [optional]
     * @return string 
     */
    protected function GetPOST($FieldName = ''){
        if($FieldName==''){
            $FieldName = $this->FieldName;
        }
        return $this->Request->GetPOST(strtolower($FieldName));
    }

    protected function IsPOST($FieldName = ''){
        if($FieldName==''){
            $FieldName = $this->FieldName;
        }
        return $this->Request->IsPOST(strtolower($FieldName));
    }

    /**
     * Returns the preset value of the current field.
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
        elseif($this->Preset!=''){
            return $this->Preset;
        }
        elseif($this->ConfigurationHelper->HasValueField($FieldName, 'Preset')){
            return $this->ConfigurationHelper->GetValue($FieldName, 'Preset');
        }
        else {
            return '';
        }
    }
    
    /**
     * Returns a lowered name of the current field name.
     * @return string 
     */
    protected function GetLowerFieldName(){
        return strtolower($this->FieldName);
    }
    
    /**
     * Checks if this field is visible in single edit/new mode.
     * @return bool 
     */
    public function IsVisibleSingle(){
        if($this->ConfigurationHelper->FieldExists($this->FieldName) 
                && $this->ConfigurationHelper->IsTypeIgnore($this->FieldName)){
            return false;
        }
        return true;
    }
    
    /**
     * Adds a label to the html.
     */
    protected function AddHtmlLabel(){
        $this->Html .= '<label class="control-label" >' . $this->FieldName . '</label>';
    }
    
    /**
     * Validates the current field.
     * @return ValidationState 
     */
    public function Validation(){
      $Message = '';
      if(!$this->ConfigurationHelper->IsDisabled($this->FieldName) &&  !$this->ConfigurationHelper->IsNullable($this->FieldName)&&($this->GetPOST($this->FieldName)===null||$this->GetPOST($this->FieldName)==="")){
          $this->ValidationState->SetError($this->FieldName, 'Can not be empty.');
      }
      return $this->ValidationState;
    }
    
    /**
     * Adds a validation field to the html.
     */
    protected function AddHtmlValidationField(){
        $Message = $this->ValidationState->GetError($this->FieldName);
        if($Message!=''){
            $this->Html .= '<span  class="help-inline">'. $Message .'</span>';
        }
    }
    
    /**
     * Adds a single field for the html in edit/update mode.
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
    /**
     * 
     */
    protected abstract function AddHtmlSingleControl();
    
    /**
     * Adds a overview field for the htm in overview mode.
     */
    public abstract function HtmlOverview();
    
    /**
     * Updates a model field
     */
    public function Update(){
        $FieldName = $this->FieldName;
        if($this->ConfigurationHelper->IsDisabled($this->FieldName)){
            if(!$this->ConfigurationHelper->HasValueField($this->FieldName, 'Preset') && !$this->ConfigurationHelper->IsNullable($this->FieldName)){
                throw new \Exception('PvikAdminTools: Field ' . $this->FieldName . ' is not nullable and is disabled but does not have a "Preset" value.');
            }
            $this->Entity->$FieldName = $this->ConfigurationHelper->GetValue($this->FieldName, 'Preset');
        }
        else {
            $this->Entity->$FieldName = $this->GetPOST();
        }
    }
}
