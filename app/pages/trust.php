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
			if ( $request['subject'] == "" || $request['description'] == "" || $request['target'] == "" ) {
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

					$ret2 = $statement2->fetchAll();

					if (!$ret2) {
						$this->message = "We don't know the specified user.";
					} else {
						// User and subject are valid, insert into database.
						$insert = DB::getPDO()->prepare(
							"insert into citation values(:id,:subject,:description)"
						);
						$insert->execute(array(':id'=>NULL, ':subject'=>$request->post['subject'], ':description'=>$request->post['description']));

						// Get return object ID
						$this->id = (int)DB::getPDO()->lastInsertId();

						// Create new Trust and store it
						$insert2 = DB::getPDO()->prepare(
							"insert into trust values(:trusterId,:trusteeId,:citeId)"
						);

						//$this->message = "truster " . $user->id . "	trustee " . $ret2[0]['id'] . " cite	" . $this->id;
						if( $user == null ){
							$this->message = "Please login first.";
						} else { 
							$insert2->execute(array(':trusterId'=>$user->id, ':trusteeId'=>$ret2[0]['id'], ':citeId'=>$this->id));
							
							$this->message = "Citation Created!";
						}
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
			$message = "";

		echo <<<HTML
		$message
		<div class="row add-citation">
			<h1 style="text-align:center">Add Citation</h1>
			<form method="post" action="{$URLPATH}trust">
					<h4 class="columns" style="width:110px">I trust</h4>
						<input class="small-2 columns" type="text" style="width:100px" size="10" placeholder="Jim" name="target" />
					<h4 class="columns" style="width:100px">about</h4>
						<input class="small-2 columns" style="width:100px" type="text" size="10" placeholder="C++" name="subject"/>
					<h4 class="small-2 columns">because</h4><br/>
						<textarea rows="4" cols="15" placeholder="What are your reasons?" name="description"></textarea>
						<input type="submit" value="Submit" class="small button" />
			</form>
		</div>
HTML;
	}
}
Router::getDefault()->register( "/trust", new trust() );
