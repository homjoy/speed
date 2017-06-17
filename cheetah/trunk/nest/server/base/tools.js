var cookie = require(config.path.base + 'cookie.js'),
	crypto = require('crypto'),
	urlHandle = require('url'),
	querystring = require('querystring'),
    cryseed = config.etc.token || ''

exports.genToken = function(key , rand , stamp){
	rand = rand || Math.random().toFixed(2)
	//head['clientIp'] rand = (rand * Math.pow(2, Math.ceil(rand*10)) ).toFixed(2)
	var seed =  [key , rand ,  stamp || new Date().getTime()]
	var text = seed.join('|')
	var encrypt = crypto.createCipher('aes-256-cbc',cryseed)
	var crypted = encrypt.update(text, 'utf8', 'base64')
	crypted += encrypt.final('base64')

	/*
	function toConfuse(str){
		var rand = Math.ceil(Math.random() * str.length) % 10
		return  rand + str.substr(0, rand) + String.fromCharCode(65+(Math.random()*25)|0)  + str.substr(rand)
		}
	crypted = toConfuse( crypted)
	*/
	return crypted
}
exports.unGenToken = function(token  ){
	token = token.trim()
	if (!token || token.length<2) return false
	/*
	function unConfuse(str){
		var rand = str.charAt(0)|0
		str = str.substr(1)
		if (rand)
				str = str.substr(0,rand) + str.substr(rand+1)
		return str
	}
	token = unConfuse(token)
	*/
	try{
		var decipher = crypto.createDecipher('aes-256-cbc',cryseed)
		var dec = decipher.update(token, 'base64', 'utf8')
		dec += decipher.final('utf8')

		dec = dec.split('|')
		if (dec.length < 3) return false
		return {key: dec[0], salt : dec[1] ,stamp : dec[2]}

	}catch(err){
		dec = false
		}
	return dec
}

exports.isTokenLive = function(token , key , ttl){
	var dec = exports.unGenToken(token,key)
	if (!dec || !dec.stamp) return false
	if (dec.key != key ) return false
	return (parseInt(dec.stamp) + ttl*60000) > new Date
}
