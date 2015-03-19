<?php
require_once('conn.php');
//Check to see if the user can see MD tools
	function MD_check(){
		
	if(!isset($_SESSION['positionID']) || empty($_SESSION['positionID']))
		return false;

	else
		$positionID = $_SESSION['positionID'];

	$valid_positions = array(0,1,2,3,8,13,14,17,18,19,20);
	foreach($valid_positions as $pos)
		if(in_array($pos, $positionID))
			return true;
	
	return false;
}	

function engineerCheck($positionID){
	$valid_positions = array(1,8,10,6,5);
	foreach($valid_positions as $pos)
		if(in_array($pos, $positionID))
			return true;
	
	return false;
	
}

/**
 * positionCheck($valid_positions)
 * Checks to see whether the current user has rights as dictated by position name given
 * 
 * @author David A. Cohen, II.
 * 
*/
function positionCheck($valid_positions){
	if(is_array($valid_positions)){
		// check each position, returning at the first valid one
		foreach($valid_positions as $pos)
			if(positionIDCheck(getPositionIDFor($pos)))
				return true;
		// else invalid position
		return false;
	}
	else if($valid_positions == "seniorstaff")
		return positionIDCheck(array(0,1,2,3,4,5,6,7,8));
}

function positionIDCheck($valid_positions){
	// check session
		$result = false;
		
	if(!empty($_SESSION['positionID'])){
		$positionID = $_SESSION['positionID'];

		if(is_array($valid_positions)){
			foreach($valid_positions as $pos)
				if(in_array($pos, $positionID))
					$result = true;
		}
	
		else if($valid_positions == $positionID)
				$result = true;
								
	}
		return $result;		
}

/**
 * Returns the positionID for a given position name, or -1 if not found in database
 * @author David A. Cohen, II.
 * @param string $position The name of the position to be checked (i.e. "Computer Engineer")
 * @return int The correspoinding positionID
 */
function getPositionIDFor($position){
	$q = sprintf("SELECT positionID FROM `def_positions` WHERE position LIKE '%s' LIMIT 1", mysql_real_escape_string($position));
	$res = mysql_query($q) or die("MySQL error [". __FILE__ ."] near line" . __LINE__ . ": " .mysql_error());
	if(mysql_num_rows($res) == 0)
		$result = -1;
	else{
		$row = mysql_fetch_assoc($res);
		$result = $row['positionID'];
	}
	
return($result);
}


// check if user is eligible to write a review
function reviewCheck(){
	return($_SESSION['statusID'] == 0 || $_SESSION['statusID'] == 1 || $_SESSION['statusID'] == 5);
}



?>
