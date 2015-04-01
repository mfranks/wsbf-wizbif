<?php

$AutomationPlaylistID = 5343;

$bAdding = false;


require_once("connect.php");
require_once("header.php");
require_once("hash_functions.php");
require_once("position_check.php");

$query =  "
    SELECT DISTINCT a.album_name, a.albumID, b.artist_name, b.artistID, t.track_num, t.track_name
      FROM libPlaylistTrackMap p
      JOIN libtrack t on t.albumID = p.albumID and t.track_num = p.track_num
      JOIN libalbum a on p.albumID = a.albumID
      JOIN libartist b on b.artistID = a.artistID
      WHERE p.playlistID = ".$AutomationPlaylistID;
  
    //Submit Query
    $list = mysql_query($query, $link);
    //If query returns FALSE, no albums were returned.  Die with error
    if (!$list) die ('No albums returned: ' . mysql_error());

echo "
        <div id = 'filters'> 
          <table><tr>
            <td><div id = 'search'><input id = 'searchInput' type='text' value='Search: '></div></td>
            <td><div id = 'centerlab'><a href='p_automate.php?'>ZAutomate Queue</a></div></td>
          </tr></table>
        </div>
        <div id='results'>
        <div id = 'list'>
          <div id = 'addQueue'>
            <div>
              <button type='submit' class='pure-button pure-input-1-2 pure-button-primary'>Add</button>
            </div>
          </div>
          <table>
            <tr>
              <th>Track</th>
              <th>Album</th>
              <th>Artist</th>
              <th>Score</th>
            </tr> ";

//Get row from SQL Query, populate tables with albums
while($row = mysql_fetch_assoc($list)) {
  echo "
            <tr>  
                <td><a href ='albumID=".$row['albumID']."&track_num=".$row['albumID']."'>".$row['track_name']."</a></td>
                <td><a href ='albumID=".$row['albumID']."'>".$row['album_name']."</a></td>
                <td><a href ='artistID=".$row['artistID']."'>".$row['artist_name']."</a></td>
                <td>∧ 13 ∨<img src='crystal.png'></td>
            </tr>  ";
}

echo "
          </table>  
        </div> </div>";

echo "
      <script type = 'text/javascript'>

        $('#addQueue button').click(function(event){
          event.preventDefault();
          
          if ($('#addQueue div').size() == 1) {
            $.post('p_addTrack.php', function(data) {
              $('#addQueue').append(data);
            });
          }
          else {
            $('#addQueue div:nth-child(2)').remove();
            //$('#addQueue').append('<p>Added!</p>');
          }
        });
        
        $('#list a').click(function(event){
          event.preventDefault();
          var qs = $(this).attr('href');
          $.post('p_library.php?' + qs, function(data) {
              $('#center').html(data);
          });
        });

        /*
        $('#crystal').hover(function (event) {
          //$('#crystalUpDown').show();
        });

        $('#crystalUpDown').click(function (event) {
          //if(this.html() = '^') {post some shit}
        });
        */

      </script>";

?>

