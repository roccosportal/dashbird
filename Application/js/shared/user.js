if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.User = SimpleJSLib.BaseObject.inherit(function (me, _protected){
        _protected.user = null;
        
        me.getUser = function(){
            return _protected.user;
        }
        
        me.isCurrentUser = function(userId){
            return (_protected.user.userId.toString() === userId.toString());
        }
        
        me.init = function(){
            _protected.user = Dashbird.InitialData.User;
        }
                    
        me.getUserShares = function(){
            return _protected.user.userShares;
        }
        

        me.getNameForUser = function(userId){
            var name = 'A person you do not know';
            if(_protected.user.userId == userId){
                name = 'You';
            }
            else {
                $.each(_protected.user.userShares, function(){
                    if(this.userId == userId){
                        name = this.name;
                        return false;
                    }
                    return true;
                });
            }
            return name;
        };


        _protected.savedGravatarHashes = {};
        me.getGravatarHashForUser = function(name){
            if(typeof(_protected.savedGravatarHashes[name]) !== 'undefined')
                return _protected.savedGravatarHashes[name];

            _protected.savedGravatarHashes[name] = Dashbird.Utils.md5(name + '@' + location.hostname);
            return _protected.savedGravatarHashes[name];
        }

        me.getGravatarUrlForUser = function(name, size){
            return 'http://www.gravatar.com/avatar/' + me.getGravatarHashForUser(name) +'?f=y&d=identicon&s=' + size;
        }

       return me;

}).construct();