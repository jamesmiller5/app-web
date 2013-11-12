<?php
class Trust extends Page {
	function handle(Request $request) {
		if( isset( $request->post ) ) {
			//put into db
		}
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
		<h1> Trust </h1>
		<h2>Mock goes here</h2>
HTML;
	}
}
Router::getDefault()->register( "/trust", new trust() );
