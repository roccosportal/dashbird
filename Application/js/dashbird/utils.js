Dashbird.Utils = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
    
    me.convertDate = function(date){
        return date.substring(0, date.length - 3);
    }
    
    me.convertLineBreaks = function(string){
        return string.replace(/\n/g,'<br />');
    }
    
    // http://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling
    me.bottomIsOnScreen = function(elem)
    {
        var docViewTop = $(window).scrollTop();
        docViewTop += 40; // there is a border
        //var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return (elemBottom >= docViewTop);
    }
    
    me.topIsOnScreen = function(elem)
    {
        var docViewTop = $(window).scrollTop();
        docViewTop += 40; // there is a border
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        //var elemBottom = elemTop + $(elem).height();

        return (elemTop <= docViewBottom);
    }

    //@source http://www.myersdaily.org/joseph/javascript/md5-text.html
    _protected.md5cycle = function(x, k) {
        var a = x[0], b = x[1], c = x[2], d = x[3];

        a = _protected.ff(a, b, c, d, k[0], 7, -680876936);
        d = _protected.ff(d, a, b, c, k[1], 12, -389564586);
        c = _protected.ff(c, d, a, b, k[2], 17,  606105819);
        b = _protected.ff(b, c, d, a, k[3], 22, -1044525330);
        a = _protected.ff(a, b, c, d, k[4], 7, -176418897);
        d = _protected.ff(d, a, b, c, k[5], 12,  1200080426);
        c = _protected.ff(c, d, a, b, k[6], 17, -1473231341);
        b = _protected.ff(b, c, d, a, k[7], 22, -45705983);
        a = _protected.ff(a, b, c, d, k[8], 7,  1770035416);
        d = _protected.ff(d, a, b, c, k[9], 12, -1958414417);
        c = _protected.ff(c, d, a, b, k[10], 17, -42063);
        b = _protected.ff(b, c, d, a, k[11], 22, -1990404162);
        a = _protected.ff(a, b, c, d, k[12], 7,  1804603682);
        d = _protected.ff(d, a, b, c, k[13], 12, -40341101);
        c = _protected.ff(c, d, a, b, k[14], 17, -1502002290);
        b = _protected.ff(b, c, d, a, k[15], 22,  1236535329);

        a = _protected.gg(a, b, c, d, k[1], 5, -165796510);
        d = _protected.gg(d, a, b, c, k[6], 9, -1069501632);
        c = _protected.gg(c, d, a, b, k[11], 14,  643717713);
        b = _protected.gg(b, c, d, a, k[0], 20, -373897302);
        a = _protected.gg(a, b, c, d, k[5], 5, -701558691);
        d = _protected.gg(d, a, b, c, k[10], 9,  38016083);
        c = _protected.gg(c, d, a, b, k[15], 14, -660478335);
        b = _protected.gg(b, c, d, a, k[4], 20, -405537848);
        a = _protected.gg(a, b, c, d, k[9], 5,  568446438);
        d = _protected.gg(d, a, b, c, k[14], 9, -1019803690);
        c = _protected.gg(c, d, a, b, k[3], 14, -187363961);
        b = _protected.gg(b, c, d, a, k[8], 20,  1163531501);
        a = _protected.gg(a, b, c, d, k[13], 5, -1444681467);
        d = _protected.gg(d, a, b, c, k[2], 9, -51403784);
        c = _protected.gg(c, d, a, b, k[7], 14,  1735328473);
        b = _protected.gg(b, c, d, a, k[12], 20, -1926607734);

        a = _protected.hh(a, b, c, d, k[5], 4, -378558);
        d = _protected.hh(d, a, b, c, k[8], 11, -2022574463);
        c = _protected.hh(c, d, a, b, k[11], 16,  1839030562);
        b = _protected.hh(b, c, d, a, k[14], 23, -35309556);
        a = _protected.hh(a, b, c, d, k[1], 4, -1530992060);
        d = _protected.hh(d, a, b, c, k[4], 11,  1272893353);
        c = _protected.hh(c, d, a, b, k[7], 16, -155497632);
        b = _protected.hh(b, c, d, a, k[10], 23, -1094730640);
        a = _protected.hh(a, b, c, d, k[13], 4,  681279174);
        d = _protected.hh(d, a, b, c, k[0], 11, -358537222);
        c = _protected.hh(c, d, a, b, k[3], 16, -722521979);
        b = _protected.hh(b, c, d, a, k[6], 23,  76029189);
        a = _protected.hh(a, b, c, d, k[9], 4, -640364487);
        d = _protected.hh(d, a, b, c, k[12], 11, -421815835);
        c = _protected.hh(c, d, a, b, k[15], 16,  530742520);
        b = _protected.hh(b, c, d, a, k[2], 23, -995338651);

        a = _protected.ii(a, b, c, d, k[0], 6, -198630844);
        d = _protected.ii(d, a, b, c, k[7], 10,  1126891415);
        c = _protected.ii(c, d, a, b, k[14], 15, -1416354905);
        b = _protected.ii(b, c, d, a, k[5], 21, -57434055);
        a = _protected.ii(a, b, c, d, k[12], 6,  1700485571);
        d = _protected.ii(d, a, b, c, k[3], 10, -1894986606);
        c = _protected.ii(c, d, a, b, k[10], 15, -1051523);
        b = _protected.ii(b, c, d, a, k[1], 21, -2054922799);
        a = _protected.ii(a, b, c, d, k[8], 6,  1873313359);
        d = _protected.ii(d, a, b, c, k[15], 10, -30611744);
        c = _protected.ii(c, d, a, b, k[6], 15, -1560198380);
        b = _protected.ii(b, c, d, a, k[13], 21,  1309151649);
        a = _protected.ii(a, b, c, d, k[4], 6, -145523070);
        d = _protected.ii(d, a, b, c, k[11], 10, -1120210379);
        c = _protected.ii(c, d, a, b, k[2], 15,  718787259);
        b = _protected.ii(b, c, d, a, k[9], 21, -343485551);

        x[0] = _protected.add32(a, x[0]);
        x[1] = _protected.add32(b, x[1]);
        x[2] = _protected.add32(c, x[2]);
        x[3] = _protected.add32(d, x[3]);
    }

    _protected.cmn = function(q, a, b, x, s, t) {
        a =  _protected.add32(_protected.add32(a, q),  _protected.add32(x, t));
        return  _protected.add32((a << s) | (a >>> (32 - s)), b);
    }

    _protected.ff = function(a, b, c, d, x, s, t) {
        return  _protected.cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }

    _protected.gg = function(a, b, c, d, x, s, t) {
        return  _protected.cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }

    _protected.hh = function(a, b, c, d, x, s, t) {
        return  _protected.cmn(b ^ c ^ d, a, b, x, s, t);
    }

    _protected.ii = function(a, b, c, d, x, s, t) {
        return _protected.cmn(c ^ (b | (~d)), a, b, x, s, t);
    }

    _protected.md51 = function(s) {
        txt = '';
        var n = s.length,
        state = [1732584193, -271733879, -1732584194, 271733878], i;
        for (i=64; i<=s.length; i+=64) {
        _protected.md5cycle(state, md5blk(s.substring(i-64, i)));
        }
        s = s.substring(i-64);
        var tail = [0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0];
        for (i=0; i<s.length; i++)
        tail[i>>2] |= s.charCodeAt(i) << ((i%4) << 3);
        tail[i>>2] |= 0x80 << ((i%4) << 3);
        if (i > 55) {
        _protected.md5cycle(state, tail);
        for (i=0; i<16; i++) tail[i] = 0;
        }
        tail[14] = n*8;
        _protected.md5cycle(state, tail);
        return state;
    }

    /* there needs to be support for Unicode here,
     * unless we pretend that we can redefine the MD-5
     * algorithm for multi-byte characters (perhaps
     * by adding every four 16-bit characters and
     * shortening the sum to 32 bits). Otherwise
     * I suggest performing MD-5 as if every character
     * was two bytes--e.g., 0040 0025 = @%--but then
     * how will an ordinary MD-5 sum be matched?
     * There is no way to standardize text to something
     * like UTF-8 before transformation; speed cost is
     * utterly prohibitive. The JavaScript standard
     * itself needs to look at this: it should start
     * providing access to strings as preformed UTF-8
     * 8-bit unsigned value arrays.
     */
    _protected.md5blk = function(s) { /* I figured global was faster.   */
        var md5blks = [], i; /* Andy King said do it this way. */
        for (i=0; i<64; i+=4) {
        md5blks[i>>2] = s.charCodeAt(i)
        + (s.charCodeAt(i+1) << 8)
        + (s.charCodeAt(i+2) << 16)
        + (s.charCodeAt(i+3) << 24);
        }
        return md5blks;
    }

    _protected.hex_chr = '0123456789abcdef'.split('');
    _protected.rhex = function(n)
    {
        var s='', j=0;
        for(; j<4; j++)
        s += _protected.hex_chr[(n >> (j * 8 + 4)) & 0x0F]
        + _protected.hex_chr[(n >> (j * 8)) & 0x0F];
        return s;
    }

    _protected.hex = function(x) {
        for (var i=0; i<x.length; i++)
        x[i] = _protected.rhex(x[i]);
        return x.join('');
    }

    me.md5 = function(s) {
        return _protected.hex(_protected.md51(s));
    }

    /* this function is much faster,
    so if possible we use it. Some IEs
    are the only ones I know of that
    need the idiotic second function,
    generated by an if clause.  */

    _protected.add32 = function(a, b) {
        return (a + b) & 0xFFFFFFFF;
    }

    if (me.md5('hello') != '5d41402abc4b2a76b9719d911017c592') {
        _protected.add32 = function(x, y) {
            var lsw = (x & 0xFFFF) + (y & 0xFFFF),
            msw = (x >> 16) + (y >> 16) + (lsw >> 16);
                return (msw << 16) | (lsw & 0xFFFF);
        }
    }

    return me;
}).construct();