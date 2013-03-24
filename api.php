<?php
require 'snaphax.php';
$call = isset($_GET['call']) ? $_GET['call'] : '';
switch($call){
	case 'login':
		$username = isset($_POST['username']) ? $_POST['username'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		
		if(!empty($username) && !empty($password)){
			$opts = array();
			$opts['username'] = $username;
			$opts['password'] = $password;
			$opts['debug'] = 0;
		
			$s = new Snaphax($opts);
			$result = $s->login();
			echo json_encode($result);
		}
	break;
	case 'getSnap':
		$username = isset($_POST['username']) ? $_POST['username'] : '';
		$auth_token = isset($_POST['auth_token']) ? $_POST['auth_token'] : '';
		$s = new Snaphax(array(
			'username' => $username,
			'auth_token' => $auth_token
		));
		$snap = isset($_POST['snap']) ? $_POST['snap'] : '{}';
		$snap = json_decode($snap);
		if($snap){
			$id = $snap->id;
			if($snap->st == SnapHax::STATUS_NEW){
				$blob_data = $s->fetch($snap->id);
				if($blob_data){
					if($snap->m == SnapHax::MEDIA_IMAGE){
						$ext = '.jpg';
					}else{
						$ext = '.mp4';
					}
					ob_clean(); 
					$imageData = base64_encode($blob_data);
					$src = 'data: image/jpeg;base64,'.$imageData;
					echo $src;
					ob_end_flush();
				}
			}
		}
	break;
	default:
		echo '{"message": "Invalid api call!"}';
	break;
}