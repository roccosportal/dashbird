<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $CommentId
 * @property string $Text
 * @property string $DateTime
 * @property int $UserId
 * @property User $User
 * @property int $PostId
 * @property Post $Post
 */
class Comment extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'Comments';
    }

    public function Insert() {
        if ($this->DateTime == null) {
            $this->DateTime = date('Y.m.d  H:i:s');
        }

        parent::Insert();
    }

    public function ToArray() {
        return array(
            "commentId" => $this->CommentId,
            "text" => $this->Text,
            "datetime" => $this->DateTime,
            "user" => array("userId" => $this->UserId, "name" => $this->User->Name)
        );
    }

}
