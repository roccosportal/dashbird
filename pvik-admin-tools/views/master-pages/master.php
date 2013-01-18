<!DOCTYPE html>
<html>
        <head>
                <?php $this->Helper->StyleSheetLink(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . 'css/bootstrap.min.css'); ?>
                <?php $this->Helper->JavaScriptLink('http://code.jquery.com/jquery-1.8.3.min.js'); ?>
                <?php $this->Helper->JavaScriptLink(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . 'js/bootstrap.min.js'); ?>
                <title>PvikAdminTools - <?php echo $this->ViewData->Get('Title'); ?></title>
                <?php $this->UseContent('Head'); ?>
        </head>
	<body>
		<div class="container-fluid">
                     <div class="row-fluid">
                        <div class="span3"> 
                            <ul class="nav nav-list">
                                <li class="nav-header">account</li>
                                <li><?php $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] .  'logout/', '[logout]'); ?></li>
                                <li class="nav-header">tables</li>
                                <li><?php $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/', '[list]'); ?></li>
                                <?php 
                                foreach(\Pvik\Core\Config::$Config['PvikAdminTools']['Tables'] as $TableName => $Table){ 
                                ?>
                                 <li><?php $this->Helper->Link('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' .strtolower($TableName) . ':list/', $TableName); ?> </li>
                                 <?php
                                  }
                                 ?>
                                <li class="nav-header">files</li>
                                <li><?php $this->Helper->Link('~' .  \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'files/upload/', '[upload]'); ?></li>
                            </ul>
                        </div>
                        <div class="span8"><?php $this->UseContent('Content'); ?></div>
                    </div>
		</div>
	</body>
</html>
