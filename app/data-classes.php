<?php
class DB {
	private static $pdo;

	static function setup() {
		//create PDO
		if( !is_readable( APPDIR . "database.sqlite" ) )
			throw new Exception("No database.sqlite, run create-database.sh");

		DB::$pdo = new PDO( "sqlite:" . APPDIR . "database.sqlite", "", "" );
		DB::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//enable foreign_key support
		DB::$pdo->exec("PRAGMA foreign_keys = ON;");
	}

	static function getPDO() {
		if( !DB::$pdo ) {
			DB::setup();
		}
		return DB::$pdo;
	}
}

class Session {
	private static $user;

	/* Load any needed data, check $_SESSION variable to see what can be loaded*/
	static function load() {
		if( session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		if( session_status() != PHP_SESSION_ACTIVE ) {
			return false;
		}

		if( isset( $_SESSION ) ) {
			if( isset( $_SESSION['user_id'] ) ) {
				$user = new User();
				$user->load( $_SESSION['user_id'] );
				Session::setUser($user);
			}
		}

		return true;
	}

	static function destroy() {
		Session::$user = null;
		session_destroy();
	}

	static function setUser(User $user) {
		if( (int)$user->id == 0 ) {
			throw new Exception("User->id == 0" );
		}
		$_SESSION['user_id'] = $user->id;
		Session::$user = $user;
	}

	static function getUser() {
		return Session::$user;
	}
}

class User {
	public $id;
	public $password;

	function load($id) {
		//fetch the results and set our instance variables to them
		$statement = DB::getPDO()->prepare(
			"SELECT * FROM user WHERE id = ?"
		);
		$statement->execute( array( (int)$id ) );

		$statement->setFetchMode(PDO::FETCH_INTO, $this);
		$ret = $statement->fetch();
		if( $ret )
			$this->id = (int)$this->id;

		return $ret;
	}

	function store() {
		//no id, create a new user by passing "null" to the DB
		if( (int)$this->id == 0 ) {
			$this->id = null;
			$statement = DB::getPDO()->prepare(
				"INSERT INTO user VALUES (:id, :password);"
			);
		} else {
			$statement = DB::getPDO()->prepare(
				"UPDATE user SET password=:password WHERE id=:id;"
			);
		}

		$ret = $statement->execute( array(
			":id" => $this->id,
			":password" => $this->password,
		) );

		//grab the unique id from insertion if we created a new row
		if( $this->id == null && $ret )
			$this->id = (int)DB::getPDO()->lastInsertId();
	}

	static function login( $email, $password ) {
		//do query
		//fetch the results and set our instance variables to them
		$statement = DB::getPDO()->prepare(
			"SELECT id FROM user WHERE email = :email AND password = :password"
		);
		$statement->execute( array(
			":email" => $email,
			":password" => $password,
		) );

		$ret = $statement->fetch();
		if( $ret ) {
			//$ret['id'] is the id we want
			$user = new User();
			if( $user->load($ret['id']) ) {
				return $user;
			}
		}

		return null;
	}
}
