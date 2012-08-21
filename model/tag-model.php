<?php
class TagsModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Tags';
       
        $this->ModelName = 'Tag';
       
        $this->PrimaryKeyName = 'TagId';
       
        $this->FieldDefinition['TagId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Title'] =  array ('Type' => 'Normal');

    }
}

/**
 * @property int $TagId
 * @property string $Title
 */
class TagModel extends Model {
    public function __construct(){
        $this->ModelTableName = 'Tags';
    }

    public function ToArray(){
        return array (
                    'title' => $this->title
                    );
    }
}
?>