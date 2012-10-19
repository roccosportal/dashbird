<?php
namespace Dashbird\Model\ModelTables;
class EntryShares extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        $this->TableName = 'EntryShares';
       
        $this->EntityName = 'EntryShare';
       
        $this->PrimaryKeyName = 'EntryShareId';
       
        $this->FieldDefinition['EntryShareId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['DashboardEntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'DashboardEntries');
       
        $this->FieldDefinition['DashboardEntry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'DashboardEntryId');
       
        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
        
    }
}