<?php

class Graph extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$ASSETS = "/assets/";
		echo <<<HTML
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="{$ASSETS}js/springy.js"></script>
<script src="{$ASSETS}js/springyui.js"></script>
<script src="${ASSETS}js/graphPage.js"></script>
		<canvas id="springydemo" width="640" height="480">
		</canvas>
HTML;
	}
}
Router::getDefault()->register( "/graph", new Graph() );
