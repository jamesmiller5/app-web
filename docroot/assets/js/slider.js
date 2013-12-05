$('#levels').val(1);

$( "#slider" ).slider({
	range: "min",
	min: 1,
	max: 6,
	value: 1,
	slide: function(event, ui) {
		$("#levels").val(ui.value);
	}
});
