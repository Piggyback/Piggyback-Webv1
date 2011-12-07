<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="../../assets/js/date.format.js" type="text/javascript"></script>
                <script type="text/javascript">
			$(function(){
                            
                                // array of friends that you have selected to refer a restaurant to
                                var friendList = new Array();
                                var vendorID;
                                var vendorName;
                                
                                // fade out background
                                $("#fuzz").css("height", $(document).height()); 
                                
                                
                                // close dialog when you click out of it
                                $("#fuzz").click(function(){
                                        $('#dialog').dialog("close"); 
                                });
                                
                                // Accordion
				$("#accordion").accordion({ header: "h3" });
                                
				// Dialog			
				$('#dialog').dialog({
					autoOpen: false,
					width: 750,
                                        closeOnEscape: true,
                                        show: 'drop',
                                        hide: 'drop',
                                        resizable: false,
                                        beforeClose: function() {
                                            // reset all values in pop up to blank
                                            document.getElementById("explanation").value = '';
                                            document.getElementById("addedFriends").innerHTML = '';
                                            document.getElementById("tags").value = '';
                                            friendList = [];
                                            
                                            // fade out dark background
                                            $("#fuzz").fadeOut();  
                                        },
					buttons: {
                                            "Refer!": function() { 
                                                if (friendList.length < 1) {
                                                    alert("You did not select any friends to refer. Please try again.");
                                                }
                                                else {
                                                    // create ajax request
                                                    var ajaxRequest;

                                                    // code for IE7+, Firefox, Chrome, Opera, Safari
                                                    if (window.XMLHttpRequest) {
                                                          ajaxRequest = new XMLHttpRequest();
                                                    }
                                                    // code for IE6, IE5
                                                    else {
                                                          ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                                                    }

                                                    // Create a function that will receive data sent from the server
                                                    ajaxRequest.onreadystatechange=function() {
                                                          if (ajaxRequest.readyState==4 && ajaxRequest.status==200) {
                                                            $('#dialog').dialog("close"); 
                                                          }
                                                    } 

                                                    // create query for inserting row
                                                    var addReferralQuery = "INSERT INTO Referrals VALUES ";
                                                    for (var i = 0; i < friendList.length; i++){
                                                        var now = new Date();
                                                        now = now.format("yyyy-mm-dd HH:MM:ss");
                                                        var comment = document.getElementById('explanation').value;
                                                        var friendUID = friendList[i][0];
                                                        addReferralQuery = addReferralQuery + "(NULL," + myUID + "," + friendUID + ",\"" + now + "\",0,\"" + comment + "\"),";
                                                    }

                                                    addReferralQuery = addReferralQuery.slice(0,-1);

                                                    var addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
                                                    var ajaxQueryString = "?q=" + addReferralQuery + "&vid=" + vendorID;
                                                        ajaxRequest.open("GET", "http://192.168.11.28/searchvendors/add_referral" + ajaxQueryString, true);
                                                        ajaxRequest.send(null); 
                                                    }
                                            }
                                      }
				});
				
				// Dialog Link
				$('.dialog_link').click(function(){
                                        // get id of the vendor, which is the id of the pop up button
                                        vendorID = $(this).attr('id');
                                        
                                        for(var i = 0; i < vendorData.length; i++) {
                                            if (vendorData[i][0] == vendorID) {
                                                vendorName = vendorData[i][1];
                                            }
                                        }
                                        
                                        $('#dialog').dialog("option","title","Refer Friends to " + vendorName);
                                        $("#fuzz").fadeIn();  
					$('#dialog').dialog('open');
					return false;
				});
				
				// hover states on the static widgets
				$('.dialog_link, ul#icons li').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
                                // tell autocomplete what source to use
                                $( "#tags" ).autocomplete({
                                        source: availableTags
                                });
                                
                                $( "input:submit, a, button", ".demo" ).button();
                                $( "a", ".demo" ).click(function() { 
                                    return false; 
                                });
                                
                                // add friend to recommendation list if they are not on list yet
                                $('#addFriend').submit(function() {
                                  var submittedFriend = document.forms["addFriend"]["friend"].value;
                                  document.forms["addFriend"]["friend"].value = '';
                                  var fullName = "";
                                  var flag = 0;
                                  for (var i = 0; i < allFriends.length; i++) {
                                      fullName = allFriends[i][3] + " " + allFriends[i][4];
                                      if (fullName == submittedFriend) {
                                          flag = 1;
                                          if (friendList.indexOf(allFriends[i]) == -1){
                                            friendList.push(allFriends[i]);
                                            displayAddedFriends();
                                          }
                                          else {
                                              alert("You have already added " + submittedFriend);
                                          }
                                      }
                                  }  
                                  // flag was not set because friend was not found with matching name
                                  if (!flag) {
                                        alert("You are not friends with " + submittedFriend);
                                  }
                                  return false;
                                });
                                
                                function deleteRow() {
                                    alert("here in delete row");
                                    var friendNameToRemove = $(this).parent().innerHTML;
                                    alert(friendNameToRemove);
                                    $(this).parent().parent().remove();
                                    var indexOfRemovedFriend = friendList.indexOf(friendNameToRemove);
                                    friendList.splice(indexOfRemovedFriend,1);
                                }
                                
                                $('table td img.delete').click(function(){
                                    alert("in click!");
                                    $(this).parent().parent().remove();
                                });
                                
                                // update display of friends added to referral list
//                                function displayAddedFriends() {
//                                    var displayFriends = "<table><th style=\"font-size:13px; font-family:helvetica; font-weight:bold;\">List of friends to refer</th>";
//                                    for (var i = 0; i < friendList.length; i++) {
//                                        displayFriends = displayFriends + "<tr><td style=\"font-size:12px; font-family:helvetica;\">" + friendList[i][3] + " " + friendList[i][4] + "</td><td><img class=\"delete\" src=\"../../assets/jquery-ui-1.8.16.custom/css/custom-theme/images/del.png\" style=\"z-index:2000;\"/></td></tr>";
//                                    }
//                                    displayFriends = displayFriends + "</table>";
//                                    document.getElementById("addedFriends").innerHTML = displayFriends; 
//                                }
                                
			});
                        
		</script>
		<style type="text/css">
			/*demo page css*/
			body{ font: 62.5% "Helvetica", sans-serif; margin: 50px;}
			.dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			.dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
		</style>	
	</head>
	<body> 
            
        <!-- create accordion div, we will add rows within the loop -->
        <div id="accordion">

        <?php        
        // error with the location geocoding
        if ($searchResults[0] == "error") {
            if ($searchResults[1] == "locationError") {
                $errorType = "Error with Location";
            }

            // error with the search
            else if ($searchResults[1] == "searchError") {
                $errorType = "Error with Search";
            }
            
            // print out error message
            switch ($searchResults[2]) {
                case "ZERO_RESULTS":
                    echo "$errorType: No results were found";
                    break;
                case "OVER_QUERY_LIMIT":
                    echo "$errorType: You are over the API query limit";
                    break;
                case "REQUEST_DENIED":
                    echo "$errorType: Your request was denied";
                    break;
                case "INVALID_REQUEST":
                    echo "$errorType: Your request is invalid";
                    break;
                default:
                    echo $errorType;      
            }
        }
        
        // results were found
        else {
            $this->load->database();

            // get friends of current user
            $currentUserData = $this->session->userdata('currentUserData');
            $currentUID = $currentUserData['uid'];
            $friendQuery = "SELECT uid, fbid, email, firstName, lastName
                            FROM Users
                            WHERE uid IN (SELECT uid2 FROM Friends WHERE uid1 = $currentUID
                                          UNION
                                          SELECT uid1 FROM Friends WHERE uid2 = $currentUID)";
            $friends = mysql_query($friendQuery);

            // create friend name list in a string that javascript will understand
            $friendTags = "[";
            while ($friend = mysql_fetch_row($friends)) {
                $friendTags = $friendTags . "\"$friend[3] $friend[4]\",";
                $friendArray[] = $friend;
            }
            $friendTags[strlen($friendTags)-1] = "]";
            $allFriendsArray = json_encode($friendArray);

            foreach ($searchResults as $row) {
                // if data for the given vendor was successfully pulled, display results in table
                if ($row->status == 'OK') {              
                    // error is given for retrieving something that is not there (e.g., no website)
                    // so only overwrite default NULL if there is a value returned for each key
                    $name = NULL;
                    $reference = NULL;
                    $id = NULL;
                    $lat = 0;
                    $lng = 0;
                    $phone = NULL;
                    $addr = NULL;
                    $addrNum = NULL;
                    $addrStreet = NULL;
                    $addrCity = NULL;
                    $addrState = NULL;
                    $addrCountry = NULL;
                    $addrZip = NULL;
                    $website = NULL;
                    $icon = NULL;
                    $rating = 0;
                    $types = NULL;

                    var_dump(json_encode($row));
                    $vendor = $row->result;
                    $vendorArray = get_object_vars($vendor);
                    $vendorKeys = array_keys($vendorArray);
                    foreach ($vendorKeys as $key){
                        if ($key == 'name') {
                            $name = $vendor->name;
                        }
                        if ($key == 'reference') {
                            $reference = $vendor->reference;
                        }
                        if ($key == 'id') {
                            $id = $vendor->id;
                        }
                        if ($key == 'geometry') {
                            $lat = $vendor->geometry->location->lat;
                            $lng = $vendor->geometry->location->lng;
                            $theta = $srcLng - $lng; 
                            $dist = sin(deg2rad($srcLat)) * sin(deg2rad($lat)) +  cos(deg2rad($srcLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta)); 
                            $dist = acos($dist); 
                            $dist = rad2deg($dist); 
                            $distMiles = $dist * 60 * 1.1515;
                        }
                        if ($key == 'formatted_phone_number') {
                            $phone = $vendor->formatted_phone_number;
                        }
                        if ($key == 'formatted_address') {
                            $addr = $vendor->formatted_address;
                        }
                        if ($key == 'address_components') {
                                @$addrNum = $vendor->address_components[0]->short_name;
                                @$addrStreet = $vendor->address_components[1]->short_name;
                                @$addrCity = $vendor->address_components[2]->short_name;
                                @$addrState = $vendor->address_components[3]->short_name;
                                @$addrCountry = $vendor->address_components[4]->short_name;
                                @$addrZip = $vendor->address_components[5]->short_name;
                        }
                        if ($key == 'website') {
                            $website = $vendor->website;
                        }
                        if ($key == 'icon') {
                            $icon = $vendor->icon;
                        }
                        if ($key == 'rating') {
                            $rating = $vendor->rating;
                        }
                        if ($key == 'types') {
                            $types = $vendor->{'types'};
                        }
                    }

                    $vendorData[] = array($id,$name);

    //                // add to vendor table if row does not exist yet
    //                $existingVendorQuery = "SELECT id 
    //                    FROM Vendors 
    //                    WHERE id = \"$id\"";
    //                $existingVendorResult = mysql_query($existingVendorQuery);
    //                $count = mysql_num_rows($existingVendorResult);
    //                if ($count == 0) {
    //                    $addVendorQuery = "INSERT INTO Vendors 
    //                                       VALUES (\"$name\",\"$reference\",\"$id\",$lat,$lng,\"$phone\",\"$addr\",\"$addrNum\",\"$addrStreet\",\"$addrCity\",\"$addrState\",\"$addrCountry\",\"$addrZip\",\"$website\",\"$icon\",$rating)";
    //                    mysql_query($addVendorQuery) or die("Query failed: " . mysql_error());
    //                }

                // add row to accordion for each search result
                echo "<div>
                        <h3><a href=\"#\">$name</a></h3>
                        <div>
                            <table class=\"formattedTable\" width=\"100%\" style=\"font-size:13px; font-family:helvetica;\">
                                <td width=\"70%\">
                                  $addrNum $addrStreet<BR>
                                  $addrCity $addrState $addrZip<br>
                                  $phone
                                </td>
                                <td width=\"30%\" align=\"right\" style=\"font-size:13px; font-family:helvetica;\">          
                                  <p><a href=\"#\" id=\"$id\"class=\"dialog_link ui-state-default ui-corner-all\">
                                      <span class=\"ui-icon ui-icon-plus\"></span>Refer to Friends
                                  </a></p>
                                </td>
                            </table>
                        </div>
                </div>";  
                }
            }

            $vendorDataJson = json_encode($vendorData);

            // set variables for javascript to use
            echo "<script type=\"text/javascript\">
            var availableTags = $friendTags;
            var myUID = $currentUID;
            var allFriends = $allFriendsArray;
            var vendorData = $vendorDataJson;
            </script>";
        }
//        // FOR TESTING PURPOSES USE DATA FROM THE TABLE
//        $q = "select * from Vendors where addrCity = \"Torrance\"";
//        $result = mysql_query($q);
//        
//
//        while($row = mysql_fetch_row($result)) {
//            $name = $row[0];
//            $phone = $row[5];
//            $addrNum = $row[7];
//            $addrCity = $row[9];
//            $addrStreet = $row[8];
//            $addrState = $row[10];
//            $addrZip = $row[11];
//            $rating = $row[15];
//            $id = $row[2];
//            
//            // save vendor id and name so we can pass it to javascript for the referral pop up
//            $vendorData[] = array($id,$name);
// 
//            // add row to accordion for each search result
//            echo "<div>
//                    <h3><a href=\"#\">$name</a></h3>
//                    <div>
//                        <table class=\"formattedTable\" width=\"100%\" style=\"font-size:13px; font-family:helvetica;\">
//                            <td width=\"70%\">
//                              $addrNum $addrStreet<BR>
//                              $addrCity $addrState $addrZip<br>
//                              $phone
//                            </td>
//                            <td width=\"30%\" align=\"right\" style=\"font-size:13px; font-family:helvetica;\">          
//                              <p><a href=\"#\" id=\"$id\"class=\"dialog_link ui-state-default ui-corner-all\">
//                                  <span class=\"ui-icon ui-icon-plus\"></span>Refer to Friends
//                              </a></p>
//                            </td>
//                        </table>
//                    </div>
//            </div>";            
//            
//        }   

        ?>
            
        </div>
         
        <!-- ui-dialog pop up-->
        <div id="dialog">

                <!-- lefthand side of referral pop up: used for searching friends -->
                <div class="ui-widget" style="width:300p; height:100px; float:left; margin-buttom:10px">
                    <form id="addFriend" method="post">
                        <label for="tags"><B>Search for friends to refer</b><BR></label>
                        <input type="search" size="35" name="friend" id="tags">
                        <input type="submit" value="Add to List"/>
                    </form>
                </div>

            <!-- righthand side of referral pop up -->
            <div id="rightCol" style="width:300px; height:100px; float:right; margin-bottom:10px;">

                <!-- show list of friends that have been added to recommendation list-->
                <div id="addedFriends" style="width:300px; height:100px; clear:both; overflow:auto;">
                </div>
                
            </div>

            <!-- add a comment -->
            <div id="commentArea" style="width:100%; margin-top:10px; clear:both">
                <label for="explanation"><B>Add a comment with your referral</B></label>
                <textarea name="comment" id="explanation" rows="4" cols="100%"></textarea>
            </div>
            
        </div>
        
        <div id="fuzz" style="position:absolute; opacity:.7; top:0; left:0; width:100%; z-index:100; background-color:black; display:none; text-align:left;"></div>
    </body>
</html>


