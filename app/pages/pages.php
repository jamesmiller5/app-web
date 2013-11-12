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

		if( $name ) {
		echo <<<HTML
		<h1>Hi "{$name}", this is the Index Page</h1>
HTML;
		} else {
		echo <<<HTML
		<h1>Welcome to APP.</h1>
		<h2>More functionality coming soon.</h2>
		<p>Please login or register to continue</p>

HTML;
		}
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/", new Index() );

//include other pages, use a full path to avoid weirdness
require APPDIR . "app/pages/login.php";
require APPDIR . "app/pages/verify.php";
require APPDIR . "app/pages/register.php";
require APPDIR . "app/pages/graph.php";
require APPDIR . "app/pages/trust.php";

//demo page, just a list of links
class Demo extends Page {
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
		<h1> Demo </h1>
		<ol>
			<li>Theme & Style</li>
			<li><a href="/register">Register</a></li>
			<li>Email</li>
			<li><a href="/verify">Verify</a></li>
			<li><a href="/login">Login</a></li>
			<li><a href="/trust">Trust Creation</a></li>
			<li><a href="/graph">Network View</a></li>
		</ol>
HTML;
	}
}
Router::getDefault()->register( "/demo", new Demo() );
