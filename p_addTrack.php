<?php
	
	echo "
	<div>
		<form class='pure-form'>
			<fieldset class='pure-group'>
				<input type='text' class='pure-input-1-2' placeholder='Track'>
				<input type='text' class='pure-input-1-2' placeholder='Album'>
				<input type='text' class='pure-input-1-2' placeholder='Artist'>
	    	</fieldset>
		</form>
	</div>
	";
	
	$addquery = "
		INSERT

	";

	echo "	
	<script type = 'text/javascript'>
		$('#list a').click(function(event){
			//event.preventDefault();
			//var qs = $(this).attr('href');
			//$.post('p_library.php?' + qs, function(data) {
			//	$('#center').html(data);

   			//});
		});

	</script>
";	
?>