/**
 * This model represents a list of comments for a post.
 *
 */
Dashbird.Models.Comments = SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.comments = null;
	_protected.post = null;
	// constructor
	// @param  parameters (.construct(<array>, <Dasbird.Modeles.Post>))
	// [0] plain comments array
	// [1] the post the comments belong to
	_protected.construct = function(parameters){
		_protected.comments = SimpleJSLib.MappingArray.construct();
		// shorthands
		me.getCommentById = _protected.comments.getByKey;
		me.each = _protected.comments.each;
		me.mergeData(parameters[0]);
		_protected.post = parameters[1];
	}
	// Fires the event '/new/comment/'
	// @param comment <Dashbird.Models.Comment>
	_protected.fireEventNewComment = function(comment){
		me.fireEvent('/new/comment/', comment);
	}
	// This method gets called when a comment is destroying.
	// It removes the given comment from the list.
	// @param comment <Dashbird.Models.Comment>
	_protected.onCommentDestroying = function(comment){
		comment.detachEvent('/destroying/', _protected.onCommentDestroying);
		_protected.comments.remove(comment.getCommentId());
	}
	// Returns the post.
	// @return <Dashbird.Models.Post>
	me.getPost = function(){
		return _protected.post;
	}
	// Merges the given data to our data.
	// @param commentsData <object> (plain data object)
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
	// Returns a comment by the id
	// @param commentId <int>
	// @return <Dashbird.Models.Comment>
	me.getCommentById = null;
	// Allows to itereate the comment array
	// @param callbackIterator <function>
	// @param makeRemovingSafe <boolean> [optional] [default=false]
	me.each = null;
	return me;
});