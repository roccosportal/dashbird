<?php
class TodosModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Todos';
       
        $this->ModelName = 'Todo';
       
        $this->PrimaryKeyName = 'TodoId';
       
        $this->FieldDefinition['TodoId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Text'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['IsDone'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
    }
}


Core::Depends('~/model/module-model.php');
/**
 * @property int $TodoId
 * @property string $Text
 * @property boolean $IsDone
 * @property int $UserId
 * @porperty UserModel $User
 */
class TodoModel extends ModuleModel {
    public function __construct(){
        parent::__construct('Todo');
        $this->ModelTableName = 'Todos';
    }

    public function ToArray(){
        return array (
            "todoId" => $this->TodoId,
            "text" => $this->Text,
            "isDone" => $this->IsDone);
    }
    

}
?>