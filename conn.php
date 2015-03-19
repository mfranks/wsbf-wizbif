<?php
/** SIMPLE redo of connect.php **/
/** Should, over time, move MOST scripts to this version. **/
/** In the interest of simplicity and efficiency. **/



$link = mysql_connect('localhost', 'logbook', 'TrNWKbDb7un45nTx') or die("Could not connect".mysql_error());
   mysql_select_db('wsbf', $link) or die("Could not select database");

function genProfileURL($username) {
	if($username == '') return "#";
	$url = "#";

	$username = strtolower($username);
	$username = str_replace(' to ', '-', $username);
	$username = str_replace(' ', '-', $username);
	$username = str_replace('...', '', $username); //nolan whitman

	$url = "http://" . $_SERVER['HTTP_HOST'] . "/users/" . $username;
	return $url;
}

function getNumConnections ($url) {
	$statarr=file($url);
	$nextLine = FALSE;
	$lookFor = "<td>Current Listeners";
	$prefix = "<td class=\"streamdata\">";
	$suffix = "</td>";
	$listeners = 0;
	foreach ($statarr as $line) {
		$where = strpos($line, $lookFor);
		if($where !== FALSE) {
			$nextLine = TRUE;
			continue;
		}
		if($nextLine === TRUE) {
			$content = str_replace($prefix, '', $line);
			$content = str_replace($suffix, '', $content);
			$listeners += (int)$content;
			$nextLine = FALSE;
		}
	}
	return $listeners;
}

function sendErrorMessage($showID, $comments){
	$q = sprintf("INSERT INTO errors (showID, comments) VALUES ('%d', '%s')", $showID, mysql_real_escape_string($comments));
	mysql_query($q) or die("MySQL error in file " . __FILE__ . " near line " . __LINE__ . ": " . mysql_error());
}



?>
