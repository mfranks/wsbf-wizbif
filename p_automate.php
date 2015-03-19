<!DOCTYPE html>
<html lang="en">
  <head>     
      <script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>     
      <meta charset='utf-8'>     
      <title>WIZBIZ</title>     
      <link rel='stylesheet' href='p_style.css' />  
    </head>     
    <body>     
      <div id='container'>     
        <div id = 'header'>
          <h1>WSBF-FM Clemson</h1>   
          <h3>Welcome, DJ  D</h3>  
        </div>   
        <div id='actions_container'>
          <a live = true href='p_library.php'>Library</a> <br>    
          <a href='fishbowl/fishbowl_app.php'>Fishbowl points</a><br>    
          <a href='show_sub/show_sub.php'>Show subs</a><br>       
          <a href='archives'>Archives</a><br>      
          <a href=\"rotation_control.php\">Labels/Rotation</a><br>
          <a href=\"import/import_main.php\">Import Music</a><br> 
          <a href='schedule_addshow.php'>Show schedule</a><br>
          <a href='dick'>Engineering Blog</a><br>
          <a href=\"weekly_top_20_tracks.php\">Weekly top 20</a><br>
          <a href=\"profiles/form_edit_profile.php\">Edit DJ info</a><br>
          <a href=\"profiles/view_show_profiles.php\">Show profiles</a><br>
          <a href=\"schedule/schedule.php\">Show schedule</a><br> 
          <a href=\"reviewsByActiveDJs.php\">Reviews</a><br>
          <a href=\"reviewsByActiveDJs.php\">Regulations</a><br>
          <a href=\"logout.php\">Log out</a>        
        </div>      
        <div id = 'center'>
          <div id = 'filters'> 
            <div id = 'search'><a>Search:</a></div>
            <div id = 'centerlab'><a>Bilbotheque</a></div>
            <div id = 'labels'>
              <div id = 'review_label'>
                 <a href=''>To Be Reviewed</a>
              </div>
               <div id = 'rotation_label'>
                <div id = 'rotationtypes'>
                   <a href=''>N</a>
                   <a href=''>H</a>
                   <a href=''>M</a>
                   <a href=''>L</a>
                   <a href=''>J</a>
               </div>
              </div>
            </div>
          </div> 
          <div id = 'list'>
            <table id = 'list'>
              <tr>
                <th>Artist</th>
                <th>Album</th>
                <th></th>  
              </tr> 
              <tr>  
                  <td><a>Fot i Hose</a></td>
                  <td><a>Heart of Man </a></td>
                  <td>hp <a href='heart.php?liker=you&thing=albumID'>    14  </a></td>
                  <td>r <a href='read_review.php?albumID=$albumID'>DJ Dan</a></td>
              </tr>  

              <tr>  
                  <td><a>Vtehutueu</a></td>
                  <td><a>Lonerism</a></td>
                  <td>l <a href='heart.php?liker=you&thing=albumID'>       </a></td>
                  <td>r <a href='read_review.php?albumID=$albumID'> 2</a></td>
              </tr> 
              <tr>  
                  <td><a>Casio Kids</a></td>
                  <td><a>?</a></td>
                  <td>l <a href='heart.php?liker=you&thing=albumID'>3</a></td>
                  <td>r <a href='read_review.php?albumID=$albumID'> Brad C</a></td>
              </tr> 
            </table>   
          </div>  
        </div>    
        <div id = 'footer'>
          <a>|> BIRBS w/ Rob & Katie  < 9</a>
        </div>
      </div>  
  </body>
</html>

