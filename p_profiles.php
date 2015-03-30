<?php

	require_once("connect.php");
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");

	$sScheduleID;
	$sPlaylistID;
	$sShowID;
	$dj;
	
	//get show that has aired on wsbf
	if (isset($_GET['scheduleID'])) {
		$sScheduleID = $_GET['scheduleID'];
	}
	
	//get individual show for archive and playlist
	if (isset($_GET['playlistID'])) {
		$sPlaylistID = $_GET['playlistID'];
	}
	else if (isset($_GET['showID'])) {
		$sShowID = $_GET['showID'];
	}
	//get shows and archives and playlists for a user
	if (isset($_GET['dj'])) {
		$dj = $_GET['dj'];
	}

	//for list view, allow filter by showType
	if (isset($_GET['showType'])) {
		$showType = $_GET['showType'];
	}
	
	$md_ret_value = MD_check();

	echo "
		<div id = 'filters'> 
			<table><tr>
				<td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
 				<td><div id = 'centerlab'><a href='p_profiles.php?'>Dasein</a></div></td>
	     		<td></td>
	     		<td><div id = 'review_label'><a href='p_library.php?rot=0'>Active</a></div></td>
			    <td></td>
			    <td><div id = 'rotationtypes'><a href='p_library.php?rot=1'>Specialty</a></div></td>
			</tr></table>
		</div>";



	//Playlist // or Show
		// detail - DJ, Show
		// list - tracks
	if( !empty($sPlaylistID) ) {

		$tracksquery = "
		SELECT 
			a.album_name, m.albumID, 
			b.artist_name, b.artistID, 
			m.track_num, t.track_name
		FROM libPlaylistTrackMap m
		JOIN libplaylists p on p.playlistID = m.playlistID
		JOIN libtrack t on t.albumID = m.AlbumID and t.track_num = m.track_num
		JOIN libalbum a on a.albumID = m.AlbumID
		JOIN libartist b on b.ArtistID = a.ArtistID
		WHERE m.playlistID = ".$sPlaylistID;
	
		$detailquery = "
		SELECT 
			a.album_name, m.albumID, 
			b.artist_name, b.artistID, 
			m.track_num, t.track_name, 
			p.playlist_name, p.username
		FROM libPlaylistTrackMap m
		JOIN libplaylists p on p.playlistID = m.playlistID
		JOIN libtrack t on t.albumID = m.AlbumID and t.track_num = m.track_num
		JOIN libalbum a on a.albumID = m.AlbumID
		JOIN libartist b on b.artistID = a.artistID
		WHERE m.playlistID = ".$sPlaylistID." 
		LIMIT 1";
		
		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($tracksquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this album is fucked up: ' . mysql_error());
		if (!$details) die ('No Tracks returned, this album is fucked up: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

		echo "
		<div id='results'>
			<div id='detail'>
				<strong>".$deet['playlist_name']."</strong><br>
				by <a href= 'p_profiles.php?dj=".$deet['username']."'>".$deet['username']." </a> <br>
			</div> 

			<div id = 'list'>
				<table>
			     <tr>
				     <th>Track</th>
				     <th>Album</th>
		             <th>Artist</th>
		             <th></th> 
	             <tr>";
	    
     	while  ( $row = mysql_fetch_assoc($list) ) {
	        echo "<tr>	
				 	    <td><a href = ''>".$row['track_name']."</a></td>
				 	    <td><a href = 'artistID=".$row['artistID']."''>".$row['artist_name']."</a></td>
				 	    <td><a href = 'albumID=".$row['albumID']."''>".$row['album_name']."</a></td>
		                
					    <td>hp <a href='heart.php?liker=you&thing=albumID'>    14  </a></td>
				</tr> ";
		}
		
		echo "</div>
		</div>";
		
	}

	// detail - scheduled show name, time, username
	// list - tracks played 
	else if( !empty($sShowID) ) {

		$tracksquery = "
		SELECT DISTINCT
			l.lb_track_name, l.lb_artist,
			l.lb_album, l.time_played,
			a.album_name, a.albumID, 
			b.artist_name, b.artistID, 
			l.lb_track_num, t.track_name
		FROM logbook l
		LEFT JOIN libalbum a on a.album_code = l.lb_album_code
		LEFT JOIN libtrack t on t.albumID = a.albumID and t.track_num = l.lb_track_num
		LEFT JOIN libartist b on b.ArtistID = a.ArtistID
		WHERE l.showID = ".$sShowID."
		and l.played = 1
		ORDER BY l.time_played ASC";
	
		$detailquery = "

		SELECT c.start_time, c.scheduleID, c.showID, sch.show_name, sh.username
		FROM `show` c
		LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
		LEFT JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
		WHERE c.showID = ".$sShowID."
		LIMIT 1";
		
		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($tracksquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('Somethings fucky: ' . mysql_error());
		if (!$details) die ('fucky with the details ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

		echo "
		<div id='results'>
			<div id='detail'>
				<strong> <a href='p_profiles.php?scheduleID=".$deet['scheduleID']."'>".$deet['show_name']."</a></strong> ".$deet['start_time']."<br>
				by <a href= 'p_profiles.php?dj=".$deet['username']."'>".$deet['username']." </a> <br>
			</div> 

			<div id = 'list'>
				<table>
			     <tr>
				     <th>Track</th>
				     <th>Artist</th>
		             <th>Album</th>
		             <th></th> 
	             <tr>";
	    
     	while  ( $row = mysql_fetch_assoc($list) ) {
	        echo "<tr>	
				 	    <td>".$row['lb_track_name']."</td>
				 	    <td><a href = 'p_library.php?artistID=".$row['artistID']."'>".$row['lb_artist']."</a></td>
				 	    <td><a href = 'p_library.php?albumID=".$row['albumID']."'>".$row['lb_album']."</a></td>
		                
					    <td><img src='crystal2.png'>  13 <a href = ''>+</a></td>
				</tr> ";
		}
		
		echo "</table></div>
		</div>";
		
	}

	// Scheduled Show
		// detail - DJ(s)
		// list - playlists
	else if(!empty($sScheduleID)) {

		$showsquery = "
		SELECT c.start_time, c.scheduleID, c.showID, sch.show_name, sh.username
		FROM `show` c
		LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
		LEFT JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
		WHERE c.scheduleID = ".$sScheduleID."
		ORDER BY c.start_time DESC";

		$detailquery = "
		SELECT c.start_time, c.scheduleID, c.showID, sch.show_name, sh.username
		FROM `show` c
		LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
		LEFT JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
		WHERE c.scheduleID = ".$sScheduleID."
		LIMIT 1";


		//Submit Queries
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($showsquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('this scheduled show is fucky: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

echo "<div id='results'>
		<div id='detail'>
			<strong>".$deet['show_name']."</strong><br>
			by <a href= 'p_profiles.php?dj=".$deet['username']."'>".$deet['username']." </a> <br>";
	
echo "</div> 

	    <div id='list'>
			<table>
			    <tr>
				    <th>Shows</th>
				    <th></th>
				    <th></th>
	            </tr>";

        //Get row from SQL Query, populate tables with shows
		while($row = mysql_fetch_assoc($list)) {
			echo "<tr>
	                <td><a href = 'p_profiles.php?showID=".$row['showID']."'>".$row['start_time']."</a></td>
	        		<td><img src='crystal2.png'>  13 <a href = ''>+</a></td>	
				</tr> ";
	    }
echo "
			</table>
		</div>
	</div>";
	}
	
	// DJ
		// detail - name, info or whatever
		// list - playlists by show

	else if(!empty($dj)) {
	
		$showsquery = "
		SELECT c.start_time, c.scheduleID, c.showID, sch.show_name, sh.username
		FROM `show` c
		LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
		LEFT JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
		WHERE username = '".$dj."'
		ORDER BY c.start_time DESC
		LIMIT 50";

		$playlistsquery = "
		SELECT 
			p.playlist_name
		FROM playlists p
		WHERE p.username = ".$dj."
		LIMIT 15";

		$detailquery = "
		SELECT c.start_time, c.scheduleID, c.showID, sch.show_name, sh.username
		FROM `show` c
		LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
		LEFT JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
		WHERE username = '".$dj."'
		LIMIT 1";

		//Submit Query
		$details = mysql_query($detailquery, $link);
		$list = mysql_query($showsquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('No Tracks returned, this artist aint got shit: ' . mysql_error());

		$deet = mysql_fetch_assoc($details);

		echo "
		<div id='results'>
			<div id='detail'>
				<strong>".$deet['username']."</strong><br>
				<p>shows</p>
			</div>
			<div id='list'>
				<table>
				    <tr>
					    <th>Show</th>
		            </tr> ";

        //Get row from SQL Query, populate tables with Albums
		while($row = mysql_fetch_assoc($list)) {
echo "
					<tr>
						<td><a href = 'p_profiles.php?scheduleID=".$row['scheduleID']."'>".$row['show_name']."</a></td>
				 	    <td><a href = 'p_profiles.php?showID=".$row['showID']."'>".$row['start_time']."</a></td>
						<td><img src='crystal2.png'>  13</td>
					</tr> ";
	    }
echo "			</table>
			</div>
		 </div>";
	}


	// list of everything
		// filter by
			// DJ 
			//	- active 
			//  - team
			// SHOW
			//  - current
			//  - showtype
			//  - genre?
	

	else { //nothing set


		// default to list of latest shows/playlists -
	 	// show/playlist name, username(s), date 
		
		$showsquery = "
			SELECT c.start_time, c.scheduleID, sch.show_name, sh.username
			FROM `show` c
			LEFT JOIN schedule sch on c.scheduleID = sch.scheduleID
			JOIN `schedule_hosts` sh on c.scheduleID = sh.scheduleID
			ORDER BY c.start_time DESC
			LIMIT 50";

		//Submit Query
		$list = mysql_query($showsquery, $link);

		//If query returns FALSE, no albums were returned.  Die with error
		if (!$list) die ('WSBF is fucked: ' . mysql_error());
echo 	"
		<div id = 'results'>
			<div id = 'list'> 
				<table>
 				<tr>
				    <th>Show</a></th> 
		            <th>DJ(s)</th>
				    <th>Time</td>
	            </tr> ";
	

		//Get row from SQL Query, populate tables with albums
			while($row = mysql_fetch_assoc($list)) {
echo "				<tr>	
						<td><a href = 'p_profiles.php?scheduleID=".$row['scheduleID']."'>".$row['show_name']."</a></td>
					 	<td><a href = 'p_profiles.php?dj=".$row['username']."'>".$row['username']."</a></td>
					 	<td>".$row['start_time']."</td>
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