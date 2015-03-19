<!DOCTYPE html>
<html lang="en">

<?php	
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");
	require_once("connect.php");
	
	if(empty($_SESSION['username']) && $_POST){
		$login = Login();
		$var = $login[0];
		$message = $login[1];
  	   if(!$var){
			die("$message<br><a href=\"/login\">Try again!</a>");
		}
	}

	$statusID = $_SESSION['statusID'];


echo "	<head>".
	"		<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>".
	"		<meta charset='utf-8'>".
	"		<title>WIZBIZ</title>".
	"		<link rel='stylesheet' href='p_style.css' />".
	"	</head>".
	"	<body>".
	"	<div id='container'>".
	"		<h1>WSBF-FM Clemson</h1>".
	"		<div id='actions_container'>".
	"			<h3>Welcome, " . $_SESSION['preferred_name'] . "!</h3>".
	"			<ul>";
	if(reviewCheck()){
echo "				<div id='review_actions'>".				
	"					<li live = true><a href='p_library.php'>Library</a></li>".
	"					<li><a href='fishbowl/fishbowl_app.php'>Fishbowl points</a></li>".
	"					<li><a href='show_sub/show_sub.php'>Show subs</a></li>".
	"					<li><a href='archives'>Archives</a></li>".
	"				</div>";
	}	

	if(MD_check()){
echo "				<div id='md_actions'>".
	"					<li><a href=\"rotation_control.php\">Print Labels or Move Rotation</a></li>".
	"					<li><a href=\"import/import_main.php\">Import Music</a></li>".
	"				</div>";
	}

	if(positionCheck("seniorstaff")){
echo "				<div id = 'ss_actions'>".
	"					<li><a href='schedule_addshow.php'>Add shows to the schedule</a></li>".
	"				</div>";
	}	
	
	if(positionCheck(array("Computer Engineer", "Chief Engineer", "Equipment Engineer", "Promotions Director"))){
echo "				<li>Write new Engineering Blog entry</li>";
	} 
   	
echo "				<li><a href=\"weekly_top_20_tracks.php\">See the weekly top 20!</a></li>";
   	
   	if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4) {
echo "				<li><a href=\"profiles/form_edit_profile.php\">Edit DJ info!</a></li>";
   	}
   
echo "				<li><a href=\"profiles/view_show_profiles.php\">See radio show profiles!</a></li>".
	"				<li><a href=\"schedule/schedule.php\">See show schedule!</a></li>".
	"				<li><a href=\"reviewsByActiveDJs.php\">Who's done their CD reviews?</a></li>".
	"				<li><a href=\"logout.php\">Log out!</a></li>".
	"			</ul>".
	"		</div>". // id = actions_container
	"		<div id = 'center'>". 
	"		</div>". // id = center
	"	</div>". //div container
	"	</body>".

"	<script type = 'text/javascript'>".
"		$(function() {".
"			$.post('p_library.php', function(data) {".
"				$('#center').html(data);".
"   		});".
"		});".

"		$('#actions_container a').click(function (event) { ".
"			event.preventDefault();".

"   		var url = $(this).attr('href');".
"   		$.post(url, function(data) {".
"				$('#center').html(data);".
"   		});".

"			$('[live=true]').attr('live', false);".
"			$(this).attr('live', true);".

"		});".
"	</script>".
"	</html>";
?>

