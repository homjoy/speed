var nMemcached = require('memcached'),
    memcached ;
var maxExpire = 3600 * 48;
exports.init = function (path , ttl) {
    memcached = new nMemcached( path );
    if (ttl ) { maxExpire = ttl;}
    }
exports.get = function(key,callBack ){
    memcached.get( key, callBack); 
    }

exports.set = function( key ,value ,callBack ) {
    memcached.set( key, value, maxExpire , callBack);
    
    }
    

exports.remove = function(key , callBack){
   // memcached.set( key, null, 1, callBack);
    memcached.del( key, callBack);
    
    }

