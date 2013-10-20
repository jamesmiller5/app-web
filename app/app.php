<?php
//APP_DIR is the directory where this file resides
define( APP_DIR, realpath( dirname( __FILE__ ) ));
//DOCROOT is the sibling directory of this files parent
define( DOCROOT, realpath( dirname( __FILE__ ) . "/../docroot" ) );

//General Functions

//Class Definitions
class User {
	public $id;
	public $password;

	function User($id) {
		load((int)$id);
	}

	function load($id) {
		//sql to load user data
		$pdo = DB::getPDO();

		//fetch the results and set our instance variables to them
		$statement = $pdo->prepare(
			"SELECT * FROM user WHERE id = ?",
			PDO::FETCH_INTO, $this
		);
		$statement->execute( array( $id ) );

		$this->id = (int)$this->id;
	}

	function store() {
		//no id, create a new user by passing "null" to the DB
		if( (int)$this->id == 0 ) {
			$this->id = null;
		}

		//Inserts a new row or updates an existing one
		$statement = DB::getPDO()->prepare(
			"INSERT OR IGNORE INTO visits VALUES (:id, :password);
			UPDATE visits SET password=:password WHERE id=:id;"
		);
		$statement->exectue( array(
			":id" => $this->id,
			":password" => $this->password,
		) );

		//grab the unique id from insertion if we created a new row
		if( $this->id == null )
			$this->id = (int)$statement->lastInsertId();
	}
}

class Session {
	public static $user;

	/* Load any needed data, check $_SESSION variable to see what can be loaded*/
	function load() {
		if( isset( $_SESSION ) {
			if( isset( $_SESSION['user_id'] ) ) {
				Session::$user = new User($_SESSION['user_id']);
			}
		}
	}
}

class DB {
	private static $pdo;

	function setup() {
		//create PDO
		DB::$pdo = new PDO( "sqlite:" . APP_DIR . "/../database.sqlite", "", "" );

		//enable foreign_key support
		DB::$pdo->exec("PRAGMA foreign_keys = ON;");
	}

	function getPDO() {
		if( !DB::$pdo ) {
			setup();
		}
		return DB::$pdo;
	}
}

//Load Session data
Session::load();
