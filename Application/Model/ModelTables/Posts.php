<?php
namespace Dashbird\Model\ModelTables;

class Posts extends \Pvik\Database\Generic\ModelTable {

        public function __construct() {
                // define the table name
                $this->TableName = 'Posts';

                $this->EntityName = 'Post';

                $this->PrimaryKeyName = 'PostId';

                $this->FieldDefinition['PostId'] = array('Type' => 'PrimaryKey');

                $this->FieldDefinition['Text'] = array('Type' => 'Normal');

                $this->FieldDefinition['Created'] = array('Type' => 'Normal');
                
                $this->FieldDefinition['Updated'] = array('Type' => 'Normal');

                $this->FieldDefinition['SearchHelper'] = array('Type' => 'Normal');

                $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');

                $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');

                $this->FieldDefinition['PostsTags'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'PostsTags', 'ForeignKey' => 'PostId');
                
                $this->FieldDefinition['SearchHelperParts'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'SearchHelperParts', 'ForeignKey' => 'PostId');
                
                $this->FieldDefinition['PostShares'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'PostShares', 'ForeignKey' => 'PostId');
                
                $this->FieldDefinition['Comments'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'Comments', 'ForeignKey' => 'PostId');
                
                
        }

}