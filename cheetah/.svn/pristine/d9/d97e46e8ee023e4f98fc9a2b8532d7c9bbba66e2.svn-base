/**
 * this is not work in nodejs ,@see below
 *
 */
//Object.prototype.forEach = function(callback){
//
//};

/**
 * @see http://davidcoallier.tumblr.com/post/1527191998/extending-objects-in-node-js
 * @param callback
 */
Object.defineProperty(Object.prototype, "forEach", {
    enumerable: false,
    value: function(callback) {
        //"use strict";
        var counter = 0,
            keys = Object.keys(this),
            currentKey,
            len = keys.length;
        var that = this;
        var next = function() {

            if (counter < len) {
                currentKey = keys[counter++];
                callback(that[currentKey], currentKey);
                next();
            } else {
                that = counter = keys = currentKey = len = next = undefined;
            }
        };
        next();
    }
});

exports = {};
//
//var obj = {
//    '1':
//    {
//        "service_id": "1",
//        "name": "视频设备",
//        "description": "视频设备",
//        "multizone": "1",
//        "type": "0",
//        "config": "{\n \"user\":[],\n \"time_start\":[-20,-10,20],\n \"time_end\":[-10,0,10]\n}",
//        "status": "1"
//    },
//'2':{
//    "service_id": "3",
//    "name": "投影设备",
//    "description": "投影设备",
//    "multizone": "0",
//    "type": "0",
//    "config": "{\n \"user\":[],\n \"time_start\":[-20,-10,20],\n \"time_end\":[-10,0,10]\n}",
//    "status": "1"
//}
//};
//
//
//
//obj.forEach(function(value, index){
//    "use strict";
//
//
//    console.log(bannar);
//});