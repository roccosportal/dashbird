Dashbird.Models.Comment = SimpleJSLib.EventHandler.inherit(function(me, _protected){

	// observable
	_protected.text = null;

	_protected.datetime = null;

	_protected.user = null;

	_protected.commentId = null;

	_protected.comments = null;

	// constructor
	// @var  parameters (.construct(<{}>, <Dashbird.Model.Comments>))
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

	// --- fire events ---

	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}

	// --- end ---

	// --- getter and setters ---

	// @return SimpleJSLib.Observable
	me.getText = function(){
		return _protected.text;
	}

	me.getDatetime = function(){
		return _protected.datetime;
	}


	me.getUser = function(){
		return _protected.user;
	}


	me.getCommentId = function(){
		return _protected.commentId;
	}

	// @return Dashbird.Model.Post
	me.getPost = function(){
		return _protected.comments.getPost();
	}

	me.isViewed = function(){
		return (me.getDatetime() <= me.getPost().getLastView().get());
	}

	// --- end ---

	// public


	// merges the given data to our data
	// @var commentsData (plain data object)
	me.mergeData = function(commentData){
		// set the data
		// the observable checks if they are new
		me.getText().set(commentData.text);
	}

	me.destroy = function(){
		_protected.fireEventDestroying();
		delete _protected;
		delete me;
	}

	me.isKeywordMatch = function(keyword){
		return (me.getText().get().indexOf(keyword) !== -1)
	}

	return me;
});