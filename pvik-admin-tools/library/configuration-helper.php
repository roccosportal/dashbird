<?php
namespace PvikAdminTools\Library;
/**
 * A helper class for the PvikAminTools configuration.
 */
class ConfigurationHelper {
    /**
     * Contains the configuration for the tables.
     * @var array 
     */
    protected $Tables;
    /**
     * Contains the configuration for the current set up table.
     * @var array 
     */
    protected $CurrentTable;
    
    /**
     * 
     */
    public function __construct(){
        $this->Tables = \Pvik\Core\Config::$Config['PvikAdminTools']['Tables'];
        // set current table to first table
        foreach($this->Tables as $TableName => $Configuration){
            $this->SetCurrentTable($TableName);
            break;
        }
    }
    
    /**
     * Sets the current used table.
     * @param string $TableName 
     */
    public function SetCurrentTable($TableName){
        if($this->TableExists($TableName)){
            $this->CurrentTable = $this->Tables[$TableName];
        }
        else {
            throw new \Exception('PvikAdminTools: table ' . $TableName  . ' does not exists in configuration');
        }
    }
    /**
     * Checks if a table exists in the configuration.
     * @param string $TableName
     * @return bool 
     */
    public function TableExists($TableName){
        return isset($this->Tables[$TableName]);
    }
    
    /**
     * Returns the configuration for all tables.
     * @return type 
     */
    public function GetTables(){
        return $this->Tables;
    }
    
    /**
     * Returns the configuration for currently used table.
     * @return array 
     */
    public function GetCurrentTable(){
        return $this->CurrentTable;
    }
    
    /**
     * Checks if a field exists in the currently used table.
     * @param string $FieldName
     * @return bool 
     */
    public function FieldExists($FieldName){
        if(isset($this->CurrentTable['Fields'][$FieldName])){
            return true;
        }
        return false;
    }
    
    /**
     * Checks if a field is nullable in the currently used table.
     * @param string $FieldName
     * @return bool 
     */
    public function IsNullable($FieldName){
        $Field = $this->GetField($FieldName);
        if(isset($Field['Nullable'])){
            return $Field['Nullable'];
        }
        return false;
    }
    
    /**
     * Returns the configuration for a field in the currently used table.
     * @param string $FieldName
     * @return array 
     */
     public function GetField($FieldName){
        if($this->FieldExists($FieldName)){
            return $this->CurrentTable['Fields'][$FieldName];
        }
        return null;
    }
    
    /**
     * Returns the type for a field in the currently used table.
     * @param string $FieldName
     * @return string 
     */
    public function GetFieldType($FieldName){
        $Field = $this->GetField($FieldName);
        if($Field!=null&&isset($Field['Type'])){
            return $Field['Type'];
        }
        return null;
    }
    
    /**
     * Checks if a field is from type "Ignore" in the currently used table.
     * @param string $FieldName
     * @return bool 
     */
    public function IsTypeIgnore($FieldName){
        $FieldType = $this->GetFieldType($FieldName);
        if($FieldType=='Ignore'){
            return true;
        }
        return false;
    }
    
    /**
     * Returns the field value for 'ShowInOverview' or false.
     * @param string $FieldName
     * @return bool 
     */
    public function ShowInOverview($FieldName){
        if($this->HasValueField($FieldName, 'ShowInOverview')){
            return $this->GetValue($FieldName, 'ShowInOverview');
        }
        return false;
    }
    
    /**
     * Returns the field value for 'Disabled' or false.
     * @param string $FieldName
     * @return bool 
     */
    public function IsDisabled($FieldName){
        if($this->HasValueField($FieldName, 'Disabled')){
            return $this->GetValue($FieldName, 'Disabled');
        }
        return false;
    }
    
    /**
     * Checks if a field is set up for a field configuration
     * @param string $FieldName
     * @param string $ValueField
     * @return bool 
     */
    public function HasValueField($FieldName, $ValueField){
        $Field = $this->GetField($FieldName);
        if($Field!=null&&isset($Field[$ValueField])){
            return true;
        }
        return false;
    }
    
    
    /**
     * Sets the value of a field configuration field.
     * @param string $FieldName
     * @param string $ValueField
     * @return mixed 
     */
    public function SetValue($FieldName, $ValueField, $Value){
        if($this->FieldExists($FieldName)){
            $this->CurrentTable['Fields'][$FieldName][$ValueField] = $Value;
            
        }
        else {
            throw new \Exception('PvikAdminTools: ' . $FieldName .' does not exists');
        } 
    }
    
    
    /**
     * Returns the value of a field configuration field.
     * @param string $FieldName
     * @param string $ValueField
     * @return mixed 
     */
    public function GetValue($FieldName, $ValueField){
        
        if($this->HasValueField($FieldName, $ValueField)){
            $Field = $this->GetField($FieldName);
            return $Field[$ValueField];
        }
        else {
            return null;
        }
       
         
    }
    /**
     * Returns a list of field in the currently used table.
     * @return ArrayObject 
     */
    public function GetFieldList(){
        $FieldList = new \ArrayObject();
        foreach($this->CurrentTable['Fields'] as $FieldName => $Definition){
            $FieldList->append($FieldName);
        }
        return $FieldList;
    }
    
    /**
     * Checks if currently used table has foreign tables.
     * @return bool 
     */
    public function HasForeignTables(){
        if(isset($this->CurrentTable['ForeignTables'])){
            return true;
        }
        return false;
    }
    
    /**
     * Returns the configuration for foreign tables for the currently used table.
     * @return array 
     */
    public function GetForeignTables(){
        if($this->HasForeignTables()){
            return $this->CurrentTable['ForeignTables'];
        }
        return null;
    }
}
