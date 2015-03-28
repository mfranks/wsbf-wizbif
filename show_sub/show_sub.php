<?php

	require_once("../connect.php");
	require_once("../header.php");
	require_once("../hash_functions.php");
	require_once("../position_check.php");

	$edit = positionCheck("seniorstaff");
	$username = $_SESSION['username'];

?>
<script type="text/javascript">
<!--
function confirmation(type, id, date) {
	if (type){
		var answer = confirm("Are you sure you want to sub this show on "+date+"?  You will be held responsible if the show is missed.")
		var link = "\show_sub_fill.php?sub_requestID="+id
	}
	else{
		var answer = confirm("Are you sure you want to remove your show sub request?  If you realize you still need a sub and request one less than 24 hours before your show, you will be held responsible for missing your show.")
		var link = "\show_sub_remove.php?sub_requestID="+id
	}	
	
	if (answer){
		window.location = link;
	}
}
//-->
</script>
<div id="contai">
	<h2>Show Sub Form</h2>
	<h3>Click <a href='/wizbif/show_sub/show_sub_add.php'>here</a> to request a show sub.</h3>
	<p><div id="successMessage" class="success"></div></p>
<p>
<?php
	
	function dateFixer ($day, $start, $end){
		
		$start_am = date("A", strtotime($start));
		$end_am = date("A", strtotime($end));
		$start_min = date("i", strtotime($start));
		$end_min = date("i", strtotime($end));
		$start_hr = date("g", strtotime($start));
		$end_hr = date("g", strtotime($end));
		
		$outDate = $day." ".$start_hr;
		
		if($start_min != '00'){
			$outDate .= ":".$start_min;
		}

		if($start_am != $end_am){
			$outDate .= $start_am;
		}
		
		$outDate .= "-".$end_hr;
		
		if($end_min != '00'){
			$outDate .= ":".$end_min;
		}
		
		return $outDate.$end_am;
		
	}

	//Query to get show subs
	$query = "SELECT `sub_request`.`sub_requestID`, `users`.`preferred_name`, `def_days`.`day`, `sub_request`.`start_time`, `sub_request`.`end_time`, `sub_request`.`date`, `sub_request`.`posted_time`, `sub_request`.`reason`, `sub_request`.`show_name`, `def_show_types`.`type`, `sub_request`.`username` 
					 FROM `sub_request`, `users`, `def_days`, `def_show_types` 
					 WHERE `users`.`username` = `sub_request`.`username` 
					 AND `def_days`.`dayID` = `sub_request`.`dayID`
					 AND `def_show_types`.`show_typeID` = `sub_request`.`show_typeID`
					ORDER BY `sub_request`.`date`";
	
	//Submit Query
	$list = mysql_query($query, $link);
	//If query returns FALSE, no albums were returned.  Die with error
	if (!$list) {
		die ('No sub requests returned: ' . mysql_error());
	}
	
	//Formatting table
	echo "<style type='text/css'> table.bottomBorder td, table.bottomBorder th { border-bottom:1px dotted black; } </style>";
	echo "<table class = 'bottomBorder'>";
	echo "	<tr><th style='text-align:center'>Requested By</th>
				<th style='text-align:center'>Show Name</th>
				<th style='text-align:center'>Show Type</th>
				<th style='text-align:center'>Show Time</th>
				<th style='text-align:center'>Request Date</th>
				<th style='text-align:center'>Reason</th>
				<th style='text-align:center'>Filled By</th>";
	if($edit) {
		echo "<th style='text-align:center'>Time Posted</th>";
	}
	echo "</tr>";
	
	//Get row from SQL Query, populate tables with albums
	while ($row = mysql_fetch_array($list, MYSQL_NUM)){
		$sub_requestID = $row[0];
		$requested_by = $row[1];
		$day = $row[2];
		$start_time = $row[3];
		$end_time = $row[4];
		$request_date = $row[5];
		$posted_time = $row[6];
		$reason = $row[7];
		$show_name = $row[8];
		$show_type = $row[9];
		$sub_request_username = $row[10];
		
		//$start_time = date("g:iA", strtotime($start_time));
		//$end_time = date("g:iA", strtotime($end_time));
		
		//$show_time = $day." ".$start_time."-".$end_time;
		$compare_date = strtotime($request_date);
		$cpr = $request_date." ".$start_time;
		$cpr = strtotime($cpr);
		$post_time = strtotime($posted_time);
		
		$request_date = date("n-j-y", $compare_date);
		$posted_time = date("n-j-y g:iA", $post_time);
		
		$right_now = strtotime("now");
		
		$two_weeks = strtotime ('-2 week' , $right_now);
		
		
		
		
		if(($compare_date >= $right_now) | (($compare_date >= $two_weeks) & ($edit))){
			$show_time = dateFixer($day, $start_time, $end_time);
			echo "<tr style=\"text-align:center\">";
		
			echo "<td>$requested_by</td><td>$show_name</td><td>$show_type</td><td>$show_time</td><td>$request_date</td><td>$reason</td>";
		
			$fill_query = "SELECT `users`.`preferred_name` FROM `sub_fill`, `users` WHERE `users`.`username` = `sub_fill`.`username` AND `sub_fill`.`sub_requestID` = $sub_requestID";
		
			$fill = mysql_query($fill_query);
			if (!$fill) {
				die ('Error getting filled request: ' . mysql_error());
			}
			
			if(mysql_num_rows($fill)){
				$fill_ret = mysql_fetch_row($fill);
				$filled_by = $fill_ret[0];
			}
			elseif($username == $sub_request_username){
				$filled_by = "<input type='button' onclick='confirmation(0,$sub_requestID,\"$request_date\")' value='Remove Request'>";
			}	
			else{
				$filled_by = "<input type='button' onclick='confirmation(1,$sub_requestID,\"$request_date\")' value='Fill Request'>";
			}
			
			echo "<td>$filled_by</td>";
		
			if($edit) {
				$time_before_post = strtotime('+24 hours' , $post_time);
				if ($cpr <= $time_before_post){
					
					echo "<td><font color=\"red\">$posted_time</font></td>";
				}
				else{
					echo "<td>$posted_time</td>";
				}
			}
		
			echo "</tr>";
		}
		
	}
	echo "</table>";
	
?>	
</p></div>
