var Kraken = require('kraken');
var fs = require('fs');

var kraken = new Kraken({
  'user': 'wpd-mls',
  'pass': 'kiwi149',
  'apikey': 'e67a882ba96e03b81d11073474126478'
});

var root = '/home/upload_pic/optimize';
var getAllFiles = function(root) {
	var res = []
	var files = fs.readdirSync(root)
	for(var f in files) {
		var path = root + '/' + files[f];
		var st = fs.lstatSync(path);
		if (!st.isDirectory()) res.push(path);
		else res = res.concat(getAllFiles(path));
	}
	return res
}
var pic_files = getAllFiles(root);
for (var i in pic_files) {
	var image = pic_files[i];
	kraken.upload(image, function(status) {
	  console.log(status)
	  if (status.success) {
		console.log('~~~~~~~~~~~Success. Optimized image URL: %s', status.krakedURL);
	  } else {
		opt_images[image] = status.error;
		console.log('Fail. Error message: %s', status.error);
	  }
	});
}

/*
var image = {
	'url' : 'http://imgtest-dl.meiliworks.com/img/_o/a1/e4/8deab01b72fade87ce7bcde2e2a2_640_240.jpg'
};

kraken.optimize(image, function(status) {
	console.log(status)
	if (status.success) {
		console.log('Success. Optimized image URL: %s', status.krakedURL);
	} else {
		console.log('Fail. Error message: %s', status.error);
	}
});
*/
