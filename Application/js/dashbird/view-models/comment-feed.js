Dashbird.ViewModels.CommentFeed =  SimpleJSLib.EventHandler.inherit(function(me, _protected){

	_protected.activityFeedLayer = null;
	_protected.comment = null;
	_protected.$layer = null;
	_protected.drawingManager = null;

	// constructor
	// @var parameters (.construct(<Dashbird.ViewModels.ActivityFeed>, <Dashbird.Models.Comment>))
	_protected.construct = function(parameters){
		_protected.activityFeedLayer = parameters[0];
		_protected.comment = parameters[1];
		_protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['viewed', 'destroying']);
		_protected.$layer = $('#templates #template-post-feed-comment li').clone();
        _protected.$layer.find('.username').text(_protected.comment.getUser().name);
        _protected.$layer.find('.date').text(_protected.comment.getDatetime());
        _protected.drawViewed();
        _protected.comment.getPost().getLastView().listen(_protected.onPostLastViewChanged);
        _protected.comment.attachEvent('/destroying/', _protected.onCommentDestroying);
	}
	// --- catch events --- 

	_protected.onPostLastViewChanged = function(){
		_protected.drawingManager.queueRedraw(['viewed']);
	}

	_protected.onCommentDestroying = function(){
		_protected.comment.detachEvent('/destroying/', _protected.onCommentDestroying);
		_protected.drawingManager.queueRedraw(['destroying']);
	}

	// --- end ---

	// --- fire events ---

	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}

	// --- end ---

	// --- getter and setters --- 

	me.isAllowedToRedraw = function(){
		return _protected.activityFeedLayer.isAllowedToRedraw();
	}

	me.getLayer = function(){
		return _protected.$layer;
	}

	me.isViewed = function(){
		return _protected.comment.isViewed();
	}

	me.getCommentId = function(){
		return _protected.comment.getCommentId();
	}

	// --- end ---

	// --- drawing --- 

	_protected.drawViewed = function(){
		if(_protected.comment.isViewed())
			_protected.$layer.addClass('viewed');
		else
			_protected.$layer.removeClass('viewed');
	}

	me.redraw = function(){
		var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
		if(drawingChangeSet.destroying){
			me.undraw();
		}
		else {
			if(drawingChangeSet.viewed){
				_protected.drawViewed();
			}
			_protected.drawingManager.setDrawingChangeSetToDefault();
		}
		
	}

	me.undraw = function(){
		me.getLayer().hide();
	}

	// --- end ---


	me.destroy = function(){
		_protected.fireEventDestroying();
		_protected.comment.getPost().getLastView().listen(_protected.onPostLastViewChanged);
      	_protected.comment.detachEvent('/destroying/', _protected.onCommentDestroying);
      	me.getPost().getLastView().unlisten( _protected.onLastViewChange);

		_protected.$layer.hide();
		delete _proteced;
		delete me;
	}



	return me;
});