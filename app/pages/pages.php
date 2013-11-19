<?php
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
		$newuser = new User();
		$newuser->email = "test@testing";
		$newuser->password = "pass";
		$newuser->store();
		$user = User::login("test@testing", "pass");
		$newuser->remove();
		
		//subject function tests
		$subject = new Subject();
		$subject->subName = "Math";
		$subject->store();
		
		//test the Citation functions
		$citation = new Citation();
		$citation->subject = "Math";
		$citation->description = "calculator";
		$citation->source = "test";
		$citation->store();
		var_dump($citation);
		$newcit = new Citation();
		$newcit->load($citation->id);
		var_dump($newcit);
		
		$citation->remove();
		$subject->remove();
		
		if( $user )
			$email = htmlentities($user->email);
		
		echo <<<HTML
		<h1> Hi this is the Test Page id:"{$email}" </h1>
		<h1> Login verifies that User->load is working </h1>
		<h1> The subject was inserted and removed correctly </h1>
		<h1> The first 2 var_dumps should match to confirm citation functions </h1>
		<h1> refresh to check that the remove functions worked correctly </h1>
		
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/test", new Test() );

