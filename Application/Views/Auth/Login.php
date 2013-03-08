<?php
/* @var $ValidationState \Pvik\Utils\ValidationState */
$ValidationState = $this->ViewData->Get('ValidationState');
$Username = $this->ViewData->Get('Username'); 
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Dashbird <?php echo Pvik\Core\Config::$Config['Version'] ?></title>
        <?php $this->Helper->StyleSheetLink('~/css/reset.css'); ?>
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <?php $this->Helper->StyleSheetLink('~/css/dashbird-' . Pvik\Core\Config::$Config['Version'] . '.css'); ?>
    </head>
    <body>
        <div id="global" >
            <div id="navbar" class="navbar navbar-fixed-top navbar-inverse">
                <div class="navbar-inner">
                    <a class="brand" href="/">Dashbird</a>
                </div>
            </div>
        <div id="login-box">
            <form class="form-horizontal" method="post">
                <div class="control-group <?php echo $ValidationState->IsValid() ?  '' : 'error' ?>">
                    <label class="control-label" for="login-box-name">Username</label>
                    <div class="controls">
                        <input type="text" id="login-box-name" name="username" placeholder="Username" value="<?php echo $Username ?>" autofocus>
                    </div>
                </div>
                <div class="control-group <?php echo $ValidationState->IsValid() ? '' : 'error' ?>">
                    <label class="control-label" for="login-box-password">Password</label>
                    <div class="controls">
                        <input type="password" id="login-box-password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn">Sign in</button>
                    </div>
                </div>
            </form>
        </div>
	<?php $this->Helper->JavaScriptLink('~/js/jquery-1.8.3.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/bootstrap.min.js'); ?>
        
    </body>
</html>
