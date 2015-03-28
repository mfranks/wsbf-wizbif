<?php

	require_once("connect.php");
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");


//if (isset($_GET['searchType'])) {}
echo "<div id = 'searchresults'>"; 

if ( isset($_GET['searchterm'])) {
	
	
	//search track names and display in column
	
	$tracksquery = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID, t.track_name, t.track_num
		FROM libartist b
		JOIN libalbum a on a.artistID = b.artistID
		JOIN libtrack t on t.albumID = a.albumID
		WHERE t.track_name LIKE '%".$_GET['searchterm']."%'
		LIMIT 15";
	$albumsquery = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID
		FROM libalbum a
		JOIN libartist b on a.artistID = b.artistID
		WHERE a.album_name LIKE '%".$_GET['searchterm']."%'
		LIMIT 15";
	$artistsquery = "
		SELECT b.artist_name, b.artistID
		FROM libartist b
		WHERE b.artist_name LIKE '%".$_GET['searchterm']."%'
		LIMIT 15";

	//Submit Queries
	$tracknames = mysql_query($tracksquery, $link);
	$albumnames = mysql_query($albumsquery, $link);
	$artistnames = mysql_query($artistsquery, $link);

	if ( mysql_num_rows($artistnames ) > 0 ) {
		echo "
			<strong>Artists</strong><br>";
		while ($deet = mysql_fetch_assoc($artistnames) ) {
			echo "
				<a href='artistID=".$deet['artistID']."'>".$deet['artist_name']."</a><br>"; 
		}
	}
	if ( mysql_num_rows($albumnames ) > 0 ) {
		echo "
			<strong>Albums</strong><br>";
		while ($deet = mysql_fetch_assoc($albumnames) ) {
			echo "
				<a href = 'albumID=".$deet['albumID']."'>".$deet['album_name']."</a> by
				<a href = 'artistID=".$deet['artistID']."'>".$deet['artist_name']."</a><br>";
		}
	} 
	if ( mysql_num_rows($tracknames ) > 0 ) {
		echo "
			<strong>Tracks</strong><br>";
			while ($deet = mysql_fetch_assoc($tracknames)) {
				echo "
					<a href = 'albumID=".$deet['albumID']."&track_num=".$deet['track_num']."'>".$deet['track_name']."</a> off 
					<a href = 'albumID=".$deet['albumID']."'>".$deet['album_name']."</a> by
					<a href = 'artistID=".$deet['artistID']."'>".$deet['artist_name']."</a><br>";
			}
	}
}

echo "</div>";

echo "
	<script type = 'text/javascript'>
		
		$('#searchresults').blur(function() {
			$('#searchresults').hide();
			$('results').show();
		});

		/*
		$('#searchInput').keydown(function(event) {
			//later, add for 8 = backspace
			if ( event.keyCode == 46 ) {
				var qs = 'p_search.php?searchterm=' + $(this).val();
				$.post(qs, function(data) {
					$('#searchresults').remove();
					$('#searchresults').html(data);
	   			});
			}
		});
		*/

		$('#searchInput').keyup(function(event) {
			var qs = 'p_search.php?searchterm=' + $(this).val();
			$.post(qs, function(data) {
				$
				$('#searchresults').html(data);
   			});
		});

		// 8, 46 

		$('#searchresults a').click(function(event) {
			event.preventDefault();
			var qs = $(this).attr('href');
			$.post('p_library.php?' + qs, function(data) {
				$('#center').html(data);
			});
		});
		
	</script>";


?>