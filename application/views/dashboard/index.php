<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Dashbird <?php echo Pvik\Core\Config::$Config['Version']?></title>
    <?php $this->Helper->StyleSheetLink('~/css/reset.css'); ?>
	<?php $this->Helper->StyleSheetLink('~/css/dashboard.css'); ?>
</head>
<body>
	<div id="global">
        <div id="board" style="display:none">
            <div id="content">
            </div>
            <div id="content-footer">
                <a href="#" id="further-entries" style="display:none">Next</a>
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
                        <img  src="<?php echo $this->RelativePath('~/images/button-note-small.png') ?>"/>
                    </div>
                    <div class="command-bar-option" data-module="Link">
                        <img  src="<?php echo $this->RelativePath('~/images/button-link-small.png') ?>" />
                    </div>
                    <div class="command-bar-option" data-module="Todo">
                        <img  src="<?php echo $this->RelativePath('~/images/button-todo-done-small.png'); ?>" />
                    </div>
                    <div class="clear-fix" ></div>
                </div>
                
            </div>
            <div id="search">
                    <span>Search:</span><input id="search-box" type="text" name="search" />
            </div>
          
            <div id="user-shares">
                <p>You can share entries with following users:</p>
                <ul id="user-shares-list"></ul>
                <input id="add-user-share-box" type="text" name="add-user-share-box" /><input type="button" id="add-user-share-button" name="add-user-share-button" value="add" />              
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
                <div id="entry-shares-box" style="display:none">
                        You are sharing this entry with:
			<ul id="entry-shares-list">
                        </ul>
                        <a href="#" class="save-button">Save</a>
                        <a href="#" class="cancel-button">Cancel</a>
		</div>
                <div id="entry-comment-box" style="display:none">
                        Your comment:<br/>
                        <textarea id="entry-comment-box-text"></textarea><br/>
                        <a href="#" class="save-button">Save</a>
                        <a href="#" class="cancel-button">Cancel</a>
		</div>
                
	</div>
    <script type="text/javascript">
            var Dashbird = {};
            Dashbird.baseUrl = '<?php echo $this->RelativePath('~/'); ?>';
    </script>
	<?php $this->Helper->JavaScriptLink('~/js/jquery-1.6.1.min.js'); ?>
	<?php $this->Helper->JavaScriptLink('~/js/constants.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
	<?php $this->Helper->JavaScriptLink('~/js/simple-js-lib/single-request-queue.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/simple-js-lib/event-handler.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/auth.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/login-box.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/modules/note.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/modules/link.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/modules/todo.js?version='. \Pvik\Core\Config::$Config['Version']); ?>    
    <?php $this->Helper->JavaScriptLink('~/js/dashboard-entry.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/dashboard.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/shares.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
    <?php $this->Helper->JavaScriptLink('~/js/init.js?version='. \Pvik\Core\Config::$Config['Version']); ?>
</body>
</html>