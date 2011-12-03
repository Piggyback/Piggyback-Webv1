<meta charset="utf-8">

<title>Your Piggyback Inbox</title> 
    <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
    <link rel="stylesheet" media="screen" href="../../assets/css/style2.css" type="text/css" />
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>

    <script>
        // eventually move all javascript code into separate file and call it,
        // want to expose as little as possible
        // TODO: migrate js code into separate file @andyjiang #inboxview
    $(function() {
        
        // initialize the accordion features
        $( "#accordion" ).accordion({
            header: 'h3',    
            collapsible: true,
            autoHeight: true,
            navigation: true,
            active: 'none'
        });
        
        // override click handler for p text, tables
        // allows for Like and Comment button in the header
        $("#accordion p a, #accordion table").click(function(e) {
            e.stopPropagation();
        });
        
        // override the space and enter key from performing accordion action
        $('.comment_input').keydown(function(e){
            if(e.which==32 || e.which==13){
                e.stopPropagation();
            }
        });
        
        // hide all comment boxes
        $('.comment_box').hide();
        
        // show comment div upon click
        $('.click_to_comment').click(function(){
            //alert("HI");
            $(this).parents('.row').children('.comment_box').show();
            
        });
        
        // perform like action upon click
        $('.click_to_like').click(function(){
            var referId = $(this).closest('.row').data("rid");
            var likes = $(this).closest('.row').find('.number_of_likes');
            
            jQuery.post("http://192.168.11.28/test/perform_like_action", {
                rid: referId
            }, function(likeCount){
                if(likeCount>0)
                    likes.text(likeCount + " people like this.");
                else
                    likes.text("");
            });
            
            // toggle Like and Unlike
            if($(this).text()=="Like")
            {
                $(this).text("Unlike");
            } else {
                $(this).text("Like");
            }
            
        });
        
        // to submit a comment
        $('.submit_comment_button').click(function(){
            var name = $(this).closest('.row').find('.comment_input').val();
            var referId = $(this).closest('.row').data("rid");
            // if the comment is not empty, then proceed with ajax 
            alert(name);
            if( name == "") {
                //alert("comment is empty");
            } else {
                jQuery.post("http://192.168.11.28/test/add_new_comment", {
                    comment: name,
                    rid: referId             
                }, function(test){
                    alert("test");
                });
            }
            //}
            
            // now do something to confirm the comment
        });
        
        
    });
    </script>

    

    <div class="inbox">

        <div id="accordion">

            <?php
                foreach ($inboxItems as $row)
                {
                    
                    // determine if $row is a list or single vendor
                    if ( $row->lid == 0 )
                    {   
                        // get some vital variables ready
                        // the count of likes
                        $tempArray = $row->LikesList;
                        $likesArray = $tempArray['LikesList'];
                        $tempArray = $row->CommentsList;
                        $commentsArray = $tempArray['CommentsList'];
                        
                        if (count($likesArray)>0) {
                            $likeNumber = count($likesArray) . " people like this.";
                        } else {
                            $likeNumber = "";
                        }
                        
                        // if uid does not exist in the Likes rid list
                        if ($row->alreadyLiked == 1) {
                            $likeStatus = "Unlike";
                        } else {
                            $likeStatus = "Like";
                        }
                 ?>
            
<!--                        BEGINNING OF HEADER HERE of the ACCORDION    -->
                        <h3><a> <?php echo $row->name; ?>
<!--                        sub title here-->
                        <h5> <?php echo $row->firstName . " " . $row->lastName; ?> says "<?php echo $row->ReferralsComment ?>"</h5>
                    
<!--                        new div for like/comment button, comment fields, etc like button here -->
                        <div class="row" data-rid=<?php echo $row->rid; ?>>
                        <p>
                        <table>
                        <td><div class="click_to_like" style="padding-right: 20px; padding-top: 5px; padding-bottom: 5px;" data-likeCounts=<?php echo $likeNumber; ?>><?php echo $likeStatus; ?></div></td>
<!--                        comment button here -->
                        <td><div class="click_to_comment" style="padding-right: 20px; padding-top: 5px; padding-bottom: 5px;">Comment</div></td>
<!--                        '# of your friends have Liked this'  here -->
                        <td><div class="number_of_likes" style="padding-top: 5px; padding-bottom: 5px;"><?php echo $likeNumber; ?></div></td>
                        </table>
<!--                        // create the divs to show other peoples comments-->
                        <?php foreach($commentsArray as $line): ?>
                            <div class="comments" style="padding-bottom: 2px;">
                            <table><td><p style="margin-left: 0px; padding-bottom: 2px;"><?php echo $line->firstName . " " . $line->lastName . ": "; ?></p></td>
                                   <td><p style="margin-left: 15px; padding-bottom: 2px;"><?php echo $line->comment; ?></p></td></table>
                            </div>
                        <?php endforeach; ?>

                            <div class="comment_box">
                            <form name="form_comment" method="post">
                                <table>
                                    <td><input type="text" class="comment_input"/></td>
                                    <td><button type="submit" class="submit_comment_button">
                                            <p>Submit</p></button></td>
                                </table>
                            </form>
                            </div>
                        </p></div>
<!--                        END HEADER OF ACCORDION HERE ENDS HERE      -->
                        </a></h3>
                        
<!--                        vendor details here (among anything else)-->
                        <div class="drop_down_details"><h5>
                        <?php echo $row->addrNum . " " . $row->addrStreet . "</br>"; // add all list detail here
                        echo $row->addrCity . " " . $row->addrState . " " . $row->addrZip . "</br>";
                        echo $row->phone . "</br>";
                        echo $row->website;
                        echo "</h5></div>";
                        
                        // TODO: dragability, @andyjiang
                    } else {
                        // list
                        echo "<h3><a href=\"#\">" . $row->UserListsName . " list</br>";
                        echo "<h5>" . $row->firstName . " " . $row->lastName . " says " . "\"" . $row->ReferralsComment . "\"";
                        echo "</p></a></h5></h3>";
                        echo "<div><p>" . $row->firstName . " " . $row->lastName; // add all list detail here
                        echo "</div>";
                    }
                }
            ?>
                                
        </div>

    </div>