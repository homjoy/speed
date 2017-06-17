 var mongodb = require('mongodb');
 var ObjectID = require('mongodb/lib/mongodb/bson/objectid').ObjectID;
 var server = new mongodb.Server("127.0.0.1", 27017, {}); 
 var db = new mongodb.Db('easywork' , server );

function query( opt ) {
    /**/
    return function(evt ,collect , records , setting){
        function queryFind (err, cursor) {
            cursor.toArray(function(err, docs) {
               // console.log("Returned #" + docs.length + " documents");
                //console.log(docs);
                if (err) throw new Error(err);
                evt && evt(docs);
             })
         }   
        function queryBack (err,doc) {
             //console.log(arguments);
             if (err) throw new Error(err);
              evt && evt(doc);
    
         }   
        db.open(function(err,db) {
            if (err) return;
            db.collection(collect , function(err , collection){
                if (err) return;
                switch (opt){
                    case 'insert':
                        /*
                        [{'a':1}, {'a':2}, {'b':3}]
                        */
                    case 'count':
                    case 'remove':
                        collection[opt](records , queryBack );
                        break;
                    case 'update':
                         collection[opt](records , setting ,{safe:true}, queryBack );
                         break;
                    case 'updateAll':
                        /*{$set:{b:2}}
                        {'$inc' :{'b' :2}} 
                        */
                         collection[opt](records , setting ,{safe:true, multi:true}, queryBack );
                         break;
                    case 'findOne':
                        if (setting)   collection[opt](records , setting , queryBack );
                         else  collection[opt](records , queryBack );
                            
                        break;
                    case 'find':
                        /*
                         collection.find({'a':1}, 
                                           {'a':{'$gt':1}},
                                           {'a':{'$in':[1,2]}} 
                        collection.find({}, {'skip':1, 'limit':1, 'sort':'a'}, 
                        */

                        if (setting) {
                            collection[opt](records , setting , queryFind );
                        } else {
                            collection[opt](records , queryFind );
                            }
                        break;
                    }
              } );
                                             
        }); 
    }
}

['find' , 'findOne'  , 'insert' , 'count' , 'update' , 'updateAll' ,'remove'].forEach(function(opt){
    exports[opt] = query(opt);
    })
exports.convertId = function(id) {
    return new ObjectID(id);
    }
    
    
    
