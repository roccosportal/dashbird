<?php
namespace Dashbird\Model\ModelTables;

class DashboardEntries extends \Pvik\Database\Generic\ModelTable {

        public function __construct() {
                // define the table name
                $this->TableName = 'DashboardEntries';

                $this->EntityName = 'DashboardEntry';

                $this->PrimaryKeyName = 'DashboardEntryId';

                $this->FieldDefinition['DashboardEntryId'] = array('Type' => 'PrimaryKey');

                $this->FieldDefinition['Module'] = array('Type' => 'Normal');

                $this->FieldDefinition['ReferenceId'] = array('Type' => 'Normal');

                $this->FieldDefinition['DateTime'] = array('Type' => 'Normal');

                $this->FieldDefinition['SearchHelper'] = array('Type' => 'Normal');

                $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');

                $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');

                $this->FieldDefinition['DashboardEntriesTags'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'DashboardEntriesTags', 'ForeignKey' => 'DashboardEntryId');
                
                $this->FieldDefinition['SearchHelperParts'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'SearchHelperParts', 'ForeignKey' => 'DashboardEntryId');
                
                $this->FieldDefinition['EntryShares'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'EntryShares', 'ForeignKey' => 'DashboardEntryId');
                
                
        }

}