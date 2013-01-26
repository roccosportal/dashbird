<?php

namespace Dashbird\Model\ModelTables;
class EntriesTags extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'EntriesTags';
       
        $this->EntityName = 'EntriesTags';
       
        $this->PrimaryKeyName = 'EntriesTagsId';
       
        $this->FieldDefinition['EntriesTagsId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['EntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Entries');
       
        $this->FieldDefinition['Entry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'EntryId');
        
        $this->FieldDefinition['TagId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Tags');
       
        $this->FieldDefinition['Tag'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'TagId');
        

    }
}