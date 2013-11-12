<?php
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

				//redirect to index
				$request->redirect("/");
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
		<h1> Logout </h1>
		<h2> All logged out! </h2>
HTML;
	}

	function loginPage() {
		echo <<<HTML
		<h1> Login </h1>
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
