Dashbird.ActivityFeedLayer =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.postFeedHtmlLayer = null;
	_protected.commentFeedLayerList = [];
	_protected.$layer = null;
	_protected.$updated = null;
	
	// constructor
	// @var parameters (.construct(<Dashbird.PostFeedHtmlLayer>, $jquery))
	_protected.construct = function(parameters){
		_protected.postFeedHtmlLayer = parameters[0];
		_protected.$layer = parameters[1];

		_protected.drawingManager = Dashbird.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['updated', 'lastview']);

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
		var commentFeedLayer = Dashbird.CommentFeedLayer.construct(me, comment);
		var arrayIndex =_protected.commentFeedLayerList.push(commentFeedLayer) - 1;
		commentFeedLayer.attachEvent('/destroying/', _protected.onCommentFeedLayerDestroying, { arrayIndex : arrayIndex });
		_protected.$layer.append(commentFeedLayer.getLayer());
	}

	// --- catch events ---

	_protected.onCommentFeedLayerDestroying = function(commentFeedLayer, additionalData){
		commentFeedLayer.detachEvent('/destroying/', _protected.onCommentFeedLayerDestroying);
		_protected.commentFeedLayerList.splice(additionalData.arrayIndex, 1);
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
		_protected.$lastview.find('.date').text(me.getPost().getLastView().get());
		_protected.$lastview.detach();
		

		if(_protected.commentFeedLayerList.length == 0){
			_protected.$layer.append(_protected.$lastview);
		}
		else {

			for (var i = 0; i < _protected.commentFeedLayerList.length; i++) {
				if(!_protected.commentFeedLayerList[i].isViewed()){
					_protected.commentFeedLayerList[i].getLayer().before(_protected.$lastview);
					return;
				}
			}
			_protected.commentFeedLayerList[_protected.commentFeedLayerList.length - 1].getLayer().after(_protected.$lastview);
		}
	}

	me.redraw = function(){
		var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
		if(drawingChangeSet.updated){
			_protected.drawUpdated();
		}

		// trigger redraw for all sub items
		var commentFeedLayerList = _protected.commentFeedLayerList.slice();
		for (var i = 0; i < commentFeedLayerList.length; i++) {
			commentFeedLayerList[i].redraw();
		};

		if(drawingChangeSet.lastview){
			_protected.drawLastView();
		}

	}

	// --- end --
	return me;
});