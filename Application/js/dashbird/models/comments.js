Dashbird.Models.Comments = SimpleJSLib.EventHandler.inherit(function(me, _protected){

	_protected.comments = null;

	// // contains a mapping like { <commentId> : <indexOfArray>, ... }
	// _protected.mappingForComments = {};

	_protected.post = null;

	// constructor
	// @var  parameters (.construct(<[]>, <Dasbird.Posts>))
	// [0] plain comments array
	// [1] the posts the comments belong to
	_protected.construct = function(parameters){
		_protected.comments = SimpleJSLib.MappingArray.construct();

		// shorthands
		me.getCommentById = _protected.comments.getByKey;
		me.each = _protected.comments.each;


		me.mergeData(parameters[0]);
		_protected.post = parameters[1];
	}


	// --- fire events ---

	// @var comment Dashbird.Models.Comment
	_protected.fireEventNewComment = function(comment){
		me.fireEvent('/new/comment/', comment);
	}

	// --- end ---

	// --- catch events ---

	// @var comment Dashbird.Models.Comment
	_protected.onCommentDestroying = function(comment){
		comment.detachEvent('/destroying/', _protected.onCommentDestroying);
		_protected.comments.remove(comment.getCommentId());
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
			comment = me.getCommentById(parseInt(commentsData[i].commentId));
			// is not in our array
			if(comment==null){ 
				// add it
				comment = Dashbird.Models.Comment.construct(commentsData[i], me);
				_protected.comments.add(comment.getCommentId(), comment);
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
			_protected.comments.each(function(index, comment){
				if(typeof(processedCommentIds[comment.getCommentId()]) == 'undefined'){
					// was not processed
					// so delete
					comment.destroy();
				}
			}, true);
		}

	}

	// will be overwritten in constructor
	// @var commentId 
	// @return Dashbird.Models.Comment
	me.getCommentById = null;

	// will be overwritten in constructor
	// @var commentId 
	// @return Dashbird.Models.Comment
	me.each = null;

	


	return me;
});