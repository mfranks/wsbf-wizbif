<?php

/* Start book-keeping */
        if(!session_id())	session_start();

	require_once("position_check.php");

        /* Must get the scheduleID */
	if(empty($_POST)) 
		die('WTF are you doing here?  Pick a show from the <a href="edit_profile.php">list!</a>');
	else
		$scheduleID = $_POST["scheduleID"];

        /* Make sure user is logged in */
	if(empty($_SESSION['username'])){  //gotta be logged in

		die ('You need to login first!<br><a href="login.php">Click here to login!</a>');
	}

        /* Get all the info about the user in the session data*/
	else{
		$username = $_SESSION['username'];
		$pref_name = $_SESSION['preferred_name'];
		$statusID = $_SESSION['statusID'];
	}

         /* Block registered user that is not a dj or anything yet */
	if($statusID == 6){
		die ("You need permission to review a CD, if you're at least an intern, you can email the computer engineer at <a href=\"mailto:computer@wsbf.net\">computer@wsbf.net</a> to request privileges.");
	}

        /* Block user if they are banned for life */
	if($statusID == 7){
		die ("Like we'd let $pref_name review a CD?  Skedaddle.");
	}

        /* Check to make sure the user is allowed to edit this show info */
        
	if(empty($_GET["edit"])){ //if you're editing a CD review
	   $disabled = "disabled";
	}
	else{

		if(!MD_check()){ //Are they GM, MD, Chief E, Comp E, or Genre Directors?  from position_check.php

			die("You aren't allowed to edit CD reviews!");

		}
		else{

			$disabled = "enabled";  //so the person editing can change the username to the username of whoever reviewed the CD
			//another idea might be to put a drop down list of DJs and let them select the name
		}
	}
	require_once("connect.php");

/* End book-keeping */



/* Get existing info about the show */

      /* Build the query */
      $show_query = sprintf("SELECT schedule.scheduleID, schedule_hosts.schedule_alias, schedule.show_name, schedule.description
                                   FROM schedule_hosts, schedule
                                   WHERE schedule.scheduleID = '%s' and schedule_hosts.scheduleID = '%s' and schedule_hosts.username = '%s'", $scheduleID, $scheduleID, $username);

      /* Execute the query */
      $show_results = mysql_query($show_query, $link);
      
      if ($show_results == 0)
           echo "<br><br>Yes it failed<br><br>";

      $show = mysql_fetch_assoc($show_results);


/* Echo the header HTML information */
echo "html";
echo "<head>";
echo "<title>WSBF: Edit Your Show Information</title>";
echo "<style>#top {text-align:right;}  #center {text-align:center;}</style>";
echo "<script type='text/javascript' src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js'></script>";
print_description_counter();
echo "</head>";


/* Show the form */
   echo "<body>";
   echo "<h5> Show info <h5>";
   echo "<form action='edit_profile_show_submit.php' method='POST'><input type='hidden' name='scheduleID' value='$scheduleID'/><input type='hidden' name='username' value='$username'/>";
      /* Start table */
      echo "<table>\n";
 
               /* Show name row */
          echo "<tr><td><div id=\"$show[show_name]\">Show name:</div></td><td> <INPUT TYPE = \"Text\" " .
               "SIZE ='40' VALUE =\" $show[show_name] \" NAME = show_name ></td></tr>\n";

               /* Show alias row */
          echo "<tr><td><div id=\"top\">Show alias:</div></td><td> <INPUT TYPE = \"Text\" " .
               "SIZE ='40' VALUE =\" $show[schedule_alias] \" NAME = show_alias ></td></tr>\n";

      /* End table */
      echo "</table>";

      /* Text area */
      echo "<textarea   id=\"$show[scheduleID]\"   style=\"resize: none;\"  cols=\"65\"   rows=\"12\"   onkeyup=\" countChar(this, $show[scheduleID])\"    > $show[description]  </textarea>";
      echo "<br>Characters remaining for show description: ";
      echo "<span id=\"$('charLeft')\"> </span> <br>";


   echo "<div><input id=\"submit\" class='review' type='submit' value='Submit Review' /></div>";
   echo "</form>";
   echo "</body>";
echo "</html>";



   function print_description_counter() {
      echo "<script>\n";
      echo "function countChar(val, charId){\n";
      echo "var len = val.value.length;\n";
      echo "if (len >= 500) {\n";
      echo "val.value = val.value.substring(0, 500);\n}\n";
      echo "$(#('charLeft'.charId)).text(500 - len);return len\n};\n";
      echo "</script>\n";

   }
?>

