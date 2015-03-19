/**
 * @author Emmanuel John
 *
 */

$(function() {
    if (typeof (WSBF) == 'undefined') {
        WSBF = {};
    }
    
    initPageItems();

    var isMWidgetPlaying = false;
    var isMCenterPlaying = false;
    var isMediaCenterVisible = false;
    //load webcam
    setInterval(function() {
        $(".webcam #webcam").attr("src","http://wsbf.net/wp-content/studioa.jpg");
    }, 5000);
    $("#menubtn").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $('.side_name').toggleClass("active");
    });

    //fixed header
    var shrinkHeader = 150;
    $(window).scroll(function() {
        var scroll = getCurrentScroll();
        if (scroll >= shrinkHeader) {
            $('div.nav').addClass('shrink').removeClass('hide').addClass('shadow');
            $('div.nav .logo-img img').attr('src', "images/logo_small.png").css("margin-top", "25px");
            ;
            //$('div.nav .logo-img img').css("margin-top","25px");
        }
        else {
            $('div.nav .logo-img img').attr('src', "images/logo.png").css("margin-top", "0");
            $('div.nav').removeClass('shrink');
            $('div.nav').removeClass('shadow');
        }
    });
    //returns current scroll position
    function getCurrentScroll() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }


    //load now playing info on player widget
    var widgetReloader;
    function loadInfoWidgetSmall() {
        //load now playing
        WSBF.playlist.nowplaying.getInfo(function(trackinfo) {
            $(".media-widget-small .track_name").html(trackinfo.song);
            $(".media-widget-small .artist_name").html(trackinfo.artist);

            //load album art
            WSBF.lastfm.getAlbumInfo(trackinfo.album, trackinfo.artist, function(albuminfo) {

                //load artist image if album was not found
                if (albuminfo.hasOwnProperty("error")) {
                    WSBF.lastfm.getArtistInfo(trackinfo.artist, function(artistinfo) {
                        if (!artistinfo.hasOwnProperty("error")) {
                            var images = artistinfo.artist.image;
                            if (images[1]["#text"] !== "" && typeof (images) !== "undefined") {
                                $(".media-widget-small .songart").attr("src", images[1]["#text"]);
                            } else {
                                $(".media-widget-small .songart").attr("src", "images/media_small.png");
                            }

                        } else {
                            $(".media-widget-small .songart").attr("src", "images/media_small.png");
                        }
                    });
                } else {
                    var images = albuminfo.album.image;
                    if (images[1]["#text"] !== "" && typeof (images) !== "undefined") {
                        $(".media-widget-small .songart").attr("src", images[1]["#text"]);
                    } else {
                        $(".media-widget-small .songart").attr("src", "images/media_small.png");
                    }

                }
            });
        });
    }

    //load now playing information on media center
    function loadInfoMC() {
        //load now playing
        WSBF.playlist.nowplaying.getInfo(function(trackinfo) {
            $(".wsbf-player-widget .song-album").html(trackinfo.song + "/" + trackinfo.album);
            $(".wsbf-player-widget .artist-name").html(trackinfo.artist);

            var show_name_info = trackinfo.show.show_name;
            if (trackinfo.show.show_alias !== null) {
                show_name_info += "(" + trackinfo.show.show_alias + ")";
            }

            $(".wsbf-player-widget .show-name").html(show_name_info);
            $(".wsbf-player-widget .show-host").html(trackinfo.show.show_host);

            //load album art
            WSBF.lastfm.getAlbumInfo(trackinfo.album, trackinfo.artist, function(albuminfo) {

                //load artist image if album was not found
                if (albuminfo.hasOwnProperty("error")) {
                    WSBF.lastfm.getArtistInfo(trackinfo.artist, function(artistinfo) {
                        if (!artistinfo.hasOwnProperty("error")) {
                            var images = artistinfo.artist.image;
                            if (images[4]["#text"] !== "" && typeof (images) !== "undefined") {
                                $(".wsbf-player-widget .song-art").attr("src", images[4]["#text"]);
                            } else {
                                $(".wsbf-player-widget .song-art").attr("src", "images/media_large.png");
                            }

                        } else {
                            $(".wsbf-player-widget .song-art").attr("src", "images/media_large.png");
                        }
                    });
                } else {
                    var images = albuminfo.album.image;
                    if (images[4]["#text"] !== "" && typeof (images) !== "undefined") {
                        $(".wsbf-player-widget .song-art").attr("src", images[4]["#text"]);
                    } else {
                        $(".wsbf-player-widget .song-art").attr("src", "images/media_large.png");
                    }
                }
            });
        });
    }

    loadInfoWidgetSmall();
    //refresh nowplaying every few seconds
    widgetReloader = setInterval(function() {
        loadInfoWidgetSmall();
    }, 10000);

    $('.listen').on("click",function() {
        displayMediaCenter();
        return false;
    });

    var playerReloader;
    function displayMediaCenter() {
        //open up thr media center and hide everything else
        //clearInterval(widgetReloader);
        //$("#wrapper").hide();
        //$(".media-widget-small").hide();
        $("#pageDialog").modal("show");
        isMediaCenterVisible = true;
        var root = $("#pageDialog .modal-body");
        $("#jquery_jplayer_1").jPlayer("stop");
        root.load("views/fragments/mediacenter.php", function() {
            root.css("background-color", "#000000");
            $("#media-center").fadeIn("slow");
            loadMostRecentPlaylist(".mc-playlist-container");
            //media center player control
            $("#jquery_jplayer_2").jPlayer({
                ready: function(event) {
                    $(this).jPlayer("setMedia", {
                        oga: "http://stream.wsbf.net:8000/v8",
                        mp3: "http://stream.wsbf.net:8000/high"
                    });
                },
                play: function(event) {
                    loadInfoMC();
                    //refresh nowplaying every few seconds
                    playerReloader = setInterval(function() {
                        loadInfoMC();
                    }, 10000);
                },
                swfPath: "js/libs/jQuery.jPlayer.2.6.0/",
                supplied: "oga, mp3",
                solution: "flash, html",
                wmode: "window",
                globalVolume: true,
                cssSelectorAncestor: '#jp_container_2',
                cssSelector: {
                    play: '.jp-play',
                    pause: '.jp-pause',
                    mute: '.jp-mute',
                    unmute: '.jp-unmute',
                    volumeBar: '.jp-volume-bar',
                    volumeBarValue: '.jp-volume-bar-value',
                    gui: '.jp-gui',
                    noSolution: '.jp-no-solution'
                }
            });
        });

    }
    //slider actions
    $(".rslides").responsiveSlides({
        auto: true,
        pager: true,
        nav: true,
        speed: 500,
        maxwidth: 800,
        namespace: "slider-controls",
        prevText: "",
        nextText: ""
    });

    //media widget player control
    $("#jquery_jplayer_1").jPlayer({
        ready: function(event) {
            $(this).jPlayer("setMedia", {
                oga: "http://stream.wsbf.net:8000/v8",
                mp3: "http://stream.wsbf.net:8000/high"
            });
        },
        swfPath: "js/libs/jQuery.jPlayer.2.6.0/",
        supplied: "oga, mp3",
        solution: "flash, html",
        wmode: "window",
        cssSelectorAncestor: '#jp_container_1',
        globalVolume: true
    });

    $(".close-page-dialog").click(function() {
        //$("#wrapper").show();
        //$(".media-widget-small").show();
        $("#pageDialog").modal("hide");
        var root = $("#pageDialog .modal-body");
        root.css("background-color", "#FFFFFF");
        $("#jquery_jplayer_2").jPlayer("stop");
        clearInterval(playerReloader);
        if(isMediaCenterVisible === true){
            isMediaCenterVisible = false;
        }else{
            window.history.back();
        }
    });

    //load recent playlist on home page
    function loadMostRecentPlaylist(container) {
        var parent = $(container);

        WSBF.playlist.getMostRecent(function(data) {
            data = JSON.parse(data);
            $.each(data, function(key, obj) {
                var contentStr = "<div class='views-row'>\
                                    <div class='views-field field-icon'>\
                                        <div class='field-content'>\
                                            <img class='song-art' alt='album art' src='images/media_small.png' width='60' height='60'>\
                                        </div>\
                                    </div>\
                                    <div class='views-field views-field-time'>\
                                        <span class='field-content time'>07:00 - 11:00</span>\
                                    </div>\
                                    <div class='views-field views-field-title'>\
                                        <span class='field-content song-artist'>First Song</span>\
                                    </div>\
                                 </div>";

                var content = $(contentStr);
                var time_played = obj.time_played.split(" ")[1];
                time_played = tConvert(time_played);
                content.find(".time").html(time_played);
                content.find(".song-artist").html(obj.lb_track_name + " - " + obj.lb_artist);
                WSBF.lastfm.getAlbumInfo(obj.lb_album, obj.lb_artist, function(albuminfo) {

                    //load artist image if album was not found
                    if (albuminfo.hasOwnProperty("error")) {
                        WSBF.lastfm.getArtistInfo(obj.lb_artist, function(artistinfo) {
                            if (!artistinfo.hasOwnProperty("error")) {
                                var images = artistinfo.artist.image;
                                if (images[1]["#text"] !== "" && typeof (images) !== "undefined") {
                                    content.find(".song-art").attr("src", images[1]["#text"]);
                                } else {
                                    content.find(".song-art").attr("src", "images/media_small.png");
                                }

                            } else {
                                content.find(".song-art").attr("src", "images/media_small.png");
                            }
                        });
                    } else {
                        var images = albuminfo.album.image;
                        if (images[1]["#text"] !== "" && typeof (images) !== "undefined") {
                            content.find(".song-art").attr("src", images[1]["#text"]);
                        } else {
                            content.find(".song-art").attr("src", "images/media_small.png");
                        }
                    }
                });
                parent.append(content);
            });
        });
    }

    loadMostRecentPlaylist(".playlist-container");

    //refresh playlist
    setInterval(function() {
        loadMostRecentPlaylist(".playlist-container");
    }, 5000);


    $(".footer-nav-unit").hover(function() {
        $(this).find("a").css("color", "#FFFFFF");
        $(this).find("h5").css("color", "#A0BD2B");
    }, function() {
        $(this).find("a").css("color", "#AAAAAA");
        $(this).find("h5").css("color", "#72861F");
    });

    $(".footer-nav-unit a").hover(function() {
        $(this).css("color", "#FF0000");
    }, function() {
        $(this).css("color", "#FFFFFF");
    });
    
    $('.join-content').scrollspy({ target: '.join-nav' });
    function loadSchedule(container, day) {
        var parent = $(container);
        $(".program").remove();
        $(".active").removeClass("active");
        $("#" + day).addClass("active");
        $(".date_header").html(moment().format("MMMM Do"));

        WSBF.schedule.getScheduleByDay(day, function(data) {
            var data = JSON.parse(data);
            if (data.length) {
                console.log(data);
                $.each(data, function(key, obj) {
                    var contentStr = "<div class='program'>\
                                            <p>" + tConvert(obj.start_time) + " - " + tConvert(obj.end_time) + "</p>\
                                            <p>" + obj.show_name + "</p>\
                                            <p> <strong>" + obj.preferred_name + "</strong></p>\
                                        </div>";
                    var content = $(contentStr);
                    parent.append(content);
                });
            } else {
                var contentStr = "<div class='program'>\
                                            <p></p>\
                                            <p></p>\
                                            <p> <strong>No event scheduled for this date. Check back for updates.</strong></p>\
                                        </div>";
                var content = $(contentStr);
                parent.append(content);
            }
        });
    }
    loadScheduleOnWidget(".schedule-current");
    function loadScheduleOnWidget(container) {
        var days = {
            '0': 'SUN',
            '1': 'MON',
            '2': 'TUE',
            '3': 'WED',
            '4': 'THU',
            '5': 'FRI',
            '6': 'SAT'
        };

        var day = moment().day();
        //show formatted date
        var dtoday = moment().format("MM[/]DD");

        $(".schedule-current-day").html(days[day] + " " + dtoday);

        var parent = $(container);
        $(".active").removeClass("active");
        $("#" + day).addClass("active");
        $(".date_header").html(moment().format("MMMM Do"));

        WSBF.schedule.getScheduleByDay(day, function(data) {
            var data = JSON.parse(data);
            if (data.length) {
                //console.log(data);
                $.each(data, function(key, obj) {
                    var contentStr = "<div class='views-row'>\
                            <div class='views-field field-icon'>\
                                <div class='field-content'><img typeof='foaf:Image' src='http://www.offradio.gr/sites/default/files/icon_18.png' width'54' height='54' alt=''>\
                                </div>\
                            </div>\
                            <div class='views-field views-field-time'>\
                                <span class='field-content'>" + tConvert(obj.start_time) + " - " + tConvert(obj.end_time) + "</span>\
                            </div>\
                            <div class='views-field views-field-title'>\
                                <span class='field-content'><strong>" + obj.show_name + "</strong></span>\
                            </div>\
                            <div class='views-field views-field-title'>\
                                <span class='field-content'><strong>" + obj.preferred_name + "</strong></span>\
                            </div>\
                        </div>";
                    /* var contentStr = "<div class='program'>\
                     <p>"+tConvert(obj.start_time)+" - "+tConvert(obj.end_time)+"</p>\
                     <p>"+obj.show_name+"</p>\
                     <p> <strong>"+obj.preferred_name+"</strong></p>\
                     </div>";*/
                    var content = $(contentStr);
                    parent.append(content);
                });
                parent.append("<a href='schedule.php' class='btn btn-default pull-right view-more'>View More</a>");
            } else {
                var contentStr = "<div class='program'>\
                                            <p></p>\
                                            <p></p>\
                                            <p> <strong>No event scheduled for this date. Check back for updates.</strong></p>\
                                        </div>";
                var content = $(contentStr);
                parent.append(content);
            }
        });
    }

    $("#showModalBtn").click(function() {
        $('#pageDialog').modal('show');
    });

    var container = document.querySelector('.blog-boxes');
    if(container){
        var msnry;
        // initialize Masonry after all images have loaded
        imagesLoaded(container, function() {
            msnry = new Masonry(container, {
               // options
               itemSelector: '.blog-item',
               "gutter": 5
            });
        });
    }


    //style blog blog boxes
    //$(".blog-box").css("border-top","1px solid #FF0000");
    
    function initPageItems(){
        //slim scroll for divs
            $('#in-page-content').slimScroll({
                height: '350px',
                size: '4px',
                color: "red",
                wheelStep: 10
            });
            $('.stream').slimScroll({
                height: '350px',
                size: '4px',
                color: "red",
                wheelStep: 10
            });
            //slim scroll for divs
            $('.inner-content-div').slimScroll({
                height: '350px',
                size: '4px',
                color: "red",
                wheelStep: 10
            });
            $('.listen').click(function() {
                displayMediaCenter();
                return false;
            });
            $(".this-week li a").on('click', function() {
                        console.log("got here");
                        loadSchedule(".programs", $(this).attr("id"));
                        return false;
                    });
                    $('#ca-container').contentcarousel();
    }
    //returns index of string
    function istr(str, str_to_find ){
        return str.indexOf(str_to_find);
    }
    //util for ajaxifying pages
    new $.LazyJaxDavis(function(router) {
        var $root = $('#wrapper');
        var $dialog = $("#pageDialog");
        var $dialog_root = $("#pageDialog .modal-body");
        //router options
        router.option({
            davis: {
                throwErrors: false,
                handleRouteNotFound: true
            }
        });

        router.bind('everyfetchstart', function(page) {
            window.scrollTo(0, 0);
            $root.css('opacity', 0.5);
            //TODO: Display Loading...
            //$root.empty().append("Loading...").css("text-align","center");
        });

        router.bind('everyfetchsuccess', function(page) {
            $root.css('opacity', 1);

            $newcontent = $(page.rip('content')).hide();
            
            var pt = page.path;
            //the following pages are loaded into a dialog if needed otherwise embeded
            if(istr(pt,"staff") !== -1 || istr(pt, "contact") !==-1){
                $dialog_root.empty().append($newcontent);
                $dialog.modal('show');
            }else if(istr(pt,"join") !== -1){
                $dialog_root.empty().append($newcontent);
                $dialog.modal('show');
                $('.join-content').scrollspy({target: '.join-nav'});
            }else if(istr(pt,"schedule") !== -1){
                $dialog_root.empty().append($newcontent);
                $(".this-week li a").on('click', function() {
                        console.log("got here");
                        loadSchedule(".programs", $(this).attr("id"));
                        return false;
                    });
                $dialog.modal('show');
            }else{//its not a dialog page
                $dialog_root.empty();
                $dialog.modal("hide");
                $newcontent.find('.dropdown-toggle').dropdownHover();
                $root.empty().append($newcontent);
                initPageItems();
                $newcontent.find('.listen').click(function() {
                    displayMediaCenter();
                    return false;
                });
                loadScheduleOnWidget(".schedule-current");
                loadMostRecentPlaylist(".playlist-container");
            }
            
            //console.log(page.path.indexOf("index"));
            if (istr(pt,"index") !== -1) {
                //slider actions
                $(".rslides").responsiveSlides({
                    auto: true,
                    pager: true,
                    nav: true,
                    speed: 500,
                    maxwidth: 800,
                    namespace: "slider-controls",
                    prevText: "",
                    nextText: ""
                });
            }
            if(istr(pt,"blog")!==-1){
                var container = document.querySelector('.blog-boxes');
                if(container){
                    var msnry;
                    // initialize Masonry after all images have loaded
                    imagesLoaded(container, function() {
                        msnry = new Masonry(container, {
                           // options
                           itemSelector: '.blog-item',
                           "gutter": 5
                        });
                    });
                }
            }
            $newcontent.fadeIn();
            page.trigger('pageready');            
        });

        router.bind('everyfetchfail', function() {
            alert('ajax error!');
            $root.css('opacity', 1);
        });
        router.route([
            {
                path: '/wsbf/',
                fetchstart: function(page) {},
                fetchsuccess: function(page) {},
                pageready: function() {
                    
                    $(".rslides").responsiveSlides({
                        auto: true,
                        pager: true,
                        nav: true,
                        speed: 500,
                        maxwidth: 800,
                        namespace: "slider-controls",
                        prevText: "",
                        nextText: ""
                    });
                }
            },
            {
                path: '/wsbf/schedule.php',
                fetchstart: function(page) {
                },
                fetchsuccess: function(page) {

                },
                pageready: function() {
                    loadSchedule(".programs", moment().day());
                }
            }
        ]);
    });
});