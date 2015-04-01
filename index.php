<?php	
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");
	require_once("connect.php");
	
	if(empty($_SESSION['username'] && $_POST)){
		$login = Login();
		$var = $login[0];
		$message = $login[1];
  	   if(!$var){
			die("$message<br><a href='p_login.php'>Try again!</a>");
		}
	}

	$statusID = $_SESSION['statusID'];
	$username = 'fractimal';


	$playlistsquery = " 
		SELECT playlist_name, playlistID, username
		FROM libplaylists
		WHERE 
			username = 'fractimal'
		LIMIT 10";

	//Submit Queries
	$playlists = mysql_query($playlistsquery, $link);


echo "
<!DOCTYPE html>
<html lang='en'>
	<head>     
		<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>   
		<script src='jPlayer-2.9.2/dist/jplayer/jquery.jplayer.min.js'></script>    
 		<meta charset='utf-8'>     
 		<title>WIZBIZ</title>     
 		<link rel='stylesheet' href='p_style.css' />  
 	</head>     
     <body>     
     	<div id='container'>     
     		<div id = 'header'>
     			<h1>WSBF-FM Clemson</h1>   
     			<strong>Welcome, ".$_SESSION['username']."</strong>
	 				<a href=\"logout.php\">Log out</a> 
     		</div>  
     		<div id ='plateau'>
	     		<div id='actions_container'>
					<a live = true href='p_library.php'>Library</a> <br> 
					<a href='schedule/schedule.php'>Schedule</a><br>
					<a href='rotation_control.php'>Labels/Rotation</a><br>
					<a href='import/import_main.php'>Import Music</a><br>
	 				<a href='p_weekly_top_20_tracks.php'>Weekly top 20</a><br>
	 				<a href='p_profiles.php'>Profiles and Playlists</a><br>";
//if ($playlists) {
 	while ( $row = mysql_fetch_assoc($playlists)) {
 echo 	"			<a href='p_profiles.php?playlistID=".$row['playlistID']."'> - ".$row['playlist_name']."</a><br>";
 	}
//}
echo 	"		  
	     		</div>
	     		<div id='center'></div>
			</div>
			<div id = 'footer'>
	 			<div id='mediaPlayer'>
				    <div id='mediaContainer'></div>
				    <div class='jp-play' id='playButton'>Play</div>
				</div>
			</div>
	 	</div>
     </body>";
     		/*   
	if(reviewCheck()){}	
	if(MD_check()){}
	if(positionCheck("seniorstaff")){}	
	if(positionCheck(array("Computer Engineer", "Chief Engineer", "Equipment Engineer", "Promotions Director"))){} 
   	if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4) {}
*/
   	echo "
	<script type = 'text/javascript'>

		$(function() {

			$.post('p_library.php', function(data) {
				$('#center').html(data);
   			});
			
			var mediaPlayer = $('#mediaContainer');
			
			mediaPlayer.jPlayer({
    			ready: function () { 
		        	$(this).jPlayer('setMedia', {
		            	m4a: 'audio/spirals.mp3'
		        	});
				},
				swfPath: 'http://jplayer.org/latest/dist/jplayer',
		        supplied : 'm4a',
		        cssSelector: {
		            play: '#playButton'
		        }
			});
		});

		$('#playButton').click( function() {
		    	if ( $('#playButton').html() == 'Play' ) {
		    		$('#mediaContainer').jPlayer('play');
		        	$('#playButton').html('Pause');
		        	console.log('played');
		    	}
		    	else {
		    		$('#mediaContainer').jPlayer('pause');
					$('#playButton').html('Play');
					console.log('paused');
		    	} 
		    });

		$('#actions_container a').click(function (event) { 
			event.preventDefault();

   			var wizbif = $(this).attr('href');
   			$.post(wizbif, function(data) {
				$('#center').html(data);
   			});

			$('[live=true]').attr('live', false);
			$(this).attr('live', true);
			
			//change browser URL to the given link location
			if(wizbif!=window.location){
				//window.history.pushState({path:wizbif},'',wizbif);
			}
			/*
			// the below code is to override back button to get the ajax content without page reload
			$(window).bind('popstate', function() {
				$.ajax({wizbif,success: function(data){
					//$('#center').html(data);
				}});
			});
			*/
		});
	</script>
</html>";
?>

