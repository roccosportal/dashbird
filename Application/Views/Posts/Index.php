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
                        <li><a class="show-new-post" href="#new-post" data-toggle="tab"><i class="icon-plus"></i> New</a></a></li>
                        <li class="active"><a class="show-board" href="#board" data-toggle="tab"><i class="icon-home"></i> Board</a></li>
                        <li><a href="/settings" ><i class="icon-wrench"></i> Settings</a></a></li>
                    </ul>
                    <form class="navbar-search pull-left">
                        <input type="text" id="search-box" class="search-query" placeholder="Search">
                    </form>
                    <ul class="nav">
                        <li><a href="/logout"><i class="icon-off"></i> Logout</a></a></li>
                    </ul>
                </div>
            </div>
            <div id="content" class="tabbable">
                <div class="tab-content">
                    <div id="new-post"  class="tab-pane">
                            <form>
                                <fieldset>
                                    <label><strong>New post</strong></label>
                                    <div class="command-bar btn-group">
                                        <a href="" class="command-link btn btn-mini"><i class="icon-globe"></i></a>
                                        <a href="" class="command-video btn btn-mini"><i class="icon-facetime-video"></i></a>
                                        <a href="" class="command-image  btn btn-mini"><i class="icon-camera"></i></a>
                                        <a href="" class="command-bold  btn btn-mini"><i class="icon-bold"></i></a>
                                    </div>
                                    <textarea class="input-block-level" placeholder="Type here"></textarea>
                                    <ul class="tags inline">
                                    </ul>
                                    <div class="tag-alert"></div>
                                    <div class="add-tag-input input-append">
                                        <input class="input-block-level" type="text" placeholder="Add a tag">
                                        <button class="btn" type="button">Add</button>
                                    </div>
                                    <label><strong>Share with people</strong></label>
                                    <div class="shares"></div>
                                    <div class="form-actions" >
                                        <button type="submit" class="submit-button btn btn-primary">Add</button>
                                        <button type="button" class="cancel-button btn">Cancel</button>
                                    </div>
                                </fieldset>
                            </form>
   
                    </div>
                    <div id="stack" class="tab-pane active">
                        <div class="posts">
                        </div>
                        <div class="loading" style="display:none">
                            <button class="btn btn-large disabled btn-block"><img src="/images/ajax-loader.gif" /></button>
                        </div>
                        <div class="more-posts" style="display:none">
                            <button class="btn btn-primary btn-large btn-block" type="button">Load more posts</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="headline"></h3>
            </div>
            <div class="modal-body">
                <p class="text"></p>
            </div>
            <div class="modal-footer">
                <a href="#" class="cancel-button btn">Cancel</a>
                <a href="#" class="submit-button btn btn-primary">Submit</a>
            </div>
        </div>
        <div id="templates" style="display:none">
            <div id="template-post">
                <div class="post">
                    <div class="content">
                        <div class="command-bar popup btn-group">
                            <a class="command-comment btn btn-mini"><i class="icon-comment"></i> </a>
                            <a class="command-edit btn btn-mini"><i class="icon-pencil"></i> </a>
                            <a class="command-share btn btn-mini"><i class="icon-retweet"></i> </a>
                            <a class="command-remove btn btn-mini"><i class="icon-trash"></i> </a>
                        </div>
                        <p class="text"></p>
                        <ul class="meta inline">
                            <li class="info"><i class="icon-user"></i> <span class="username"></span> on <span class="date"></span></li>
                            <li class="tags"><ul class="inline"></ul></li>
                            <li class="private-sharing"><i class="icon-lock"></i> private</li>
                            <li class="sharing"><i class="icon-retweet"></i><a data-placement="bottom" href="#" rel="tooltip" title="" > <span class="count"></span> <span class="persons"></span></a> </li>
                        </ul>
                        <div class="command command-comment">
                            <form>
                                <fieldset>
                                    <label><strong>Add a new comment</strong></label>
                                    <textarea class="input-block-level" placeholder="Type here"></textarea>
                                    <div class="form-actions" >
                                        <button type="submit" class="submit-button btn btn-primary">Add</button>
                                        <button type="button" class="cancel-button btn">Cancel</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="command command-edit">
                            <form>
                                <fieldset>
                                    <label><strong>Edit your post</strong></label>
                                    <div class="command-bar btn-group">
                                        <a href="" class="command-link btn btn-mini"><i class="icon-globe"></i></a>
                                        <a href="" class="command-video btn btn-mini"><i class="icon-facetime-video"></i></a>
                                        <a href="" class="command-image  btn btn-mini"><i class="icon-camera"></i></a>
                                        <a href="" class="command-bold  btn btn-mini"><i class="icon-bold"></i></a>
                                    </div>
                                    <textarea class="input-block-level" placeholder="Type here"></textarea>
                                    <ul class="tags inline">
                                    </ul>
                                    <div class="tag-alert"></div>
                                    <div class="add-tag-input input-append">
                                        <input class="input-block-level" type="text" placeholder="Add a tag">
                                        <button class="btn" type="button">Add</button>
                                    </div>
                                    <div class="form-actions" >
                                        <button type="submit" class="submit-button btn btn-primary">Save changes</button>
                                        <button type="button" class="cancel-button btn">Cancel</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="command command-share">
                            <form>
                                <fieldset>
                                    <label><strong>Share with people</strong></label>
                                    <div class="shares"></div>
                                    <div class="form-actions" >
                                        <button type="submit" class="submit-button btn btn-primary">Share</button>
                                        <button type="button" class="cancel-button btn">Cancel</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                    <div class="comments"></div>
                </div>
            </div>
            <div id="template-post-comment">
                <div class="comment">
                    <div class="command-bar popup">
                        <a class="command-delete btn btn-mini"><i class="icon-trash"></i> </a>
                    </div>
                    <p class="text">
                    </p>
                    <ul class="meta inline">
                        <li class="info"><i class="icon-user"></i> <span class="username"></span> on <span class="date"></span></li>
                    </ul>
                </div>
            </div>
            <div id="template-tag">
                <ul>
                    <li class="tag"><i class="icon-tag"></i> <span></span></li>
                </ul>
            </div>
            <div id="template-user">
                <ul>
                    <li><i class="icon-user"></i> <span></span></li>
                </ul>
            </div>
            <div id="template-tag-editable">
                <ul>
                    <li class="tag"><i class="icon-tag"></i> <span></span><button type="button" class="close delete-button" data-dismiss="modal" aria-hidden="true">&times;</button></li>
                </ul>
            </div>
            <div id="template-tag-alert">
                <div class="alert fade in">
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    <strong>Ooops!</strong>
                    This tag is already in the list.
                </div>
            </div>
            <div id="template-share-editable">
                <label class="checkbox inline"><input type="checkbox" value=""><span></span></label>
            </div>
            <div id="template-notification">
                <div class="notification">
                    <div class="command-bar popup">
                        <a class="command-mark-as-read btn btn-mini" title="mark as read"><i class="icon-eye-open"></i> </a>
                    </div>
                    <p class="text">
                    </p>
                    <a class="read" title="read" href="">[...]</a>
                    <ul class="meta inline">
                        <li class="info"><i class="icon-user"></i> <span class="username"></span> on <span class="date"></span></li>
                        <li class="comments"><i class="icon-comment"></i> <span></span></li>
                    </ul>
                </div>
            </div>
            <div id="template-bbcode-link-modal">
                 <div class="modal-body">
                    <input class="link input-block-level" type="text" placeholder="Place link in here..." />
                 </div>
            </div>
            <div id="template-bbcode-image-modal">
                 <div class="modal-body">
                    <input class="image input-block-level" type="text" placeholder="Place image link in here..." />
                 </div>
            </div>
            <div id="template-bbcode-video-modal-alert">
                <div class="alert alert-block alert-error fade in">
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    <strong>Ooops!</strong>
                    Only youtube and vimeo videos are supported.
                </div>
            </div>
            <div id="template-bbcode-video-modal">
                 <div class="modal-body">
                     <div class="alerts"></div>
                    <input class="video input-block-level" type="text" placeholder="Place video link in here..." />
                 </div>
            </div>
        </div>
        <script type="text/javascript">
            var Dashbird = {};
            Dashbird.InitialData = {};
            Dashbird.InitialData.User = <?php echo $this->ViewData->Get('UserData'); ?>;
            Dashbird.InitialData.LoadedAt = '<?php echo date('Y-m-d H:i:s'); ?>';
        </script>
	<?php $this->Helper->JavaScriptLink('~/js/jquery-1.8.3.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/bootstrap.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/jquery-fieldselection.min.js'); ?>
        <?php $this->Helper->JavaScriptLink('~/js/dashbird-developer-' .  Pvik\Core\Config::$Config['Version'] .'.js'); ?>
    </body>
</html>
