/**
 * This view model represents the activity for a post as feed.
 *
 */
Dashbird.ViewModels.ActivityFeed =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.viewModelPostFeed = null;
	_protected.viewModelCommentFeedList = null;
	_protected.$layer = null;
	_protected.$updated = null;
	// constructor
	// @param parameters (.construct(<Dashbird.ViewModels.PostFeed>, $jquery))
	// [0] the post feed view model
	// [1] the jQuery html object
	_protected.construct = function(parameters){
		_protected.viewModelPostFeed = parameters[0];
		_protected.$layer = parameters[1];
		_protected.viewModelCommentFeedList = SimpleJSLib.MappingArray.construct();
		_protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['updated', 'lastview']);
		_protected.$updated = $('#templates #template-post-feed-update li').clone();
		_protected.drawUpdated();
		_protected.$layer.append(_protected.$updated);
		_protected.$lastview = $('#templates #template-post-feed-viewed li').clone();
		_protected.viewModelPostFeed.getPost().getComments().each(function(key, comment){
			_protected.addComment(comment);
		});
		_protected.drawLastView();
		_protected.viewModelPostFeed.getPost().getComments().attachEvent('/new/comment/', _protected.onNewComment);
		_protected.viewModelPostFeed.getPost().getLastView().listen(_protected.onPostLastViewedChanged);
		_protected.viewModelPostFeed.getPost().getUpdated().listen(_protected.onPostUpdatedChanged);
	}
	// Adds a comment to the activity feed.
	// @param comment <Dashbird.Models.Comment>
	_protected.addComment = function(comment){
		var viewModelCommentFeed = Dashbird.ViewModels.CommentFeed.construct(me, comment);
		_protected.viewModelCommentFeedList.add(viewModelCommentFeed.getCommentId(), viewModelCommentFeed);
		viewModelCommentFeed.attachEvent('/destroying/', _protected.onViewModelCommentFeedDestroying);
		_protected.$layer.append(viewModelCommentFeed.getLayer());
	}
	// Gets called when a view model comment feed gets destoyed.
	// @param viewModelCommentFeed <Dashbird.ViewModels.CommentFeed>
	_protected.onViewModelCommentFeedDestroying = function(viewModelCommentFeed){
		viewModelCommentFeed.detachEvent('/destroying/', _protected.onViewModelCommentFeedDestroying);
		_protected.viewModelCommentFeedList.remove(viewModelCommentFeed.getCommentId());
	}
	// Gets called when a new comment was added to the post.
	// @param comment <Dashbird.Models.Comment>
	// @param additionalData <object>
	_protected.onNewComment = function(comment, additionalData){
		_protected.addComment(comment);
	}
	// Gets called when last view of post changed.
	_protected.onPostLastViewedChanged = function(){
		_protected.drawingManager.queueRedraw(['lastview']);
	}
	// Gets called when updated of post changed.
	_protected.onPostUpdatedChanged = function(){
		_protected.drawingManager.queueRedraw(['updated']);
	}
	// Returns the post.
	// @return <Dashbird.Models.Post>
	me.getPost = function(){
		return _protected.viewModelPostFeed.getPost();
	}
	// Returns the jQuery html layer.
	// @return <$jquery>
	me.getLayer = function(){
		return _protected.$layer;
	}
	// Checks if view model is allowed to redraw.
	// @return <boolean>
	me.isAllowedToRedraw = function(){
		return true;
	}
	// Draws the updated field.
	_protected.drawUpdated = function(){
        _protected.$updated.find('.date').text(me.getPost().getUpdated().get());
        if(!_protected.viewModelPostFeed.isViewed())
            _protected.$updated.removeClass('viewed');
        else 
            _protected.$updated.addClass('viewed');
	}
	// Draws the last view field.
	_protected.drawLastView = function(){
		if(me.getPost().getLastView().get() != null){
			_protected.$lastview.find('.date').text(me.getPost().getLastView().get());
			_protected.$lastview.detach();
			if(_protected.viewModelCommentFeedList.length == 0){
				_protected.$layer.append(_protected.$lastview);
			}
			else {
				 _protected.viewModelCommentFeedList.each(function(index, viewModelCommentFeed){
				 	if(!viewModelCommentFeed.isViewed()){
						viewModelCommentFeed.getLayer().before(_protected.$lastview);
						return;
					}
				 });
				_protected.viewModelCommentFeedList.getLast().getLayer().after(_protected.$lastview);
			}
		}
		else {
			_protected.$lastview.detach();
		}
	}
	// Redraws the layer.
	me.redraw = function(){
		var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
		if(drawingChangeSet.updated){
			_protected.drawUpdated();
		}
		// trigger redraw for all sub items
		 _protected.viewModelCommentFeedList.each(function(index, viewModelCommentFeed){
			 	viewModelCommentFeed.redraw();
		 }, true);
		if(drawingChangeSet.lastview){
			_protected.drawLastView();
		}
	}
	return me;
});