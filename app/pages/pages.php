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

//include other pages, use a full path to avoid weirdness
require APPDIR . "app/pages/login.php";
require APPDIR . "app/pages/verify.php";
require APPDIR . "app/pages/register.php";

//demo page, just a list of links
