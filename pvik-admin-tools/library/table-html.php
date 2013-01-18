<?php
namespace PvikAdminTools\Library;
use \Pvik\Database\Generic\Entity;
use \Pvik\Database\Generic\EntityArray;
use \Pvik\Web\Request;
use \Pvik\Utils\ValidationState;
use \PvikAdminTools\Library\ConfigurationHelper;

/**
 * Displays html for a table list.
 */
class TableHtml {
    /**
     * The entries of the list. 
     * @var EntityArray 
     */
    protected $EntityArray;
    /**
     * The html that is displayed.
     * @var type 
     */
    protected $Html;
    /**
     * A helper class for the PvikAdminTools configuration.
     * @var ConfigurationHelper 
     */
    protected $ConfigurationHelper;

    /**
     * 
     * @var \Pvik\Web\Request 
     */
    protected $Request;
    /**
     * Fields that will be hidden in the list.
     * @var array 
     */
    protected $HiddenFields;

    
    /**
     * The count of columns
     * @var int 
     */
    protected $Columns;
    
    /**
     * Contains the redirect back url for the buttons.
     * @var string 
     */
    protected $ButtonRedirectBack;
    /**
     * Contains the preset values for the new button.
     * @var array 
     */
    protected $NewButtonPresetValues;
    
    /**
     *
     * @param EntityArray $EntityArray 
     */
    public function __construct(EntityArray $EntityArray,Request  $Request){
        $this->EntityArray = $EntityArray;
        $this->Request = $Request;
        $this->ConfigurationHelper = new ConfigurationHelper();
        $this->ConfigurationHelper->SetCurrentTable($this->EntityArray->GetModelTable()->GetTableName());
        $this->HiddenFields = array();
        $this->Columns = 0;
    }
    
    /**
     * Sets the array of fields that will be hidden.
     * @param array $HiddenFields 
     */
    public function SetHiddenFields(array $HiddenFields){
        $this->HiddenFields = $HiddenFields;
    }
    
    /**
     * This functions set a redirect back url when you click on new.
     * After you submitted the new table entry the form redirects back to the the url.
     * @param string $Url 
     */
    public function SetButtonRedirectBack($Url){
        $this->ButtonRedirectBack = $Url;
    }
    
    /**
     * This functions set preset values for the new button url.
     * The new table entry form recognises the values and preset them.
     * @param array $PresetValues 
     */
    public function SetNewButtonPresetValues(array $PresetValues){
        $this->NewButtonPresetValues = $PresetValues;
    }
    
    /**
     * Returns the html for the list.
     * @return type 
     */
    public function ToHtml(){
        $this->Html = '<table class="table table-hover table-bordered">';
        // create table header
        $this->AddHtmlTableHeader();
        // create rows
        $this->Html .= '<tbody>';
        foreach($this->EntityArray as $Entity){
            $this->AddHtmlTableRow($Entity);
        }
        $this->AddHtmlTableFooter();
        $this->Html .= '</tbody>';
        $this->Html .= '</table>';
        return $this->Html;
    }
    
    /**
     * Adds a table header to the html.
     */
    protected function AddHtmlTableHeader(){
        $this->Html .= '<thead><tr>';
        foreach($this->ConfigurationHelper->GetFieldList() as $FieldName){
            if($this->ConfigurationHelper->ShowInOverView($FieldName)&&!in_array($FieldName, $this->HiddenFields)){
                    $this->Columns++;
                    $this->Html .= '<th>' . $FieldName . '</th>';
            }
        }
        if($this->ConfigurationHelper->HasForeignTables()){
            foreach($this->ConfigurationHelper->GetForeignTables() as $ForeignTableName => $ForeignTable ){
                if(isset($ForeignTable['ShowCountInOverview'])&&$ForeignTable['ShowCountInOverview'] == true){
                    $this->Columns++;
                    $this->Html .= '<th>' . $ForeignTableName . '</th>';
                }
            }
        }
        
        // add for options
        $this->Columns++;
        $this->Html .= '<th class="options"></th>';
        $this->Html .= '</thead></tr>';
        
    }
    
    /**
     * Adds a table row to the html.
     * @param Entity $Entity 
     */
    protected function AddHtmlTableRow(Entity $Entity){
        $this->Html .= '<tr>';
        $ModelTable = $Entity->GetModelTable();
        foreach($this->ConfigurationHelper->GetFieldList() as $FieldName){
            if($this->ConfigurationHelper->ShowInOverView($FieldName)&&!in_array($FieldName, $this->HiddenFields)){
                $Type = $this->ConfigurationHelper->GetFieldType($FieldName);
                $FieldClassName = 'PvikAdminTools\\Library\\Fields\\' . $Type;
                if(!class_exists($FieldClassName)){
                    throw new \Exception('PvikAdminTools: The type '.$Type . ' does not exists. Used for the field '. $FieldName);
                }
                $Field = new $FieldClassName($FieldName, $Entity, $this->Request, new ValidationState());
              
                /* @var $Field PvikAdminToolsBaseField */
                $this->Html .= '<td>' . $Field->HtmlOverview() . '</td>';
          
            }
        }
        if($this->ConfigurationHelper->HasForeignTables()){
            foreach($this->ConfigurationHelper->GetForeignTables() as $ForeignTableName => $ForeignTable){
                if(isset($ForeignTable['ShowCountInOverview'])&&$ForeignTable['ShowCountInOverview'] == true){
                    // much faster than accessing view $Entity->$ForeignTableName->count
                    // this would load the entire entries
                    $ForeignKeys = $Entity->GetFieldData($ForeignTableName);
                    $ForeignKeys = explode(',', $ForeignKeys);
                    $this->Html .= '<td>' .  count($ForeignKeys) . '</td>';
                }
            }
        }
        // add options
        $this->Html .= '<td class="options">[';
        
        $EditButtonUrl = \Pvik\Core\Path::RelativePath('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($ModelTable->GetTableName()) . ':edit:'. $Entity->GetPrimaryKey().'/');
        if($this->ButtonRedirectBack!=null){
            $EditButtonUrl .= '?redirect-back-url=' . urlencode($this->ButtonRedirectBack);
        }
        
        $this->Html .= '<a href="'. $EditButtonUrl .'">edit</a>|';
        
        $DeleteButtonUrl = \Pvik\Core\Path::RelativePath('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($ModelTable->GetTableName()) . ':delete:'. $Entity->GetPrimaryKey().'/');
        if($this->ButtonRedirectBack!=null){
            $DeleteButtonUrl .= '?redirect-back-url=' . urlencode($this->ButtonRedirectBack);
        }
        $this->Html .= '<a href="'. $DeleteButtonUrl .'" onclick="return confirm(\'Do you really want to delete this entry?\')">delete</a>';
        $this->Html .= ']</td>';
        $this->Html .= '</tr>';
    }
    
    /**
     * Adds a table footer to the html.
     */
    protected function AddHtmlTableFooter(){
        $ModelTable = $this->EntityArray->GetModelTable();
        $this->Html .= '<tr>';
        
        $NewButtonUrl =  \Pvik\Core\Path::RelativePath('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url']) . 'tables/' .strtolower($ModelTable->GetTableName()) . ':new';
        
        if($this->NewButtonPresetValues!=null){
            $NewButtonUrl .= '/';
            $First = true;
            foreach($this->NewButtonPresetValues as $Key => $Value){
                if($First){
                    $First = false;
                }
                else {
                    $NewButtonUrl .= ':';
                }
                $NewButtonUrl .= strtolower($Key). ':' . $Value;
            }
        }
        $NewButtonUrl .= '/';
        
        if($this->ButtonRedirectBack!=null){
            $NewButtonUrl .= '?redirect-back-url=' . urlencode($this->ButtonRedirectBack);
        }
        for ($Index = 0; $Index < $this->Columns ; $Index++) {
            if($Index + 1 == $this->Columns){
                // last column
                $this->Html .= '<td class="options">[';
                $this->Html .= '<a href="'. $NewButtonUrl .'">new</a>';
                $this->Html .= ']</td>';
            }
            else {
                $this->Html .= '<td>';
                $this->Html .= '</td>';
            }
        }
        $this->Html .= '</tr>';
    }
    
}
