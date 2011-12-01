<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
                <script type="text/javascript">
			$(function(){
                            
                                // friend list array
                                var friendList = new Array();
                                
				// Dialog			
				$('#dialog').dialog({
					autoOpen: false,
					width: 750,
					buttons: {
						"Refer!": function() { 
                                                        
							$(this).dialog("close"); 
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
                                  var submittedFriend = document.forms["addFriend"]["friend"].value;
                                  if (friendList.indexOf(submittedFriend) == -1){
                                      friendList.push(submittedFriend);
                                  }
                                  displayAddedFriends();
                                  return false;
                                });
                                
                                // update display of friends added to referral list
                                function displayAddedFriends() {
                                    var displayFriends = "<table>";
                                    for (var i = 0; i < friendList.length; i++) {
                                        displayFriends = displayFriends + "<tr><td>" + friendList[i] + "</td></tr>";
                                    }
                                    displayFriends = displayFriends + "</table>";
                                    document.getElementById("addedFriends").innerHTML = displayFriends; 
                                    alert(displayFriends);   
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
            <?php
            $this->load->database();
            
            // get friends of current user
            $currentUserData = $this->session->userdata('currentUserData');
            $currentUID = $currentUserData['uid'];
            $friendQuery = "SELECT uid, fbid, firstName, lastName
                            FROM Users
                            WHERE uid IN (SELECT uid2 FROM Friends WHERE uid1 = $currentUID
                                          UNION
                                          SELECT uid1 FROM Friends WHERE uid2 = $currentUID)";
            $friends = mysql_query($friendQuery);
            
            // create friend name list in a string that javascript will understand
            $friendTags = "[";
            while ($friend = mysql_fetch_row($friends)) {
                $friendTags = $friendTags . "\"$friend[2] $friend[3]\",";
            }
            $friendTags[strlen($friendTags)-1] = "]";
            
            // set friend names for autocomplete
            echo "<script type=\"text/javascript\">
            var availableTags = $friendTags;
            </script>";
            ?>
            
                <!-- button for dialog pop up-->
                <p><a href="#" id="dialog_link" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-plus"></span>Refer to Friends</a></p>
		
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
                    <div id="commentArea" style="width:720px; margin-top:10px; clear:both">
                        <label for="explanation"><B>add a comment with your referral</B></label>
                        <textarea name="comment" id="explanation" rows="4" cols="103"></textarea>
                    </div>
                        <?php 
                        
                        ?>
                </div>
	</body>
</html>


