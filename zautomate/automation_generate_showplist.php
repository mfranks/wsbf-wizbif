<?php 
define('DEBUG', false);
require_once('automation_library.php');
$rewind_days = 12;
$min_tracks = 10;

$rewindID = rewindShowID($rewind_days, time());
if(DEBUG)
	echo "found rewindID\n";

if(!isset($_GET['sid'])) 
	$sID = $rewindID;
else $sID = (int)$_GET['sid'];


/** we need to rewind **/
if($sID >= maxShowID()) {
	$sID = $rewindID;
}

/** we need to move forward to the max days back **/
if($sID < $rewindID) {
	$sID = $rewindID;
}

function genBestOfPlaylist($sID) {
	$final_output = array();
	$cdcodes = array(); //don't play the same CD twice
	
	
	/** get all the non-optional cdcodes/tracks from the current playlist **/

//logbookID	showID	lb_album_code	lb_rotation	lb_track_num	lb_track_name	lb_artist	lb_album	lb_label	time_played	played	deleted
	//$qv = sprintf("SELECT lb_album_code, lb_track_num FROM `logbook` WHERE showID = '%s' AND lb_rotation != 'O' ORDER BY logbookID ASC", $sID);
	$qv = sprintf("SELECT lb_album, lb_track_num FROM `logbook`, `libalbum` where showID = '%s' and lb_album_code = libalbum.album_code and liblabum.rotationID != 5");
	$rt = mysql_query($qv) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());
	// echo "ROWS: ".mysql_num_rows($rt)."\n";
	while ($song = mysql_fetch_array($rt, MYSQL_ASSOC)) {
		
		$cdcode = $song['lb_album_code'];
		$trnum = $song['lb_track_num'];
		if($cdcode == "" || $trnum == "") {
			continue;
		}
		//echo "---$cdcode $trnum \n";
		
		/** for this one song, pull artist, album, label info **/
// albumID	album_name	num_discs	album_code	artistID	labelID	mediumID	genre	general_genreID	rotationID
	$qw = sprintf("SELECT * FROM `libartist`, `libalbum`, `liblabel`, `def_rotations` WHERE libalbum.album_code = '%s' AND libalbum.artistID = libartist.artistID AND libalbum.labelID = liblabel.labelID AND def_rotations.rotationID = libalbum.rotationID LIMIT 1", $cdcode);

		$ru = mysql_query($qw) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());
		$cd = mysql_fetch_array($ru, MYSQL_ASSOC);
		$cdid = $cd['albumID'];
		
		/** get the song and file names **/


	// libtrack: track_name	disc_num	track_num	artistID	airabilityID	file_name	albumID
	$qx = sprintf("SELECT track_name, file_name 
			FROM `libtrack` 
			WHERE albumID = '%s' 
			AND track_num = '%s'
			AND airabilityID <= 1 
			LIMIT 1", $cdid, $trnum);
		
		$rv = mysql_query($qx) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());
		$track = mysql_fetch_array($rv, MYSQL_ASSOC);
		
		/** it's here, and it hasn't been played in *this* set before **/
		if($track['file_name'] != "" && !in_array($cdcode, $cdcodes)) {
			$cdcodes[] = $cdcode;
			
			$output = array();
			$output[] = $cdcode; //0
			$output[] = $trnum;
			$output[] = $cd['genre'];
			$output[] = substr($cd['rotation_bin'], 0, 1);
			$output[] = $cd['artist_name'];
			$output[] = $track['track_name'];
			$output[] = $cd['album_name'];
			$output[] = $cd['label'];
			$output[] = $track['file_name']; //7
		
			$final_output[] = $output;
		
		}
	}
	return $final_output;
}

/* require the plist to have 10 valid tracks - otherwise it's too short for cohesion */

$list = genBestOfPlaylist($sID);
while(count($list) < $min_tracks) {
$list = genBestOfPlaylist($sID);
//		if(DEBUG)
//			echo "invalid $sID \n";

		$sID = nextShowID($sID);
		
		if($sID == -1){
			$sID = rewindShowID($rewind_days);
		}
		$list = genBestOfPlaylist($sID);
	}

echo "SHOWID ".$sID."\n";
printOutput($list);





?>
