<?php

class AdminError extends Exception {}

$ADMIN = '';

function admin_login($username, $password) {
	global $AUTH_LOGIN;

	if (empty($password)) {
		throw new AdminError("The provided password is empty");
	}

	if ($username == $AUTH_LOGIN['admin']['username'] && $password == $AUTH_LOGIN['admin']['password']) {
		return true;
	}
	throw new AdminError('Invalid username/password');
}

session_start();

$errors = array();
$username = '';
$focus = 'login';

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;


switch ($action) {
    case 'logout':
        unset($_SESSION['admin']);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    break;

    case 'login':
        if (array_key_exists('user_submit', $_POST)) {
            // Process the login attempt
            $username = array_key_exists('user_login', $_POST) ? $_POST['user_login'] : null;
            $password = array_key_exists('user_pass', $_POST) ? $_POST['user_pass'] : null;
            if ($username) {
                $focus = 'pass';
            }
            try {
                $token = admin_login($username, $password);
                if ($token !== false) {
                    $_SESSION['admin'] = array('username'=>$username);
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
                unset($password, $token);
            }
            catch (AdminError $e) {
                $errors[] = $e->getMessage();
            }
        }
    break;
}

if (array_key_exists('admin', $_SESSION)) {
	$ADMIN = $_SESSION['admin']['username'];
} else {

	require 'lib/views/login.php';

	// Can't go any further now
	exit();
}


require 'lib/db.class.php';

function create_salt() {
	return hash('md5', uniqid(mt_rand(), true));
}

switch ($action) {
	case 'adduser':
        try {
            if (empty($_POST['password'])) {
                throw new AdminError('Password can not be empty');
            }

            if ($_POST['password'] != $_POST['password2']) {
                throw new AdminError('Passwords do not match');
            }

            $user = $_POST['username'];
            $salt = create_salt();
            $pass = hash('sha512', $_POST['password'].$salt);

            //DB::instance()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            DB::instance()->beginTransaction();
            $statement = DB::instance()->prepare('INSERT OR IGNORE INTO `users` (`name`, `password`, `salt`) VALUES (:name, :password, :salt)');
            $statement->execute(array('name'=>$user, 'password'=>$pass, 'salt'=>$salt));

            if ($statement->rowCount() == 0) {
            	$statement = DB::instance()->prepare('UPDATE `users` SET `password`=:password, `salt`=:salt WHERE `name`=:name');
            	$statement->execute(array('name'=>$user, 'password'=>$pass, 'salt'=>$salt));
            }
            DB::instance()->commit();

            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        }
        catch (AdminError $e) {
        	$errors[] = $e->getMessage();
        }
    break;

	case 'deleteuser':
        $statement = DB::instance()->prepare('DELETE FROM `users` WHERE `id`=:id');
        $statement->execute(array('id'=>$_GET['id']));

        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    break;
}
