var data = {};
exports.get = function(key , callBack){

   var result =  data[key] || {}; 
   if (callBack){
       callBack(null, result);
    }else{
       return result;    
           }
    }

exports.set = function( key ,value ,callBack) {
    data[key] = value;
    callBack && callBack();
    
    }
    

exports.remove = function(key, callBack){
    delete data[key];
    callBack && callBack();
    
    }

