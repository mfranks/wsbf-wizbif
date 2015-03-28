<?
/** show_sub_remove.php - Kevin Haag - Jan 2012
 *  Deletes a show sub request into 
 *  the motherfucking sub_request table
 */
if(!session_id())
session_start();
require_once('../conn.php');
if(empty($_SESSION['username'])){  //gotta be logged in
	die ('You need to login first!<br><a href="/login">Click here to login!</a>');
}
else{
	$username = $_SESSION['username'];
}

if($_GET){
	
	$sub_requestID = $_GET['sub_requestID'];
	
	$security_query = sprintf("SELECT `username` FROM `sub_request` WHERE sub_requestID = %d", $sub_requestID);
	
	//echo "$security_query<br>";
	$row = mysql_query($security_query);
	if (!$row) {
		die ('Error getting username: ' . mysql_error());
	}
	
	if(mysql_num_rows($row)){
		$row_ret = mysql_fetch_row($row);
		$requested_by = $row_ret[0];
	}
	else {
		die ('Go to hell!');	
	}
	
	if($username == $requested_by){
	
	$delete_query = sprintf("DELETE FROM `sub_request` WHERE sub_requestID = %d AND username = '%s'", $sub_requestID, mysql_real_escape_string($username)); 
	//echo "$requested_by<br>";
	//echo "$delete_query";
	mysql_query($delete_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	header( 'Location: http://new.wsbf.net/wizbif/show_sub/show_sub.php' ) ;
	}	
	else{
		die ('Go to hell!');
	}
}
?>