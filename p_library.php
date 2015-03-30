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

	if (isset($_GET['rot'])) {
		$rot = $_GET['rot'];
	}
	
	$md_ret_value = MD_check();

	echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_library.php?'>Bilbotheque</a></div></td>
	     		<td></td>
	     		<td><div id = 'reviewlabel'><a href='p_library.php?rot=0'>To Be Reviewed</a></div></td>
			    <td></td>
			    <td><div id = 'rotationlabel'>
			    		<div id='rotationButton'><a href=' '>Rotation</a></div>
			    		<div id='rotationtypes' style='display:none'>
			    			<a href=''>: </a>
			    			<a href = 'p_library.php?rot=1'>N</a>
							<a href = 'p_library.php?rot=2'>H</a>
							<a href = 'p_library.php?rot=3'>M</a>
							<a href = 'p_library.php?rot=4'>L</a>
							<a href = 'p_library.php?rot=6'>J</a>
						</div>
		    		</div>
		    	</td>
			</tr></table>
		</div>";

	// Album Detail, muy important
	if(!empty($sAlbumID)) {

		$tracksquery = "
		SELECT DISTINCT t.track_num, t.track_name, d.airability, t.disc_num
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		JOIN def_airability d on d.airabilityID = t.airabilityID
		WHERE a.albumID = ".$sAlbumID."
		";

		$detailquery = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID, r.username, r.reviewer, r.review, r.review_date
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		LEFT JOIN libreview r on r.albumID = a.albumID
		WHERE a.albumID = ".$sAlbumID."
		LIMIT 1";

		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($tracksquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this album is fucked up: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

echo "<div id='results'>
		<div id='detail'>
			<strong>".$deet['album_name']."</strong><br>
			by <a href= 'p_library.php?artistID=".$deet['artistID']."'>".$deet['artist_name']." </a> <br>";
if( $deet['reviewer']) echo "<p>".$deet['review']." - <a href = 'p_profiles.php?dj=".$deet['username']."'>".$deet['reviewer']."</a> ".$deet['review_date']."</p>";
else echo "<a href='p_review.php?albumID=".$deet['albumID']."'>Review this album</a>";
	
echo "</div> 

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
	                <td>".$row['track_num'].". ".$row['track_name']."</td>
	        		<td>".$row['airability']."</td>
	        		<td><img src='crystal2.png'>  13 <a href = ''>+</a></td>
				</tr> ";
	    }
echo "
			</table>
		</div>
	</div>";
	}
	
	else if(!empty($sArtistID)) {

		$albumsquery = "
		SELECT a.albumID, a.album_name, a.artistID, r.reviewer, r.username
		FROM libalbum a
		LEFT JOIN libartist b on b.artistID = a.artistID
		LEFT JOIN libreview r on r.albumID = a.albumID
		LEFT JOIN def_rotations d on d.rotationID = a.rotationID
		WHERE a.artistID = ".$sArtistID;

		/*
		LEFT JOIN libreview r on r.albumID = a.albumID
		LEFT JOIN def_rotations d on d.rotationID = a.rotationID
		*/

		$detailquery = "
		SELECT b.artist_name, b.artistID
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		WHERE b.artistID = ".$sArtistID." LIMIT 1";

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
			</div>
			<div id='list'>
				<table>
				    <tr>
					    <th>Album</th>
					    <th>Reviewer</th>
		            </tr> ";

        //Get row from SQL Query, populate tables with Albums
		while($row = mysql_fetch_assoc($list)) {
echo "
					<tr>	
				 	    <td><a href = 'p_library.php?albumID=".$row['albumID']."'>".$row['album_name']."</a></td>";
if ( $row['reviewer']){
echo "				 	<td><a href = 'p_profiles.php?dj=".$row['username']."'>".$row['reviewer']."</a></td>";
} else {
echo "					<td><a href = 'p_library.php?albumID=".$row['albumID']."'>review this! </a></td>";
}
echo"		            <td>".$row['binAbbr']."</td>  
						<td><img src='crystal2.png'>  13</td>
					</tr> ";
	    }
echo "			</table>
			</div>
		 </div>";
	}


	else { //nothing set

		$query = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID, r.reviewer, r.review_date, r.username, a.rotationID
		FROM libalbum a
		JOIN libartist b on a.artistID = b.artistID
		LEFT JOIN libreview r on a.albumID = r.albumID
		LEFT JOIN def_rotations d on d.rotationID = a.rotationID";

		
		//unreviewed or rotation
		if ( isset($rot) ) {
			$query.=" WHERE a.rotationID = ";
			$query.=$rot;
			$query.=" ORDER BY r.review_date ASC";
		}
		else { // default to new, recently reviewed rotation
			$query.=" WHERE a.rotationID = 1
					ORDER BY r.review_date DESC";
		}
	
		$query.=" LIMIT 50";

		//Submit Query
		$list = mysql_query($query, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No albums returned: ' . mysql_error());
echo 	"
		<div id = 'results'>
			<div id = 'list'> 
				<table>
 				<tr>
				    <th>Artist</th>
				    <th>Album</th>
				    <th>Reviewer</th> 
		            <th><a href = ''></a></th>
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
echo "				 	<td><a href = 'p_profiles.php?dj=".$row['username']."'>".$row['reviewer']."</a></td>";
} else {
echo "					<td><a href = 'p_review.php?albumID=".$row['albumID']."'>review this! </a></td>";
}

echo "					<td>".$row['binAbbr']."</td>
						<td><img src='crystal2.png'> 13</td>
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

		$('#reviewlabel a').click(function(event) {
			event.preventDefault();
			var qs = $(this).attr('href');
			$.post(qs, function(data) {
				$('#center').html(data);
   			});
		});

		$('#rotationButton a').click(function(event) {
			event.preventDefault();
			$('#rotationtypes').toggle();
		});

		$('#rotationtypes a').click(function(event) {
			event.preventDefault();
			$('#')
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