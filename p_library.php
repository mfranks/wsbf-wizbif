<?php

	require_once("connect.php");
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");

	$rot;
	$bUnreviewed;
	$sTrackID;
	$sArtistID;
	$sAlbumID;
	
	if (isset($_GET['trackID'])) {
		$sTrackID = $_GET['trackID'];
	}
	
	if (isset($_GET['albumID'])) {
		$sAlbumID = $_GET['albumID'];
	}
	
	if (isset($_GET['artistID'])) {
		$sArtistID = $_GET['artistID'];
	}
	
	$md_ret_value = MD_check();
	$rot = 0;



	//Track Detail // do later
	if(!empty($sTrackNum) ) {
	echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_library.php?'>Bibliotheque</a></div></td>
	     		<td><div id = 'review_label'><a href=''>To Be Reviewed</a></div></td>
			    <td><div id = 'rotationtypes'><a href=''>Rotation</a></div></td>
			</tr></table>
		</div>";


		$query = "
		SELECT DISTINCT a.album_name, a.albumID, b.artist_name, b.artistID, t.track_num, t.track_name
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		WHERE 1=1 
		AND a.albumID = ".$sAlbumID."
		AND t.track_num = ".$sTrackID;

		echo "<div id = 'list'>
				<table id = 'list'>
			     <tr>
				     <th></th>
				     <th>Track</th>
				     <th>Album</th>
		             <th>Artist</th>
		             <th></th> 
	             <tr> 
	             <tr>	
				 	    <td><a href = 'albumID=".$row['albumID']."&track_num=".$row['track_num'].">".$row['track_name']."</a></td>
				 	    <td><a href = 'albumID=".$row['albumID']."''>".$row['album_name']."</a></td>
		                <td><a href = 'artistID=".$row['artistID']."''>".$row['artist_name']."</a></td>
					    <td>hp <a href='heart.php?liker=you&thing=albumID'>    14  </a></td>
				</tr> 
			</div>";
	}

	// Album Detail, muy important
	if(!empty($sAlbumID)) {

echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_library.php?'>Bilbotheque</a></div></td>
	     		<td><div id = 'review_label'><a href=''>To Be Reviewed</a></div></td>
			    <td><div id = 'rotationtypes'><a href=''>Rotation</a></div></td>
			</tr></table>
		</div>";

		$tracksquery = "
		SELECT DISTINCT t.track_num, t.track_name, t.airabilityID, t.disc_num
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		WHERE a.albumID = ".$sAlbumID."
		";

		$detailquery = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		WHERE a.albumID = ".$sAlbumID;

		$query.=" LIMIT 15;";

		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($tracksquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this album is fucked up: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

echo "<div id='results'>
		<div id='detail'>
			<strong>".$deet['album_name']."</strong><br>
			by <a href= 'artistID=".$deet['artistID']."'>".$deet['artist_name']." </a> <br>
			<a href=''>reviews and shit</a>
		</div> 
	    <div id='list'>
			<table>
			    <tr>
				    <th>Tracks</th>
				    <th></th>
				    <th></th>
	            </tr>";

        //Get row from SQL Query, populate tables with tracks
		while($row = mysql_fetch_assoc($list)) {
			echo "<tr>	
	                <td><a href = ''>".$row['track_num'].". ".$row['track_name']."</a></td>
	        		<td><img src='crystal2.png'>  13 <a href = ''>+</a></td>";
	        if (!$row['airabilityID']) echo "<td>NO AIR</td>";
	        		
			echo "</tr> ";
	    }
echo "
			</table>
		</div>
	</div>";
	}
	
	else if(!empty($sArtistID)) {

echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_library.php?'>Bibliotheque</a></div></td>
	     		<td><div id = 'review_label'><a href=''>To Be Reviewed</a></div></td>
			    <td><div id = 'rotationtypes'><a href=''>Rotation</a></div></td>
			</tr></table>
		</div>";

		$albumsquery = "
		SELECT DISTINCT a.albumID, a.album_name
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		WHERE b.artistID = ".$sArtistID;

		$detailquery = "
		SELECT b.artist_name, b.artistID
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		WHERE b.artistID = ".$sArtistID;
		$query.=" LIMIT 15;";

		//Submit Query
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($albumsquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this artist aint got shit: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

		echo "
		<div id='results'>
			<div id='detail'>
				<strong>".$deet['artist_name']."</strong><br>
				<a href=''>reviews and shit</a>
			</div>
			<div id='list'>
				<table>
				    <tr>
					    <th>Album</th>
		            </tr> ";

        //Get row from SQL Query, populate tables with Albums
		while($row = mysql_fetch_assoc($list)) {
		echo "
					<tr>	
				 	    <td><a href = 'albumID=".$row['albumID']."'>".$row['album_name']."</a></td>
		                <td><img src='crystal2.png'>  13</td>

					</tr> ";
	    }
echo "			</table>
			</div>
		 </div>";
	}


	else { //no details set

		$query = "
		SELECT DISTINCT a.album_name, a.albumID, b.artist_name, b.artistID, r.reviewer
		FROM libartist b
		JOIN libalbum a on a.artistID = b.artistID
		JOIN libtrack t on t.albumID = a.albumID
		LEFT JOIN libreview r on r.albumID = a.albumID
		LIMIT 15;";

		//Submit Query
		$list = mysql_query($query, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No albums returned: ' . mysql_error());
echo 	"
		<div id = 'filters'>
			<table>
				<tr>
					<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 					<td><div id = 'centerlab'><a href='p_library.php?'>Bilbotheque</a></div></td>
		            <th><a href = ''>Reviewed</a></th> 
		            <th><a href = ''>Rotation</a></th>
 				</tr>
 			</table>
 		</div>
		<div id = 'results'>
			<div id = 'list'> 
				<table>
 				<tr>
				    <th>Artist</th>
				    <th>Album</th>
				    <th></td>
	            </tr> ";
	

		//Get row from SQL Query, populate tables with albums
		while($row = mysql_fetch_assoc($list)) {
		
			$albumID = $row['albumID'];
			$album_code = $row['album_code'];
			$album_name = $row['album_name'];

			$artist_name = $row['artist_name'];
			$artistID = $row['artistID'];
			
			/*
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
			*/

echo "				<tr>	
						<td><a href = 'p_library.php?artistID=".$row['artistID']."'>".$row['artist_name']."</a></td>
					 	<td><a href = 'p_library.php?albumID=".$row['albumID']."'>".$row['album_name']."</a></td>";
if ( $row['reviewer']){
echo "				 	<td><a href = 'p_library.php?albumID=".$row['albumID']."'> - ".$row['reviewer']."</a></td>";
} else {
echo "					<td><a href = 'p_review.php?albumID=".$row['albumID']."'> - review this! </a></td>";
}
echo "					<td><img src='crystal2.png'>  13</td>
					</tr>";
	    }
echo "		</table>
		</div>
	</div>";
	}
	

echo "	
	<script type = 'text/javascript'>
		
		$('#search').click(function(event) {
			
			$('#results').hide();
			
			if ( $('#searchInput').attr('value') == 'Search: ' ) {
				$('#searchInput').attr('value','');
				$.post('p_search.php', function(data) {
					$('#center').append(data);
   				});
			}

			$('#searchresults').show();
		});
		
		$('#centerlab a').click(function(event){
			event.preventDefault();
			var qs = $(this).attr('href');
			$.post(qs, function(data) {
				$('#center').html(data);

   			});
		});

		$('#results a').click(function(event){
			event.preventDefault();
			var qs = $(this).attr('href');
			$.post( qs, function(data) {
				$('#center').html(data);

   			});
		});

	</script>
";	


?>