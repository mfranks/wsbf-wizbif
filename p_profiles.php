<?php 


	require_once("connect.php");
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");

	$rot;
	$bUnreviewed;
	$sTrackID;
	$sShowID;
	$sUsername;
	
	if (isset($_GET['showID'])) {
		$sShowID = $_GET['showID'];
	}
	
	if (isset($_GET['username'])) {
		$sUsername = $_GET['username'];
	}
	
	$md_ret_value = MD_check();
	$rot = 0;

if(!empty($sAlbumID)) {

echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_library.php?'>Profiles</a></div></td>
	     		<td><div id = 'review_label'><a href=''>To Be Reviewed</a></div></td>
			    <td><div id = 'rotationtypes'><a href=''>Rotation</a></div></td>
			</tr></table>
		</div>";

		$tracksquery = "
		SELECT DISTINCT t.track_num, t.track_name, t.airabilityID
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		WHERE a.albumID = ".$sAlbumID;

		$detailquery = "
		SELECT a.album_name, a.albumID, b.artist_name, b.artistID
		FROM libartist b
		JOIN libalbum a on a.ArtistID = b.ArtistID
		JOIN libtrack t on t.AlbumID = a.AlbumID
		WHERE a.albumID = ".$sAlbumID;

		$reviewquery = "
		SELECT review, reviewer, albumID
		FROM libreview
		WHERE albumID = ".$sAlbumID;

		$query.=" LIMIT 15;";

		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($tracksquery, $link);
		$review = mysql_query($review, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this album is fucked up: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);
		$rev = mysql_fetch_assoc($review);

echo "<div id='results'>
		<div id='detail'>
			<strong>".$deet['album_name']."</strong><br>
			by <a href= 'artistID=".$deet['artistID']."'>".$deet['artist_name']." </a> <br>
			<div id = 'review'>
				<p>".$rev['review']." - ".$rew['reviewer']".</p>
			</div>
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


	?>