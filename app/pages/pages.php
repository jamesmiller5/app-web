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
			if( $user->name == null ){
				$name = $user->email;
			} else {
				$name = $user->name;
			}
		}

		if( $name ) {
		echo <<<HTML
		<h1>Hi {$name}, Welcome to APP.</h1>
		<h2>Use the menu above to explore your network and make connections.</h2>
HTML;
		} else {
		echo <<<HTML
		<h1>Welcome to APP.</h1>
		<p>If you don't have an account, please <a href="/register">register</a>.</p>
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
require APPDIR . "app/pages/profile.php";

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

