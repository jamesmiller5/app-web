<?php
class Index extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
?>
		<h1> Hi this is the Index Page </h1>
<?php
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
			if( $user == null ) {
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
?>
		<h1> All logged out! </h1>
<?php
	}

	function loginPage() {
?>
		<h1> Hi this is the Login page </h1>
<?php
		if( $this->badLogin ) {
			Page::alert("Bad Login");
		}
?>
		<form method="post">
			<label for="u">Username:</label><input type="text" id="u" name="username" />
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<input type="submit" />
		</form>
<?php
	}
}
Router::getDefault()->register( "/login", new Login() );
