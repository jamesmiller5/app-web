<?php
class Trust extends Page {
	function handle(Request $request) {
		if( isset( $request->post ) ) {
			//put into db
		}
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {

		echo <<<HTML
		<h1> Trust </h1>
		<div class="row add-citation">
			<h2 style="text-align:center">Add Citation</h2>
			<form name="input" method="post" action="#">
					<h4 class="columns" style="width:110px">I trust</h4>
						<input class="small-2 columns" type="text" style="width:100px" size="10" placeholder="Jim" />
					<h4 class="columns" style="width:100px">about</h4>
						<input class="small-2 columns" style="width:100px" type="text" size="10" placeholder="C++" />
					<h4 class="small-2 columns">because</h4><br/>
						<textarea rows="4" cols="15" placeholder="What are your reasons?"></textarea>
						<input type="submit" value="Submit" class="small button" />
			</form>
		</div>
HTML;
	}
}
Router::getDefault()->register( "/trust", new trust() );
