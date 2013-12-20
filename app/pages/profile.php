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

			if( count( $request->files ) && isset( $request->files['icon'] ) ) {
				$request->files['icon']->moveTo( APPDIR . "/docroot/profiles/{$user->id}.png" );
			}

			if($this->success) {
				$user->store();
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
		$img = "default.png";

		if( $user ) {
			$name = $user->name;
			$company = $user->company;
			$title = $user->title;
			$website = $user->website;
			if( is_readable( APPDIR . "/docroot/profiles/{$user->id}.png" ) ) {
				$img = $user->id . ".png";
			}

		}

		echo <<<HTML
		$error
		<form method="post" action="{$URLPATH}profile" enctype="multipart/form-data">
			<img src="{$URLPATH}profiles/{$img}" />
			<label for="i">Image:</label><input type="file" id="i" name="icon" />
			<label for="n">Name:</label><input type="text" id="n" name="name" value="{$name}" />
			<label for="c">Company:</label><input type="text" id="c" name="company" value="{$company}" />
			<label for="t">Title:</label><input type="text" id="t" name="title" value="{$title}" />
			<label for="w">Website:</label><input type="text" id="w" name="website" value="{$website}" />
			<input type="submit" />
		</form>
HTML;
	}
}
Router::getDefault()->register( "/profile", Page::pagify("Profile") );


class ProfileView extends Page {
	private $user;

	function handle(Request $request) {
		$this->user = new User();
		if( !isset($request['id']) || false == $this->user->load($request['id']) ) {
			//404
			header("HTTP/1.1 404 Not Found");
			return false;
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$user = $this->user;

		$img = "default.png";
		if( is_readable( APPDIR . "/docroot/profiles/{$user->id}.png" ) ) {
			$img = $user->id . ".png";
		}

		$URLPATH = URLPATH;
		$name = $user->name;
		$company = $user->company;
		$title = $user->title;
		$website = $user->website;
echo <<<HTML
		<h1> Profile </h1>
		<img src="{$URLPATH}profiles/{$img}" />
		<label for="n">Name:</label><input type="text" id="n" name="name" value="{$name}" readonly />
		<label for="c">Company:</label><input type="text" id="c" name="company" value="{$company}" readonly />
		<label for="t">Title:</label><input type="text" id="t" name="title" value="{$title}" readonly />
		<label for="w">Website:</label><input type="text" id="w" name="website" value="{$website}" readonly />
HTML;
	}
}

//simple view others profile
Router::getDefault()->register("/profile/view", Page::pagify("ProfileView") );
