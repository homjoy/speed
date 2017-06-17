function jqEach(item , callFn){
  item.forEach(function(v,k){
        callFn(k,v)
        })	
	
	}
exports.compile = function(str){
   var tpl = new Function("obj",
        "var p=[],jqEach = arguments.callee.jqEach;" +
        "with(obj){p.push('" +
        str
        .replace(/[\r\t\n]/g, " ")
        .split("<\?").join("\t")
        .replace(/((^|\?>)[^\t]*)'/g, "$1\r")
        .replace(/\t=(.*?)\?>/g, "',$1,'")
        .split("\t").join("');")
        .split("\?>").join("p.push('")
        .split("\r").join("\\'")
        + "');}return p.join('');");
   tpl.jqEach = jqEach 
   return tpl
}
