<?php

session_start();

require 'lib/db.class.php';
require 'lib/auth.class.php';

$errors = array();
$username = '';
$focus = 'login';

if (array_key_exists('action', $_GET) && $_GET['action']=='logout') {
	Auth::logout();
	header('Location: ' . $_SERVER['PHP_SELF']);
	exit();
}

if (array_key_exists('action', $_GET) && $_GET['action']=='login' && array_key_exists('user_submit', $_POST)) {
	// Process the login attempt
	$username = array_key_exists('user_login', $_POST) ? $_POST['user_login'] : null;
	$password = array_key_exists('user_pass', $_POST) ? $_POST['user_pass'] : null;
	if ($username) {
		$focus = 'pass';
	}
	try {
		$token = Auth::login($username, $password);
		if ($token !== false) {
			$_SESSION['auth'] = array('tenant'=>$username);
			header('Location: ' . $_SERVER['PHP_SELF']);
			exit();
		}
		unset($password, $token);
	}
	catch (AuthError $e) {
		$errors[] = $e->getMessage();
	}
}

if (array_key_exists('auth', $_SESSION)) {
	$TENANT = $_SESSION['auth']['tenant'];
} else {

	require 'lib/views/login.php';

	// Can't go any further now
	exit();
}
