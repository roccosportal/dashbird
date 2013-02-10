<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $PostsTagsId
 * @property int $PostId
 * @property Post $Post
 * @property int $TagId
 * @property TagModel $Tag
 */
class PostsTags extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'PostsTags';
    }

}
?>