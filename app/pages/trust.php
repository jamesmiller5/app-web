<?php
class Trust extends Page {
	private $message = false;
	private $subject;
	private $description;
	private $target;
	private $citID;

	function handle(Request $request) {
		$user = Session::getUser();
		if( isset( $request->post ) ) {
			// Error message if not all fields are set
			$this->message = "Post";
			if ( !isset($request['subject']) || !isset($request['description']) || !isset($request['target']) ) {
			  $this->message = "Please fill out all citation areas.";
			} else {
				//find if subject exists
				$statement = DB::getPDO()->prepare(
					"SELECT * FROM subject WHERE name = ?"
				);
				$statement->execute( array( $request->post['subject'] ) );

				$ret = $statement->fetchAll();

				if (!$ret) {
					$this->message = "Please use a valid subject: C++, Java, etc.";
				} else {
					// Find if target user exists
					$statement2 = DB::getPDO()->prepare(
						"SELECT * FROM user where EMAIL = ?"
					);
					$statement2->execute( array( $request->post['target'] ) );

					$ret2 = $statement2->fetchColumn();

					if (!$ret2) {
						$this->message = "We don't know the specified user.";
					} else {
						// User and subject are valid, insert into database.
						$insert = DB::getPDO()->prepare(
							"insert into citation values(:subject,:description,:source)"
						);
						$insert->execute(array(':email'=>$request->post['subject'], ':token'=>$request->post['description'], ':source'=>"none"));

						// Get return object ID
						$this->id = (int)DB::getPDO()->lastInsertId();
						
						// Create new Trust and store it
						$trust = new Trust();
						$trust->trusterId = $user->id;
						$trust->trusteeId = $ret2->id;
						$trust->citeId = $this->id;
						$trust->store();

						$this->message = "Citation Created!";
					}
				}
			}
			
		}
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$URLPATH = URLPATH;
		if( $this->message )
			$message = "<h2>{$this->message}</h2>";
		else
			$message = "<h3>No message.</h3>";

		echo <<<HTML
		$message
		<div class="row add-citation">
			<h1 style="text-align:center">Add Citation</h1>
			<form name="input" method="post" action="{$URLPATH}trust">
					<h4 class="columns" style="width:110px">I trust</h4>
						<input class="small-2 columns" type="text" style="width:100px" size="10" placeholder="Jim" id="target" />
					<h4 class="columns" style="width:100px">about</h4>
						<input class="small-2 columns" style="width:100px" type="text" size="10" placeholder="C++" id="subject"/>
					<h4 class="small-2 columns">because</h4><br/>
						<textarea rows="4" cols="15" placeholder="What are your reasons?" id="description"></textarea>
						<input type="submit" value="Submit" class="small button" />
			</form>
		</div>
HTML;
	}
}
Router::getDefault()->register( "/trust", new trust() );
