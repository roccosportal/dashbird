/**
 * This model represents a comment.
 *
 */
Dashbird.Models.Comment = SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.text = null;
	_protected.datetime = null;
	_protected.user = null;
	_protected.commentId = null;
	_protected.comments = null;
	// constructor
	// @param  parameters (.construct(<object>, <Dashbird.Models.Comments>))
	// [0] plain comment data
	// [1] the comments container the comment belongs to
	_protected.construct = function(parameters){
		var commentData = parameters[0]
		_protected.comments = parameters[1]
		_protected.commentId = parseInt(commentData.commentId);
		_protected.datetime = commentData.datetime;
		_protected.user = commentData.user;
		_protected.text = SimpleJSLib.Observable.construct(commentData.text);
	}
	// Fires the event /destroying/
	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}
	// Returns the text.
	// @return <SimpleJSLib.Observable>
	me.getText = function(){
		return _protected.text;
	}
	// Returns the datetime.
	// @return <datetime>
	me.getDatetime = function(){
		return _protected.datetime;
	}
	// Returns the user.
	// @return <object>
	me.getUser = function(){
		return _protected.user;
	}
	// Returns the comment id.
	// @retun <int>
	me.getCommentId = function(){
		return _protected.commentId;
	}
	// Returns the post.
	// @return <Dashbird.Model.Post>
	me.getPost = function(){
		return _protected.comments.getPost();
	}
	// Checks if the comment is viewed.
	// @return <boolean>
	me.isViewed = function(){
		return (me.getDatetime() <= me.getPost().getLastView().get());
	}
	// Merges the given data to our data.
	// @param commentsData <object> (plain data object)
	me.mergeData = function(commentData){
		// set the data
		// the observable checks if they are new
		me.getText().set(commentData.text);
	}
	// Destroys this model
	me.destroy = function(){
		_protected.fireEventDestroying();
		delete _protected;
		delete me;
	}
	// Checks if the comment matches a keyword.
	// @param <string>
	// @return <boolean>
	me.isKeywordMatch = function(keyword){
		return (me.getText().get().indexOf(keyword) !== -1)
	}
	return me;
});