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
			//error, can't turn session handling on
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
	public $email;
	public $password;
	public $name;
	public $company;
	public $title;
	public $website;

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
				"INSERT INTO user VALUES (:id, :email, :password, null, null, null, null);"
			);

			$ret = $statement->execute( array(
				":id" => $this->id,
				":email" => $this->email,
				":password" => $this->password,
			) );
		} else {
			$statement = DB::getPDO()->prepare( "
				UPDATE
					user
				SET
					password=:password, email=:email, name=:name,
					company=:company, title=:title, website=:website
				WHERE
					id=:id;"
			);

			$ret = $statement->execute( array(
				":id" => $this->id,
				":email" => $this->email,
				":password" => $this->password,
				":name" => $this->name,
				":company" => $this->company,
				":title" => $this->title,
				":website" => $this->website
			) );
		}

		//grab the unique id from insertion if we created a new row
		if( $this->id == null && $ret )
			$this->id = (int)DB::getPDO()->lastInsertId();
	}

	function remove() {
		//make sure the citation exists
		if( (int)$this->id != 0 ) {
			$statement = DB::getPDO()->prepare(
				"DELETE FROM User WHERE id = :id;"
			);
		}

		$ret = $statement->execute( array(
			":id" => $this->id,
		) );
	}
}

class Citation {
	public $id;		//int
	public $subject;	//string, foreign key
	public $description;	//string
	public $source;		//string

	function load($id) {
		//fetch results and set equal to instance variables
		$statement = DB::getPDO()->prepare(
			"SELECT * FROM citation WHERE id = ?"
		);
		$statement->execute( array( (int)$id ) );

		$statement->setFetchMode(PDO::FETCH_INTO, $this);
		$ret = $statement->fetch();
		if( $ret )
			$this->id = (int)$this->id;

		return $ret;
	}

	function store() {
		//citation does not exist in table, insert it
		if( (int)$this->id == 0 ) {
			$this->id = null;
			$statement = DB::getPDO()->prepare(
				"INSERT INTO Citation VALUES (:id, :subject, :description, :source);"
			);
		} else {
			//citation does exist in table, update it
			$statement = DB::getPDO()->prepare(
				"UPDATE Citation SET subject=:subject, description=:description, source=:source WHERE id=:id;"
			);
		}

		$ret = $statement->execute( array(
			":id" => $this->id,
			":subject" => $this->subject,
			":description" => $this->description,
			":source" => $this->source,
		) );

		//grab the unique id from insertion if we created a new row
		if( $this->id == null && $ret )
			$this->id = (int)DB::getPDO()->lastInsertId();
	}

	function remove() {
		//make sure the citation exists
		if( (int)$this->id != 0 ) {
			$statement = DB::getPDO()->prepare(
				"DELETE FROM Citation WHERE id = :id;"
			);
		}

		$ret = $statement->execute( array(
			":id" => $this->id,
		) );
	}
}

class Trustent {
	public $trusterId;	//int
	public $trusteeId;	//int
	public $citeId;		//int

	function trusterload($trusterId) {
		//fetch results and set equal to instance variables
		$statement = DB::getPDO()->prepare(
			"SELECT * FROM Trust WHERE trusterId = ?"
		);
		$statement->execute( array( (int)$trusterId ) );

		$statement->setFetchMode(PDO::FETCH_INTO, $this);
		$ret = $statement->fetch();
		if( $ret )
			$this->trusterId = (int)$this->trusterId;

		return $ret;
	}

	function trusteeload($trusteeId) {
		//fetch results and set equal to instance variables
		$statement = DB::getPDO()->prepare(
			"SELECT * FROM Trust WHERE trusteeId = ?"
		);
		$statement->execute( array( (int)$trusteeId ) );

		$statement->setFetchMode(PDO::FETCH_INTO, $this);
		$ret = $statement->fetch();
		if( $ret )
			$this->trusteeId = (int)$this->trusteeId;

		return $ret;
	}

	function citeload($citeId) {
		//fetch results and set equal to instance variables
		$statement = DB::getPDO()->prepare(
			"SELECT * FROM Trust WHERE citeId = ?"
		);
		$statement->execute( array( (int)$citeId ) );

		$statement->setFetchMode(PDO::FETCH_INTO, $this);
		$ret = $statement->fetch();
		if( $ret )
			$this->citeId = (int)$this->citeId;

		return $ret;
	}

	function store() {
		//trust edge does not exist in table, insert it
		//trust edges should not be updated only inserted and removed
		$statement = DB::getPDO()->prepare(
			"INSERT INTO Trust VALUES (:trusterId, :trusteeId, :citeId);"
		);

		$ret = $statement->execute( array(
			":trusterId" => $this->trusterId,
			":trusteeId" => $this->trusteeId,
			":citeId" => $this->citeId,
		) );
	}

	function remove() {
		//remove a trust edge from the table
		$statement = DB::getPDO()->prepare(
			"DELETE FROM Trust WHERE trusterId = :trusterId AND trusteeId = :trusteeId AND citeId = :citeId;"
		);

		$ret = $statement->execute( array(
			":trusterId" => $this->trusterId,
			":trusteeId" => $this->trusteeId,
			":citeId" => $this->citeId,
		) );
	}
}

class Subject {
	public $subName;

	//Subject class does not need a load function since there's only one column

	function store() {
		//subject does not exist, insert it
		$statement = DB::getPDO()->prepare(
			"INSERT INTO Subject VALUES (:name);"
		);

		$ret = $statement->execute( array(
			":name" => $this->subName,
		) );
	}

	function remove() {
		//remove a subject from the table
		$statement = DB::getPDO()->prepare(
			"DELETE FROM Subject WHERE name = :name;"
		);

		$ret = $statement->execute( array(
			":name" => $this->subName,
		) );
	}
}
