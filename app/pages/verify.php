<?php
class Verify extends Page {
	private $error = false;
	private $success = false;

	function handle(Request $request) {
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
					$this->error = "Invalid verification token";
				} else {
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
		<h1> Validate </h1>
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

		echo <<<HTML
		$error
		<form method="post" action="{$URLPATH}verify">
			<label for="u">Email:</label><input type="text" id="u" name="email" />
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<label for="t">Token:</label><input type="text" id="t" name="token" />
			<input type="submit" />
		</form>
HTML;
	}
}
//register
Router::getDefault()->register("/verify", new Verify());
