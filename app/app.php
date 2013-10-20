<?php
session_start();
ob_start();
error_reporting( E_ALL | E_STRICT );

//General Functions
function define_constants() {
	//APPDIR is the directory where this file resides
	define( "APPDIR", realpath( dirname( __FILE__ ) ) . "/../");

	//Figure out our app-url by config file
	if( !is_readable( APPDIR . "/config.ini" ) ) {
		throw new Exception("No config.ini found, expected one at " . APPDIR);
	}

	$config = parse_ini_file( APPDIR . "/config.ini" );
	if( !isset( $config['path'] ) ) {
		throw new Exception("'path' key is missing in " . APPDIR . "/config.ini" );
	}

	define( "URLPATH", $config['path'] );
}

//put into a function as not to polute the global namespace
define_constants();

//Class Definitions
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
}

class Session {
	private static $user;

	/* Load any needed data, check $_SESSION variable to see what can be loaded*/
	static function load() {
		if( isset( $_SESSION ) ) {
			if( isset( $_SESSION['user_id'] ) ) {
				Session::$user = new User($_SESSION['user_id']);
			}
		}
	}

	function setUser(User $user) {
		if( (int)$user->id == 0 ) {
			throw new Exception("User->id == 0" );
		}
		$_SESSION['user_id'] = $user->id;
		Session::$user = $user;
	}

	function getUser() {
		return Session::$user;
	}
}

class DB {
	private static $pdo;

	function setup() {
		//create PDO
		DB::$pdo = new PDO( "sqlite:" . APPDIR . "/database.sqlite", "", "" );
		DB::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//enable foreign_key support
		DB::$pdo->exec("PRAGMA foreign_keys = ON;");
	}

	function getPDO() {
		if( !DB::$pdo ) {
			DB::setup();
		}
		return DB::$pdo;
	}
}

//Load Session data
Session::load();
