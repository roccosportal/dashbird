/**
 * This view model represents the comment for a post.
 *
 */
Dashbird.ViewModels.Comment =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	_protected.commentsViewModel = null;
	_protected.commentModel = null;
	_protected.$layer = null;
	_protected.drawingManager = null;
	// constructor
	// @param parameters (.construct(<Dashbird.ViewModel.Comments>, <Dashbird.Models.Comment>))
	_protected.construct = function(parameters){
		_protected.commentsViewModel = parameters[0];
		_protected.commentModel = parameters[1];
		_protected.$layer = Dashbird.Views.Utils.Templates.getTemplate('post-comment');
		_protected.$layer.find('img.media-object').attr('src',  Dashbird.Controllers.User.getGravatarUrlForUser(_protected.commentModel.getUser().name, 32));
		// initialize drawings
		_protected.drawViewed();
		_protected.drawText();
		_protected.drawUsername();
		_protected.drawCreated();
		if(Dashbird.Controllers.User.isCurrentUser(_protected.commentModel.getUser().userId)){
            // show options
            _protected.$layer.mouseover(function(){
                _protected.$layer.find('.command-bar.popup').show();
            });
            _protected.$layer.mouseleave(function (){
                _protected.$layer.find('.command-bar.popup').hide();
            });
            // delete comment button
            _protected.$layer.find('.command-bar.popup .command-delete').click(function(){
                Dashbird.Controllers.Post.setLastView(me.getPost());
                Dashbird.Views.Utils.Modal.show({
                    headline: 'Deleting comment', 
                    text : 'Do you really want to delete this comment?',
                    'cancel-button-text' : 'No, no, I am sorry', 
                    'submit-button-text' : 'Remove the rubish!', 
                    callback : function(){
                    	Dashbird.Controllers.Post.deleteComment(me.getPost(), _protected.commentModel.getCommentId());
                    }
                })
            });
        }
      	// add drawing manager
      	_protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['text', 'destroying', 'viewed']);
      	// catch events
      	_protected.commentModel.getText().listen(_protected.onTextChange);
      	_protected.commentModel.attachEvent('/destroying/', _protected.onCommentDestroying);
      	_protected.$layer.find('.viewStatus').click(function(){
      		Dashbird.Controllers.Post.setLastView(_protected.commentModel.getPost(), _protected.commentModel.getDatetime());
      	});
      	me.getPost().getLastView().listen( _protected.onLastViewChange);
	}
	// Draws the viewed status.
	_protected.drawViewed = function(){
		if(me.isViewed())
            _protected.$layer.addClass('viewed');
       	else 
       		_protected.$layer.removeClass('viewed');
    }
    // Draws the text.
	_protected.drawText = function(){
		_protected.$layer.find('.text').html(Dashbird.Utils.convertLineBreaks(_protected.commentModel.getText().get()));
	}
	// Draws the user name.
	_protected.drawUsername = function(){
		_protected.$layer.find('.meta .info .username').html(_protected.commentModel.getUser().name);
	}
	// Draws the crreated.
	_protected.drawCreated = function(){
		_protected.$layer.find('.meta .info .date').html(Dashbird.Utils.convertDate(_protected.commentModel.getDatetime()));
	}
	// Redraws the view model
	me.redraw = function(){
		var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
		if(drawingChangeSet.destroying == true){
			me.destroy();
		}
		else {
			if(drawingChangeSet.text){
				_protected.drawText();
			}
			if(drawingChangeSet.viewed){
				_protected.drawViewed();
			}

			_protected.drawingManager.setDrawingChangeSetToDefault();
		}
	}
	// Gets called when the text is changed.
	_protected.onTextChange = function(){
		_protected.drawingManager.queueRedraw(['text']);
	}
	// Gets called when last view is changed.
	_protected.onLastViewChange = function(){
		_protected.drawingManager.queueRedraw(['viewed']);
	}
	// Gets called when the comment model is destroying.
	_protected.onCommentDestroying = function(){
		_protected.commentModel.detachEvent('/destroying/', _protected.onCommentDestroying);
		_protected.drawingManager.queueRedraw(['destroying']);
	}
	// Fires the event /destroying/
	_protected.fireEventDestroying = function(){
		me.fireEvent('/destroying/', me);
	}
	// Returns the post model.
	// @return <Dashbird.Models.Post>
	me.getPost = function(){
		return _protected.commentsViewModel.getPost();
	}
	// Checks if the comment is viewed.
	// @return <boolean>
	me.isViewed = function(){
		return _protected.commentModel.isViewed();
	}
	// Checks if the view model is allowed to redraw.
	// @return <boolean>
	me.isAllowedToRedraw = function(){
		return _protected.commentsViewModel.isAllowedToRedraw();
	}
	// Returns the jQuery html layer.
	// @return <$jquery>
	me.getLayer = function(){
		return _protected.$layer;
	}
	// Returns the comment id.
	// @return <int>
	me.getCommentId = function(){
		return _protected.commentModel.getCommentId();
	}
	// Destroys the view model.
	me.destroy = function(){
		_protected.fireEventDestroying();
		_protected.commentModel.getText().unlisten(_protected.onTextChange);
      	_protected.commentModel.detachEvent('/destroying/', _protected.onCommentDestroying);
      	me.getPost().getLastView().unlisten( _protected.onLastViewChange);

		_protected.$layer.hide();
		delete _proteced;
		delete me;
	}
	return me;
});