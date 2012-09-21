<?php


namespace Dashbird\Model\Entities;
/**
 * @property int $TodoId
 * @property string $Text
 * @property boolean $IsDone
 * @property int $UserId
 * @porperty UserModel $User
 */
class Todo extends Module {
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