<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
        
        <script>
                
                    $.ajax({
                        url: "http://api.ihackernews.com/page?format=jsonp",
                        dataType: "jsonp",
                        success: function( data, textStatus, jqXHR ) {
                            alert(data); 
                            if ( callback ) callback ( data );
                        },
                        error: function( jqXHR, textStatus, errorThrown ) {
                            console.log( textStatus + ": " + errorThrown );
                        }
                    });     

        </script>
        
        
    </head>
    
    <body>
        <div data-role="page" id="hackerNews">
    
            <div data-role="header">
                <a id="btnRefresh" href="#" data-icon="refresh">Refresh</a>
                <h1>Hacker News &nbsp;<span id="itemCount" class="count ui-btn-up-c ui-btn-corner-all">0</span></h1>
            </div>

            <div id="content" data-role="content">
                <ol class="newsList" data-role="listview"></ol>
            </div>

        </div>

        <script id="newsItem" type="text/x-jquery-tmpl">
            <li class="newsItem">
                <h3><a href="${url}">${title}</a></h3>
                <p class="subItem"><strong>${postedAgo} by ${postedBy} </strong></p>
                <div class="ui-li-aside">
                    <p><strong>${points} points</strong></p>
                    <p>${commentCount} comments</p>
                </div>
            </li>
        </script>

    </body>
</html>
