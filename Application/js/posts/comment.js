Dashbird.Comment = SimpleJSLib.EventHandler.inherit(function(me, _protected){

	// observable
	_protected.text = null;

	_protected.datetime = null;

	_protected.user = null;

	_protected.commentId = null;

	// constructor
	// @var  parameters (.construct(<{}>))
	// [0] plain comment data 
	_protected.construct = function(parameters){
		var commentData = parameters[0]
		_protected.commentId = parseInt(commentData.commentId);
		_protected.datetime = commentData.datetime;
		_protected.user = commentData.user;
		_protected.text = SimpleJSLib.Observable.construct(commentData.text);
	}

	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}

	// getter and setters
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

	return me;
});