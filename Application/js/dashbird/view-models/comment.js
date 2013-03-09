Dashbird.ViewModels.Comment =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
	
	_protected.commentsLayer = null;
	_protected.comment = null;
	_protected.$layer = null;
	_protected.drawingManager = null;

	// constructorc
	// @var parameters (.construct(<Dashbird.ViewModel.Comments>, <Dashbird.Models.Comment>))
	_protected.construct = function(parameters){
		_protected.commentsLayer = parameters[0];
		_protected.comment = parameters[1];
		

		_protected.$layer = Dashbird.Views.Utils.Templates.getTemplate('post-comment');
		
		_protected.$layer.find('img.media-object').attr('src',  Dashbird.Controllers.User.getGravatarUrlForUser(_protected.comment.getUser().name, 32));

		// initialize drawings
		_protected.drawViewed();
		_protected.drawText();
		_protected.drawUsername();
		_protected.drawCreated();

		if(Dashbird.Controllers.User.isCurrentUser(_protected.comment.getUser().userId)){
                // show options
                _protected.$layer.mouseover(function(){
                    _protected.$layer.find('.command-bar.popup').show();
                });
                _protected.$layer.mouseleave(function (){
                    _protected.$layer.find('.command-bar.popup').hide();
                });
                // delete comment button
                _protected.$layer.find('.command-bar.popup .command-delete').click(function(){
                    me.getPost().setLastView();
                    Dashbird.Views.Utils.Modal.show({
                        headline: 'Deleting comment', 
                        text : 'Do you really want to delete this comment?',
                        'cancel-button-text' : 'No, no, I am sorry', 
                        'submit-button-text' : 'Remove the rubish!', 
                        callback : function(){
                            me.getPost().deleteComment(_protected.comment.getCommentId(), function(){
                                 me.getPost().setLastView();
                            });
                        }
                    })
                });
        }
      	

      	

      	// add drawing manager
      	_protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['text', 'destroying', 'viewed']);

      	// catch events
      	_protected.comment.getText().listen(_protected.onTextChange);
      	_protected.comment.attachEvent('/destroying/', _protected.onCommentDestroying);
      	_protected.$layer.find('.viewStatus').click(function(){_protected.comment.getPost().setLastView(_protected.comment.getDatetime())});
      	me.getPost().getLastView().listen( _protected.onLastViewChange);

	}

	// --- drawing  ---

	_protected.drawViewed = function(){
		if(me.isViewed())
            _protected.$layer.addClass('viewed');
       	else 
       		_protected.$layer.removeClass('viewed');
    }

	_protected.drawText = function(){
		_protected.$layer.find('.text').html(Dashbird.Utils.convertLineBreaks(_protected.comment.getText().get()));
	}

	_protected.drawUsername = function(){
		_protected.$layer.find('.meta .info .username').html(_protected.comment.getUser().name);
	}

	_protected.drawCreated = function(){
		_protected.$layer.find('.meta .info .date').html(Dashbird.Utils.convertDate(_protected.comment.getDatetime()));
	}

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

	// --- end ---

	// --- catch events ---

	_protected.onTextChange = function(){
		_protected.drawingManager.queueRedraw(['text']);
	}

	_protected.onLastViewChange = function(){
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

	me.getPost = function(){
		return _protected.commentsLayer.getPost();
	}


	me.isViewed = function(){
		return _protected.comment.isViewed();
	}


	me.isAllowedToRedraw = function(){
		return _protected.commentsLayer.isAllowedToRedraw();
	}

	me.getLayer = function(){
		return _protected.$layer;
	}

	me.getCommentId = function(){
		return _protected.comment.getCommentId();
	}

	// --- end ---

	// --- other ---

	me.destroy = function(){
		_protected.fireEventDestroying();
		_protected.comment.getText().unlisten(_protected.onTextChange);
      	_protected.comment.detachEvent('/destroying/', _protected.onCommentDestroying);
      	me.getPost().getLastView().unlisten( _protected.onLastViewChange);

		_protected.$layer.hide();
		delete _proteced;
		delete me;
	}

	// --- end ---

	return me;
});