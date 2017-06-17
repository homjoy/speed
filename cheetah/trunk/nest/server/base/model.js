var eventCache = {};
function Mmodel(){
	this.callee = null;
	return this;
}

Mmodel.prototype = {
	__toString : function () {
		return 'blahblah';
	} , 
	__return : function(){
		return Array.prototype.slice.call(arguments,0);
	}

}


exports.__create = function (mod , extFn){
        if (extFn ) { mod.prototype = extFn;}
        return function(){
                var modObj = base.inherit(mod , Mmodel);
                return mod.apply(modObj , arguments);
        }
}

