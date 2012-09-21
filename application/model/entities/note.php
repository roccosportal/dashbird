<?php


namespace Dashbird\Model\Entities;
/**
 * @property int $NotesId
 * @property string $Text
 * @property int $UserId
 * @porperty UserModel $User
 */
class Note extends Module {
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