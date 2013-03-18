Dashbird.ViewModels.ActivityFeed =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.postFeedHtmlLayer = null;
	_protected.commentFeedLayerList = null;
	_protected.$layer = null;
	_protected.$updated = null;
	
	// constructor
	// @var parameters (.construct(<Dashbird.ViewModels.PostFeed>, $jquery))
	_protected.construct = function(parameters){
		_protected.postFeedHtmlLayer = parameters[0];
		_protected.$layer = parameters[1];
		_protected.commentFeedLayerList = SimpleJSLib.MappingArray.construct();

		_protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['updated', 'lastview']);

		_protected.$updated = $('#templates #template-post-feed-update li').clone();
		_protected.drawUpdated();
		_protected.$layer.append(_protected.$updated);

		_protected.$lastview = $('#templates #template-post-feed-viewed li').clone();

		_protected.postFeedHtmlLayer.getPost().getComments().each(function(key, comment){
			_protected.addComment(comment);
		});

		_protected.drawLastView();

		_protected.postFeedHtmlLayer.getPost().getComments().attachEvent('/new/comment/', _protected.onNewComment);
		_protected.postFeedHtmlLayer.getPost().getLastView().listen(_protected.onPostLastViewedChanged);
		_protected.postFeedHtmlLayer.getPost().getUpdated().listen(_protected.onPostUpdatedChanged);
	}

	_protected.addComment = function(comment){
		var commentFeedLayer = Dashbird.ViewModels.CommentFeed.construct(me, comment);
		_protected.commentFeedLayerList.add(commentFeedLayer.getCommentId(), commentFeedLayer);
		commentFeedLayer.attachEvent('/destroying/', _protected.onCommentFeedLayerDestroying);
		_protected.$layer.append(commentFeedLayer.getLayer());
	}

	// --- catch events ---

	_protected.onCommentFeedLayerDestroying = function(commentFeedLayer){
		commentFeedLayer.detachEvent('/destroying/', _protected.onCommentFeedLayerDestroying);
		_protected.commentFeedLayerList.remove(commentFeedLayer.getCommentId());
	}

	_protected.onNewComment = function(comment, additionalData){
		_protected.addComment(comment);
	}

	_protected.onPostLastViewedChanged = function(){
		_protected.drawingManager.queueRedraw(['lastview']);
	}


	_protected.onPostUpdatedChanged = function(){
		_protected.drawingManager.queueRedraw(['updated']);
	}

	// --- end --

	// --- getter and setters ---

	me.getPost = function(){
		return _protected.postFeedHtmlLayer.getPost();
	}

	me.isAllowedToRedraw = function(){
		return _protected.postFeedHtmlLayer.isAllowedToRedraw();
	}

	me.getLayer = function(){
		return _protected.$layer;
	}

	me.isAllowedToRedraw = function(){
		return true;
	}

	// --- end --

	// --- drawing ---

	_protected.drawUpdated = function(){
        _protected.$updated.find('.date').text(me.getPost().getUpdated().get());
        if(!_protected.postFeedHtmlLayer.isViewed())
            _protected.$updated.removeClass('viewed');
        else 
            _protected.$updated.addClass('viewed');
	}


	_protected.drawLastView = function(){
		if(me.getPost().getLastView().get() != null){
			_protected.$lastview.find('.date').text(me.getPost().getLastView().get());
			_protected.$lastview.detach();
			

			if(_protected.commentFeedLayerList.length == 0){
				_protected.$layer.append(_protected.$lastview);
			}
			else {
				 _protected.commentFeedLayerList.each(function(index, commentFeedLayer){
				 	if(!commentFeedLayer.isViewed()){
						commentFeedLayer.getLayer().before(_protected.$lastview);
						return;
					}
				 });
				
				_protected.commentFeedLayerList.getLast().getLayer().after(_protected.$lastview);
			}
		}
		else {
			_protected.$lastview.detach();
		}
	}

	me.redraw = function(){
		var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
		if(drawingChangeSet.updated){
			_protected.drawUpdated();
		}

		// trigger redraw for all sub items
		 _protected.commentFeedLayerList.each(function(index, commentFeedLayer){
			 	commentFeedLayer.redraw();
		 }, true);

		if(drawingChangeSet.lastview){
			_protected.drawLastView();
		}

	}

	// --- end --
	return me;
});