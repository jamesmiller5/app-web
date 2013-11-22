var rawJSON = [
  { src: "Michael",
	targets: [
	{target: "Dennis", topic: "C++"},
	{target: "Jessica", topic: "Java"},
	{target: "Timothy", topic: "C++"},
	{target: "Bianca", topic: "C#"},
	{target: "Franklin", topic: "C#"},
	{target: "Barbara", topic: "C++"}
  ] },
  { src: "Dennis",
	targets: [
	{target: "Michael", topic: "C#"},
	{target: "Monty", topic: "Javascript"}
  ] },
  { src: "Jessica",
	targets: [
	{target: "Barbara", topic: "Javascript"}
  ] },
  { src: "Franklin",
	targets: [
	{target: "Timothy", topic: "C#"}
  ] },
  { src: "Monty",
	targets: [
	{target: "James", topic: "Java"}
  ] },
  { src: "Bianca",
	targets: [
	{target: "Monty", topic: "Java"}
  ] },
  { src: "Barbara",
	targets: [
	{target: "Timothy", topic: "C++"}
  ] },
  { src: "Timothy",
	targets: []
  },
  { src: "James",
	targets: []
  }
	
];

var randomColor = function() {
	var colors = ["#000000", "#00A0B0", "#6A4A3C", "#CC333F",
					"#EB6841", "#EDC951", "#7DBE3C", "#123456",
					"#FFEEFF", "#ABCDEF" ];
	return colors[Math.floor(Math.random()*10)];
};

var transformJSON = function (rawJson) {
	var nodes = new Array();
	var edges = new Array();
	var edgeCount = 0;
	$.each(rawJson, function(x, set) {
		nodes[x] = set.src;
		$.each(set.targets, function(y, edge) {
			var newEdge = new Array();
			newEdge[0] = set.src;
			newEdge[1] = edge.target;
			var prop = { color: randomColor() };
			newEdge[2] = prop;
			edges[edgeCount] = newEdge;
			edgeCount += 1;
		});
	});

	//$.each(edges, function(x, edge) { alert(edge) });

	return { "nodes": nodes, "edges": edges };
}

jQuery(function(){
  var graph = new Springy.Graph();	
  graph.loadJSON(transformJSON(rawJSON));

  var layout = new Springy.Layout.ForceDirected( graph,
	100.0,
	100.0,
	0.1 );

  jQuery('#springydemo').unbind('mousedown');

  var springy = jQuery('#springydemo').springy({
    graph: graph
  });
});
