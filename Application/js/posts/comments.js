Dashbird.Comments = SimpleJSLib.EventHandler.inherit(function(me, _protected){

	_protected.comments = [];

	// contains a mapping like { <commentId> : <indexOfArray>, ... }
	_protected.mappingForComments = {};

	_protected.post = null;

	// constructor
	// @var  parameters (.construct(<[]>, <Dasbird.Posts>))
	// [0] plain comments array
	// [1] the posts the comments belong to
	_protected.construct = function(parameters){
		me.mergeData(parameters[0]);
		_protected.post = parameters[1];
	}


	// --- fire events ---

	// @var comment Dashbird.Comment
	_protected.fireEventNewComment = function(comment){
		me.fireEvent('/new/comment/', comment);
	}

	// --- end ---

	// --- catch events ---

	// @var comment Dashbird.Comment
	_protected.onCommentDestroying = function(comment){
		comment.detachEvent('/destroying/', _protected.onCommentDestroying);
		if(typeof(_protected.mappingForComments[comment.getCommentId()]) != 'undefined'){
			_protected.comments.splice(_protected.mappingForComments[comment.getCommentId()], 1);
			delete _protected.mappingForComments[comment.getCommentId()];
		}
	}
	// --- end ---

	// --- getter and setters ---

	me.getPost = function(){
		return _protected.post;
	}

	// --- end ---


	// merges the given data to our data
	// @var commentsData (plain data object)
	me.mergeData = function(commentsData){
		var indexOfArray = null;
		var comment = null;
		var processedCommentIds = {};
		// merge and add comments
		for (var i = 0; i < commentsData.length; i++) {
			comment = me.getCommentById(commentsData[i].commentId);
			// is not in our array
			if(comment==null){ 
				// add it
				comment = Dashbird.Comment.construct(commentsData[i], me);
				indexOfArray = _protected.comments.push(comment) - 1;
				_protected.mappingForComments[comment.getCommentId()] = indexOfArray;
				comment.attachEvent('/destroying/', _protected.onCommentDestroying);
				_protected.fireEventNewComment(comment);
				
			}
			else {
				// pass merge data to instance
				comment.mergeData(commentsData[i]);
			}
			processedCommentIds[comment.getCommentId()] = true; 
		};

		// if these are different there are some old comments
		if(commentsData.length != _protected.comments.length){
			// delete old ones

			// work with copy because comment.destroy() alters _protected.comments
			var comments = _protected.comments.slice();
			for (var j = 0; j < comments.length; j++) {
				if(typeof(processedCommentIds[comments[j].getCommentId()]) == 'undefined'){
					// was not processed
					// so delete
					comments[j].destroy();
				}
			};
		}

	}


	// @var commentId 
	// @return Dashbird.Comment
	me.getCommentById = function(commentId){
		// search in the mapping object by the comment id
		if(typeof(_protected.mappingForComments[commentId]) != 'undefined')
			return _protected.comments[_protected.mappingForComments[commentId]];
		return null;
	}

	me.each = function(callbackIterator){
		var callbackIteratorReturnValue = null;
		for (var i = 0; i < _protected.comments.length; i++) {
			callbackIteratorReturnValue = callbackIterator(i, _protected.comments[i]);
			if(typeof(callbackIteratorReturnValue) != 'undefined' && callbackIteratorReturnValue === false)
				return null;
		};
		return null;
	}


	return me;
});