/**
 *  This module manages the 'allowedToRedraw' state of view model posts.
 *  Not every view model post should refresh its content/layout especially when it is outside of the top of the screen.
 *  The changeAllowedToRedraw method should be called when the user changed the position of its screen (after scrolling) to allow 'hidden'
 *  view model posts to redraw and others not to redraw.
 */
Dashbird.Views.Utils.ViewModelPostsManager = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.viewModelPosts = [];
    _protected.viewModelPostsDeniedForRedraw = [];
    _protected.viewModelPostsAllowedForRedraw = [];   
    // Returns all view model posts that are managed by this module.
    me.getPostHtmlLayers = function(){
        return  _protected.viewModelPosts;
    }
    // Registers a view model post to this manager.
    // @param viewModelPost <Dashbird.ViewModels.Post>
    // @param position [optional] 'bottom'[default]|'top'
    me.registerViewModelPost = function(viewModelPost, position){
         if(typeof(position) === 'undefined'){
            position = 'bottom';
        }
        viewModelPost.setAllowedToRedraw(true);
        viewModelPost.attachEvent('/destroying/', _protected.onViewModelPostDestroying);
        var index = _protected.viewModelPosts.push(viewModelPost) - 1;
        if(position==='bottom'){
            _protected.viewModelPostsAllowedForRedraw.push(index);
        }
        else if(position==='top'){
            // if no view model post is denied for redraw, we safely can put the current 
            // view model post to the top of the allowed array.
            // Otherwhise we have to put it to the top of the denied array.
            if(_protected.viewModelPostsDeniedForRedraw.length == 0){
                _protected.viewModelPostsAllowedForRedraw.unshift(index);
            }
            else {
                _protected.viewModelPostsDeniedForRedraw.unshift(index);
            }
        }
    }
    // This method recalulates the 'allowedToRedraw' state of the managed view model posts.
    // For perfomance optimizations it is assumed the user can scroll up and down.
    me.changeAllowedToRedraw = function(){
        var movePostsToAllow = [];
        var viewModelPost = null; // <ViewModels.Post>
        // go reverse through all view model posts that are not allowed to redraw
        // if we find one, that is not on the screen anymore, we can break the loop
        for(var j = _protected.viewModelPostsDeniedForRedraw.length - 1; j >= 0; j --){
            viewModelPost = _protected.viewModelPosts[_protected.viewModelPostsDeniedForRedraw[j]];
            if(Dashbird.Utils.bottomIsOnScreen(viewModelPost.getLayer()))
                movePostsToAllow.push(j);
            else
                break;
        }
        // if we found any posts which were not allowed to redraw and now appeared on the screen (scrolled up)
        // we move them to array with the allowed redrawing posts and remove them from the denied array
        var viewModelPostIndex = null;
        if(movePostsToAllow.length > 0){
            var index = null;
            for(var k = 0; k < movePostsToAllow.length; k ++){
                // the index of _protected.viewModelPostsDeniedForRedraw
                index = movePostsToAllow[k];
                // the actual index of the view model post
                viewModelPostIndex = _protected.viewModelPostsDeniedForRedraw[index];
                // delete from denied array
                // we started with the highest index, so we do not have to worry about a changing index
                _protected.viewModelPostsDeniedForRedraw.splice(index, 1);
                // add to top of allowed array
                _protected.viewModelPostsAllowedForRedraw.unshift(viewModelPostIndex);
                _protected.viewModelPosts[viewModelPostIndex].setAllowedToRedraw(true);
            }
        }
        // no posts were moved to the allow array
        // this could mean the screen scrolled down
        else {
            var movePostsToDenied = [];
            // searching for view model posts that were allowed but are not on the screen anymore
            // if we find one that is still on the screen we can break
            for(var i = 0; i < _protected.viewModelPostsAllowedForRedraw.length; i ++){
                viewModelPost = _protected.viewModelPosts[_protected.viewModelPostsAllowedForRedraw[i]];
                if(!Dashbird.Utils.bottomIsOnScreen(viewModelPost.getLayer()))
                    movePostsToDenied.push(i);
                else
                    break;
            }
            // if we found some view models that were allowed to redraw and now not anymore
            // we have to move them
            if(movePostsToDenied.length > 0){
                for(var h = movePostsToDenied.length - 1; h >= 0; h --){
                     // the index of _protected.viewModelPostsAllowedForRedraw
                    index = movePostsToDenied[h];
                     // the actual index of the view model post
                    viewModelPostIndex =_protected.viewModelPostsAllowedForRedraw[index];
                    // delete from allowed array
                    // we going reverse, so we do not have to worry about a changing index
                    _protected.viewModelPostsAllowedForRedraw.splice(index, 1);
                    // add to bottom of denied array
                    _protected.viewModelPostsDeniedForRedraw.push(viewModelPostIndex);
                    _protected.viewModelPosts[viewModelPostIndex].setAllowedToRedraw(false);
                }
            }
        }
    };
    
    // Gets called when a view model post is destroying
    // @param viewModelPost <Dashbird.ViewModels.Post>
    _protected.onViewModelPostDestroying = function(viewModelPost){
        var index = null;
        // find the index in viewModelPosts of the given view model posts
        for(var i = 0; i < _protected.viewModelPosts.length; i++){
            if(viewModelPost.getPost().getPostId().toString() == _protected.viewModelPosts[i].getPost().getPostId().toString()){
                index = i;
                break;
            }
        }
        if(index!=null){
            // now we delete it from the array
            _protected.viewModelPosts[index].detachEvent('/destroying/', _protected.onViewModelPostDestroying);
            _protected.viewModelPosts.splice(index, 1);
            // lets search for the index in the allowed array
            // and because we removed an item from the array the index changed
            var indexReference = null
            for(var j = 0; j < _protected.viewModelPostsAllowedForRedraw.length; j++){
                if(indexReference == null && _protected.viewModelPostsAllowedForRedraw[j] == index){
                    indexReference = j;
                }
                else if(_protected.viewModelPostsAllowedForRedraw[j] > index){
                    // every old index that was higher than the deleted index is now one less
                    _protected.viewModelPostsAllowedForRedraw[j] -= 1;
                }
            }
            // we found an reference in the allowed array
            if(indexReference!=null)
                _protected.viewModelPostsAllowedForRedraw.splice(indexReference, 1);
            // reset variable
            indexReference = null;
            // lets search for the index in the denied array
            // and because we removed an item from the array the index changed
            for(var k = 0; k < _protected.viewModelPostsDeniedForRedraw.length; k++){
                if(indexReference == null && _protected.viewModelPostsDeniedForRedraw[k]== index){
                    indexReference = k;
                }
                else if(_protected.viewModelPostsDeniedForRedraw[k] > index){
                    // every old index that was higher than the deleted index is now one less
                    _protected.viewModelPostsDeniedForRedraw[k] -= 1;
                }
            }
             // we found an reference in the denied array
            if(indexReference!=null)
                _protected.viewModelPostsDeniedForRedraw.splice(indexReference, 1);
        }
    };
    // This method allows all view model posts that are managed by this module to redraw.
    me.allowAll = function(){
         var viewModelPostIndex = null;
        // allow all posts to redraw
        for(var j = _protected.viewModelPostsDeniedForRedraw.length - 1; j >= 0; j --){
            viewModelPostIndex = _protected.viewModelPostsDeniedForRedraw[j];
            _protected.viewModelPosts[viewModelPostIndex].setAllowedToRedraw(true);
            _protected.viewModelPostsAllowedForRedraw.unshift(viewModelPostIndex);
        }
        _protected.viewModelPostsDeniedForRedraw = [];
    }
    // This module destroys all view model posts that are managed by this module.
    // This function should be prefered than deleting view model posts in a loop.
    // Otherwhise it would costs huge array recalculations.
    me.clear = function(){
        for (var i = 0; i <  _protected.viewModelPosts.length; i++) {
            // detach the destroying event catcher to prevent array recalculations
            _protected.viewModelPosts[i].detachEvent('/destroying/', _protected.onViewModelPostDestroying);
            _protected.viewModelPosts[i].destroy();
        }
        _protected.viewModelPostsDeniedForRedraw = [];
        _protected.viewModelPostsAllowedForRedraw = [];
        _protected.viewModelPosts = [];
    }
    return me;
});