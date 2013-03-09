Dashbird.ViewModels.Comments =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.postHtmlLayer = null;
	_protected.commentLayerList = null;
	_protected.$layer = null;
	_protected.$showMoreComments = null;
	_protected.$hideSomeComments = null;

	_protected.showAllComments = false;

	_protected.ALWAYS_VISIBLE_COMMENT_COUNT = 3;

	// constructor
	// @var parameters (.construct(<Dashbird.ViewModels.Post>, $jquery))
	_protected.construct = function(parameters){
		_protected.postHtmlLayer = parameters[0];
		_protected.$layer = parameters[1];

		_protected.commentLayerList = SimpleJSLib.MappingArray.construct();

		_protected.$showMoreComments =_protected.postHtmlLayer.getLayer().find('.show-more-comments');
		_protected.$hideSomeComments =_protected.postHtmlLayer.getLayer().find('.hide-some-comments');

		_protected.postHtmlLayer.getPost().getComments().each(function(key, comment){
			_protected.addComment(comment);
		});
		_protected.postHtmlLayer.getPost().getComments().attachEvent('/new/comment/', _protected.onNewComment);

		_protected.$showMoreComments.click(_protected.onShowMoreCommentsClick);
		_protected.$hideSomeComments.click(_protected.onHideSomeCommentsClick);
	}

	_protected.addComment = function(comment){
		var commentLayer = Dashbird.ViewModels.Comment.construct(me, comment);
		_protected.commentLayerList.add(commentLayer.getCommentId(), commentLayer);
		commentLayer.attachEvent('/destroying/', _protected.onCommentLayerDestroying);
		_protected.$layer.append(commentLayer.getLayer());
	}

	// --- catch events ---

	_protected.onCommentLayerDestroying = function(commentLayer){
		commentLayer.detachEvent('/destroying/', _protected.onCommentLayerDestroying);
		_protected.commentLayerList.remove(commentLayer.getCommentId());
		me.hideUneccessaryComments();
	}

	_protected.onNewComment = function(comment){
		// wait a little bit
		// because the model is sending us it has a new comment which of course is unviewed
		// when the user added this comment by himself, lastView gets updated
		// this should prevent flickering from unseen to seen
		setTimeout(function(){
			_protected.addComment(comment);
			me.hideUneccessaryComments();
		}, 50);
	}

	_protected.onShowMoreCommentsClick = function(){
		_protected.showAllComments = true;

		_protected.commentLayerList.each(function(index, commentLayer){
			commentLayer.getLayer().show();
		});
		me.drawHideSomeComments();
	}

	_protected.onHideSomeCommentsClick = function(){
		_protected.showAllComments = false;
		me.hideUneccessaryComments();
	}

	// --- end --

	// --- getter and setters ---

	me.getPost = function(){
		return _protected.postHtmlLayer.getPost();
	}

	me.isAllowedToRedraw = function(){
		return _protected.postHtmlLayer.isAllowedToRedraw();
	}

	me.getLayer = function(){
		return _protected.$layer;
	}

	// --- end --

	// --- drawing ---



	me.redraw = function(){
		me.hideUneccessaryComments();

		// trigger redraw for all sub items
		_protected.commentLayerList.each(function(index, commentLayer){
			commentLayer.redraw();
		});
	}

	me.drawShowMoreComments = function(countOfHiddenComments){
		_protected.$showMoreComments.find('.count').text(countOfHiddenComments);
		if(countOfHiddenComments == 1){
			_protected.$showMoreComments.find('.multiple').hide();
		}
		else {
			_protected.$showMoreComments.find('.multiple').show();
		}
		_protected.$hideSomeComments.hide();
		_protected.$showMoreComments.show();
	}

	me.drawHideSomeComments = function(){
		_protected.$hideSomeComments.show();
		_protected.$showMoreComments.hide();
	}


	me.hideUneccessaryComments = function(){
		if(!_protected.showAllComments){
			var startShowingIndex = (_protected.commentLayerList.length - _protected.ALWAYS_VISIBLE_COMMENT_COUNT) - 1;
			var countOfHiddenComments = 0;
			_protected.commentLayerList.each(function(index, commentLayer){
				if(index <= startShowingIndex && commentLayer.isViewed()){
					commentLayer.getLayer().hide();
					countOfHiddenComments++;
				}
				else {
					commentLayer.getLayer().show();
				}
			});
			

			if(countOfHiddenComments > 0){
				me.drawShowMoreComments(countOfHiddenComments);
			}
			else {
				_protected.$hideSomeComments.hide();
				_protected.$showMoreComments.hide();
			}
		}
	}

	// --- end --

	return me;
});