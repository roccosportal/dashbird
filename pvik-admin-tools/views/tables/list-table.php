<?php
$this->UseMasterPage(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] .'views/master-pages/master.php');
// set data for the masterpage
$this->ViewData->Set('Title', 'tables');
$TableHtml = $this->ViewData->Get('TableHtml');
?>
<?php $this->StartContent('Head'); ?>
<?php $this->EndContent(); ?>
<?php $this->StartContent('Content'); ?>
<div id="tables">
        <h2>tables</h2>
        <?php 
            echo $TableHtml->ToHtml();
        ?>
</div>
<?php $this->EndContent(); ?>