<?php

namespace Dashbird\Model\ModelTables;
class DashboardEntriesTags extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'DashboardEntriesTags';
       
        $this->EntityName = 'DashboardEntriesTags';
       
        $this->PrimaryKeyName = 'DashboardEntriesTagsId';
       
        $this->FieldDefinition['DashboardEntriesTagsId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['DashboardEntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'DashboardEntries');
       
        $this->FieldDefinition['DashboardEntry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'DashboardEntryId');
        
        $this->FieldDefinition['TagId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Tags');
       
        $this->FieldDefinition['Tag'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'TagId');
        

    }
}