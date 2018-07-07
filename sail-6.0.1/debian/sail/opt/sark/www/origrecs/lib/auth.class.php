<?php

class AuthError extends Exception {}

class Auth {

	// Cannot create instances of this class
	private function __construct() {
	}

	public static function _find_salt($username) {
		$statement = DB::instance()->prepare('SELECT `salt` FROM `users` WHERE `name`=:username LIMIT 1');
		$statement->execute(array('username'=>$username));

		return $statement->fetchObject()->salt;
	}

	public static function login($username, $password) {
		if (empty($password)) {
			throw new AuthError("The provided password is empty");
		}

		// load the salt
		$salt = self::_find_salt($username);

		$statement = DB::instance()->prepare('SELECT `name` FROM `users` WHERE `name`=:username AND `password`=:password');

		$statement->execute(array('username'=>$username, 'password'=>hash('sha512', $password.$salt)));
		$result = $statement->fetchObject();

		if ($result) {
			return $result->name;
		}

		throw new AuthError('Invalid username/password');
	}

	public static function logout() {
		unset($_SESSION['auth']);
	}
}
