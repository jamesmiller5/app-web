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


	$level = 3;
	$topic_list = null;

	if( isset( $request['level'] ) )
		$level = (int) $request['level'];

	if( isset( $request['topic'] ) )
		if( !is_array( $request['topic'] ) ) {
			$topic_list = array( $request['topic'] );
		} else {
			$topic_list = $request['topic'];
		}

	//cap level between 1 and 6
	$level = min(6,$level);
	$level = max(1,$level);

	$me = Session::getUser();
	if( !isset($me) )
		exit();


	//id start
	$graph = array(
		$me->id => array( "name" => $me->name, "email" => $me->email, "targets" => array() )
	);
	$id_list = array($me->id);

	$cit_list = array();

	$i = 0;
	while( $i < $level && count($id_list) > 0 ) {
		//first get a k
		$statement = DB::getPDO()->prepare(
			"SELECT
				t.trusterId, r.id, r.name, r.email, c.subject, c.description, c.id as citeId
			FROM
				Trust as t
			INNER JOIN
				User as r ON t.trusteeId = r.id
			INNER JOIN
				Citation as c ON t.citeId = c.id
			WHERE
				t.trusterId IN(" . implode( ",", $id_list ) . ")
			" . ( ( !isset($topic_list) ) ? "" :
				" AND c.subject IN( ?" . str_repeat( ",?", count($topic_list)-1 ) . ")" )
		);

		$statement->setFetchMode(PDO::FETCH_NAMED);
		if( !isset($topic_list) ) {
			$ret = $statement->execute();
		} else {
			$ret = $statement->execute($topic_list);
		}
		if(!$ret) {
			decho($ret);
			exit();
		}

		$id_list = array();
		foreach( $statement->fetchAll() as $row ){
			if( !isset($graph[$row['id']]) ) {
				$graph[$row['id']] = array( "name" => $row['name'], "email" => $row['email'], "targets" => array(), );
			}

			//add this inbound edge to the outbound node
			if( !isset($graph[$row['trusterId']]) ) {
				decho($graph);
				exit();
			}

			if( !isset( $cit_list[$row['citeId']] ) ) {
				$graph[$row['trusterId']]['targets'][] = array( "name" => $row['name'], 'email' => $row['email'], 'topic' => $row['subject'] );
				$id_list[] = $row['id'];
				$cit_list[$row['citeId']] = true;
			}
		}

		$i++;
	}


	header('Content-type: application/json');
	echo json_encode( array_values($graph), JSON_PRETTY_PRINT );
});
