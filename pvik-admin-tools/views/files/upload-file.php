<?php
$this->UseMasterPage(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . 'views/master-pages/master.php');
$ValidationState = $this->ViewData->Get('ValidationState');
$Uploaded = $this->ViewData->Get('Uploaded');
// set data for the masterpage
$this->ViewData->Set('Title', 'upload file');
?>
<?php $this->StartContent('Head'); ?>
<?php $this->EndContent(); ?>
<?php $this->StartContent('Content'); ?>
<div id="files">
    <h2>upload file</h2>
    <?php if ($Uploaded) { ?>
        <div class="form-message-success">
            file uploaded
        </div>
    <?php } ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="control-group">
            <label class="control-label">name (leave this blank if you don't want to change the name)</label>
            <div class="controls">
                <input class="span8" type="text" name="name" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">folder</label>
            <div class="controls">
                <select class="span8" name="folder">
                    <?php
                    foreach (\Pvik\Core\Config::$Config['PvikAdminTools']['FileFolders'] as $Folder) {
                        ?>
                        <option><?php echo $Folder ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="control-group <?php echo $ValidationState->GetError('File') != null ? 'error' : ''; ?>">
            <label class="control-label">file</label>
            <div class="controls">
                <input class="span8" type="file" name="file" />
                <?php $this->Helper->Errorfield($ValidationState, 'File', 'help-inline'); ?>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" name="submit" class="btn">submit</button>
            </div>
        </div>

    </form>
</div>
<?php $this->EndContent(); ?>