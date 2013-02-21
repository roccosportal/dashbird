Dashbird.CommentsLayer =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.postHtmlLayer = null;
	_protected.commentLayerList = [];
	_protected.$layer = null;

	// constructor
	// @var parameters (.construct(<Dashbird.PostHtmlLayer>, $jquery))
	_protected.construct = function(parameters){
		_protected.postHtmlLayer = parameters[0];
		_protected.$layer = parameters[1];
		var commentLayer = null;
		_protected.postHtmlLayer.getPost().getComments().each(function(key, comment){
			_protected.addComment(comment);
		});

		_protected.postHtmlLayer.getPost().getComments().attachEvent('/new/comment/', _protected.onNewComment);
	}

	_protected.addComment = function(comment){
		var commentLayer = Dashbird.CommentLayer.construct(me, comment);
		var arrayIndex =_protected.commentLayerList.push(commentLayer) - 1;
		commentLayer.attachEvent('/destroying/', _protected.onCommentLayerDestroying, { arrayIndex : arrayIndex });
		_protected.$layer.append(commentLayer.getLayer());
	}

	// --- catch events ---

	_protected.onCommentLayerDestroying = function(commentLayer, additionalData){
		commentLayer.detachEvent('/destroying/', _protected.onCommentLayerDestroying);
		_protected.commentLayerList.splice(additionalData.arrayIndex, 1);
	}

	_protected.onNewComment = function(comment, additionalData){
		_protected.addComment(comment);
	}

	// --- end --

	// --- public ---

	me.getPost = function(){
		return _protected.postHtmlLayer.getPost();
	}

	me.isAllowedToRedraw = function(){
		return _protected.postHtmlLayer.isAllowedToRedraw();
	}

	me.getLayer = function(){
		return _protected.$layer;
	}

	me.redraw = function(){
		// trigger redraw for all sub items
		for (var i = 0; i < _protected.commentLayerList.length; i++) {
			_protected.commentLayerList[i].redraw();
		};
	}

	// --- end --

	return me;
});