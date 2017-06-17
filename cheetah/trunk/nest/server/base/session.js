var session_cookie_name = 'nsessid';
var handler;
switch (config.session.mode) {
    case 'memory':
        handler = require (config.path.base + '/sessStrorge/memory.js');
        break;
    case 'memcache':
        handler = require (config.path.base + '/sessStrorge/memcache.js');
        handler.init(config.session.path);
        break;
    }

/*generate sessionid*/
function genSessID() {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/', // base64 alphabet
         ret = '';

     for (var bits=24; bits > 0; --bits){
             ret += chars[0x3F & (Math.floor(Math.random() * 0x100000000))];
          }
     return ret;
    
    }
/*
create new session object
note  should use callback param while not in memory mode ,it's async
*/  
function session(id ,callBack){
    this.sid = id ;
    if (callBack) {
        var mSelf = this;
        this.data = {};
        handler.get(id , function(err ,result){
            if (!err )  mSelf.data =  result;
            callBack && callBack(mSelf);
            }) ;
      } else{
           this.data = handler.get(id);
           }
    }

session.prototype = {
    get : function(key) {
        return this.data[key];
        //this.writeBack();
        },
    getAll : function() {
        return this.data;
        },
    set : function(key,value) {
        this.data[key] = value;
        this.writeBack();
        },
    reset : function ( s){
        this.data = s;
        this.writeBack();
        },
    remove : function(key){
        delete this.data[key];
        this.writeBack();
        },    
    clear : function (){
       handler.remove (this.sid); 
        },    
    writeBack : function(){
        handler.set (this.sid , this.data);
        }
    }
exports.getHandler = function(req , res){
    function start(callBack) {
        if (! req.__cookies) {
         //read cookie 
            var cookies = {};
            req.headers.cookie && req.headers.cookie.split(';').forEach(function( cookie ) {
                var parts = cookie.split('=');
                cookies[ parts[ 0 ].trim() ] = ( parts[ 1 ] || '' ).trim();
                });
            req.__cookies = cookies;
         } else {
            cookies = req.__cookies;    
           }

        var sessid = cookies[session_cookie_name]; 

        if (!sessid) {
            sessid = genSessID();
            //write cookie;
            res.setHeader('set-cookie' ,session_cookie_name + '=' + sessid + ';Path=/;');
        
        } 
   


        return new session( sessid , callBack); 
        }
    return {start : start};
    }    
