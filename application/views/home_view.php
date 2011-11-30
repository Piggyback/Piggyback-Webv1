<!-- 
    Document   : home_view.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        home view
-->
<!--
   TO-DOs:
        TODO: Resize logo and link to 'home' url with anchor; is logo a sub-div of top-nav-bar or left-list-pane or none?
-->
<html>
    <head>
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script>
	$(function() {
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
        <div class="top-nav-bar">
            <div class="search">
                <form method="get" id="searchform" action="">
                    <input type="text" class="box" />
                    <button class="btn" title="Submit Search">Search</button>
                </form>
            </div>
            <div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
                <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
                    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#tabs-1">Inbox</a></li>
                    <li class="ui-state-default ui-corner-top"><a href="ajax/content2.html">Friend Activity</a></li>
                    <li class="ui-state-default ui-corner-top"><a href="ajax/content3.html">Referral Tracking</a></li>
                </ul>
                <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="tabs-1"> <p> SPRINT TIME </p> </div>
            </div>
        </div>
        <div class="left-list-pane"></div>
        <div class="logo"></div>
        <div class="content"></div>
    </body>
</html>