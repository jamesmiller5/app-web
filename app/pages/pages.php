<?php

// include section
include 'graph.php';


class Index extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$user = Session::getUser();

		$name = "";
		if( $user ) {
			$name = $user->email;
		}

		echo <<<HTML
		<h1> Hi "{$name}", this is the Index Page</h1>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/", new Index() );

class Login extends Page {
	public $logout = false;
	public $badLogin = false;

	function handle(Request $request) {

		if( isset($request['logout']) ) {
			Session::destroy();

			$this->logout = true;
		} else if( isset( $request->post ) ) {
			//attempt login
			$user = User::login( $request->post['username'], $request->post['password'] );
			if( $user != null ) {
				//set Session to this $user
				Session::setUser( $user );
			} else {
				$this->badLogin = true;
			}
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		if( $this->logout ) {
			$this->logoutPage();
		} else {
			$this->loginPage();
		}
	}

	function logoutPage() {
		echo <<<HTML
		<h1> All logged out! </h1>
HTML;
	}

	function loginPage() {
		echo <<<HTML
		<h1> Hi this is the Login page </h1>
HTML;
		if( $this->badLogin ) {
			Page::alert("Bad Login");
		}

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;
		echo <<<HTML
		<form method="post" action="{$URLPATH}login">
			<label for="u">Username:</label><input type="text" id="u" name="username" />
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<input type="submit" />
		</form>
HTML;
	}
}
Router::getDefault()->register( "/login", new Login() );

class Test extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$email  = "";
		$user = User::login("foo@bar","number");
		if( $user )
			$email = htmlentities($user->email);
		echo <<<HTML
		<h1> Hi this is the Test Page id:"{$email}" </h1>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/test", new Test() );

class Register extends Page {
	function handle(Request $request) {
		if( isset( $request->post ) ) {
			//find if user already exists
			$statement = DB::getPDO()->prepare(
				"SELECT * FROM user WHERE email = ?"
			);
			$statement->execute( array( $request->post['email'] ) );

			$statement->setFetchMode(PDO::FETCH_INTO, $this);
			$ret = $statement->fetch();
			
			if($ret) {
				echo <<<HTML
				<p>email already registered</p>
HTML;
			}else {
				//register email, send claimtoken
				$to = $request->post['email'];
				$subject = "Welcome to APP";
			
				$statement2 = DB::getPDO()->prepare(
					"SELECT * FROM email WHERE address = ?"
				);
				$statement2->execute( array( $request->post['email'] ) );
				$ret2 = $statement2->fetchAll();
				
				if($ret2) {
					$message = "follow link to verify: " . "$_SERVER[HTTP_HOST]" . "/verify  use token: " . $ret2[0]['token'];
				}else {
					$token = uniqid('', true);
					$message = "follow link to verify: " . "$_SERVER[HTTP_HOST]" . "/verify  use token: " . $token;
					$insert = DB::getPDO()->prepare(
						"insert into email values(:email,:token)"
					);
					$insert->execute(array(':email'=>$request->post['email'], ':token'=>$token));
				
				}
			
			$from = "no-reply@app.com";
			$headers = "From:" . $from;
			mail($to,$subject,$message,$headers);
			echo <<<HTML
			<p> Mail Sent. Please check your email for instructions. </p>
HTML;
			}
			
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}
	
	function render() {
				echo <<<HTML
		<h1> Hi this is the Register page </h1>
HTML;

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;
		echo <<<HTML
		<form method="post" action="{$URLPATH}register">
			<label for="u">Email:</label><input type="text" id="u" name="email" />
			<input type="submit" />
		</form>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/register", new Register() );

class Verify extends Page {
	function handle(Request $request) {
		if( isset( $request->post ) ) {
			//todo
			$userStmnt = DB::getPDO()->prepare(
				"SELECT * FROM user WHERE email = ?"
			);
			$userStmnt->execute( array( $request->post['email'] ) );
			$ret = $userStmnt->fetchAll();
			if($ret) {
				//user found, do not verify
				print "error! user already exists";
			}else {
				//user not found, attempt to validate w/ token
				$tokenStmnt = DB::getPDO()->prepare(
					"select * from email where token = ?"
				);
				$tokenStmnt->execute(array($request->post['token']));
				$ret2 = $tokenStmnt->fetchAll();
				
				if($ret2[0]['address'] == $request->post['email']) {
					//its a match! add user to db with entered password
					$user = new User();
					$user->id = 0;
					$user->email = $request->post['email'];
					$user->password = $request->post['password'];
					$user->store();
				}else {
					//error token does not match to email
					print "error! token and email do not match";
				}
			}
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}
	
	function render() {
				echo <<<HTML
		<h1> Hi this is the Validate page </h1>
HTML;

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;
		echo <<<HTML
		<form method="post" action="{$URLPATH}verify">
			<label for="u">Email:</label><input type="text" id="u" name="email" />
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<label for="t">Token:</label><input type="text" id="t" name="token" />
			<input type="submit" />
		</form>
HTML;
	}
}
//register
Router::getDefault()->register("/verify", new Verify());