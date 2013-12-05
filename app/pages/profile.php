<?php

class Profile extends Page {	
	private $error = false;
	private $message = "Please login to update your profile";
	private $success = false;

	function handle(Request $request) {
		$user = Session::getUser();
		if( $user == null ){
			$this->error = true;
		}
		
		if( isset( $request->post ) && $user != null ) {
			//todo
			if($user->name == null || ($user->name != null && $request["name"] != $user->name)) {
				$user->name = $request["name"];
				//$user->title = $request["title"];
				//$user->website = $request["website"];
				$this->success = true;
			}
			if($user->company == null || ($user->company != null && $request["company"] != $user->company)) {
				$user->company = $request["company"];
				$this->success = true;
			}
			if($user->title == null || ($user->title != null && $request["title"] != $user->title)) {
				$user->title = $request["title"];
				$this->success = true;
			}
			if($user->website == null || ($user->website != null && $request["website"] != $user->website)) {
				$user->website = $request["website"];
				$this->success = true;
			}
			
			if($this->success) {
				$user->update();
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
			$error = "<h2>$this->message</h2>";
		else
			$error = "";
			
		$user = Session::getUser();

		$name = "";
		$company = "";
		$title = "";
		$website = "";
		
		if( $user ) {
			$name = $user->name;
			$company = $user->company;
			$title = $user->title;
			$website = $user->website;
		}

		echo <<<HTML
		$error
		<form method="post" action="{$URLPATH}profile">
			<label for="n">Name:</label><input type="text" id="n" name="name" value="{$name}" />
			<label for="n">Company:</label><input type="text" id="c" name="company" value="{$company}" />
			<label for="n">Title:</label><input type="text" id="t" name="title" value="{$title}" />
			<label for="n">Website:</label><input type="text" id="w" name="website" value="{$website}" />
			<input type="submit" />
		</form>
HTML;
	}
}
Router::getDefault()->register( "/profile", new Profile() );
