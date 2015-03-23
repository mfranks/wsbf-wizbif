<?php

/*
  define variables

  initialize/read in from querystring

  build query 

  build page while fetching results


*/


$actionAdd
$trackID

require_once("connect.php");
require_once("header.php");
require_once("hash_functions.php");
require_once("position_check.php");

$query =  "
    SELECT `libalbum`.`albumID`, `album_code`, `artist_name`, `album_name`
    FROM `libalbum`, `libartist`, `libtrack`
    WHERE `rotationID` = '$rot' 
    AND `libalbum`.`artistID` = `libartist`.`artistID`
    LIMIT 15";
  
    //Submit Query
    $list = mysql_query($query, $link);
    //If query returns FALSE, no albums were returned.  Die with error
    if (!$list) die ('No albums returned: ' . mysql_error());

echo "
        <div id = 'center'>
          <div id = 'filters'> 
            <div id = 'search'><a>Search:</a></div>
            <div id = 'centerlab'><a>Bilbotheque</a></div>
            <div id = 'labels'></div>
          </div> 
          <div id = 'list'>
            <table id = 'list'>
              <tr>
                <th>Track/th>
                <th>Album</th>
                <th>Artist</th>
                <th>Score</th>
              </tr> ";

//Get row from SQL Query, populate tables with albums
while($row = mysql_fetch_assoc($list)) {
  echo "
              <tr>  
                  <td><a>".$row['trackID']."</a></td>
                  <td><a>".$row['albumID']."</a></td>
                  <td><a>".$row['artistID']."</a></td>
                  <td><img src='crystal.png'>".$row['score']."</td>
              </tr>  ";
}

echo "
            </table>   
          </div>  
        </div> ";

echo "
      <script type = 'text/javascript'>

        $('#actions_container a').click(function (event) { 
          event.preventDefault();
        };

        $('#crystal').hover(function (event) {
          $('#crystalUpDown').show();
        });

        $('#crystalUpDown').click(function (event) {
          if(this.html() = '^') {post some shit}
        });


        $('#list a').click(function (event) { 
          event.preventDefault();

        });

      </script>";





?>

