$('#levels').val(3);

$( "#slider" ).slider({
	range: "min",
	min: 1,
	max: 6,
	value: 3,
	slide: function(event, ui) {
		$("#levels").val(ui.value);
	}
});
