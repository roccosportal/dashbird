<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Dashbird <?php echo Pvik\Core\Config::$Config['Version'] ?></title>
        <?php $this->Helper->StyleSheetLink('~/css/reset.css'); ?>
        <?php $this->Helper->StyleSheetLink('~/css/dashboard-' . Pvik\Core\Config::$Config['Version'] . '.css'); ?>
    </head>
    <body>
        <div id="global">
         
            <div id="side-bar" style="display:none">
                <div class="command new">
                    <img src="/images/aroma/post-it.png" />
                </div>
                <div class="command search">
                    <img src="/images/aroma/search.png" />
                </div>

                <div class="command settings">
                    <img src="/images/aroma/settings.png" />
                </div>
                <div id="logout" class="command logout">
                    <img src="/images/aroma/padlock-closed.png" />
                </div>
            </div>
            <div id="search-bar" style="display:none"> 
                <input id="search-box" type="text" name="search" />
            </div>
            <div id="board" style="display:none">
                <div id="new-entry" style="display:none">
                    <textarea id="new-entry-text"></textarea>
                    <div class="commands">
                        <a href="#" class="save-button">Save</a>
                        <a href="#" class="cancel-button">Cancel</a>
                    </div>
                </div>
                <div id="content">
                </div>
                <div id="content-footer">
                    <a href="#" id="back-entries" style="display:none">Newer posts</a>
                    <a href="#" id="next-entries" style="display:none">Older posts</a>
                </div>
            </div>
           <div id="settings" style="display:none">
                <div id="user-shares">
                   <p>You can share entries with following users:</p>
                   <ul id="user-shares-list"></ul>
                   <input id="add-user-share-box" type="text" name="add-user-share-box" /><input type="button" id="add-user-share-button" name="add-user-share-button" value="add" />              
                </div>
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
        <div id="login-box" style="display:none">
                <fieldset>
                    <span>Name</span><input type="text" id="login-box-name" /><br />
                    <span>Password</span><input type="password" id="login-box-password" />
                </fieldset>
        </div>
        <script type="text/javascript">
            var Dashbird = {};
            Dashbird.baseUrl = '<?php echo $this->RelativePath('~/'); ?>';
        </script>
        <?php $this->Helper->JavaScriptLink('~/js/jquery-1.6.1.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/constants.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/simple-js-lib/single-request-queue.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/simple-js-lib/event-handler.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/auth.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/login-box.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/dashboard-entry.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/dashboard.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/shares.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
        <?php $this->Helper->JavaScriptLink('~/js/init.js?version=' . \Pvik\Core\Config::$Config['Version']); ?>
    </body>
</html>