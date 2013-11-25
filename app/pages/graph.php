<?php

class Graph extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$ASSETS = "/assets/";
		echo <<<HTML
		<script src="{$ASSETS}js/vendor/jquery.min.js"></script>
		<script src="{$ASSETS}js/vendor/jquery-ui.min.js"></script>
<script src="{$ASSETS}js/springy.js"></script>
<script src="{$ASSETS}js/springyui.js"></script>
<script src="{$ASSETS}js/graphPage.js"></script>
<style>#slider {margin: 10px; }</style>
<link rel="stylesheet" href="{$ASSETS}css/jquery-ui.min.css">

		<div class="small-8 columns">
			<canvas id="springydemo" width="640" height="480">
			</canvas>
		</div>

		<div class="small-4 columns">
			<div id="slider"></div>
			<form name="input" method="get" action="#">
				Levels: <input type="text" text="1" id="levels" name="levels" size="3" disabled/>
				<input type="checkbox" name="topic" value="C++">C++<br>
				<input type="checkbox" name="topic" value="Java">Java<br>
				<input type="submit" value="Submit">
			</form>
		</div>
		<script src="{$ASSETS}js/slider.js"></script>

HTML;
	}
}
Router::getDefault()->register( "/graph", new Graph() );
