/*!
 * Ext JS Library 3.1.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.util.Cookies
 * Utility class for managing and interacting with cookies.
 * @singleton
 */
Ext.util.Cookies = {
    /**
     * Create a cookie with the specified name and value. Additional settings
     * for the cookie may be optionally specified (for example: expiration,
     * access restriction, SSL).
     * @param {String} name The name of the cookie to set. 
     * @param {Mixed} value The value to set for the cookie.
     * @param {Object} expires (Optional) Specify an expiration date the
     * cookie is to persist until.  Note that the specified Date object will
     * be converted to Greenwich Mean Time (GMT). 
     * @param {String} path (Optional) Setting a path on the cookie restricts
     * access to pages that match that path. Defaults to all pages (<tt>'/'</tt>). 
     * @param {String} domain (Optional) Setting a domain restricts access to
     * pages on a given domain (typically used to allow cookie access across
     * subdomains). For example, "extjs.com" will create a cookie that can be
     * accessed from any subdomain of extjs.com, including www.extjs.com,
     * support.extjs.com, etc.
     * @param {Boolean} secure (Optional) Specify true to indicate that the cookie
     * should only be accessible via SSL on a page using the HTTPS protocol.
     * Defaults to <tt>false</tt>. Note that this will only work if the page
     * calling this code uses the HTTPS protocol, otherwise the cookie will be
     * created with default options.
     */
    set : function(name, value){
        var argv = arguments;
        var argc = arguments.length;
        var expires = (argc > 2) ? argv[2] : null;
        var path = (argc > 3) ? argv[3] : '/';
        var domain = (argc > 4) ? argv[4] : null;
        var secure = (argc > 5) ? argv[5] : false;
        document.cookie = name + "=" + escape(value) + ((expires === null) ? "" : ("; expires=" + expires.toGMTString())) + ((path === null) ? "" : ("; path=" + path)) + ((domain === null) ? "" : ("; domain=" + domain)) + ((secure === true) ? "; secure" : "");
    },

    /**
     * Retrieves cookies that are accessible by the current page. If a cookie
     * does not exist, <code>get()</code> returns <tt>null</tt>.  The following
     * example retrieves the cookie called "valid" and stores the String value
     * in the variable <tt>validStatus</tt>.
     * <pre><code>
     * var validStatus = Ext.util.Cookies.get("valid");
     * </code></pre>
     * @param {String} name The name of the cookie to get
     * @return {Mixed} Returns the cookie value for the specified name;
     * null if the cookie name does not exist.
     */
    get : function(name){
        var arg = name + "=";
        var alen = arg.length;
        var clen = document.cookie.length;
        var i = 0;
        var j = 0;
        while(i < clen){
            j = i + alen;
            if(document.cookie.substring(i, j) == arg){
                return Ext.util.Cookies.getCookieVal(j);
            }
            i = document.cookie.indexOf(" ", i) + 1;
            if(i === 0){
                break;
            }
        }
        return null;
    },

    /**
     * Removes a cookie with the provided name from the browser
     * if found by setting its expiration date to sometime in the past. 
     * @param {String} name The name of the cookie to remove
     */
    clear : function(name){
        if(Ext.util.Cookies.get(name)){
            document.cookie = name + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
        }
    },
    /**
     * @private
     */
    getCookieVal : function(offset){
        var endstr = document.cookie.indexOf(";", offset);
        if(endstr == -1){
            endstr = document.cookie.length;
        }
        return unescape(document.cookie.substring(offset, endstr));
    }
};