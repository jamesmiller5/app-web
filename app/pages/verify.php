<?php
class Verify extends Page {
	function handle(Request $request) {
		if( isset( $request->post ) ) {
			//todo
			$userStmnt = DB::getPDO()->prepare(
				"SELECT * FROM user WHERE email = ?"
			);
			$userStmnt->execute( array( $request->post['email'] ) );
			$ret = $userStmnt->fetchAll();
			if($ret) {
				//user found, do not verify
				print "error! user already exists";
			}else {
				//user not found, attempt to validate w/ token
				$tokenStmnt = DB::getPDO()->prepare(
					"select * from email where token = ?"
				);
				$tokenStmnt->execute(array($request->post['token']));
				$ret2 = $tokenStmnt->fetchAll();

				if($ret2[0]['address'] == $request->post['email']) {
					//its a match! add user to db with entered password
					$user = new User();
					$user->id = 0;
					$user->email = $request->post['email'];
					$user->password = $request->post['password'];
					$user->store();
				}else {
					//error token does not match to email
					print "error! token and email do not match";
				}
			}
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
				echo <<<HTML
		<h1> Hi this is the Validate page </h1>
HTML;

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;
		echo <<<HTML
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
