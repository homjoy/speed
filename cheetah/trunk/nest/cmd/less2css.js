var fs = require('fs')
    ,path = require('path')
//	,exec = require('child_process').exec;
var less = require('./config/settings.json').less
if (!less) throw 'less is not config'
less = require(less)
function printLog(logTxt , code){
	if (logTxt) console.log(logTxt)
	if (code >= 0) process.exit(code)
	}

function mkdirp(p){
    p = p.split('/');
    var pathnow = '';
    p.map(function(pi){
        pathnow += pi + '/';
        if (!fs.existsSync(pathnow) ) {
			try{
				fs.mkdirSync(pathnow);
			}catch(err){
				console.log(err)
			}
            }
        });

    }

function genCss(filepath){
	var cssFile = path.resolve( filepath.replace('/less/','/css/').replace('.less' , '.css')  );
	var dir = path.dirname(cssFile)
	if (!fs.existsSync(dir)){
		mkdirp(dir)
		}
	
	function errTxt(err){
		var logTxt = 'Parse error : '+ filepath 
		printLog(logTxt)
		
		if ('extract' in err){
			logTxt = err.type + ' Error : [' + err.message + '] at [' +err.line +':' + err.column + "] in "+ err.filename +"\n"
					+ err.extract.join("\n ") + "\n=================\n"  
		}else{
			logTxt = err.type + ' Error : ' + err.message 
			}

		fs.writeFile(cssFile , logTxt , function(){})
		printLog(logTxt , 2)
		//console.log(err)
		}
	fs.readFile (filepath , 'utf8' , function (err ,file) {
			var parser = new(less.Parser)({
				paths: [filepath.substr(0 , filepath.lastIndexOf('/'))]
				})
			parser.parse(file , function(err ,tree){
				if (err) {
					return errTxt(err) 
					}
				try {
					file = tree.toCSS({compress:true , yuicompress: true })
					//file = tree.toCSS({})
				}catch(err){
					return errTxt(err) 
					}
				fs.writeFile(cssFile , file , function(err){
					if (err){
						return printLog('Write error :'+ cssFile,3)
						}

					if (!process.env.child)
						console.log(cssFile)
					else
						printLog('' , 0)
					})

				})
		})
}
var args = process.argv.splice(2)
args.forEach(function(filepath){
	genCss(filepath)
})

