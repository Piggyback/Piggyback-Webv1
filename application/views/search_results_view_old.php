<html>
    <head>
        <title>piggyback</title>
        <!--<link rel="stylesheet" media="screen" href="../../assets/css/style2.css" type="text/css" />-->
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../../assets/js/date.format.js"></script>
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js"></script>
        <!--<script type="text/javascript" src="../../assets/js/popup.js"></script>  
        <script type="text/javascript" src="../../assets/js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.cust.js"></script>-->
        <script type="text/javascript">
                $(function(){

                        // array of friends that you have selected to refer a restaurant to
                        var friendList = new Array();

                        // Dialog			
                        $('#dialog').dialog({
                                autoOpen: false,
                                width: 750,
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
                                                    document.getElementById('explanation').value = '';
                                                    document.getElementById("addedFriends").innerHTML = '';
                                                    friendList = [];
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

                                            var ajaxQueryString = "?q=" + addReferralQuery;
                                                ajaxRequest.open("GET", "test2" + ajaxQueryString, true);
                                                ajaxRequest.send(null); 
                                            }
                                    }
                              }
                        });

                        // Dialog Link
                        $('#dialog_link').click(function(){
                                $('#dialog').dialog('open');
                                return false;
                        });

                        // hover states on the static widgets
                        $('#dialog_link, ul#icons li').hover(
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
                            alert($(this).attr('id'));
                          var submittedFriend = document.forms["addFriend"]["friend"].value;
                          document.forms["addFriend"]["friend"].value = '';
                          var fullName = '';
                          var flag = 0;
                          for (var i = 0; i < allFriends.length; i++) {
                              fullName = allFriends[i][3] + " " + allFriends[i][4];
                              if (fullName == submittedFriend) {
                                  flag = 1;
                                  if (friendList.indexOf(allFriends[i]) == -1){
                                    friendList.push(allFriends[i]);
                                    alert(allFriends[i]);
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
                          displayAddedFriends();
                          return false;
                        });

                        // update display of friends added to referral list
                        function displayAddedFriends() {
                            var displayFriends = "<table>";
                            for (var i = 0; i < friendList.length; i++) {
                                displayFriends = displayFriends + "<tr><td>" + friendList[i][3] + " " + friendList[i][4] + "</td><td><img class=\"delete\" src=\"../../assets/jquery-ui-1.8.16.custom/css/custom-theme/images/del.png\" /></td></tr>";
                            }
                            displayFriends = displayFriends + "</table>";
                            document.getElementById("addedFriends").innerHTML = displayFriends; 
                        }

                        // remove table row when image is clicked
                        $('table td img.delete').click(function(){
                            $(this).parent().parent().remove();
                        });

                });

        </script>
        <style type="text/css">
                /*demo page css*/
                body{ font: 62.5% "Helvetica", sans-serif; margin: 50px;}
                #dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
                #dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
        </style>	
    </head>
    <body>
        <p><a href="#" id="dialog_link" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-plus"></span>Refer to Friends</a></p>

    <?php
    // if there was an error
//    if (is_string($searchResults)) {
//        echo $searchResults;
//    } 
//    
//    // if there are results, display them
//    else {
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

        // set friend names for autocomplete
        echo "<script type=\"text/javascript\">
        var availableTags = $friendTags;
        var myUID = $currentUID;
        var allFriends = $allFriendsArray;
        </script>";
        
        // set up table to display search results
        echo "<table id='searchResults' class='tablesorter'>";
        echo "<thead>";
        echo "<tr>";
           echo "<th align='left'>Name</th>";
           echo "<th align='left'>Rating</th>";
           //echo "<th align='left'>Distance</th>";
           echo "<th align='left'>Refer</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        // FOR TESTING PURPOSES USE DATA FROM THE TABLE
        $q = "select * from Vendors where addrCity = \"Torrance\"";
        $result = mysql_query($q);
        
        while($row = mysql_fetch_row($result)) {
            $name = $row[0];
            $phone = $row[5];
            $addrNum = $row[7];
            $addrCity = $row[9];
            $addrStreet = $row[8];
            $addrState = $row[10];
            $addrZip = $row[11];
            $rating = $row[15];
            
            echo "<tr>
                      <td><B>$name</b><br>
                      $addrNum $addrStreet<BR>
                      $addrCity $addrState $addrZip<br>
                      $phone
                      <td>";
                if ($rating == 0) {
                    echo "N/A</td>";
                } else {
                    echo $rating."<BR></td>";
                }
                echo "<td><p><a href=\"#\" id=\"dialog_link\" class=\"ui-state-default ui-corner-all\"><span class=\"ui-icon ui-icon-plus\"></span>Refer to Friends</a></p></td>";
                echo "</tr>"; 
        }
//        
//        
//        foreach ($searchResults as $row) {
//            // if data for the given vendor was successfully pulled, display results in table
//            if ($row->status == 'OK') {              
//                // error is given for retrieving something that is not there (e.g., no website)
//                // so only overwrite default NULL if there is a value returned for each key
//                $name = NULL;
//                $reference = NULL;
//                $id = NULL;
//                $lat = 0;
//                $lng = 0;
//                $phone = NULL;
//                $addr = NULL;
//                $addrNum = NULL;
//                $addrStreet = NULL;
//                $addrCity = NULL;
//                $addrState = NULL;
//                $addrCountry = NULL;
//                $addrZip = NULL;
//                $website = NULL;
//                $icon = NULL;
//                $rating = 0;
//                $types = NULL;
//        
//                $vendor = $row->result;
//                $vendorArray = get_object_vars($vendor);
//                $vendorKeys = array_keys($vendorArray);
//                foreach ($vendorKeys as $key){
//                    if ($key == 'name') {
//                        $name = $vendor->name;
//                    }
//                    if ($key == 'reference') {
//                        $reference = $vendor->reference;
//                    }
//                    if ($key == 'id') {
//                        $id = $vendor->id;
//                    }
//                    if ($key == 'geometry') {
//                        $lat = $vendor->geometry->location->lat;
//                        $lng = $vendor->geometry->location->lng;
//                        $theta = $srcLng - $lng; 
//                        $dist = sin(deg2rad($srcLat)) * sin(deg2rad($lat)) +  cos(deg2rad($srcLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta)); 
//                        $dist = acos($dist); 
//                        $dist = rad2deg($dist); 
//                        $distMiles = $dist * 60 * 1.1515;
//                    }
//                    if ($key == 'formatted_phone_number') {
//                        $phone = $vendor->formatted_phone_number;
//                    }
//                    if ($key == 'formatted_address') {
//                        $addr = $vendor->formatted_address;
//                    }
//                    if ($key == 'address_components') {
//                            @$addrNum = $vendor->address_components[0]->short_name;
//                            @$addrStreet = $vendor->address_components[1]->short_name;
//                            @$addrCity = $vendor->address_components[2]->short_name;
//                            @$addrState = $vendor->address_components[3]->short_name;
//                            @$addrCountry = $vendor->address_components[4]->short_name;
//                            @$addrZip = $vendor->address_components[5]->short_name;
//                    }
//                    if ($key == 'website') {
//                        $website = $vendor->website;
//                    }
//                    if ($key == 'icon') {
//                        $icon = $vendor->icon;
//                    }
//                    if ($key == 'rating') {
//                        $rating = $vendor->rating;
//                    }
//                    if ($key == 'types') {
//                        $types = $vendor->{'types'};
//                    }
//                }
                
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
//                    
//                echo "<tr>
//                      <td><B>$name</b><br>
//                      $addrNum $addrStreet<BR>
//                      $addrCity $addrState $addrZip<br>
//                      $phone
//                      <td>";
//                if ($rating == 0) {
//                    echo "N/A</td>";
//                } else {
//                    echo $rating."<BR></td>";
//                }
//                echo "<td>".number_format($distMiles,2)." mi.<br></td>";
//                echo "<td><a href=\"#\" id=\"dialog_link\" class=\"ui-state-default ui-corner-all\"><span class=\"ui-icon ui-icon-plus\"></span>Refer to Friends</a></td>";
//                echo "</tr>";    
//            }
//        }
//    }
        ?>
        
        
        
        <!-- ui-dialog pop up-->
        <div id="dialog" title="Refer Friends to VendorName">

            <!-- lefthand side of referral pop up: used for searching friends -->
            <div class="ui-widget" style="width:300p; height:100px; float:left; margin-buttom:10px">
                <form id="addFriend" method="post">
                    <label for="tags"><B>search for friends to refer</b><BR></label>
                    <input type="search" size="35" name="friend" id="tags">
                    <input type="submit" value="Add to List"/>
                </form>
            </div>

        <!-- righthand side of referral pop up -->
        <div id="rightCol" style="width:300px; height:100px; float:right; margin-bottom:10px;">

            <!-- display title for added friends -->
            <div style="width:300px; height:10px; margin-bottom:5px;">
                <b>list of friends to refer</b>
            </div>

            <!-- show list of friends that have been added to recommendation list-->
            <div id="addedFriends" style="width:300px; height:85px; clear:both; overflow:auto;">
            </div>
        </div>

        <!-- add a comment -->
        <div id="commentArea" style="width:100%; margin-top:10px; clear:both">
            <label for="explanation"><B>add a comment with your referral</B></label>
            <textarea name="comment" id="explanation" rows="4" cols="100%"></textarea>
        </div>
        </tbody>
        </table>
    </body>
</html>