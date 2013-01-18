<?php
$this->UseMasterPage(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] .'views/master-pages/master.php');
// set data for the masterpage
$this->ViewData->Set('Title', 'tables');
?>
<?php $this->StartContent('Head'); ?>
<?php $this->EndContent(); ?>
<?php $this->StartContent('Content'); ?>
<div id="tables">
        <h2>tables</h2>
        <ul>
            <?php 
            foreach(\Pvik\Core\Config::$Config['PvikAdminTools']['Tables'] as $TableName => $Table){ 
            ?>
            <li>
                <span class="option-name"><?php $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($TableName) . ':list/', $TableName); ?>
                </span>
                <span class="option-links">
                    [<?php $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($TableName) . ':list/', 'list'); ?>|<?php  $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($TableName) . ':new/', 'new'); ?>]
                </span>
            </li>
            <?php
            }
            ?>
        </ul>
</div>
<?php $this->EndContent(); ?>