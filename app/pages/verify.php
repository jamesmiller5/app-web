<?php
class Verify extends Page {
	private $error = false;
	private $success = false;
	private $email;
	private $token;

	function handle(Request $request) {
		$this->email = (isset($request['email']) ) ? $request['email'] : "";
		$this->token = (isset($request['token']) ) ? $request['token'] : "";
		
		if( isset( $request->post ) ) {
			//todo
			$userStmnt = DB::getPDO()->prepare(
				"SELECT * FROM user WHERE email = ?"
			);
			$userStmnt->execute( array( trim($request->post['email']) ) );
			$ret = $userStmnt->fetchAll();
			if($ret) {
				//user found, do not verify
				$this->error = "Already verified";
			} else {
				//user not found, attempt to validate w/ token
				$tokenStmnt = DB::getPDO()->prepare(
					"SELECT * FROM email WHERE token = ?"
				);
				$tokenStmnt->execute( array( trim($request->post['token']) ) );
				$ret2 = $tokenStmnt->fetchAll();

				if(		!is_array( $ret2 )
					|| 	!isset( $ret2[0] )
					|| 	!($ret2[0]['address'] == $request->post['email']) ) {
					$this->error = "Invalid email/token combination";
				} else if(		strlen($request->post['password']) < 6 
							||	strlen($request->post['password']) > 18
							|| 	preg_match('/\s/', $request->post['password'])) {
					$this->error = "Invalid password";
				}else {
					//its a match! add user to db with entered password
					$user = new User();
					$user->id = NULL;
					$user->email = $request->post['email'];
					$user->password = $request->post['password'];
					$user->store();

					//login as this user as well
					Session::setUser( $user );
					$this->success = true;
				}
			}
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
				echo <<<HTML
		<h1> Verify </h1>
HTML;
		if( $this->success ) {
			$this->renderSuccess();
		} else {
			$this->renderForm();
		}
	}

	function renderSuccess() {
		echo <<<HTML
		<h2>All Verified! Enjoy using APP</h2>
HTML;
	}

	function renderForm() {
		$URLPATH = URLPATH;
		if( $this->error )
			$error = "<h2>$this->error</h2>";
		else
			$error = "";
			
		$e = htmlentities($this->email);
		$t = htmlentities($this->token);

		
		echo <<<HTML
		$error
		<form method="post" action="{$URLPATH}verify">
			<label for="u">Email:</label><input type="text" id="u" name="email" value="{$e}" />
			<label for="t">Token:</label><input type="text" id="t" name="token" value="{$t}"/>
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<input type="submit" />
		</form>
HTML;
	}
}
//register
Router::getDefault()->register("/verify", new Verify());
