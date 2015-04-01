



// Actions

//Update resubmit query
$('#filter a').click(function(event){
	// rotation
	// 
	// post back
});



// Navigation
$('#detail a').click(function(event){
	event.preventDefault();

	var url = $(this).attr('href');
	$.post(url, function(data) {
		$('#center').html(data);
	});
});

