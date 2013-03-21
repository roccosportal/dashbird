/**
 * This view model represents the comment for a post as feed.
 *
 */
Dashbird.ViewModels.CommentFeed =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.activityFeedLayer = null;
	_protected.comment = null;
	_protected.$layer = null;
	_protected.drawingManager = null;
	// constructor
	// @param parameters (.construct(<Dashbird.ViewModels.ActivityFeed>, <Dashbird.Models.Comment>))
	// [0] 
	// [1] 
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
	// Gets called when last view was changed.
	_protected.onPostLastViewChanged = function(){
		_protected.drawingManager.queueRedraw(['viewed']);
	}
	// Gets called when the comment model gets destroyed.
	_protected.onCommentDestroying = function(){
		_protected.comment.detachEvent('/destroying/', _protected.onCommentDestroying);
		_protected.drawingManager.queueRedraw(['destroying']);
	}
	// Fires the event /destroying/.
	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}
	// Checks if the view model is allowed to redraw.
	// @return <boolean>
	me.isAllowedToRedraw = function(){
		return _protected.activityFeedLayer.isAllowedToRedraw();
	}
	// Returns the jQuery html layer.
	// @return <$jquery>
	me.getLayer = function(){
		return _protected.$layer;
	}
	// Checks if the comment is viewed.
	// @return <boolean>
	me.isViewed = function(){
		return _protected.comment.isViewed();
	}
	// Returns the comment id.
	// @return <int>
	me.getCommentId = function(){
		return _protected.comment.getCommentId();
	}
	// Draws the viewed status.
	_protected.drawViewed = function(){
		if(_protected.comment.isViewed())
			_protected.$layer.addClass('viewed');
		else
			_protected.$layer.removeClass('viewed');
	}
	// Redraws the view model.
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
	// Undraws/hides the view model
	me.undraw = function(){
		me.getLayer().hide();
	}
	// Destroyes the view model.
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