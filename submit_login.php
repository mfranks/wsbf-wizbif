<!DOCTYPE html>
<html lang="en">

<?php	
	require_once("header.php");
	require_once("hash_functions.php");
	require_once("position_check.php");
	require_once("connect.php");
?>

<head>
	<meta charset="utf-8">
	<title>WSBF: Add Show</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
	<div id="container">
		<center><h1>WSBF-FM Clemson</h1></center>
		<ul>
<?

	if(empty($_SESSION['username']) && $_POST){
		$login = Login();
		$var = $login[0];
		$message = $login[1];
  	   if(!$var){
		die("$message<br><a href=\"/login\">Try again!</a>");
		}
	}
		echo "<h3>Welcome, " . $_SESSION['preferred_name'] . "!</h3>";
		//fishbowl/index.php
	//	echo "<li><a href='https://docs.google.com/a/g.clemson.edu/forms/d/1UPddJuQAMkxxLYmg1Y89hLI5a_ooLv3ifjhrv3zDGTo/viewform' target=\"_blank\">SUBMIT FISHBOWL POINTS</a></li>"; //Spring 2013
	if(reviewCheck()){
		echo "<li><a href=\"library.php\">Go review a CD!</a></li>";
		echo "<li>Or <a href='fishbowl/fishbowl_app.php'>Fill out some fishbowl points!</a></li>";
		echo "<li><a href=\"show_sub/show_sub.php\">Go request/fill a show sub!</a></li>";
		echo "<li><a href=\"archives\">Archives</a></li>";
		}	
	
	if(MD_check()){
		echo "<li>Or you can <a href=\"rotation_control.php\">Print Labels or Move Rotation</a></li>";
		echo "<li>Or you can <a href=\"import/import_main.php\">Import Music</a></li>";
	}

	if(positionCheck("seniorstaff")){
		echo '<li>Or you can <a href="schedule_addshow.php">Add shows to the schedule</a></li>';
	}

	if(positionCheck(array("Computer Engineer", "Chief Engineer", "Equipment Engineer", "Promotions Director"))){
		echo "<li>Write new Engineering Blog entry</li>";
	}



   echo "<li><a href=\"weekly_top_20_tracks.php\">See the weekly top 20!</a></li>";
   $statusID = $_SESSION['statusID'];
   if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4)
      echo "<li><a href=\"profiles/form_edit_profile.php\">Edit your contact info and radio show info!</a></li>";
   echo "<li><a href=\"profiles/view_show_profiles.php\">See radio show profiles!</a></li>";
   echo "<li><a href=\"schedule/schedule.php\">See show schedule!</a></li>";
   echo "<li><a href=\"reviewsByActiveDJs.php\">Who's done their CD reviews?</a></li>";
   echo "<li><a href=\"logout.php\">Log out!</a></li>";
   echo "</ul>";
   echo "</div>";
   echo "</body>";
   echo "</html>";
?>
