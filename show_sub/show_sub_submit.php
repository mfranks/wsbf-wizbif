<?
/** show_sub_submit.php - Kevin Haag - Jan 2012
 *  Inserts a show sub request into 
 *  the motherfucking sub_request table
 */
require_once('../conn.php');

if($_POST){

	$username = $_POST['username'];
	$date = $_POST['date'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$show_type = $_POST['show_type'];
	$showName = $_POST['showName'];
	$reason = $_POST['reason'];
	
	$start = date("H:i:s", strtotime($start_time));
	$end = date("H:i:s", strtotime($end_time));
	$dayID = date('w', strtotime($date));
	$date = date('Y-m-d', strtotime($date));

	
	$insert_query = sprintf("INSERT INTO `sub_request` (username, dayID, start_time, end_time, date, reason, show_name, show_typeID) VALUES ('%s', %d, '%s', '%s', '%s', '%s', '%s', %d)", mysql_real_escape_string($username), $dayID, $start, $end, $date, mysql_real_escape_string($reason), mysql_real_escape_string($showName), $show_type); 

	//echo "$insert_query";
	mysql_query($insert_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	//header( 'Location: http://new.wsbf.net/wizbif/show_sub/show_sub.php' ) ;
}
?>