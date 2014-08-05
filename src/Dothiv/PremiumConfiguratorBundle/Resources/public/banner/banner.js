/**
 * This is the dotHIV banner control.
 */
(function() {
    // These are grunt includes
    /**
 * This function provides a reliable event for 'DOM ready'
 */
!function (name, definition) {
  if (typeof module != 'undefined') module.exports = definition()
  else if (typeof define == 'function' && typeof define.amd == 'object') define(definition)
  else this[name] = definition()
}('domready', function (ready) {
  var fns = [], fn, f = false
    , doc = document
    , testEl = doc.documentElement
    , hack = testEl.doScroll
    , domContentLoaded = 'DOMContentLoaded'
    , addEventListener = 'addEventListener'
    , onreadystatechange = 'onreadystatechange'
    , readyState = 'readyState'
    , loadedRgx = hack ? /^loaded|^c/ : /^loaded|c/
    , loaded = loadedRgx.test(doc[readyState])

  function flush(f) {
    loaded = 1
    while (f = fns.shift()) f()
  }

  doc[addEventListener] && doc[addEventListener](domContentLoaded, fn = function () {
    doc.removeEventListener(domContentLoaded, fn, f)
    flush()
  }, f)

  hack && doc.attachEvent(onreadystatechange, fn = function () {
    if (/^c/.test(doc[readyState])) {
      doc.detachEvent(onreadystatechange, fn)
      flush()
    }
  })

  return (ready = hack ?
    function (fn) {
      self != top ?
        loaded ? fn() : fns.push(fn) :
        function () {
          try {
            testEl.doScroll('left')
          } catch (e) {
            return setTimeout(function() { ready(fn) }, 50)
          }
          fn()
        }()
    } :
    function (fn) {
      loaded ? fn() : fns.push(fn)
    })
});

    /*
    json2.js
    2013-05-26

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html


    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.


    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the value

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be
            used to select the members to be serialized. It filters the results
            such that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.
*/

/*jslint evil: true, regexp: true */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/


// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

if (typeof JSON !== 'object') {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function () {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function () {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());

    /**
 * Check of we are on a touch device.
 *
 * @see http://stackoverflow.com/a/4819886
 * @returns {boolean}
 */
function isTouchDevice() {
    return 'ontouchstart' in window // works on most browsers
        || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0); // works on ie10, ie11
}


/**
 * Check for Internet Explorer
 *
 * @param ver Version to check for
 * @returns {boolean}
 */
function isIE(ver) {
    return getIE() == ver;
}

/**
 * Returns the Internet Explorer Version or NaN if not an IE.
 *
 * @return {int}
 */
function getIE()
{
    return parseInt((/msie (\d+)/.exec(navigator.userAgent.toLowerCase()) || [])[1], 10);
}

/**
 * Add Date.now() if not present.
 */
if (typeof Date.now === "undefined") {
    Date.now = Date.now || function() { return +new Date; };
}


    // -------- This is the main procedure -------- //
    // Check if this is the first visit and if we can set cookies
    var firstVisit = false;
    if (!getCookie())
        firstVisit = setCookie() ? true : false;

    // Fetch banner configuration from dotHIV server and add banner to DOM
    requestConfig(firstVisit);
    // -------- End of main procedure -------- //

    /**
     * Gets the dothiv status cookie and returns its value. If the cookie 
     * cannot be found, 'null' is returned.
     */
    function getCookie() {
        var cookieArray = document.cookie.split(';');
        for (var i = 0; i < cookieArray.length; i++)
            if (cookieArray[i] == 'dothivstatus=returning')
                return true;
        return false;
    }

    /**
     * Set a cookie to be able to distinguish new visitors from those who have
     * already seen the banner. Returns 'true' on success and 'false' otherwise.
     */
    function setCookie() {
        document.cookie='dothivstatus=returning';
        return getCookie() ? true : false;
    }

    /**
     * Sends a POST request to the server and receive banner configuration. The
     * server will be informed whether this is the first visit.
     */
    function requestConfig(firstVisit) {
        try {
            var request;
            if (window.XDomainRequest) {
                request = new XDomainRequest();
                request.onload = function() { ajaxCallback(request.responseText); };
                request.onprogress = function() {};
            } else {
                request = new XMLHttpRequest();
                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) 
                        ajaxCallback(request.responseText);
                }
            }
            // Send request.
            request.open("GET", "demo.json", true);
            request.send();
        } catch(e) {
            // Use default config if request fails
            var responseText = '{"secondvisit":"top","firstvisit":"top"}';
            ajaxCallback(responseText);
        }
    }

    /**
     * Saves time t as time last visited in a cookie.
     * 
     * @param t
     */
    function setPreviousVisit(t)
    {
        var lifetime = 2592000;
        var d = new Date();
        var expires = d.setTime(d.getTime() + lifetime * 1000);
        document.cookie = "dothivpt=" + t + ";path=/;max-age=" + lifetime + ";expires=" + d.toGMTString() + ";";
    }

    /**
     * Returns the timestamp in milliseconds of the previous visit (as stored in a cookie) or an empty string.
     * 
     * @return ''|int
     */
    function getPreviousVisit() 
    {
        var pt = '';
        var ptmatch = document.cookie.match('dothivpt=([0-9]{13})');
        if (ptmatch) {
            pt = ptmatch[1];
        }
        return pt;
    }

    /**
     * Callback function for handling config data and moving on.
     */
    function ajaxCallback(responseText) {
        var config = JSON.parse(responseText);
        if (hasMessaging()) {
            registerMessageHandling(config);
        }
        manipulateDOM(config);
    }

    /**
     * Returns whether window.postMessage is supported in this browser.
     *
     * @returns {boolean}
     */
    function hasMessaging()
    {
        return !!window.postMessage;
    }

    /**
     * Register message handling. Supported messages are:
     *
     *  - 'get config': config object requested, send it back
     *  - 'remove':     iframe removal requested, delete it from DOM
     */
    function registerMessageHandling(config) {
        var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
        var eventer = window[eventMethod];
        var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
        eventer(messageEvent, function(e) {
            var iframe = document.getElementById('dothiv-clickcounter');
            switch (e.data) {
                case 'get config':
                    iframe.contentWindow.postMessage(JSON.stringify(config), "*");
                    break;
                case 'remove':
                    if (document.getElementById('dothiv-outer'))
                        document.body.removeChild(document.getElementById('dothiv-outer'));
                    if (document.getElementById('dothiv-clickcounter'))
                        document.body.removeChild(document.getElementById('dothiv-clickcounter'));
                    if (document.getElementById('dothiv-background'))
                        document.body.removeChild(document.getElementById('dothiv-background'));
                  break;
                case 'expand':
                    iframe.className += ' dothiv-expanded';
                    break;
                case 'compact':
                    iframe.className = iframe.className.replace('dothiv-expanded', '');
                    break;
            }
        }, false);
    }

    /**
     * Manipulate the DOM by inserting dotHIV banner and css code. This will be
     * done once the DOM is ready.
     */
    function manipulateDOM(config) {
        // Enable a switch to force the rendering of a specific format.
        // Use #dothiv:{format} in the hash.
        if (window.location.hash.substr(1,7) == "dothiv:") {
            var forceFormat = window.location.hash.substr(8);
            switch(forceFormat) {
                case 'center':
                case 'right':
                case 'top':
                    config.firstvisit = config.secondvisit = forceFormat;
                    break;
            }
        }
        domready(function () {
            // Determine which of the three banner versions to render
            if (firstVisit || (config.secondvisit != 'top' && config.secondvisit != 'right' && config.secondvisit != 'center'))
                switch(config.firstvisit) {
                    case 'center':
                        createCenterBanner(config);
                        break;
                    case 'right':
                        createRightBanner(config);
                        break;
                    default:
                        createTopBanner(config);
                        break;
                }
            else
               switch(config.secondvisit) {
                     case 'right':
                         createRightBanner(config);
                         break;
                     case 'center':
                         createCenterBanner(config);
                         break;
                     default:
                         createTopBanner(config);
                         break;
    }});}

    /**
     * Inserts style rules for the iframes into the DOM.
     */
    function includeCSS() {
        var styleElement = document.createElement('style');
        var styleRules = "#dothiv-clickcounter.dothiv-clickcounter-center{border:0;left:0;position:fixed;width:100%;z-index:2147483647}#dothiv-clickcounter.dothiv-clickcounter-right{border:0;height:66px;position:fixed;width:300px;z-index:2147483647;right:-122px;bottom:190px;-ms-filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);-moz-transform:rotate(270deg);-o-transform:rotate(270deg);-webkit-transform:rotate(270deg);transform:rotate(270deg)}#dothiv-clickcounter.dothiv-clickcounter-right.dothiv-rb-mouseover,#dothiv-clickcounter.dothiv-clickcounter-right.dothiv-expanded{height:94px;right:-108px;bottom:176px}#dothiv-clickcounter.dothiv-clickcounter-top{height:58px;left:0;position:fixed;text-align:center;top:0;width:100%}#dothiv-clickcounter.dothiv-clickcounter-top.dothiv-expanded{height:86px}#dothiv-background{background-color:#fff;-ms-filter:'alpha(Opacity=70)';filter:alpha(opacity=70);left:0;height:100%;margin:0;opacity:.7;padding:0;position:fixed;top:0;width:100%;z-index:2147483646}#dothiv-outer{border:0;background:0;padding:0;margin:0;width:auto;height:auto;display:block;position:static}* html #dothiv-background,* html #dothiv-clickcounter{position:absolute}@media screen{#dothiv-clickcounter.dothiv-clickcounter-center{height:196px;top:100px}}@media screen and (min-height:480px){#dothiv-clickcounter.dothiv-clickcounter-center{top:200px}}@media screen and (min-width:720px){}@media screen and (min-width:1000px){#dothiv-clickcounter.dothiv-clickcounter-center{height:160px}}";
        styleElement.type = 'text/css';
        if (styleElement.styleSheet) {
            styleElement.styleSheet.cssText = styleRules;
        } else {
           var textNode = document.createTextNode(styleRules);
            styleElement.appendChild(textNode);
        }
        document.getElementsByTagName('head')[0].appendChild(styleElement);
    }

    /**
     * Returns an iframe DOM element configured for the given position. 
     * Supported positions are 'top', 'center' and 'right'.
     */
    function createIframeElement(position) {
        var bannerContainer = document.createElement('iframe');
        bannerContainer.id = 'dothiv-clickcounter';
        bannerContainer.className = 'dothiv-clickcounter-' + position;
        bannerContainer.src = 'banner-' + position + '.html?' + Date.now();
        bannerContainer.scrolling = 'no';
        bannerContainer.frameBorder = 0;
        bannerContainer.allowTransparency = true;
        bannerContainer.setAttribute("allowtransparency", "true");
        return bannerContainer;
    }

    /**
     * Creates the 'center' version of the banner and inserts it into the DOM.
     */
    function createCenterBanner(config) {
        var outerContainer = document.createElement('div');
        outerContainer.id = 'dothiv-outer';
        outerContainer.style.zIndex = 1;

        // Create banner iframe
        var bannerContainer = createIframeElement('center');

        // Create background HTML structure
        var bannerBackground = document.createElement('div');
        bannerBackground.id = 'dothiv-background';

        // If we have to deal with IE and it's running in Quirks mode...
        if(navigator.appName.indexOf("Internet Explorer")!=-1 && document.compatMode!=='CSS1Compat')
            bannerContainer.style.position = 'absolute';

        // Specials for IE6 standard mode
        if (isIE(6) && document.compatMode=='CSS1Compat') {
            bannerContainer.style.position = 'absolute';
            bannerBackground.style.height = '1200px';
        }

        outerContainer.appendChild(bannerBackground);
        outerContainer.appendChild(bannerContainer);

        document.body.insertBefore(outerContainer, null);

        // Insert CSS rules
        includeCSS();

        // Register event for removing the banner when clicking on background
        document.getElementById("dothiv-background").onclick = function() {
            document.body.removeChild(document.getElementById('dothiv-outer'));
        };
    }

    /**
     * Creates the 'right' version of the banner and inserts it into the DOM.
     */
    function createRightBanner(config) {
        // Create banner iframe
        var bannerContainer = createIframeElement('right');
        document.body.insertBefore(bannerContainer, null);

        // If we have to deal with IE and it's running in Quirks mode...
        var msie = getIE();
        if(navigator.appName.indexOf("Internet Explorer")!=-1 && document.compatMode!=='CSS1Compat') {
            bannerContainer.style.position = 'absolute';
            bannerContainer.style.bottom = '120px';
            bannerContainer.style.height = '56px'; // 48 + 8
            bannerContainer.style.right = '0';
        } else if (msie <= 9 && document.compatMode==='CSS1Compat') {
            bannerContainer.style.bottom = '240px';
            bannerContainer.style.right = '-240px';
            bannerContainer.style.height = '89px'; // 48 + 36 + 5
        }

        // Insert CSS rules
        includeCSS();

        if(navigator.appName.indexOf("Internet Explorer")!=-1 && document.compatMode!=='CSS1Compat') {
            bannerContainer.onmouseover = function() {
                bannerContainer.style.height = '84px'; // 48 + 36
            };
            bannerContainer.onmouseout = function() {
                bannerContainer.style.height = '56px'; // 48 + 8
            };
        } else if (msie <= 9 && document.compatMode==='CSS1Compat') {
            bannerContainer.onmouseover = function() {
                bannerContainer.style.right = '-212px'; // 240px - (36 - 8)
            };
            bannerContainer.onmouseout = function() {
                bannerContainer.style.right = '-240px';
            };
        } else {
            if (!isTouchDevice()) {
                // Register event for mouseover on iframe if messaging is not supported
                if (!hasMessaging()) {
                    bannerContainer.onmouseover = function() {
                        bannerContainer.className = 'dothiv-clickcounter-right dothiv-rb-mouseover';
                    };
                    bannerContainer.onmouseout = function() {
                        bannerContainer.className = 'dothiv-clickcounter-right';
                    };
                }
            }
        }
    }

    function createTopBanner(config) {
        // Create banner iframe
        var bannerContainer = createIframeElement('top');
        document.body.insertBefore(bannerContainer, null);

        // Insert CSS rules
        includeCSS();

        // Register event for mouseover on iframe
        bannerContainer.onmouseover = function() {
            bannerContainer.style.height = '90px';
        };
        bannerContainer.onmouseout = function() {
            bannerContainer.style.height = '60px';
        };
    }
})();
