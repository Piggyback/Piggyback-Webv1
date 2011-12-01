<!-- 
    Document   : home_view.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        home view
-->
<!--
   TO-DOs:
        TODO: Resize logo and link to 'home' url with anchor; is logo a sub-div of top-bar or left-list-pane or none? Complete logo CSS
        TODO: Set div alignments from Google Reader firebug analysis
        TODO: Don't set max height to 100% -- prevent the main div from scrolling
-->
<html>
    <head>
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script>
	$(function() 
        {
            // set height of scrollable divs depending on window size
            $('#scrollable-sections').height(($(window).height()-150));
            $('#viewer-page-container').height(($(window).height()-92));
            
            $(window).resize(function() {
                $('#scrollable-sections').height($(window).height()-150)
                $('#viewer-page-container').height($(window).height()-92)
            });
            
            $( "#tabs" ).tabs({
                ajaxOptions: {
                    error: function( xhr, status, index, anchor ) {
                        $( anchor.hash ).html(
                            "Couldn't load this tab. We'll try to fix this as soon as possible.");
                    }
                }
            });
            
            //hover states on the static widgets
            $('ul#icons li').hover(
                function() { $(this).addClass('ui-state-hover'); }, 
                function() { $(this).removeClass('ui-state-hover'); }
            );
	});
	</script>
    </head>
    <body>
        <div id="top-bar">
            <a id="logo-container" href="home">
                <h1 id="logo">
                    <span class="none">Piggyback</span>
                </h1>
            </a>
            <div id="search">
                <form method="get" id="searchform" action="">
                    <input type="text" class="box" id="searchbox"/>
                    <button id="searchbutton" class="btn" title="Submit Search">Search</button>
                </form>
            </div>
        </div>
        <div id="main">
            <div id="left-list-pane">
                <div id="left-list-pane-header">
                    <h1 id="my-lists-heading">
                        My lists
                    </h1>
                </div>
                <div id="scrollable-sections-holder">
                    <div id="scrollable-sections">
                        <div id="scrollable-sections-bottom">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div id="content-frame">
                <div id="content-viewer-container">
                    <div id="content-viewer">
                        <div id="viewer-container">
                            <div id="viewer-page-container">
                                <div id="viewer-page">
                                    <div id="content">
                                        <div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
                                            <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
                                                <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#tabs-1">Inbox</a></li>
                                                <li class="ui-state-default ui-corner-top"><a href="ajax/content2.html">Friend Activity</a></li>
                                                <li class="ui-state-default ui-corner-top"><a href="ajax/content3.html">Referral Tracking</a></li>
                                            </ul>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="tabs-1"> <p> SPRINT TIME </p> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer">
            
        </div>
    </body>
</html>