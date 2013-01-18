<?php
namespace PvikAdminTools\Library;
use \Pvik\Database\Generic\Entity;
use \Pvik\Database\Generic\EntityArray;
use \Pvik\Database\Generic\ModelTable;
use \PvikAdminTools\Library\ConfigurationHelper;
use \Pvik\Utils\ValidationState;
use \Pvik\Web\Request;
/**
 * Display html for a single entity entry.
 */
class SingleHtml{
    /**
     * The entity entry that is displayed.
     * @var Entity 
     */
    protected $Entity;
    /**
     * A helper class for the PvikAdminTools configuration.
     * @var ConfigurationHelper 
     */
    protected $ConfigurationHelper;
    /**
     * The ValidationState of the current entity.
     * @var ValidationState
     */
    protected $ValidationState;
    /**
     * Indicates if a model is new.
     * @var bool 
     */
    protected $IsNewEntity;

    /**
     * 
     * @var \Pvik\Web\Request 
     */
    protected $Request;
    
    /**
     * Contains an associative array of field preset values.
     * @var array 
     */
    protected $PresetValues;
    
    /**
     * Contains the redirect back url for foreign tables after clicking new and submitting the form
     * @var string 
     */
    protected $ForeignTableButtonRedirectBackUrl;
    
    /**
     * The html that is displayed.
     * @var string. 
     */
    protected $Html;
    
    /**
     *
     * @param Entity $Entity
     * @param ValidationState $ValidationState 
     */
    public function __construct(Entity $Entity,ValidationState $ValidationState, Request $Request){
        
        $this->Entity = $Entity;
        $this->Request = $Request;
        $this->ConfigurationHelper = new ConfigurationHelper();
        $this->ConfigurationHelper->SetCurrentTable($this->Entity->GetModelTable()->GetTableName());
        $this->ValidationState = $ValidationState;
        if($this->Entity->GetPrimaryKey()==null||$this->Entity->GetPrimaryKey()==''){
            $this->IsNewEntity = true;
        }
        else {
            $this->IsNewEntity = false;
        }
        $this->PresetValues = array();
    }
    
    /**
     * Checks if the entity is new.
     * @return bool 
     */
    protected function IsNewEntity(){
        return $this->IsNewEntity;
    }
    
    /**
     * Set the preset values for fields.
     * Must be an associative array.
     * @param array $PresetValues 
     */
    public function SetPresetValues(array $PresetValues){
        $this->PresetValues = $PresetValues;
    }
    
    /**
     * This functions set a redirect back url when you click on new in a foreign table.
     * After you submitted the new table entry the form redirects back to the the url.
     * @param string $Url 
     */
    public function SetForeignTableButtonRedirectBackUrl($Url){
        $this->ForeignTableButtonRedirectBackUrl = $Url;
    }
    
    /**
     * Returns the html of the entry.
     * @return string 
     */
    public function ToHtml(){
        $this->Html = '<form class="form-vertical" method="post">';
        foreach($this->ConfigurationHelper->GetFieldList() as $FieldName){
            $Type = $this->ConfigurationHelper->GetFieldType($FieldName);
            $FieldClassName = '\\PvikAdminTools\\Library\\Fields\\' . $Type;
            if(!class_exists($FieldClassName)){
                throw new \Exception('PvikAdminTools: The type '.$Type . ' does not exists. Used for the field '. $FieldName);
            }
            $Field = new $FieldClassName($FieldName, $this->Entity, $this->Request, $this->ValidationState);
            /* @var $Field \PvikAdminTools\Library\Fields\Base */
            if($Field->IsVisibleSingle()){
                if(isset($this->PresetValues[strtolower($FieldName)])){
                    $Field->SetPreset($this->PresetValues[strtolower($FieldName)]);
                }
               
                $this->Html .= $Field->HtmlSingle();
              
            }
            
            
        }
       
        $this->AddHtmlSubmit();
        $this->Html .= '</form>';
        
        if(!$this->IsNewEntity()&&$this->ConfigurationHelper->HasForeignTables()){
             $this->Html .= '<div class="foreign-tables-field">';
            foreach($this->ConfigurationHelper->GetForeignTables() as $ForeignTable => $Configuration){
                $PrimaryKey = $this->Entity->GetPrimaryKey();
                $ForeignKey = $Configuration['ForeignKey'];
                $ModelTable = ModelTable::Get($ForeignTable);
                $EntityArray =  $ModelTable->LoadAll();
                $EntityArray = $EntityArray->FilterEquals($ForeignKey, $PrimaryKey);
                $TableHtml =  new \PvikAdminTools\Library\TableHtml($EntityArray,$this->Request);
                // saerch for fields that we don't need to show
                $ForeignObjectFieldNames = array ();
                $Helper = $ModelTable->GetFieldDefinitionHelper();
                foreach($Helper->GetFieldList() as $FieldName){
                    // search for a foreign object that uses that refers to the original model
                    // we don't need to show this table column
                    if($Helper->IsTypeForeignObject($FieldName)){
                        if($Helper->GetForeignKeyFieldName($FieldName)==$ForeignKey){
                            array_push($ForeignObjectFieldNames,$FieldName);
                        }
                    }
                }
                
                $TableHtml->SetHiddenFields($ForeignObjectFieldNames);
                
                // set preset values
                $PresetValues = array ();
                foreach($ForeignObjectFieldNames as $ForeignObjectFieldName){
                    $PresetValues[$ForeignObjectFieldName] = $PrimaryKey;
                }
                $TableHtml->SetNewButtonPresetValues($PresetValues);
                
                if($this->ForeignTableButtonRedirectBackUrl!=null){
                    $TableHtml->SetButtonRedirectBack($this->ForeignTableButtonRedirectBackUrl);
                }
                $this->Html .= '<div class="field">';
                $this->Html .= '<label class="label-field">' . $ForeignTable . '</label>';
                $this->Html .= $TableHtml->ToHtml();
                $this->Html .= '</div>';
            }
            $this->Html .= '</div>';
        }
        
        return $this->Html;
    }
    
    /**
     * Adds a submit button to the html.
     */
    protected function AddHtmlSubmit(){
         $this->Html .= '<div class="control-group">
                    <div class="controls">
                        <button type="submit" name="submit" class="btn">Submit</button>
                    </div>
                </div>';
    }
    
}
?>