<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $DashboardEntriesTagsId
 * @property int $DashboardEntryId
 * @property DashboardEntryModel $DashboardEntry
 * @property int $TagId
 * @property TagModel $Tag
 */
class DashboardEntriesTags extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'DashboardEntriesTags';
    }

}
?>