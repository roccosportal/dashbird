/**
 * This controller provides functions for users.
 *
 */
Dashbird.Controllers.User = SimpleJSLib.BaseObject.inherit(function (me, _protected){
    _protected.user = null;
    _protected.savedGravatarHashes = {};
    // Returns the current user.
    // @return <object>
    me.getUser = function(){
        return _protected.user;
    }
    // Checks if the given user id is the current user.
    // @return <boolean>
    me.isCurrentUser = function(userId){
        return (_protected.user.userId.toString() === userId.toString());
    }
    // Instantiate the user controller
    me.init = function(){
        _protected.user = Dashbird.InitialData.User;
    }
    // Returns the user shares for the current user
    // @return <object>            
    me.getUserShares = function(){
        return _protected.user.userShares;
    }
    // Returns the name for a given user id
    // @return <string>
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
    // Returns a hash for gravatar for a user name
    // @param name <string>
    // @return <string>
    me.getGravatarHashForUser = function(name){
        if(typeof(_protected.savedGravatarHashes[name]) !== 'undefined')
            return _protected.savedGravatarHashes[name];

        _protected.savedGravatarHashes[name] = Dashbird.Utils.md5(name + '@' + location.hostname);
        return _protected.savedGravatarHashes[name];
    }
    // Returns a url for a gravatar for a user name
    // @param name <string>
    // @param size <int>
    // @return <string>
    me.getGravatarUrlForUser = function(name, size){
        return 'http://www.gravatar.com/avatar/' + me.getGravatarHashForUser(name) +'?f=y&d=identicon&s=' + size;
    }
    return me;
}).construct();