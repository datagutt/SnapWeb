<!doctype html>
<html>
<head>
	<title>SnapWeb</title>
	<script type="text/javascript" src="http://idioticphotos.com/layout/js/N1.min.js"></script>
	<link href="http://necolas.github.com/normalize.css/2.1.0/normalize.css" rel="stylesheet" />
	<script type="text/javascript">
		var config = {
			username: '',
			logged: 0,
			auth_token: ''
		};
		var data = {
			snaps: {}
		};
	</script>
	<style type="text/css">
		html, body {
			height: 100%;
			font-family: Verdana;
		}
	</style>
</head>
<body>
<div id="wrapper"> 
	<h1>SnapWeb</h1>
	<div id="user_login">
		<form method="post" action="api.php?call=login">
			<input type="text" name="username" />
			<br>
			<input type="password" name="password" />
			<br>
			<input type="submit" name="submit" value="Login!" />
		</form>
	</div>
	<div id="snapslist">
		<ul></ul>
	</div>
</div>
<div id="snap-popup">
	<canvas id="tehsnap" width="640" height="1136"></canvas>
</div>
<script>
(function(global){
	const STATUS_NEW = '?';
	const MEDIA_IMAGE = 0;
	const MEDIA_VIDEO = 1;
	var drawSnap = function(dataURI){
		var canvas = document.getElementById('tehsnap');
		var ctx = canvas.getContext('2d');
		var img = new Image;
		img.onload = function(){
			ctx.drawImage(img, 0, 0);
			console.log(img.width, img.height);
		};
		img.src = dataURI;
	};
	var getSnap = function(snap, callback){
		var result;
		if(N1 && N1.isFeature('ajax')){
			N1.ajax.post('api.php?call=getSnap',
			{
				'username': config.username,
				'snap': JSON.stringify(snap),
				'auth_token': config.auth_token
			},
			function(response){
				if(response && callback){
					callback(response);
				}
			});
		}
	};
	var makeSnapsList = function(){
		var snaps = data.snaps,
			snapsList = document.getElementById('snapslist').getElementsByTagName('ul')[0];
		document.body.onclick = document.body.ontouchend = function(){
			// clear canvas
		};
		for (key in snaps){
			var snap = snaps[key];
			var snapEl = document.createElement('li');
			snap.type = (snap.m == MEDIA_IMAGE) ? 'image' : 'video';
			snapEl['data-id'] = snap.id;
			snapEl['data-sn'] = snap.sn;
			snapEl['data-m'] = snap.m;
			snapEl['data-st'] = snap.st;
			if(snap.st !== 1){
				snapEl.style.textDecoration = 'line-through';
			}
			snapEl.innerHTML = '<p>Snap by ' + snap.sn + ' at ' + (new Date(snap.ts)).toString() + ' (' + snap.type + ')</p>';
			snapEl.onclick = function(){
			console.log(this);
				getSnap({
					'id': this['data-id'],
					'sn': this['data-sn'],
					'm': this['data-m'],
					'st': this['data-st']
				}, function(blob_data){
					//this['data-st'] = 0;
					drawSnap(blob_data);
				})
			};
			snapsList.appendChild(snapEl);
		}
	}
	var login = function(username, password, callback){
		var result;
		if(N1 && N1.isFeature('ajax')){
			N1.ajax.post('api.php?call=login',
			{
				'username': username,
				'password': password
			},
			function(response){
				result = eval('(' + response + ')');
				if(result && callback){
					callback(result);
				}
			});
		}
	};
	var onLogin = function(result){
		if(result){
			if(result.username && result.snaps && result.auth_token){
				config.username = result.username;
				config.auth_token = result.auth_token;
				data.snaps = result.snaps;
				var user_login = document.getElementById('user_login');
				user_login.style.display = 'none';
				makeSnapsList();
			}
			config.logged = result.logged ? 1 : 0;
			if(result && result.message){
				alert(result.message);
			}
		}
	}
	var loginEl = document.forms[0];
	if(loginEl){
		loginEl.onsubmit = function(e){
			// return false;
			var username, password;
			if((username = document.forms[0].username.value) && (password = document.forms[0].password.value)){
				login(username, password, onLogin);
			}
			if(e && N1.isHostMethod(e, 'preventDefault')){
				e.preventDefault();
			}
		}
	}
})(window);
</script>
</body>
</html>