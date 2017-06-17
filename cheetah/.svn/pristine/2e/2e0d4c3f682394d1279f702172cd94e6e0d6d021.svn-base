var util = require('util')
    ,cryto = require('crypto')
	,fs = require('fs')
//status|X-Real-IP|uri|cookie|request time|error message(error log)
var accessLogId = (config.etc.debug && config.etc.debug.length > 5 )? config.etc.debug + 'access-' +date('ymd') + '.log' : false
	,errorLogId = (config.etc.debug && config.etc.debug.length > 5 )? config.etc.debug + 'error-' +date('ymd') + '.log' : false
	,dataErrLogId = (config.etc.debug && config.etc.debug.length > 5 )? config.etc.debug + 'renderError-' +date('ymd') + '.log' : false
	

exports.accessLog = function (status , req , msg){
	var headers = req.headers
		,logTxt = [status , headers['clientIp'] || headers['x-forwarded-for'] || headers['x-real-ip'] ,
					req.url , headers.seashell , req.__request_time , msg || ''].join(' | ')
	mkLog(accessLogId, logTxt)
	}
exports.errorLog = function (){
	var logTxt = new Date + ' | ' + Array.prototype.join.call(arguments , ' | ')
	mkLog(errorLogId , logTxt)
	}
exports.dataErrLog = function(logTxt){
	mkLog(dataErrLogId , logTxt)
	
	}
function mkLog(file , logTxt){
	if (file)
		fs.appendFile(file , logTxt + "\n", function(){}) 
	else
		console.log(logTxt)
	}

function var_dump(obj,showHidden,depth){
	if ('undefined' == typeof showHidden) { showHidden = true;}
	if ('undefined' == typeof depth) { depth = 2;}
	console.log(util.inspect(obj, showHidden , depth));
}

function assert(condition , message){
	if (! condition){
		console.log('ABORT: ' + message + '\n\t' + exports.displayStack(1).join('\n\t'))
		process.exit(1)
		} 
	
	}
function format(str , params){
	params.unshift(str);
	return util.format.apply(null , params);
	}
function cloneObj(obj){
	var ret = {};
	for(var keys = Object.keys(obj), l = keys.length; l; --l)
		 ret[ keys[l-1] ] = obj[ keys[l-1] ];

	return ret;
	}
function isUnDefined (varObj){
	return ('undefined' == typeof varObj);
	}

function uniqid (prefix, more_entropy) {
	// +   original by: Kevin van Zonneveld
	// (http://kevin.vanzonneveld.net)
	// +    revised by: Kankrelune (http://www.webfaktory.info/)
	// %        note 1: Uses an internal counter (in php_js
	// global) to avoid collision
	// *     example 1: uniqid();
	// *     returns 1: 'a30285b160c14'
	// *     example 2: uniqid('foo');
	// *     returns 2: 'fooa30285b1cd361'
	// *     example 3: uniqid('bar', true);
	// *     returns 3: 'bara20285b23dfd1.31879087'
	if (typeof prefix == 'undefined') {
		prefix = "";
	}

	var retId;
	var formatSeed = function (seed, reqWidth) {
		seed = parseInt(seed, 10).toString(16); // to hex str
		if (reqWidth < seed.length) { // so long we split
			return seed.slice(seed.length - reqWidth);
		}
		if (reqWidth > seed.length) { // so short we pad
			return Array(1 + (reqWidth - seed.length)).join('0') + seed;
		}
		return seed;
	};

	if (!this.php_js) {
		this.php_js = {};
	}
	if (!this.php_js.uniqidSeed) { // init seed with big random int
		this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
	}
	this.php_js.uniqidSeed++;

	retId = prefix; // start with prefix, add current milliseconds hex string
	retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
	retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
	if (more_entropy) {
		retId += (Math.random() * 10).toFixed(8).toString();
	}

	return retId;
}

function date (format, timestamp) {
    // Format a local date/time  

    var jsdate, f, formatChr = /\\?([a-z])/gi,
        formatChrCb,
        // Keep this here (works, but for code commented-out
        // below for file size reasons)
        //, tal= [],
        txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    function  _pad (n, c) {
            if ((n = n + '').length < c) {
                return new Array((++c) - n.length).join('0') + n;
            }
            return n;
        }
    function formatChrCb  (t, s) {
        return f[t] ? f[t]() : s;
    	}
    f = {
        // Day
        d: function () { // Day of month w/leading 0; 01..31
            return _pad(f.j(), 2);
        },
        D: function () { // Shorthand day name; Mon...Sun
            return f.l().slice(0, 3);
        },
        j: function () { // Day of month; 1..31
            return jsdate.getDate();
        },
        l: function () { // Full day name; Monday...Sunday
            return txt_words[f.w()] + 'day';
        },
        N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
            return f.w() || 7;
        },
        S: function () { // Ordinal suffix for day of month; st, nd, rd, th
            var j = f.j();
            return j > 4 && j < 21 ? 'th' : {1: 'st', 2: 'nd', 3: 'rd'}[j % 10] || 'th';
        },
        w: function () { // Day of week; 0[Sun]..6[Sat]
            return jsdate.getDay();
        },
        z: function () { // Day of year; 0..365
            var a = new Date(f.Y(), f.n() - 1, f.j()),
                b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 864e5) + 1;
        },
 
        // Week
        W: function () { // ISO-8601 week number
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                b = new Date(a.getFullYear(), 0, 4);
            return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
        },
 
        // Month
        F: function () { // Full month name; January...December
            return txt_words[6 + f.n()];
        },
        m: function () { // Month w/leading 0; 01...12
            return _pad(f.n(), 2);
        },
        M: function () { // Shorthand month name; Jan...Dec
            return f.F().slice(0, 3);
        },
        n: function () { // Month; 1...12
            return jsdate.getMonth() + 1;
        },
        t: function () { // Days in month; 28...31
            return (new Date(f.Y(), f.n(), 0)).getDate();
        },
 
        // Year
        L: function () { // Is leap year?; 0 or 1
            return new Date(f.Y(), 1, 29).getMonth() === 1 | 0;
        },
        o: function () { // ISO-8601 year
            var n = f.n(),
                W = f.W(),
                Y = f.Y();
            return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
        },
        Y: function () { // Full year; e.g. 1980...2010
            return jsdate.getFullYear();
        },
        y: function () { // Last two digits of year; 00...99
            return (f.Y() + "").slice(-2);
        },
 
        // Time
        a: function () { // am or pm
            return jsdate.getHours() > 11 ? "pm" : "am";
        },
        A: function () { // AM or PM
            return f.a().toUpperCase();
        },
        B: function () { // Swatch Internet time; 000..999
            var H = jsdate.getUTCHours() * 36e2,
                // Hours
                i = jsdate.getUTCMinutes() * 60,
                // Minutes
                s = jsdate.getUTCSeconds(); // Seconds
            return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
        },
        g: function () { // 12-Hours; 1..12
            return f.G() % 12 || 12;
        },
        G: function () { // 24-Hours; 0..23
            return jsdate.getHours();
        },
        h: function () { // 12-Hours w/leading 0; 01..12
            return _pad(f.g(), 2);
        },
        H: function () { // 24-Hours w/leading 0; 00..23
            return _pad(f.G(), 2);
        },
        i: function () { // Minutes w/leading 0; 00..59
            return _pad(jsdate.getMinutes(), 2);
        },
        s: function () { // Seconds w/leading 0; 00..59
            return _pad(jsdate.getSeconds(), 2);
        },
        u: function () { // Microseconds; 000000-999000
            return _pad(jsdate.getMilliseconds() * 1000, 6);
        },
 
 
        I: function () { // DST observed?; 0 or 1
            // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
            // If they are not equal, then DST is observed.
            var a = new Date(f.Y(), 0),
                // Jan 1
                c = Date.UTC(f.Y(), 0),
                // Jan 1 UTC
                b = new Date(f.Y(), 6),
                // Jul 1
                d = Date.UTC(f.Y(), 6); // Jul 1 UTC
            return 0 + ((a - c) !== (b - d));
        },
        O: function () { // Difference to GMT in hour format; e.g. +0200
            var tzo = jsdate.getTimezoneOffset(),
                a = Math.abs(tzo);
            return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
        },
        P: function () { // Difference to GMT w/colon; e.g. +02:00
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2));
        },
        T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
        
            return 'UTC';
        },
        Z: function () { // Timezone offset in seconds (-43200...50400)
            return -jsdate.getTimezoneOffset() * 60;
        },
 
        // Full Date/Time
        c: function () { // ISO-8601 date.
            return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
        },
        r: function () { // RFC 2822
            return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
        },
        U: function () { // Seconds since UNIX epoch
            return jsdate.getTime() / 1000 | 0;
        }
    };

	jsdate = ((typeof timestamp === 'undefined') ? new Date() : // Not provided
	(timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
	new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
	)
	return format.replace(formatChr, formatChrCb);
}

exports.date = date

exports.uuid = uniqid 

exports.md5 = function md5(str) {
    return str ? cryto.createHash('md5').update(str.toString()).digest("hex") : '';
    }
exports.isEmpty = function isEmpty(o){
    if ('object' == typeof o) {
        for (var k in o){
            return false;
            }
        return true;
     }else{
       return !o;     
            }
    }

exports.array_merge = function (f){
    var ret = f;
	var args = Array.prototype.slice.call(arguments,1);
	var argLen = args.length
	for (var i = 0 ; i < argLen; i++){
		var o = args[i]
		if (!o) continue
		for (var k in o){
			ret[k] = o[k]	
			}
		}
	return ret;
}

exports.var_dump = var_dump;
exports.isUnDefined = isUnDefined;
exports.cloneObj = cloneObj;
exports.format = format;

/*
*继承并创建新对象*/
exports.inherit = function (clsContruct , supClsObj,  override){
    
    //override = !!override;
	if ('function' == typeof supClsObj) {
        	supClsObj = new supClsObj;
	}
    clsObj = new clsContruct;


     for (var attr in supClsObj){
         if (override || !clsObj[attr]){
               if( supClsObj.hasOwnProperty(attr) ) {
                     clsObj[attr] = supClsObj[attr];
                 }else{
                     clsContruct.prototype[attr] = supClsObj[attr];
                  }
            };
       }
	return clsObj;
}
/*construct my exception*/
exports.exception = function (code , message){
    var e = new Error(code + ':' + message)
    e.code = code
    return e
    };
