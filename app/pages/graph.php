<?php

class Graph extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		echo <<<HTML
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="/js/springy.js"></script>
<script src="/js/springyui.js"></script>
<script>
var graphJSON = {
  "nodes": [
    "Michael",
    "Jessica",
    "Timothy",
    "Barbara",
    "Franklin",
    "Monty",
    "James",
    "Bianca",
    "Dennis"
  ],
  "edges": [
    ["Dennis", "Michael", {color: '#00A0B0'}],
    ["Michael", "Dennis", {color: '#6A4A3C'}],
    ["Michael", "Jessica", {color: '#CC333F'}],
    ["Jessica", "Barbara", {color: '#EB6841'}],
    ["Michael", "Timothy", {color: '#EDC951'}],
    ["Franklin", "Timothy", {color: '#7DBE3C'}],
    ["Dennis", "Monty", {color: '#000000'}],
    ["Monty", "James", {color: '#00A0B0'}],
    ["Barbara", "Timothy", {color: '#6A4A3C'}],
    ["Dennis", "Bianca", {color: '#CC333F'}],
    ["Bianca", "Monty", {color: '#EB6841'}]
  ]
};

jQuery(function(){
  var graph = new Springy.Graph();
  graph.loadJSON(graphJSON);

  var springy = jQuery('#springydemo').springy({
    graph: graph
  });
});
</script>
		<canvas id="springydemo" width="640" height="480">
		</canvas>
HTML;
	}
}
Router::getDefault()->register( "/graph", new Graph() );
