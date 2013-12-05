<?php

class Profile extends Page {	
	private $error = false;
	private $success = false;

	function handle(Request $request) {
		$user = Session::getUser();
		
		if( isset( $request->post ) ) {
			//todo
			if($user->name == null || ($user->name != null && $request["name"] != $user->name)) {
				$user->name = $request["name"];
				$user->update();
				$this->success = true;
			}
		}
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
				echo <<<HTML
		<h1> Profile </h1>
HTML;
		if( $this->success ) {
			echo <<<HTML
				<h2>Profile Updated!</h2>
HTML;
		}
		
		$this->renderForm();
	}

	function renderForm() {
		$URLPATH = URLPATH;
		if( $this->error )
			$error = "<h2>$this->error</h2>";
		else
			$error = "";
			
		$user = Session::getUser();

		$name = "";
		if( $user ) {
			$name = $user->name;
		}

		echo <<<HTML
		$error
		<form method="post" action="{$URLPATH}profile">
			<label for="n">Name:</label><input type="text" id="n" name="name" value="{$name}" />
			<input type="submit" />
		</form>
HTML;
	}
}
Router::getDefault()->register( "/profile", new Profile() );
