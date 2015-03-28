<?
/** show_sub_fill.php - Kevin Haag - Jan 2012
 *  Inserts a show sub fill into 
 *  the motherfucking sub_fill table
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
	
	$insert_query = sprintf("INSERT INTO `sub_fill` (sub_requestID, username) VALUES (%d, '%s')", $sub_requestID, mysql_real_escape_string($username)); 

	//echo "$insert_query";
	mysql_query($insert_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	header( 'Location: http://new.wsbf.net/wizbif/show_sub/show_sub.php' ) ;
	 }
?>