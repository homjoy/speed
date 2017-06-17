/*
最热
/guang/hot				=>		/wapHot/hot
最新		
/guang/new			=>		/wapHot/latest		
分类
/guang/catalog/dress	=>		/wapCategory/dress
/guang/catalog/shoes	=>		/wapCategory/shoes
/guang/catalog/bag	=>		/wapCategory/bag
/guang/catalog/access	=>		/wapCategory/access
/guang/catalog/jiaju	=>		/wapCategory/jiaju

属性词
/guang/attr/属性词ID	=>		/wapAttrNew/attr?attr_id=属性词ID
/guang/attr/34380		=>		/wapAttrNew/attr?attr_id=34380（例）
杂志社
/group/杂志社ID			=>		/wapGroup/group?group_id=杂志社ID
/group/13505237		=>		/wapGroup/group?group_id=13505237（例）
book
/book/BOOKID			=>		/wapBook/book?book_id= BOOKID
/book/欧美				=>		/wapBook/book?book_id=欧美（例）
单宝页
/share/推ID				=>		/wapSingleGoods/show?t= 推ID
/share/504721781		=>		/wapSingleGoods/show?t= 504721781（例）
*/

var querystring = require('querystring')
	,urlHandle = require('url')

var map = {
	"welcome/_index/" : "/agreement/notice/"
	,"guang/index/hot" : "/guang/hot"
	,"guang/index/new" : "/guang/new"
	,"guang/catalog/dress" : "/guang/catalog/?nid=827&pnid=827" 
	,"guang/catalog/shoes" : "/guang/catalog/?nid=707&pnid=707uu" 
	,"guang/catalog/bag" : "/guang/catalog/?nid=895&pnid=895 " 
	,"guang/catalog/access" : "guang/catalog/?nid=1017&pnid=1017" 
	,"guang/catalog/jiaju" : "/" 
	,"guang/attr" : "/guang/attr/%s" 
	,"magazine/index" : "/group/%s" 
	,"dict/show" : "/book/%s"
	,"share/index" : "/share/%s"
	,"share/item" : "/share/item/%s"
	,"club/single" : "/club/single/%s"
	}
exports.getUrl = function(mods , request){
	var getParam =	request.__get 
		,refer = request.headers.referer
	var wapHost = 'http://m.meilishuo.com'
	var full = mods.join('/')
	if (! mods[2] && full != 'welcome/_index/') return false
	
	refer = refer && urlHandle.parse(refer)	
	var fromTabo = refer && (refer.hostname.indexOf('tmall.com')>-1 || refer.hostname.indexOf('taobao.com') > -1 )
	///console.log(refer , fromTabo)

	var ret = false
		,hadQ = false

	if (full in map) {
		if (!fromTabo && ('welcome/_index/' == full)) full = 'guang/index/hot' 
		ret = wapHost + map[full] 
	}else{
		var pattern = mods[0] + '/' + mods[1]
		if (pattern in map) {
			hadQ = true
			ret = wapHost + map[pattern].replace('%s',mods[2]) 
		}
	}
	if (ret){
		var getQstr = querystring.stringify(getParam)
		if (getQstr.length) ret += (ret.indexOf('?') > -1? '&' :'?') + getQstr
	}

	return ret 
	
	}
