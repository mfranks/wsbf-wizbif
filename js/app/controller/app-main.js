/*
 * Emmanuel John
 */
if (typeof (WSBF) == 'undefined') {
    WSBF = {};
}

//function for dynamically creating namespaces
// adapted from  Stoyan Sefanov's Javascript Patterns book
WSBF.namespace = function(ns_string) {
    var parts = ns_string.split('.'),
            parent = WSBF, i;
    // strip redundant leading global
    if (parts[0] === "WSBF") {
        parts = parts.slice(1);
    }
    for (i = 0; i < parts.length; i += 1) {
        // create a property if it doesn't exist
        if (typeof parent[parts[i]] === "undefined") {
            parent[parts[i]] = {};
        }
        parent = parent[parts[i]];
    }
    return parent;
};


function Track(song, album, artist, show) {
    this.artist = artist;
    this.song = song;
    this.album = album;
    this.show = show;
}

function Show(show_name, show_host, show_alias){
    this.show_name = show_name;
    this.show_host = show_host;
    this.show_alias = show_alias;
}
// create the playlist module
WSBF.namespace("WSBF.playlist");

WSBF.playlist = (function() {

    (function() {
        //initialization code goes here
    })();

    //reveal code
    return{
        
        /**
         * @public
         * @argument {function} callback callback function that takes the returned data
         * @returns {json}  returns the most recent first 20 played song. Do JSON.stringify() to see returned object properties
         */
        getMostRecent:function(callback){
            getResource("api/playlist/current.php",null, function(data){
                callback(data);
            });
        },
        
        //exposed functions go here
        find: function() {
            //find a particular song
        },
        findByTime: function() {
            //find song from time played
        }
    };
})(WSBF, this);

WSBF.namespace("WSBF.playlist.nowplaying");

WSBF.playlist.nowplaying = (function() {
    return{
        getInfo: function(callback) {
            getResource("api/playlist/now.php",null, function(data) {
                //console.log(data);
                data = JSON.parse(data);

                var show = new Show(data.show_name, data.username, data.show_alias);

                var trackinfo = new Track(data.lb_track_name,data.lb_album,
                    data.lb_artist, show);

                callback(trackinfo);
            }, function(error) {
                console.log(error);
            });
        }
    };
})();

WSBF.namespace("WSBF.lastfm");

//some set of functions for working with last.fm's apis
// e.g url = http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=&artist=Cher&album=Believe&format=json
WSBF.lastfm = (function(){
    var rest_url = "http://ws.audioscrobbler.com/2.0/?";
    var api_key = "74e3ab782313ff6e306a5f52a0e043ab";
    var default_format = "json";

    var construct_lastfm_url = function(object){
        var _url = rest_url, key;
        for(key in object){
            _url= _url + key+"="+object[key]+"&";
        }
        _url= _url+"format="+default_format;
        return _url;
    };

    return{
        getAlbumInfo:function(album, artist, callback){
            var properties = {
                method:"album.getinfo",
                api_key:api_key,
                artist:artist,
                album:album
            };
            var _url = construct_lastfm_url(properties);
            getResource(encodeURI(_url), null,function(data) {
                //console.log(data);
                callback(data);
            }, function(error) {
                console.log(error);
            });
        },
        getArtistInfo:function(artist, callback){
            var properties = {
                method:"artist.getinfo",
                api_key:api_key,
                artist:artist
            };
            var _url = construct_lastfm_url(properties);
            getResource(encodeURI(_url),null, function(data) {
                callback(data);
            }, function(error) {
                console.log(error);
            });

        }
    };
})();

WSBF.namespace("WSBF.schedule");

WSBF.schedule = (function(){
    var _url = "api/schedule/schedule.php";
    return{
        getScheduleByDay: function(day, callback){
            var pdata = {
                'day':day
            };
            getResource(_url,pdata, function(data) {
                callback(data);
            }, function(error) {
                console.log(error);
            });
        },
        getScheduleByWeek: function(callback){
            var pdata = {
                'day':-1
            };
            getResource(_url,pdata, function(data) {
                callback(data);
            }, function(error) {
                console.log(error);
            });
        },
        findInSchedule:function(callback){
            
        }
    };
})();

//convert time to 12 hour format
function tConvert (time) {
  // Check correct time format and split into components
  time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

  if (time.length > 1) { // If time format correct
    time = time.slice (1);  // Remove full string match value
    time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
    time[0] = +time[0] % 12 || 12; // Adjust hours
  }
  
  return time.join (''); // return adjusted time or original string
}

//data type is json by default
function getResource(resource_url, data, success_callback, error_callback) {
    $.ajax({
        url: resource_url,
        data:data
    }).then(success_callback, error_callback);
};



