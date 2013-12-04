<?php
class Register extends Page {
	private $message = false;
	private $email;

	function handle(Request $request) {
		//$request['email'] is the same as checking both request->get['email'] || request->post['email']
		$this->email = (isset($request['email']) ) ? $request['email'] : "";
		if( isset( $request->post ) ) {
			//save these variables for display

			//find if user already exists
			$statement = DB::getPDO()->prepare(
				"SELECT * FROM user WHERE email = ?"
			);
			$statement->execute( array( $request->post['email'] ) );

			$statement->setFetchMode(PDO::FETCH_INTO, $this);
			$ret = $statement->fetch();

			if($ret) {
				$this->message = "Email already registered. Please login instead";
			} else {
				//register email, send claimtoken
				$to = $request->post['email'];
				$subject = "Welcome to APP";

				$statement2 = DB::getPDO()->prepare(
					"SELECT * FROM email WHERE address = ?"
				);
				$statement2->execute( array( $request->post['email'] ) );
				$ret2 = $statement2->fetchAll();

				if($ret2) {
					$message = "follow link to verify: " . "$_SERVER[HTTP_HOST]" . "/verify  use token: " . $ret2[0]['token'];
				}else {
					$token = uniqid('', true);
					$message = "follow link to verify: " . "$_SERVER[HTTP_HOST]" . "/verify  use token: " . $token;
					$insert = DB::getPDO()->prepare(
						"insert into email values(:email,:token)"
					);
					$insert->execute(array(':email'=>$request->post['email'], ':token'=>$token));

				}

				$from = "no-reply@app.com";
				$headers = "From:" . $from;
				mail($to,$subject,$message,$headers);

				$this->message = "Mail sent. Please check your email for instructions.";
			}
		}

		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		if( $this->message )
			$message = "<h2>{$this->message}</h2>";
		else
			$message = "";

		echo <<<HTML
		<h1> Register </h1>
		$message
HTML;

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;

		//escape user input for XXS
		$email = htmlentities($this->email);
		echo <<<HTML
		<form method="post" action="{$URLPATH}register">
			<label for="u">Email:</label><input type="text" id="u" name="email" value="{$email}" />
			<input type="submit" />
		</form>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/register", new Register() );

