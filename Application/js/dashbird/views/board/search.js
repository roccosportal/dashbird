Dashbird.Views.Board.Search = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.searchRequestQueue = null;
    _protected.$searchBox = null;
    _protected.currentSearchPhrase = null;    
        
    me.init = function(){
        _protected.$pane = $('#search');
        _protected.$posts = _protected.$pane.find('.posts');
        _protected.viewModelPostsManager = Dashbird.Views.Utils.ViewModelPostsManager.construct();
        _protected.$searchBox = $('#search-box');
        _protected.searchRequestQueue = SimpleJSLib.SingleRequestQueue.construct();
        _protected.searchRequestQueue.setTimeout(500);
        _protected.$searchBox.keydown(function(e){
            if(e.keyCode == 13){
                e.preventDefault();
            }
            else {
                _protected.searchRequestQueue.addToQueue({}, function(data){
                    var searchObject = me.getSearchObject();
                    if(searchObject!=null)
                        me.search(searchObject);
                   
                });
            }
        });
        $('#navigation .search').click(me.show);
    };
    
    _protected.isVisible = function(){
        return (_protected.$pane.hasClass('active'));
    };
    
    me.search = function(search){
        _protected.viewModelPostsManager.clear();
        _protected.currentSearch = search;
        var posts = Dashbird.Controllers.Posts.search(search);
        var postHtmlLayer = null;
        for(var i = 0; i < posts.length; i++){
            postHtmlLayer = Dashbird.ViewModels.Post.construct(posts[i]);
            _protected.viewModelPostsManager.registerViewModelPost(postHtmlLayer, 'bottom');
            _protected.$posts.append(postHtmlLayer.getLayer());
        }
        Dashbird.Controllers.Posts.attachEvent('/posts/new/', _protected.onNewPosts);
        me.show();
        Dashbird.Controllers.Posts.loadPostBySearch(search);
    }
    
    me.show = function(e){
        if(typeof(e) !== 'undefined')
            e.preventDefault();

        if(!_protected.isVisible())
            $('#navigation .search').tab('show');
        
        $('#navigation .search').show();
    
        // move to top
        window.scrollTo(0,0);
        
         // view is now on top again;
        _protected.viewModelPostsManager.allowAll();
    }
        
    me.getSearchObject = function(){
        var searchPhrase = _protected.$searchBox.val();
        if(searchPhrase.length < 3){
            // too short
            return null;
        }
        var search = {
            keywords : searchPhrase.split(' ')
        };
        var keywords =  search.keywords.slice();
        for(var i =  keywords.length - 1; i >= 0; i--){
            if(keywords[i].length < 3){
                // too short, delete keyword
                search.keywords.splice(i, 1);
            }
        }
        if(search.keywords.length == 0){
            // no keywords left
            return null;
        }
        
        return search;
    };
    
    _protected.onNewPosts = function(result){
        
        var postHtmlLayer = null;
        for(var i = 0; i < result.newPosts.length; i++){
            if(result.newPosts[i].isSearchMatch(_protected.currentSearch)){
                postHtmlLayer = Dashbird.ViewModels.Post.construct(result.newPosts[i]);
                _protected.viewModelPostsManager.registerViewModelPost(postHtmlLayer);
                _protected.$posts.append(postHtmlLayer.getLayer());
            }
        }
    }
    
    
    return me;
}).construct();

