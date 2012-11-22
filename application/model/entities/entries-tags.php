<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $EntriesTagsId
 * @property int $EntryId
 * @property Entry $Entry
 * @property int $TagId
 * @property TagModel $Tag
 */
class EntriesTags extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'EntriesTags';
    }

}
?>