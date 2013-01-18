<!DOCTYPE html>
<html>
    <head>
        <title>PvikAdminTools - login</title>
        <?php $this->Helper->StyleSheetLink(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . 'css/bootstrap.min.css'); ?>
        <?php $this->Helper->JavaScriptLink('http://code.jquery.com/jquery-1.8.3.min.js'); ?>
        <?php $this->Helper->JavaScriptLink(\Pvik\Core\Config::$Config['PvikAdminTools']['BasePath'] . 'js/bootstrap.min.js'); ?>
    </head>
    <body>
        <div class="container" style="margin: 40px">
            
            <form class="form-horizontal" method="POST">
                <div class="control-group  <?php echo ($this->ViewData->ContainsKey('Error')) ? 'error' : ''; ?>">
                    <label class="control-label" for="username">Username</label>
                    <div class="controls">
                        <input type="text" id="username" name="username" placeholder="Username">
                    </div>
                </div>
                <div class="control-group <?php echo ($this->ViewData->ContainsKey('Error')) ? 'error' : ''; ?>">
                    <label class="control-label" for="password">Password</label>
                    <div class="controls">
                        <input type="password" id="password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" name="login" class="btn">Sign in</button>
                    </div>
                </div>
            </form>

        </div>
    </body>
</html>