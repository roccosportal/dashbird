<?php
class NotesModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Notes';
       
        $this->ModelName = 'Note';
       
        $this->PrimaryKeyName = 'NoteId';
       
        $this->FieldDefinition['NoteId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Text'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
    }
}

Core::Depends('~/model/module-model.php');
/**
 * @property int $NotesId
 * @property string $Text
 * @property int $UserId
 * @porperty UserModel $User
 */
class NoteModel extends ModuleModel {
    public function __construct(){
        parent::__construct('Note');
        $this->ModelTableName = 'Notes';
    }
    
    public function ToArray(){
        return array (
            "noteId" => $this->NoteId,
            "text" => $this->Text);
    }
}
?>