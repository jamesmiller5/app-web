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
			<form name="input" method="get" action="#" onsubmit="return graphHandle(this)">
				Levels: <input type="text" text="1" id="levels" name="levels" size="3" disabled/>
				<input type="checkbox" name="topic" value="C++">C++<br>
				<input type="checkbox" name="topic" value="Java">Java<br>
				<input type="submit" value="Submit">
			</form>
		</div>
		<script src="{$ASSETS}js/slider.js"></script>
		<script>
		function graphHandle(form) {
			var list = Array();
			var level = document.getElementById('levels').value;
			for(var elm in form.elements ) {
				if( form.elements[elm].checked ) {
					list.push(form.elements[elm].value)
				}
			}

			graphDraw(level, list);

			return false;
		}

		function graphDraw(level, topic) {
			$.getJSON( "/graph/view-subjective", {"level": level, "topic": topic} )
				.done( function(json) {
					var graph = new Springy.Graph();
					graph.loadJSON(transformJSON(json));

					var layout = new Springy.Layout.ForceDirected( graph,
						100.0,
						100.0,
						0.1 );

					var springy = jQuery('#springydemo').springy({
						graph: graph
					});
				})
				.fail(function( jqxhr, textStatus, error ) {
					var err = textStatus + ", " + error;
					console.log( "Request Failed: " + err );
				});
		}
		jQuery(function(){
			graphDraw(null);
		});
		</script>

HTML;
	}
}
Router::getDefault()->register( "/graph", new Graph() );

Router::getDefault()->registerHandler( "/graph/view-subjective", function($request) {
	$data = array(
		['src' => "Jim", 'targets' => [
				["target" => "Jaye", "topic" => "C++"],
				['target' => 'Andrew', 'topic' => 'Ideas'],
			]
		],
		['src' => 'Jaye', 'targets' => [ ] ],
		['src' => 'Andrew', 'targets' => [ ] ],
	);

	header('Content-type: application/json');
	echo json_encode( $data, JSON_PRETTY_PRINT );
});
