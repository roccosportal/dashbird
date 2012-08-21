<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<title>Dashboard</title>
    <?php Html::StyleSheetLink('~/css/reset.css'); ?>
	<?php Html::StyleSheetLink('~/css/dashboard.css'); ?>
</head>
<body>
	<div id="global">
        <div id="board" style="display:none">
            <div id="content">
            </div>
        </div>
        <div id="board-bar" style="display:none">
            <div id="command-bar" style="display:none" >
                <div id="command-bar-top" class="closed">
                    <div class="command-form note-module" id="note-module-add-form" style="display:none">
                            <textarea id="note-module-add-form-text"></textarea>
                    </div>
                    <div class="command-form link-module" id="link-module-add-form" style="display:none">
                            <input type="text" id="link-module-add-form-link" /><br />
                            IsImage:<input type="checkbox" id="link-module-add-form-is-image" /><br />
                        </fieldset>
                    </div>
                    <div class="command-form todo-module" id="todo-module-add-form" style="display:none">
                            <textarea id="todo-module-add-form-text"></textarea>
                    </div>
                </div>
                <div id="command-bar-bottom">
                    <div class="command-bar-option" data-module="Note">
                        <img  src="<?php echo Core::RelativePath('~/images/button-note-small.png') ?>"/>
                    </div>
                    <div class="command-bar-option" data-module="Link">
                        <img  src="<?php echo Core::RelativePath('~/images/button-link-small.png') ?>" />
                    </div>
                    <div class="command-bar-option" data-module="Todo">
                        <img  src="<?php echo Core::RelativePath('~/images/button-todo-done-small.png') ?>" />
                    </div>
                    <div class="clear-fix" ></div>
                </div>
                
            </div>
            <div id="search">
                    <span>Search:</span><input id="search-box" type="text" name="search" />
            </div>
            <div>
                    <a id="logout" href="#">Logout</a>
            </div>
            <div class="clear-fix"></div>
        </div>
		<div id="login-box" style="display:none">
			<fieldset>
				Name:<input type="text" id="login-box-name" />
				Password:<input type="password" id="login-box-password" />
			</fieldset>
		</div>
	</div>
    <script type="text/javascript">
            var Dashbird = {};
            Dashbird.baseUrl = '<?php echo Core::RelativePath('~/'); ?>';
    </script>
	<?php Html::JavaScriptLink('~/js/jquery-1.6.1.min.js'); ?>
	<?php Html::JavaScriptLink('~/js/constants.js'); ?>
	<?php Html::JavaScriptLink('~/js/simple-js-lib/single-request-queue.js'); ?>
    <?php Html::JavaScriptLink('~/js/simple-js-lib/event-handler.js'); ?>
    <?php Html::JavaScriptLink('~/js/auth.js'); ?>
    <?php Html::JavaScriptLink('~/js/login-box.js'); ?>
    <?php Html::JavaScriptLink('~/js/modules/note.js'); ?>
    <?php Html::JavaScriptLink('~/js/modules/link.js'); ?>
    <?php Html::JavaScriptLink('~/js/modules/todo.js'); ?>    
    <?php Html::JavaScriptLink('~/js/dashboard-entry.js'); ?>
    <?php Html::JavaScriptLink('~/js/dashboard.js'); ?>
    <?php Html::JavaScriptLink('~/js/init.js'); ?>
</body>
</html>