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
        TODO: What if list names are too long?
-->
<html>
    <head>
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../assets/js/home_mike_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_kim_js.js"></script>
        <script type="text/javascript">
//	$(function() 
//        {                
            // Dialog Link
//            $('.dialog_link').click(function(){
//                    // get id of the vendor, which is the id of the pop up button
//                    vendorID = $(this).attr('id');
//
////                    for(var i = 0; i < vendorData.length; i++) {
////                        if (vendorData[i][0] == vendorID) {
////                            vendorName = vendorData[i][1];
////                        }
////                    }
//
////                    $('#dialog').dialog("option","title","Refer Friends to " + vendorName);
////                    $("#fuzz").fadeIn();  
////                    $('#dialog').dialog('open');
//                    return false;
//            });
            
//            $(document).on("mouseenter", ".dialog_link", function() {
//                $(this).addClass('ui-state-hover');
//            });
            
//            $(document).on("mouseleave", ".dialog_link", function() {
//                $(this).removeClass('ui-state-hover');
//            });
                
            // close dialog when you click out of it
//            $(document).on("click", "#fuzz", function(){
//                $('#dialog').dialog("close"); 
//            });
            
//            // merge kim's search with home shell
//            $('#searchform').submit(function() {
////                alert('handler called');
////                alert(document.domain);
////                
//                // jqueryUI tabs handling
//                var $inputs = $('#searchform :input');
//                var values = {};
//                $inputs.each(function() {
//                    values[this.name] = $(this).val();
//                });
//                
//                jQuery.post('searchvendors/perform_search', {
//                    searchLocation: values['searchLocation'],
//                    searchText: values['searchText']
//                    }, function(data) {
//                        // data will be JSON-encoded
////                        alert(data);
//                        var parsedJSON = jQuery.parseJSON(data);
//                        var vendorData = getVendorData(parsedJSON);
//                        displaySearchResults(vendorData);
////                        alert(parsedJSON.length);
////                        eval(data);
//
////                        alert(parsedJSON.searchResults[0].result.formatted_address);  // THIS FRACKIN' WORKS!
////                        alert(array.length);
//                    });
//                    
//                $('#search-content').removeClass("ui-tabs-hide");
//                $('#inbox-content, #ui-tabs-1, #ui-tabs-2').addClass("ui-tabs-hide")
//                $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");
//                
//                return false;
//            });
//	});
        
//        function getVendorData(parsedJSON) {
//        //TODO: ERROR HANDLING
//        //TODO: MAKE CODE ROBUST -- if there is a missing element or if multiple cities (for example): check for type
//            var srcLat = parsedJSON.srcLat;
//            var srcLng = parsedJSON.srcLng;
//            
//            var results = parsedJSON.searchResults;
//
//            var vendorData = new Array();
//            for(var i=0; i<results.length; i++) {
//                var singleVendor = new Array();
//                singleVendor['name'] = results[i].result.name;
//                singleVendor['reference'] = results[i].result.reference;
//                singleVendor['id'] = results[i].result.id;
//                singleVendor['lat'] = results[i].result.geometry.location.lat;
//                singleVendor['lng'] = results[i].result.geometry.location.lng;
//                singleVendor['phone'] = results[i].result.formatted_phone_number;
//                singleVendor['addr'] = results[i].result.formatted_address;
//                singleVendor['addrNum'] = results[i].result.address_components[0].short_name;
//                singleVendor['addrStreet'] = results[i].result.address_components[1].short_name;
//                singleVendor['addrCity'] = results[i].result.address_components[2].short_name;
//                singleVendor['addrState'] = results[i].result.address_components[3].short_name;
//                singleVendor['addrCountry'] = results[i].result.address_components[4].short_name;
//                singleVendor['addrZip'] = results[i].result.address_components[5].short_name;
//                singleVendor['website'] = results[i].result.website;
//                singleVendor['icon'] = results[i].result.icon;
//                singleVendor['rating'] = results[i].result.rating;
//                var types = new Array();
//                // store types
////                alert("name:" + name + "\nreference:" + reference + "\nid:" + id + "\nlat:" + lat + "\nlng:" + lng + "\nphone:" + phone + "\naddr:" + addr + "\naddrNum:" + addrNum +
////                "\naddrStreet:" + addrStreet + "\naddrCity:" + addrCity + "\naddrState:" + addrState + "\naddrCountry:" + addrCountry + "\naddrZip:" + addrZip + "\nwebsite:" + website +
////                "\nicon:" + icon + "\nrating:" + rating);
//                for(var j=0; j<results[i].result.types.length; j++) {
//                    types[j] = results[i].result.types[j];
////                    alert(types[j]);
//                }
//                singleVendor['types'] = types;
//                
//                // add singleVendor to vendorData array
//                vendorData[i] = singleVendor;
//            }
//            return vendorData;
//        }
//        
//        function displaySearchResults(vendorData) {
//            <div id='accordion'>
//            <div>
//                        <h3><a href=\"#\">$name</a></h3>
//                        <div>
//                            <table class=\"formattedTable\" width=\"100%\" style=\"font-size:13px; font-family:helvetica;\">
//                                <td width=\"70%\">
//                                  $addrNum $addrStreet<BR>
//                                  $addrCity $addrState $addrZip<br>
//                                  $phone
//                                </td>
//                                <td width=\"30%\" align=\"right\" style=\"font-size:13px; font-family:helvetica;\">          
//                                  <p><a href=\"#\" id=\"$id\"class=\"dialog_link ui-state-default ui-corner-all\">
//                                      <span class=\"ui-icon ui-icon-plus\"></span>Refer to Friends
//                                  </a></p>
//                                </td>
//                            </table>
//                        </div>
//                </div>
//
//            // add rows to accordion
//            var htmlString = "<div id='accordion'>";
//            
//            for (var i=0; i<vendorData.length; i++) {
//                htmlString = htmlString + "<div><h3><a href='#'>" + vendorData[i].name + "</a></h3>" +
//                    "<div> <table class='formatted-table'> <td class='formatted-table-info'>" +
//                    vendorData[i].addrNum + " " + vendorData[i].addrStreet + "</br>" +
//                    vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip + "</br>" +
//                    vendorData[i].phone + "</td>" +
//                    "<td class='formatted-table-button' align='right'>" +
//                    "<p><a href='#' id=" + vendorData[i].id + " class='dialog_link ui-state-default ui-corner-all'>" +
//                    "<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p></td></table></div></div>";
//            }
//            
//            htmlString = htmlString + "</div>";
//            
//            // create pop up div
//            htmlString = htmlString + 
//                "<div id='dialog'>" +
//                    "<div id='popup-widget' class='ui-widget'>" +
//                        "<form id='addFriend' method='post'>" +
//                            "<label for='tags'><B>Search for friends to refer</b><BR></label>" +
//                            "<input type='search' size='35' name='friend' id='tags'>" +
//                            "<input type='submit' value='Add to List'/>" +
//                        "</form>" +
//                    "</div>" +
//                    "<div id='right-col'>" +
//                        "<div id='addedFriends' style='width:300px; height:100px; clear:both; overflow:auto;'>" +
//                        "</div>" +
//                    "</div>" +
//                    "<div id='comment-area'>" +
//                        "<label for='explanation'><B>Add a comment with your referral</B></label>" +
//                        "<textarea name='comment' id='explanation' rows='4' cols='100%'></textarea>" +
//                    "</div>" +
//                "</div>";
//            
////            alert(htmlString);
//            $('#search-content').html(htmlString);
//            
//            // TODO: OPTIMIZE BELOW JQUERY BINDING
//            // Accordion
//            $("#accordion").accordion({ header: "h3" });
//            
//            // Dialog			
//            $('#dialog').dialog({
//                    autoOpen: false,
//                    width: 750,
//                    closeOnEscape: true,
//                    show: 'drop',
//                    hide: 'drop',
//                    resizable: false,
//                    beforeClose: function() {
                        // reset all values in pop up to blank
                        //document.getElementById("explanation").value = '';
                        //document.getElementById("addedFriends").innerHTML = '';
                        //document.getElementById("tags").value = '';
                        //friendList = [];

                        // fade out dark background
//                        $("#fuzz").fadeOut();  
//                    },
//                    buttons: {
//                        "Refer!": function() { 
//                            if (friendList.length < 1) {
//                                alert("You did not select any friends to refer. Please try again.");
//                            }
//                            else {
//                                // create ajax request
//                                var ajaxRequest;
//
//                                // code for IE7+, Firefox, Chrome, Opera, Safari
//                                if (window.XMLHttpRequest) {
//                                      ajaxRequest = new XMLHttpRequest();
//                                }
//                                // code for IE6, IE5
//                                else {
//                                      ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
//                                }
//
//                                // Create a function that will receive data sent from the server
//                                ajaxRequest.onreadystatechange=function() {
//                                      if (ajaxRequest.readyState==4 && ajaxRequest.status==200) {
//                                        $('#dialog').dialog("close"); 
//                                      }
//                                } 
//
//                                // create query for inserting row
//                                var addReferralQuery = "INSERT INTO Referrals VALUES ";
//                                for (var i = 0; i < friendList.length; i++){
//                                    var now = new Date();
//                                    now = now.format("yyyy-mm-dd HH:MM:ss");
//                                    var comment = document.getElementById('explanation').value;
//                                    var friendUID = friendList[i][0];
//                                    addReferralQuery = addReferralQuery + "(NULL," + myUID + "," + friendUID + ",\"" + now + "\",0,\"" + comment + "\"),";
//                                }
//
//                                addReferralQuery = addReferralQuery.slice(0,-1);
//
//                                var addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
//                                var ajaxQueryString = "?q=" + addReferralQuery + "&vid=" + vendorID;
//                                    ajaxRequest.open("GET", "http://192.168.11.28/searchvendors/add_referral" + ajaxQueryString, true);
//                                    ajaxRequest.send(null); 
//                                }
//                        }
//                  }
//            });
//            
//            // Dialog Link
//            $('.dialog_link').click(function(){
//                    // get id of the vendor, which is the id of the pop up button
//                    vendorID = $(this).attr('id');
//
//                    for(var i = 0; i < vendorData.length; i++) {
//                        if (vendorData[i][0] == vendorID) {
//                            vendorName = vendorData[i][1];
//                        }
//                    }
//
//                    $('#dialog').dialog("option","title","Refer Friends to " + vendorName);
//                    $("#fuzz").fadeIn();  
//                    $('#dialog').dialog('open');
//                    return false;
//            });
//
//        }
        
	</script>
    </head>
    <body>
        <div id='fuzz'></div>
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '251920381531962',
                    status     : true, 
                    cookie     : true,
                    xfbml      : true
                });
                
                // If user is not logged in, redirect user to login page
                FB.getLoginStatus(function(response) {
                    if (response.status != "connected") {
                        // logged in and connected user
                        window.location = "http://192.168.11.28/login";
                    } else {
                        // do nothing
                    }
                });
                
                $('#logout').click(function () {
                    //logout when div is clicked
                    FB.logout(function(response) {
                        // user is now logged out of service AND facebook
                        // return to login page
                        window.location = "http://192.168.11.28/login";
                    });
                });
                
            };
            (function(d){
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js";
                d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
        </script>
        <div id="top-bar">
            <a id="logo-container" href="home">
                <h1 id="logo">
                    <span class="none">Piggyback</span>
                </h1>
            </a>
            <div id="top-nav-bar">
                <div id="search">
                <form id="searchform" action="#" method="post">
                    <label for="search-box" id="search-box-label">Search for: </label>
                    <input type="text" name="searchText" size="50" class="box" id="search-box"/>
                    <label for="search-location" id="search-location-label">Near: </label>
                    <input type="text" name ="searchLocation" size="35" class="box" id="search-location"/>
                    <button id="searchbutton" name="submitSearch" class="btn" title="Submit Search">Search for Businesses!</button>
                </form>
                </div>
                <div id="logout">
                    Logout
                </div>
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
                        <div id="lists-container">
                            <ul id="lists">
                                <?php 
                                // add each list as its own <li>
                                foreach ($myLists as $list) {
                                    echo ("<li>" . $list->name . "</li>");
                                }
                                ?>
                            </ul>
                        </div>
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
                                                <li id="inbox-tab" class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#inbox-content">Inbox</a></li>
                                                <li id="friend-activity-tab" class="ui-state-default ui-corner-top"><a href="ajax/content2.html">Friend Activity</a></li>
                                                <li id="referral-tracking-tab" class="ui-state-default ui-corner-top"><a href="ajax/content3.html">Referral Tracking</a></li>
                                                <li id="search-tab" class="ui-state-default ui-corner-top"><a href="#search-content">test</a></li>
                                            </ul>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="inbox-content"> <p> SPRINT TIME </p> 
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="search-content"> <p> search </p>
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
            <div id="tempfooter">Piggyback</div>
        </div>
    </body>
</html>