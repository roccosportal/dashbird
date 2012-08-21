<?php
class UsersModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Users';
       
        $this->ModelName = 'User';
       
        $this->PrimaryKeyName = 'UserId';
       
        $this->FieldDefinition['UserId'] = array ('Type' => 'PrimaryKey');
        
        $this->FieldDefinition['Name'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['Password'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['Links'] = array ('Type' => 'ManyForeignObjects', 'ModelTable' => 'Links','ForeignKey' => 'UserId');

        $this->FieldDefinition['Notes'] = array ('Type' => 'ManyForeignObjects', 'ModelTable' => 'Notes','ForeignKey' => 'UserId');
    }
}

/**
 * @property int $UserId
 * @property string $Name
 * @property string $Password
 * @porperty ModelArray $Links
 * @porperty ModelArray $Notes
 */
class UserModel extends Model {
    public function __construct(){
        $this->ModelTableName = 'Users';
    }
}
?>