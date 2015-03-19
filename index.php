<?php

include('../conn.php');
define('FIRST_SHOW', 20634); //9545);
/* Shows are now nominally archived on the external bc_china, with mount point in www/new	*/
define('BASE_PATH', "/var/www/wizbif/ZAutoLib/archives/");
define('WEB_PATH', "http://new.wsbf.net/wizbif/ZAutoLib/archives/");

/* This should keep backwards compatibility for archives that remain (if they do). There's an elseif in finding the file to check these two paths as well. */

// define('OLD_BASE_PATH', "E:/WAMP/www/new/archpk/");
// define('OLD_WEB_PATH', "http://wsbf.net/archpk/");

$query = "SELECT s.showID,  s.show_name, s.start_time, s.end_time, dst.type, GROUP_CONCAT(u.preferred_name) dj_names FROM `show` s
INNER JOIN `show_hosts` sh on sh.showID = s.showID
INNER JOIN `users` u on u.username = sh.username
INNER JOIN `def_show_types` dst on dst.show_typeID = s.show_typeID
WHERE dst.type <> 'Automation' AND s.showID >= ".FIRST_SHOW;

if(isset($_GET['showID'])){
	$query .= " AND s.showID = ".mysql_real_escape_string($_GET['showID'])." LIMIT 1";
} else {
	$query .= " GROUP BY s.showID ORDER BY s.showID DESC";
}



if(!isset($_GET['showID'])) {
	// die("no showID");

	// $query = $query . " ";

	$rsc = mysql_query($query) or die(mysql_error());
	$back = $_SERVER['HTTP_REFERER'];




	echo "<p><a href='../submit_login.php'>Go back</a></p>";
	echo "<h2 style='margin: 0 auto; width: 300px'>Download MP3 Show Archives</h2>";
	echo "<table><tr><th>Click</th><th>DJ</th><th>Show(s)</th><th>Date</th></tr>";

	while($arr = mysql_fetch_array($rsc, MYSQL_ASSOC)) {
		if(!empty($arr['end_time'])){
			$endTime = date("g:i a", strtotime($arr['end_time']));
		} else {
			$endTime = "<em>[present]</em>";
		}

		echo "<tr><td><a href='?showID=".$arr['showID']."'>".$arr['showID']."</a></td>".
				"<td style='width: 25%'>".$arr['dj_names']."</td>".
				"<td style='width: 25%'>".$arr['show_name']."</td>".
				"<td>".date("l, F j, Y g:i a", strtotime($arr['start_time'])).
				" - ". $endTime."</td></tr>"; 


	}
	echo "</table>";
}
else {
	$rsc = mysql_query($query) or die(mysql_error());
	$arr = mysql_fetch_array($rsc, MYSQL_ASSOC);
	$playlist = "playlists?showid=".$_GET['showID'];
	echo "<h2 style='margin: 20px auto; width: 30%; text-align: center'>Records for ".$arr['showID']."</h2>";
	$back = $_SERVER['HTTP_REFERER'];
	echo "<p><a href='$back'>Go back</a></p>";

	echo "<table style='margin: 0 auto; width: 90%'><tr><td>DJ</td><td>".$arr['dj_names']."</td></tr>".
		"<tr><td>Show Name</td><td>".$arr['show_name']."</td></tr>".
		"<tr><td>Show Type</td><td>".$arr['type']."</td></tr>".
		"<tr><td>Start Time</td><td>".date("g:i a, l, F j, Y", strtotime($arr['start_time']))."</td></tr>".
		"<tr><td>End Time</td><td>".date("g:i a, l, F j, Y", strtotime($arr['end_time']))."</td></tr>".
		"</table>";
	echo "<p><h2><a href='$playlist'>View playlist</a></h2></p>";
	echo "<h2 style='margin: 20px auto; width: 30%; text-align: center'>Download Links</h2>";
	echo "<table style='margin: 20px auto; width: 40%'>";
	$count = 0;
	$append = "";

	// echo "<tr><td><a href='".WEB_PATH.$arr['showID'].$append.".mp3'>".$arr['showID'].$append.".mp3</a></td></tr>";

	while(TRUE) {
		if($count > 0) $append = " ($count)";
		$filename = $arr['showID'].$append.".mp3";
		$relpath = BASE_PATH.$filename;
		$oldpath = OLD_BASE_PATH.$filename;

		if(file_exists($relpath)){
			$downloadURL = WEB_PATH.$filename;
			echo "<tr><td><audio controls><source src='" . $downloadURL . "' type='audio/mpeg'></audio></td><td><a href='" . $downloadURL . "'>$filename</a></td></tr>";
		}
		elseif(file_exists($oldpath))
			echo "<tr><td><a href='".OLD_WEB_PATH.$arr['showID'].$append.".mp3'>".$arr['showID'].$append.".mp3</a></td></tr>";
		elseif($count > 0){
			break;
		}
		$count++;
		$relpath = BASE_PATH.$filename;
	}
	echo "</table>";

}




?>
