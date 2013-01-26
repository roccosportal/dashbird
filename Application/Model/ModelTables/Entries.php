<?php
namespace Dashbird\Model\ModelTables;

class Entries extends \Pvik\Database\Generic\ModelTable {

        public function __construct() {
                // define the table name
                $this->TableName = 'Entries';

                $this->EntityName = 'Entry';

                $this->PrimaryKeyName = 'EntryId';

                $this->FieldDefinition['EntryId'] = array('Type' => 'PrimaryKey');

                $this->FieldDefinition['Text'] = array('Type' => 'Normal');

                $this->FieldDefinition['DateTime'] = array('Type' => 'Normal');

                $this->FieldDefinition['SearchHelper'] = array('Type' => 'Normal');

                $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');

                $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');

                $this->FieldDefinition['EntriesTags'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'EntriesTags', 'ForeignKey' => 'EntryId');
                
                $this->FieldDefinition['SearchHelperParts'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'SearchHelperParts', 'ForeignKey' => 'EntryId');
                
                $this->FieldDefinition['EntryShares'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'EntryShares', 'ForeignKey' => 'EntryId');
                
                $this->FieldDefinition['Comments'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'Comments', 'ForeignKey' => 'EntryId');
                
                
        }

}