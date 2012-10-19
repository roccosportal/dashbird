<?php

namespace Dashbird\Controllers;

use Dashbird\Library\Constants\AJAX;
use Pvik\Database\Generic\ModelTable;

class Link extends Base {

    public function AjaxAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }


        $Link = htmlentities(utf8_decode($this->Request->GetGET('link')));

        $IsImage = $this->Request->GetGET('isImage');

        // everything else then true is false
        $IsImage = ($IsImage == '1');

        if (empty($Link)) {
            return $this->ResponseWrongData();
        }
        $LinkEntity = new \Dashbird\Model\Entities\Link();
        $LinkEntity->Link = $Link;
        $LinkEntity->IsImage = $IsImage;
        $LinkEntity->UserId = $this->GetUserId();
        $LinkEntity->Insert();

        $DashboardEntry = new \Dashbird\Model\Entities\DashboardEntry();
        $DashboardEntry->Module = 'Link';
        $DashboardEntry->ReferenceId = $LinkEntity->LinkId;
        $DashboardEntry->SearchHelper = '';
        $DashboardEntry->UserId = $this->GetUserId();
        $DashboardEntry->Insert();

        $DashboardEntry->SetSearchHelpePart('link-link', $LinkEntity->Link);
        $DashboardEntry->AddTag('link');
        $DashboardEntry->Update();


        return $this->ResponseSuccess($DashboardEntry->ToArray());
    }

    public function AjaxDeleteAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $LinkId = $this->Request->GetGET('linkId');
        $Link = ModelTable::Get('Links')->LoadByPrimaryKey($LinkId);
        /* @var $Link \Dashbird\Model\Entities\Link */
        if ($Link == null || !$Link->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $DashboardEntry = $Link->GetDashboardEntry();

        $DashboardEntry->Delete();
        $Link->Delete();
        return $this->ResponseSuccess();
    }

    public function AjaxEditAction() {

        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $LinkId = $this->Request->GetGET('linkId');
        $Link = htmlentities(utf8_decode($this->Request->GetGET('link')));
        $Tags = $this->Request->GetGET('tags');
        $IsImage = $this->Request->GetGET('isImage');

        // everything else then true is false
        $IsImage = ($IsImage == '1');


        $LinkEntity = ModelTable::Get('Links')->LoadByPrimaryKey($LinkId);
        /* @var $LinkEntity \Dashbird\Model\Entities\Link */
        if ($LinkEntity == null || empty($Link) || !$LinkEntity->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $LinkEntity->Link = $Link;
        $LinkEntity->IsImage = $IsImage;
        $LinkEntity->Update();

        $DashboardEntry = $LinkEntity->GetDashboardEntry();
        if (is_array($Tags)) {
            $DashboardEntry->SetTags($Tags);
        } else {
            $DashboardEntry->SetTags(array());
        }
        $DashboardEntry->SetSearchHelpePart('link-link', $LinkEntity->Link);
        $DashboardEntry->Update();
        $this->ResponseSuccess();
    }

}

?>