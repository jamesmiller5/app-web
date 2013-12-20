<?php
class Register extends Page {
	private $message = false;
	public $email;

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

				$message = "Thank you for registering your email address with Authority Publishing Platform.\n\n";
				$message = $message . "To finish creating your account, you must verify that you received this email.\n";
				$message = $message . "Please click on the following link to verify your email and create a password for your brand new account with APP.\n\n";

				if($ret2) {
					$message = $message . "http://$_SERVER[HTTP_HOST]" . "/verify?email=" . $to . "&token=" . $ret2[0]['token'] . "\n\n";
				}else {
					$token = uniqid('', true);
					$message = $message . "http://$_SERVER[HTTP_HOST]" . "/verify?email=" . $to . "&token=" . $token . "\n\n";
					$insert = DB::getPDO()->prepare(
						"insert into email values(:email,:token)"
					);
					$insert->execute(array(':email'=>$request->post['email'], ':token'=>$token));

				}

				$message = $message . "We hope you enjoy your stay,\n";
				$message = $message . "The APP Team";

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
Router::getDefault()->register( "/register", Page::pagify("Register") );

