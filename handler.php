<?php 
session_start();

$HOUSES = ['Arryn', 'Baratheon', 'Greyjoy', 'Lanister', 'Stark', 'Targaryen'];
$ERRORS_LIST = ['error_mail', 'error_pasword', 'error_name', 'error_select', 'error_info'];
$file_dir = 'users';
$users = scandir('/'.$file_dir);
$profile = ['name'=>'','password'=>'', 'mailbox'=>'', 'clan'=>'', 'aboutself'=>'', 'remember'=>false];

if (isset($_POST['submitsign'])) {

	$arr_userdata = $profile;

	if ( validateMail( $_POST['email'])) {
		$json_filename = $file_dir.'/'.$_POST['email'].'.json';
		$_SESSION['file_path'] = $json_filename;
	}
	if ( validatePassword( $_POST['password'])) {
		$arr_userdata['password'] = $_POST['password'];
	}
	
	if (isset($_SESSION['error_mail']) || isset($_SESSION['error_pasword'])) {
		header('Location: index.php');
		return;
	}
	if (isset($_POST['remember'])) {
		$arr_userdata['remember'] = true;
	} else {
		$arr_userdata['remember'] = false;
	}
	file_put_contents($json_filename, json_encode($arr_userdata));
	header('Location: form.php');
}

function formatJson($string) {
	str_replace("\n\r", ' ', $string);
	$raw = ['"',"'","\\"];
	for ( $i = 0; $i < count($raw); $i++) { 
		str_replace($raw[$i], '\\'.$raw[$i], $string);
	}
	return $string;
}

function resetErrors($list) {
	for ($i=0; $i < count($list); $i++) { 
		$the_error = $list[$i];
		if (isset($_SESSION[$the_error])) {
			unset($_SESSION[$the_error]);
		}
	}
}

function validateMail($email) {
	$mailPatern ='/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i';
	$users = scandir($GLOBALS['file_dir']);
	$profile = $email.'.json';
	if (isset($users[$profile])) {
		$_SESSION['erorr_mail'] = 'The email already exist';
		return false;
	} else if ($email === ''){
		$_SESSION['error_mail'] = 'Input an email';
		return false;	
	} else if(preg_match($mailPatern, $email)) {
		$_SESSION['user_mail'] = $email;
		return true;
	} else {
		$_SESSION['error_mail'] = 'Incorrect email';
		return false;
	}
}

function validatePassword($pass) {
	$passPatern = '/^\S{8,}$/';
	if (preg_match($passPatern, $pass)) {
		return true;
	} else if ($pass === ''){
		$_SESSION['error_pasword'] = 'Input a assword';
		return false;
	}else if ( strlen($pass) < 8 ) {
		$_SESSION['error_pasword'] = 'Password too short';
		return false;
	} else {
		$_SESSION['error_pasword'] = 'Incorrect password';
		return false;
	}
}

function validateName($name) {
	$namePattern = '/^[\w]{4,15}$/';
	if ( $name === '') {
		$_SESSION['error_name'] = 'Input a name';
		return false;
	} else if (strlen($name) < 4) {
		$_SESSION['error_name'] = 'Name too short';
		return false;
	}
	else if (preg_match($namePattern, $name)) {
		$_SESSION['user_name'] = $name;
		return true;
	}
	else {
		$_SESSION['error_name'] = 'Incorrect name';
		return false;
	}
}

?>
