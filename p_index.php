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


echo "
<!DOCTYPE html>
<html lang='en'>
	<head>     
 		<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>     
 		<meta charset='utf-8'>     
 		<title>WIZBIZ</title>     
 		<link rel='stylesheet' href='p_style.css' />  
 	</head>     
     <body>     
     	<div id='container'>     
     		<div id = 'header'>
     			<h1>WSBF-FM Clemson</h1>   
     			<strong>Welcome, ".$_SESSION['username']."</strong>
	 				<a href=\"logout.php\">Log out</a> <br>
     		</div>  
     		<div id ='plateau'>
	     		<div id='actions_container'>
					<a live = true href='p_library.php'>Library</a> <br>
					<a href='p_automate.php'>Automation Queue</a> <br>  
					<a href='fishbowl/fishbowl_app.php'>Fishbowl points</a><br>    
					<a href='show_sub/show_sub.php'>Show subs</a><br>       
					<a href='archives'>Archives</a><br>      
					<a href=\"rotation_control.php\">Labels/Rotation</a><br>
					<a href=\"import/import_main.php\">Import Music</a><br> 
					<a href='schedule_addshow.php'>Show schedule</a><br>
	 				<a href='dick'>Engineering Blog</a><br>
	 				<a href=\"weekly_top_20_tracks.php\">Weekly top 20</a><br>
	 				<a href=\"profiles/form_edit_profile.php\">Edit DJ info</a><br>
	 				<a href=\"profiles/view_show_profiles.php\">Show profiles</a><br>
	 				<a href=\"schedule/schedule.php\">Show schedule</a><br> 
	 				<a href=\"reviewsByActiveDJs.php\">Reviews</a><br>      
	     		</div>
	     		<div id='center'></div>
			</div>
			<div id = 'footer'>
	 			<a>|> BIRBS w/ Rob & Katie</a>
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
		});

		$('#actions_container a').click(function (event) { 
			event.preventDefault();

   			var url = $(this).attr('href');
   			$.post(url, function(data) {
				$('#center').html(data);
   			});

			$('[live=true]').attr('live', false);
			$(this).attr('live', true);

		});
	</script>
</html>";
?>

