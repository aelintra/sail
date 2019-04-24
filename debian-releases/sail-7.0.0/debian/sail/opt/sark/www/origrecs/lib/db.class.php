<?php

class DB {
	private static $connection = null;
	private function __construct() {}

	public static function instance() {

		if (self::$connection == null) {
			global $AUTH_LOGIN;
			try {
				self::$connection = new PDO($AUTH_LOGIN['pdo_connection_string']);
			} catch (PDOException $e) {
				echo 'Error connecting to the database. See error logs for details.';
				error_log('Available PDO drivers: ' . var_export(PDO::getAvailableDrivers(), true));
				throw $e;
				exit();
			}
		}

		return self::$connection;
	}
}
