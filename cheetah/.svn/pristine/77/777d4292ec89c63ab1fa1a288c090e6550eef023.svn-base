exports.getHandler = function(req , res){
        var cookies = {};
		if (!('__addCookie' in req))	req.__addCookie = []
			
        if (! req.__cookies) {
         //read cookie 
            req.headers.cookie && req.headers.cookie.split(';').forEach(function( cookie ) {
                var parts = cookie.split('=');
                cookies[ parts[ 0 ].trim() ] = ( parts[ 1 ] || '' ).trim();
                });
            req.__cookies = cookies;
         } else {
            cookies = req.__cookies;    
         }
		 var host = req.headers.host
		 if (host) host = 'domain=.' + host.split('.').slice(-2).join('.') +';'
		 else host = ''

		function set(name , val , expires){
			if (!res) return
			if (arguments.length ==  1){
				var cookie_v = name
			}else{
				var cookie_v = name + '=' + val + ';Path=/;'+ host 
				if (expires)
					cookie_v += 'expires='+ expires.toGMTString()+';'

				cookies[name] = val
			}
			if (req.__addCookie.indexOf(cookie_v) < 0){
				req.__addCookie.push(cookie_v)
				try{
				res.setHeader('set-cookie' ,req.__addCookie );
				}catch(e){
					 console.log('Cookie unwrite' ,name , e)
				}
				}
		 }
		 function get(name) {
			return cookies[name];		 
		 }
		function clear(name){
			return set(name, '') 
		}

		 return {set:set , get :get, clear:clear};
   
    }    
