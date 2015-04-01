
//Load Page Defaults
$(function() {
	$.post('p_library.php', function(data) {
		$('#center').html(data);
	});
});


// Navigation
	// Links to change center
$('#actions_container a').click(function (event) { 
	event.preventDefault();

	var url = $(this).attr('href');
	$.post(url, function(data) {
		$('#center').html(data);
	});
	$('[live=true]').attr('live', false);
	$(this).attr('live', true);
});
	// Link to own DJ profile

	// Log out 


// Player Actions

