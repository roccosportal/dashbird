Dashbird.PostHtmlLayersManager = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    
    _protected.postHtmlLayers = [];
    _protected.postHtmlLayersDeniedForRedraw = [];
    _protected.postHtmlLayersAllowedForRedraw = [];
    
    
   
    
    me.getPostHtmlLayers = function(){
        return  _protected.postHtmlLayers;
    }
    
    me.registerPostHtmlLayer = function(postHtmlLayer, position){
         if(typeof(position) === 'undefined'){
            position = 'bottom';
        }
        
        postHtmlLayer.setAllowedToRedraw(true);
        postHtmlLayer.attachEvent('/destroying/', _protected.onPostHtmlLayerDestroying);
        var index = _protected.postHtmlLayers.push(postHtmlLayer) - 1;
        
          if(position==='bottom'){
                _protected.postHtmlLayersAllowedForRedraw.push(index);
          }
          else if(position==='top'){
                _protected.postHtmlLayersAllowedForRedraw.unshift(index);
          }

    }
    
    
    me.changeAllowedToRedraw = function(){
        
        var movePostsToAllow = [];
        // go reverse
        var postHtmlLayer = null
        for(var j = _protected.postHtmlLayersDeniedForRedraw.length - 1; j >= 0; j --){
            postHtmlLayer = _protected.postHtmlLayers[_protected.postHtmlLayersDeniedForRedraw[j]];
            if(Dashbird.Utils.bottomIsOnScreen(postHtmlLayer.getLayer()))
                movePostsToAllow.push(j);
            else
                break;
        }
    
        if(movePostsToAllow.length > 0){
            var index = null;
            var postHtmlLayerIndex = null;
            for(var k = 0; k <movePostsToAllow.length; k ++){
                index = movePostsToAllow[k];
                postHtmlLayerIndex = _protected.postHtmlLayersDeniedForRedraw[index];
                // delete from denied array
                _protected.postHtmlLayersDeniedForRedraw.splice(index, 1);
                // add to top of allowed array
                _protected.postHtmlLayersAllowedForRedraw.unshift(postHtmlLayerIndex);
                _protected.postHtmlLayers[postHtmlLayerIndex].setAllowedToRedraw(true);
            }
        }
        else {
            var movePostsToDenied = [];
            for(var i = 0; i < _protected.postHtmlLayersAllowedForRedraw.length; i ++){
                postHtmlLayer = _protected.postHtmlLayers[_protected.postHtmlLayersAllowedForRedraw[i]];
                if(!Dashbird.Utils.bottomIsOnScreen(postHtmlLayer.getLayer()))
                    movePostsToDenied.push(i);
                else
                    break;
            }
            if(movePostsToDenied.length > 0){
                for(var h = 0; h < movePostsToDenied.length; h ++){
                    index = movePostsToDenied[h];
                    postHtmlLayerIndex =_protected.postHtmlLayersAllowedForRedraw[index];
                    // delete from allowed array
                    _protected.postHtmlLayersAllowedForRedraw.splice(index, 1);
                    // add to bottom of denied array
                    _protected.postHtmlLayersDeniedForRedraw.push(postHtmlLayerIndex);
                    _protected.postHtmlLayers[postHtmlLayerIndex].setAllowedToRedraw(false);
                }
            }
        }
    };
    
    
    _protected.onPostHtmlLayerDestroying = function(postHtmlLayer){
        var index = null;
        for(var i = 0; i < _protected.postHtmlLayers.length; i++){
            if(postHtmlLayer.getPost().getPostData().postId.toString() == _protected.postHtmlLayers[i].getPost().getPostData().postId.toString()){
                index = i;
                break;
            }
        }
        
        if(index!=null){
            _protected.postHtmlLayers[index].detachEvent('/destroying/', _protected.onPostHtmlLayerDestroying);
            _protected.postHtmlLayers.splice(index, 1);
            
            var indexReference = null
            for(var j = 0; j < _protected.postHtmlLayersAllowedForRedraw.length; j++){
                if(_protected.postHtmlLayersAllowedForRedraw[j]== index){
                    indexReference = j;
                    break;
                }
            }
            if(indexReference!=null){
                _protected.postHtmlLayersAllowedForRedraw.splice(indexReference, 1);
            }
            else {
                for(var k = 0; k < _protected.postHtmlLayersDeniedForRedraw.length; k++){
                    if(_protected.postHtmlLayersDeniedForRedraw[k]== index){
                        indexReference = k;
                        break;
                    }
                }
                if(indexReference!=null){
                    _protected.postHtmlLayersDeniedForRedraw.splice(indexReference, 1);
                }
            }
        }
    };
    
    me.allowAll = function(){
         var postHtmlLayerIndex = null;
        // allow all posts to redraw
        for(var j = _protected.postHtmlLayersDeniedForRedraw.length - 1; j >= 0; j --){
            postHtmlLayerIndex = _protected.postHtmlLayersDeniedForRedraw[j];
            _protected.postHtmlLayers[postHtmlLayerIndex].setAllowedToRedraw(true);
            _protected.postHtmlLayersAllowedForRedraw.unshift(postHtmlLayerIndex);
        }
        
        _protected.postHtmlLayersDeniedForRedraw = [];
    }
    
    me.clear = function(){
        for (var i = 0; i <  _protected.postHtmlLayers.length; i++) {
            _protected.postHtmlLayers[i].detachEvent('/destroying/', _protected.onPostHtmlLayerDestroying);
            _protected.postHtmlLayers[i].destroy();
        }
        
         _protected.postHtmlLayersDeniedForRedraw = [];
         _protected.postHtmlLayersAllowedForRedraw = [];
         _protected.postHtmlLayers = [];
    }
    return me;
});