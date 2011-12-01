<meta charset="utf-8">


    <title>piggyback search</title> 
    <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
    <link rel="stylesheet" media="screen" href="../../assets/css/style2.css" type="text/css" />
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>

    <script>
        // eventually move all javascript code into separate file and call it,
        // want to expose as little as possible
        // TODO: migrate js code into separate file @andyjiang #inboxview
        // TODO: retrieve data such as count of Likes and array of comments per RID @andyjiang
    $(function() {
                
        $( "#accordion" ).accordion({
            header: 'h3',    
            collapsible: true,
            autoHeight: true,
            navigation: true,
            active: 'none'
        });
        
        // override click handler for p text
        // allows for Like and Comment button in the header
        $("#accordion p a, #accordion table").click(function(e) {
            e.stopPropagation();
        });
        
        $('.comment_input').keydown(function(e){
            if(e.which==32 || e.which==13){
                e.stopPropagation();
            }
        });
        
        // show comment div upon click
        $('div .click_to_comment').click(function(){
            alert($(this).attr("id"));
            $('#comment_box_' + $(this).attr("id")).show();     
        });
        
        // perform like action upon click
        $('div .click_to_like').click(function(){
            // show 'unlike' in place of like
            //document.getElementById("#click_to_like_" + $(this).attr("id")).innerHTML = "Unlike"
            alert($(this).attr("id"));
            $($(this).attr("id")).update("Unlike")
            //$('#like_success_' + $(this).attr("id")).show();
        });
        
        // to submit a comment
        $('div .submit_comment').click(function(){
            var id = "#form_comment_" + ($(this).attr("id")).substring(14);
            var $inputs = $(id + ' :input');
            jQuery.post("http://192.168.11.28/test/add_new_comment", {
                comment: $inputs[0].value,
                rid: $(this).attr("id").substring(14)                
            });
            
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
                        $tempArray = ($row->LikesList);
                        $likesArray = $tempArray['LikesList'];
                        $tempArray = $row->CommentsList;
                        $commentsArray = $tempArray['CommentsList'];
                        $likeButtonId = urlencode("#click_to_like_" . $row->rid);
                        $commentButtonId = urlencode("#click_to_comment_" . $row->rid);

                        //var_dump($likesArray);
                        
                        // single vendor
                        // vendor name here
                 ?>
                        <h3><a href=#> <?php echo $row->name; ?>
<!--                        sub title here-->
                        <h5> <?php echo $row->firstName . " " . $row->lastName; ?> says "<?php echo $row->ReferralsComment ?>"</h5>
<!--                        like button here -->
                        <p><table><td><a href=# class="click_to_like" id=<?php echo $likeButtonId; ?>>Like</a></td>
<!--                        comment button here -->
                        <td><a href=# class="click_to_comment" id=<?php echo $commentButtonId; ?>>Comment</a></td>
<!--                        '# of your friends have Liked this'  here -->
                        <td><?php if (count($likesArray)>0) {echo count($likesArray) . " people like this.";} ?></td>
                        </table></p>
                        
<!--                        comment box div-->
                        
                        <?php
                        $comment_box_id = urlencode("comment_box_" . $row->rid);
                        $form_comment_id = urlencode("form_comment_" . $row->rid);
                        $submit_button_id = urlencode("submit_button_" . $row->rid);
                        $input_id = urlencode("input_" . $row->rid);
                        ?>
                        
                        <p><div id=<?php echo $comment_box_id; ?> class = "comment_box" style="display: none">
                        <form name="form_comment" id=<?php echo $form_comment_id; ?>>
                            <table><td><input type="text" class="comment_input" id="<?php echo $input_id; ?>"/></td><td>
                                    <a href="#" class="submit_comment" id=<?php echo $submit_button_id; ?>><p>Submit</p></a></form></td></table>
                        </div> </p>
                        
<!--                        end of the header of accordion section-->
                        </a></h3>
                        
<!--                        vendor details here (among anything else)-->
                        <div><h5>
                        <?php echo $row->addrNum . " " . $row->addrStreet . "</br>"; // add all list detail here
                        echo $row->addrCity . " " . $row->addrState . " " . $row->addrZip . "</br>";
                        echo $row->phone . "</br>";
                        echo $row->website;
                        echo "</h5></div>";
                                
                        // TODO: dragability, @andyjiang
                    } else {
                        // list 
                        // TODO: make the List referrals into an accordion of vendor names @andyjiang
                        echo "<h3><a href=\"#\">" . $row->UserListsName . " list</br>";
                        echo "<h5>" . $row->firstName . " " . $row->lastName . " says " . "\"" . $row->ReferralsComment . "\"";
                        echo "</p></a></h5></h3>";
                        echo "<div><p>" . $row->firstName . " " . $row->lastName; // add all list detail here
                        echo "</div>";
                    }
                }
            ?>
<!--                <h3><a href="#">Section 1</a></h3>
                <div>
                        <p>
                Vendor detail
                </div>
                <h3><a href="#">Section 2</a></h3>
                <div>
                        <p>
                DATA HERE
                </div>
                <h3><a href="#">Section 3</a></h3>
                <div>
                        <p> data here
                        <ul>
                                <li>List item one</li>
                                <li>List item two</li>
                                <li>List item three</li>
                        </ul>
                </div>
                <h3><a href="#">Section 4</a></h3>
                <div>
                        <p>Cras dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia mauris vel est. </p><p>Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
                </div>

                ?>-->

        </div>

    </div>