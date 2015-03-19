<?php
	require_once("connect.php");
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");


	$md_ret_value = MD_check();
	$rot = 0;


	echo  "<div id = 'review_label'>".
		 	"<table cellpadding='5'>".
				"<th><a href=''>To Be Reviewed</a></th>".
				"<th><a href=''>Recently Reviewed</a></th>".
			"</table>".
		"</div>".
		"<div id = 'rotation_label'>".
			"<table cellpadding='5'>".
				"<th><a href=''>Rotation</a></th>".
				"<th><a href=''>New</a></th>".
				"<th><a href=''>Heavy</a></th>".
				"<th><a href=''>Medium</a></th>".
				"<th><a href=''>Light</a></th>".
				"<th><a href=''>Jazz</a></th>".
			"</table>".
		"</div>".
		"<br>";

		$query = sprintf("SELECT `libalbum`.`albumID`, `album_code`, `artist_name`, `album_name` FROM `libalbum`, `libartist` WHERE `rotationID` = '%d' AND `libalbum`.`artistID` = `libartist`.`artistID`", mysql_real_escape_string($rot));

	    $query .= " LIMIT 15";

	
		//Submit Query
		$list = mysql_query($query, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No albums returned: ' . mysql_error());


		echo "<table id = 'list'>".
				"<tr>".
					"<th>Artist</th>".
					"<th>Album</th>".
		            "<th></th>".
	            "<tr>";

		//Get row from SQL Query, populate tables with albums
		while($row = mysql_fetch_assoc($list)) {
		
			$albumID = $row['albumID'];
			$album_code = $row['album_code'];
			$artist_name = $row['artist_name'];
			$album_name = $row['album_name'];

	        if ($rot != 0) {
			   $review_date = $row['review_date'];
			   $last_name = $row['last_name'];
			   $first_name = $row['first_name'];
	        }


			if($album_code == $albumID){
				$a_code = "<a href=\"review.php?albumID=$albumID\">REVIEW THIS!</a>";
			}
			else{
				$a_code = "<a href=\"read_review.php?albumID=$albumID\">$album_code</a>";
				//If user is music director, show extra link to edit a review
				if($md_ret_value)
					$a_code .= "<br><a href=\"review.php?albumID=$albumID&edit=1\">Edit Review</a>";
			}

			echo "<tr>".	
			 		"<td>$artist_name</td>".
	                "<td>$album_name</td>".
					"<td>.  3x    <3   .   1x    [R]  .</td>".
	             "</tr>";
	     }

//drawtable();
/*

echo "	<script type = 'text/javascript'>".

"		$('#actions_container a').click(function (event) { ".
"			event.preventDefault();".

"   		var url = $(this).attr('href');".
"   		$.post(url, function(data) {".
"				$('#center').html(data);".
"   		});".

"			$('[live=true]').attr('live', false);".
"			$(this).attr('live', true);".

"		});".

"	</script>";	
*/
?>
