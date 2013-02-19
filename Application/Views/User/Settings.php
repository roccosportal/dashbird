<!DOCTYPE html>
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
                    <ul class="nav">
                        <li><a href="/"><i class="icon-home"></i> Board</a></li>
                        <li class="active"><a  href="/settings" ><i class="icon-wrench"></i> Settings</a></a></li>
                    </ul>
                    <ul class="nav">
                        <li><a href="/logout"><i class="icon-off"></i> Logout</a></a></li>
                    </ul>
                </div>
            </div>
            <div id="content">

                    <div id="settings"  class="tab-pane" >
                        <div class="tabbable">
                            <ul class="nav nav-list pull-left">
                                <li class="active"><a href="#settings-persons" data-toggle="tab"><i class="icon-chevron-right"></i> Persons</a></li>
                                <li><a href="#settings-password" data-toggle="tab"><i class="icon-chevron-right"></i> Password</a></li>
                            </ul>
                            <div class="content tab-content">
                                <div id="settings-persons"  class="tab-pane active">
                                        <div class="add-person-input input-append">
                                            <input class="" type="text" placeholder="Add a person">
                                            <button class="btn" type="button">Add</button>
                                        </div>
                                        <ul class="unstyled">
                                        </ul>
                                </div>
                                <div id="settings-password"  class="tab-pane">
                                     <form>
                                <fieldset>
                                    <label><strong>Change password</strong></label>
                                    <label>Old password</label>
                                    <input type="password" class="old-password input-block-level" placeholder="Password" />
                                    <label>New password</label>
                                    <input type="password" class="new-password input-block-level" placeholder="Password" />
                                    <div class="alerts"></div>
                                    <div class="form-actions" >
                                        <button type="submit" class="submit-button btn btn-primary">Change password</button>
                                        <button type="button" class="cancel-button btn">Cancel</button>
                                    </div>
                                    </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div id="notifications"  class="tab-pane" >
                        <div class="loading" style="display:none">
                            <button class="btn btn-large disabled btn-block"><img src="/images/ajax-loader.gif" /></button>
                        </div>
                        <div class="content">
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div id="templates" style="display:none">
            <div id="template-user">
                <ul>
                    <li><i class="icon-user"></i> <span></span></li>
                </ul>
            </div>
            <div id="template-settings-password-alert-success">
                <div class="alert alert-block alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Password changed</h4>
                    We changed your password. Do not forget it!
                </div>
            </div>
            <div id="template-settings-password-alert-error">
                <div class="alert alert-block alert-error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Password not changed</h4>
                    We could not change your password.
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var Dashbird = {};
            Dashbird.InitialData = {};
            Dashbird.InitialData.User = <?php echo $this->ViewData->Get('UserData'); ?>;
        </script>
	<?php $this->Helper->JavaScriptLink('~/js/jquery-1.8.3.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/bootstrap.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/dashbird-developer-settings-' .  Pvik\Core\Config::$Config['Version'] .'.js'); ?>
    </body>
</html>
