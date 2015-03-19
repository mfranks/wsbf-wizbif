<?php
   require_once('../conn.php');
   require_once('../utils_ccl.php');
   sanitizeInput();
   define("SECS_DAY", 60*60*24);
   if(!is_numeric($_GET['days']))
   $days = 30;
   else $days = $_GET['days'];
   $now = time();
   $then = $now - $days * SECS_DAY;
   $fmt = date("Y-m-d H:i:s", $then);
   //echo $fmt."\n";
   $qu = "SELECT showID FROM `show` WHERE start_time > '$fmt' and TIMESTAMPDIFF(MINUTE, start_time, end_time) > 60 ORDER BY end_time ASC LIMIT 300";
   //echo $qu;
   $rows = mysql_query($qu) or die(mysql_error());
   while($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      $shows[] = $row;
   echo $shows[rand(0, 99)]['showID'];

?> 
