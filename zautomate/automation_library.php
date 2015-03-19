<?php
$debug = FALSE;

if($debug)
	echo "<pre>";

require_once('../conn.php');
require_once('../utils_ccl.php');
// sanitizeInput();

/** get newest lbshow primary key **/
function maxShowID() {
	$rs = mysql_query("SELECT MAX(showID) FROM `show`") or die(mysql_error());
	$maxShow = mysql_fetch_array($rs);
	return $maxShow[0];
}

/** pull `show` for next newest rotation show; -1 if future **/
function nextShowID($prior) {
	$qu = sprintf("SELECT showID FROM `show` WHERE showID > %d AND show_typeID = 0 ORDER BY showID ASC LIMIT 1", $prior);
	$rs = mysql_query($qu) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());
	$show = mysql_fetch_array($rs, MYSQL_ASSOC);
	if($show['showID'] > 0 && $show['showID'] != $prior)
		return $show['showID'];
	else return -1;
}


function rewindShowID($days) {
	$then = time() - $days * 86400;
//	$then = $timeNow - ($days * 86400)
	$mysql_then = date("Y-m-d H:i:s", $then);
	$qu = sprintf("SELECT showID FROM `show` WHERE show_typeID = 0 AND start_time > '%s' ORDER BY showID ASC LIMIT 1", $mysql_then);
	$rs = mysql_query($qu) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());
	$show = mysql_fetch_array($rs, MYSQL_ASSOC);
	return $show['showID'];
}




/** prints output as created in genBestOfPlaylist() **/
function printOutput($output) {
	$ctr = 0;
	foreach($output as $line)
		//if ($ctr < 4) {
//		if(TRUE) {		// why is this if statement here?
			echo implode("<|>", $line) . "\n";
			$ctr += 1;
//		}
		
}

/**
 * logs in automation
 * requires that no one is logged in already
 * @return int $showID the showID of the newly created show in the database
 */
function automationLogin(){
		$ifs = sprintf("INSERT INTO `show` (start_time, show_typeID, show_name, scheduleID) VALUES ('%s', 8, 'The Best of WSBF', NULL)", $time);
	$insert_fucking_show = mysql_query($ifs) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

	$showID = mysql_insert_id($link);	// get showID of row just inserted

			$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES (%d, 'Automation', NULL)", $showID);
	$insert_fucking_show_host = mysql_query($ifsh) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());	
	return($showID);
}

/** allWordsIn
 * David Cohen
 * Takes the search string and a the column name (or an array of column names)
 * Returns a string to insert into the WHERE clause of a MySQL query to 
 * return rows with that contain words in the following column.
 * 
 */

function allWordsIn($searchString, $columnNames){
	if(!is_array($columnNames))	// make into 1-element array if not already
		$columnNames = array($columnNames);
	$words = preg_split("/\s+/",$searchString, NULL, PREG_SPLIT_NO_EMPTY);
			// split into array alphanumeric characters and underscore
	$colWords = array();
	
	foreach($columnNames as $col){
		$thisColumnArray = array(); 
		foreach($words as $word)
			$thisColumnArray[] = "{$col} LIKE '%{$word}%'";
		$colWords[] = implode(" AND ", $thisColumnArray);
	}
	$result = "((" . implode(") OR (", $colWords) . "))";

	return($result);
}

?>