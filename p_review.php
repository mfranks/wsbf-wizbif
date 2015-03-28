<?php
	
	if(empty($_GET)){ //gotta get the albumID
		die('WTF are you doing here?  Pick an album from the <a href="library.php">list!</a>');
	}
	else {
		$albumID = $_GET["albumID"];
	}
	/*
	if(empty($_SESSION['username'])){  //gotta be logged in

		die ('You need to login first!<br><a href="p_login.php">Click here to login!</a>');
	}
	else{  //get all the shit they have in sesion data that we need
			$username = $_SESSION['username'];
		$pref_name = $_SESSION['preferred_name'];
		$statusID = $_SESSION['statusID'];

	}
	*/
	$username = 'fractimal';
	$pref_name = 'Max Franks';
	$statusID = 9;

	if($statusID == 6) { //status of registered user that isn't anything yet
		die ("You need permission to review a CD, if you're at least an intern, you can email the computer engineer at <a href=\"mailto:computer@wsbf.net\">computer@wsbf.net</a> to request privileges.");
	}
	if($statusID == 7) { //banned 4 life
		die ("Like we'd let $pref_name review a CD?  Skedaddle.");
	}

	if(empty($_GET["edit"])) { //if you're editing a CD review
	   $disabled = "disabled";
	}
	else {
		if (!MD_check()) { //Are they GM, MD, Chief E, Comp E, or Genre Directors?  from position_check.php
			die("You aren't allowed to edit CD reviews!");
		}
		else {
			$disabled = "enabled";  //so the person editing can change the username to the username of whoever reviewed the CD
			//another idea might be to put a drop down list of DJs and let them select the name
		}

	}

	require_once("connect.php");
	require_once("position_check.php");


	//get shit from the album
	$album_query = sprintf("SELECT album_name, num_discs, artistID, labelID, genre, general_genreID, album_code, rotationID 
		FROM libalbum WHERE albumID = '%d'", $albumID);

	$album = mysql_query($album_query, $link);
	
	if (!$album) {
		die ('This is an error message: ' . mysql_error());
	}

	$row = mysql_fetch_array($album, MYSQL_NUM);

	$album_name = $row[0];
	$num_discs = $row[1];
	$artistID = $row[2];
	$labelID = $row[3];
	$genre = $row[4];
	$general_genreID = $row[5];
	$album_code = $row[6];
	$rotationID = $row[7];

	//get artist shit
	$artist_query = sprintf("SELECT artist_name FROM libartist WHERE artistID = '%d'", $artistID);
	$artist = mysql_query($artist_query, $link);
	if (!$artist) {
		die ('This is an error message: ' . mysql_error());
	}
	$artist_get = mysql_fetch_array($artist, MYSQL_NUM);
	$artist = $artist_get[0];

	//Get review shit
	$review = "";
	$review_query = sprintf("SELECT review, username, reviewer FROM libreview WHERE albumID = '%d'", $albumID);
	$rev = mysql_query($review_query, $link);
	if (!$rev) {
		die ('This is an error message: ' . mysql_error());
	}

	if ($review_get = mysql_fetch_array($rev, MYSQL_NUM)){
		$review = $review_get[0];
		$username = $review_get[1];  //if editing a CD review, the right username shows up
		$pref_name = $review_get[2];
	}

	//get label shit
	$label_query = sprintf("SELECT label FROM liblabel WHERE labelID = '%d'", $labelID);
	$label = mysql_query($label_query, $link);
	if (!$label) {
		die ('This is an error message: ' . mysql_error());
	}
	$label_get = mysql_fetch_array($label, MYSQL_NUM);
	$label = $label_get[0];

	//I guess we use foobar records as default
	if($label == "foobar records"){

		$label = ""; 

	}
	echo "
		<div id='detail'>
			<div id = 'fields'>
				<strong>".$album_name."</strong><br>
				by <a href= 'p_library?artistID=".$artistID."'>".$artist." </a> <br>
				Label: <INPUT TYPE = \"Text\" VALUE =\"$label\" NAME = \"label\"><br>";
	//echo "<tr><td><div id=\"top\">Album Artist:</div></td><td> <INPUT TYPE = \"Text\" VALUE =\"$artist\" NAME = \"artist\"><--------</td><td style=\"vertical-align:top\" rowspan=\"7\">This is the album artist, if it's various artists, put \"Various Artists\" and make the track artist the name of the artist who performed each track. If one artist performed all the tracks, leave the track artists alone. If an album is by an artist, but has tracks featuring other artists, you can change the track artist to say \"Album Artist feat. Another Artist.\" If an album has remixes, and titles like \"Track Name remixed by Artist Name,\" you should enter the track as \"Track Name (Remix)\" and make the track artist the name of the artist who did the remix.</td></tr>\n";

	//get the general genre combobox
	$genres_query = "SELECT general_genreID, genre FROM def_general_genres ORDER BY general_genreID ASC";
	$genres = mysql_query($genres_query, $link);
	if (!$genres) {
		die ('This is an error message: ' . mysql_error());
	}

	echo "		Genre: <select $disabled name=\"general_genreID\">";
	while ($genre_get = mysql_fetch_array($genres, MYSQL_NUM)){

		$genreID = $genre_get[0];
		$genre = $genre_get[1];

		echo "			<option value=\"$genreID\"";
		if($genreID == $general_genreID){
			echo " 	selected='true'";	//selects correct general genre in the combobox if editing a CD
		}
		echo "									>$genre</option>";

	}
echo "
				</select> <br>
				<INPUT TYPE = \"Text\" VALUE =\"$genre\" NAME = \"genre\">
			</div>
			<div id ='review'>
				<div id=\"charLeft\"> <strong>Review</strong></div><br>
				<textarea id=\"ta\" name='review' style=\"resize: none;\" cols=\"65\" rows=\"12\">$review</textarea>
			</div>
		</div>";


	//if the username box is disabled it won't post, so we need to pass it somehow
	if($disabled == "disabled"){
			echo "<input type='hidden' name='username' value='$username'>";
			echo "<input type='hidden' name='general_genreID' value='$general_genreID'>";
	}
	else{
		echo "<input type='hidden' name='edit' value='1'/>";  //we use this on submit to see the difference between review and edit
		//I would have done it at the top, but it echoed it before the <form>
	}
echo "
	<div id='list'>
		<table>
		    <tr>
			    <th>Tracks</th>
			    <th></th>
			    <th></th>
			    <th>Status</th>
            </tr>";

	//get the track shit
	$track_query = sprintf("SELECT track_name, disc_num, track_num, artistID, airabilityID FROM libtrack WHERE albumID = '%d'", $albumID);
	$track = mysql_query($track_query, $link);
	if (!$track) {
		die ('This is an error message: ' . mysql_error());
	}
	$track_total = 0;

	while($row = mysql_fetch_array($track, MYSQL_NUM)){

		$track_name = $row[0];
		$disc_num = $row[1];
		$track_num = $row[2];
		$artistID = $row[3];
		$airabilityID = $row[4];

		$tr = "track".$track_num."disc".$disc_num; //name of $track_name
		$ai = $tr."air";						   //name of $airabilityID
		$art= $tr."art";						   //name of $artist_name
		$track_total++;  //we use this to see if they user had all no-airs

		//get shit for each track artist
		$artist_query = sprintf("SELECT artist_name FROM libartist WHERE artistID = '%d'", $artistID);
		$artist = mysql_query($artist_query, $link);
		if (!$artist) {
			die ('This is an error message: ' . mysql_error());
		}
		$artist_get = mysql_fetch_array($artist, MYSQL_NUM);
		$artist_name = $artist_get[0];

		echo "
			<tr>
				<td>";
	 	if ($album_num) {
	 		echo  	$album_num." - ";
	 	}
	 		echo 	$track_num."
	 			</td>
				<td>
					<INPUT TYPE = \"Text\" SIZE ='40' VALUE =\"$track_name\" NAME = \"$tr\">
				</td>
				<td>
					<INPUT TYPE = \"Text\" SIZE ='40' VALUE =\"$artist_name\" NAME = \"$art\">
				</td>
				<td>";

		//get the No Air, reccomended, etc shit
		$air_query = "SELECT airabilityID, airability FROM def_airability";
		$airables = mysql_query($air_query, $link);
		if (!$airables) {
			die ('This is an error message: ' . mysql_error());
		}

		//make the combo boxes
		echo "		<select name='$ai'>\r";
		while ($air_get = mysql_fetch_array($airables, MYSQL_NUM)){

			$airID = $air_get[0];
			$air = $air_get[1];

			echo "\t<option";

			if($airabilityID == $airID){
				echo " selected=\"true\"";  //selects the airabilityID for each, default is No Air
			}
			echo " value=\"$airID\">$air</option>\r";
		}
		echo "
					</select>
				</td>
			</tr>\n";

	}
	echo "
		</table>\n<br>\n";
	echo "<input type='hidden' name='track_total' value='$track_total'>";
	echo "IF ANY OF THE TRACKS ARE MISSING, DON'T REVIEW THE ALBUM.<br>You'll need to email the music director at <a href=\"mailto:music@wsbf.net\">music@wsbf.net</a> and tell them the name and artist of the CD and that they need to re-rip it, you can include your review in the email as well and they can put it in for you. You also need to make sure you actually give them the CD so they can re-rip it.<br><br>";

//character counter
echo "
<script type='text/javascript'>
	$(document).ready(function() {
		var maxlen = 750;
		$('#charLeft').text(maxlen);
		$('#ta').keyup(function() {

			var len = this.value.length;
			if (len >= maxlen) {
			 this.value = this.value.substring(0, maxlen);
			}
			$('#charLeft').text(maxlen - len);
		});
	});
</script>";
echo "<div><input id=\"submit\" class='review' type='submit' value='Submit Review' /></div>
</form>";

echo "</div>";

?>