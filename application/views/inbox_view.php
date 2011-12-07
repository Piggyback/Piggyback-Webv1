<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_mike_css.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_andy_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../assets/js/date.format.js"></script>
        <script type="text/javascript" src="../assets/js/home_mike_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_andy_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_kim_js.js"></script>
        
        <script>
        // eventually move all javascript code into separate file and call it,
        // want to expose as little as possible
        // TODO: migrate js code into separate file @andyjiang #inboxview
        $(function() {
            
            // bind functions
            function bindInboxItem() {
                bindAccordionInbox();
                overrideAccordionEvent();
                initCommentInputDisplay();
                initLike();
                initComment();
            }
            
            var loadStart=3;
            // call more data from mysql ('load more' button action)
            $('.load-more-button').click(function(){
                // jquery post to retrieve more rows
                jQuery.post("http://192.168.11.28/referrals/get_more_inbox", {
                    rowStart: loadStart
                }, function(data) {
                    var parsedJSON = jQuery.parseJSON(data);
                    displayMoreReferrals(parsedJSON);
                    loadStart = loadStart+3;
                });
            });

            // display additional rows
            function displayMoreReferrals(moreRows) {
                // moreRows is a parsedJSON object
                // create a string that captures all HTML required to write the next referral
                var displayReferralsHTMLString = "";
                var likeNumber = 0;
                var likeStatus = "";

                // destroy the accordion first
                $('#accordion-inbox').accordion('destroy');

                for(var i=0; i<moreRows.length; i++) {
                    likeNumber = moreRows[i].LikesList['LikesList'].length;
                    if(likeNumber>0) {
                        if(likeNumber == 1) {
                            likeNumber = likeNumber + " person likes this.";
                        } else {
                            likeNumber = likeNumber + " people like this.";
                        }
                    }
                    else {
                        likeNumber = "";
                    }
                    
                    if (moreRows[i].alreadyLiked==1) {
                        likeStatus = "Unlike";
                    } else {
                        likeStatus = "Like";
                    }
                    
                    alert(likeNumber + " and you " + likeStatus);
                    
                    displayReferralsHTMLString = "" +
                    "<div class='inbox-single-wrapper'>" +
                        "<div class='referral-date'>" +
                            moreRows[i].refDate +
                        "</div>" +
                        "<a>" + moreRows[i].name +
                            "<div class='friend-referral-comment'>" + 
                                moreRows[i].firstName + " " + moreRows[i].lastName + " says \"" + moreRows[i].ReferralsComment + "\"" +
                            "</div>" + 
                                "<div class='row' data-rid=" + moreRows[i].rid +
                                    "<div class='click-to-like no-accordion' data-likeCounts=" + likeNumber + ">" +
                                        likeStatus + 
                                    "</div>" +
                                    "<div class='click-to-comment no-accordion'>" + 
                                        "Comment" +
                                    "</div>" +
                                    "<div class='number-of-likes no-accordion'>" +
                                        likeNumber +
                                    "</div>" +
                                    "<div class='comments'>" +
                                        "<table class='comments-table'>";
                                   // comments here
                                   for(var j=0; j<moreRows[i].CommentsList['CommentsList'].length; j++) {
                                       displayReferralsHTMLString = displayReferralsHTMLString +
                                           "<tr class='inbox-single-comment'>" +
                                                "<td class='comments-name'>" +
                                                    moreRows[i].CommentsList['CommentsList'][j].firstName + " " + moreRows[i].CommentsList['CommentsList'][j].lastName + ": " +
                                                "</td>" +
                                                "<td class='comments-content'>" +
                                                    moreRows[i].CommentsList['CommentsList'][j].comment +
                                                "</td>" +
                                           "</tr>";
                                   }
                                   displayReferralsHTMLString = displayReferralsHTMLString +
                                       "</table>" +
                                   "</div>" + 
                                   "<div class='comment-box no-accordion'>" +
                                        "<form name='form-comment' class='form-comment' method='post'>" +
                                            "<input type='text' class='comment-input'/>" +
                                            "<button type='submit' class='submit-comment-button'>" +
                                                "Submit" +
                                            "</button>" +
                                        "</form>" +
                                   "</div>" +
                               "</div>" +
                           "</a>" +
                       "</div>" +
                   
               // details of the row
                       "<div class='drop-down-details'>" +
                           moreRows[i].addrNum + " " + moreRows[i].addrStreet + "<br>" +
                           moreRows[i].addrCity + " " + moreRows[i].addrState + " " + moreRows[i].addrZip + "<br>" +
                           moreRows[i].phone + "<br>" +
                           moreRows[i].website +
                       "</div>";
                   
                    $(displayReferralsHTMLString).appendTo('#inbox-wrapper');
                    
                    //overrideAccordionEvent();
                    
                    initCommentInputDisplay();
                    initLike();
                    initComment();
                    initRemoveComment();
                }
               

                bindAccordionInbox();
//                overrideAccordionEvent();
//                initCommentInputDisplay();
//                initLike();
//                initComment();
//                initRemoveComment();
  
            }

        });
    </script>
        
    </head>
    <body>


        <div id="inbox">
            <div id="accordion-inbox">
                <div id="inbox-wrapper">
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

                            $likesArrayCount = count($likesArray);
                            $likeNumber = "";
                            if ($likesArrayCount>0) {
                                $likeNumber = "$likesArrayCount";
                                if ($likesArrayCount == 1) {
                                    $likeNumber = $likeNumber . " person likes this.";
                                } else {
                                    $likeNumber = $likeNumber . " people like this.";
                                }
                            } 
                            // if uid does not exist in the Likes rid list
                            if ($row->alreadyLiked == 1) {
                                $likeStatus = "Unlike";
                            } else {
                                $likeStatus = "Like";
                            }
                 ?>

                    <!-- comments for andy
                        -Add proper indenting to HTML
                        -Like text has lots of whitespace; use jQuery.trim
                        -Make comment font smaller
                        -Clean up code
                    -->

                            <!-- BEGINNING OF HEADER HERE of the ACCORDION    -->
                        <div class="inbox-single-wrapper">
                            <!-- DATE -->
                            <div class="referral-date">
                                <?php echo $row->refDate; ?>
                            </div>
                            <a> <?php echo $row->name; ?>
                                <!-- sub title here-->
                                <div class="friend-referral-comment"> 
                                    <?php echo $row->firstName . " " . $row->lastName; ?> says "<?php echo $row->ReferralsComment ?>"
                                </div>
                                    <!-- new div for like/comment button, comment fields, etc like button here -->
                                    <div class="row" data-rid=<?php echo $row->rid; ?>>
                                        <div class="click-to-like no-accordion" data-likeCounts=<?php echo $likeNumber; ?>>
                                            <?php echo $likeStatus; ?>
                                        </div>
                <!--                                                                                     comment button here -->
                                        <div class="click-to-comment no-accordion">
                                            Comment
                                        </div>
                                        <div class="number-of-likes no-accordion">
                                            <?php echo $likeNumber; ?>
                                        </div>

                                        <!-- create the divs to show other peoples comments-->
                                        <div class="comments" >
                                            <table class="comments-table"> 
                                                <?php foreach($commentsArray as $line): ?>
                                                    <tr class='inbox-single-comment'>
                                                      <tbody>
                                                        <td class="comments-name">
                                                            <?php echo $line->firstName . " " . $line->lastName . ": "; ?>
                                                        </td>
                                                        <td class="comments-content">
                                                            <?php echo $line->comment; ?>
                                                        </td>
                                                        <td>
                                                            <button class="delete-comment" data-cid=<?php echo $line->cid;?>>x</button>
                                                        </td>
                                                      </tbody>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        </div>

                                        <div class="comment-box no-accordion">
                                            <form name="form-comment" class="form-comment" method="post">
                                                <input type="text" class="comment-input"/>
                                                <button type="submit" class="submit-comment-button">
                                                    Submit
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                            </a>
                        </div>
                        <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                        <!-- vendor details here (among anything else)-->
                        <div class="drop-down-details">
                            <?php echo $row->addrNum . " " . $row->addrStreet . "<br>"; // add all list detail here
                            echo $row->addrCity . " " . $row->addrState . " " . $row->addrZip . "<br>";
                            echo $row->phone . "<br>";
                            echo $row->website;
                        echo "</div>";

                        // TODO: dragability, @andyjiang
                        } else {
                            // list
                            echo "<div class='inbox-list-wrapper'>";
                                echo "<a href=\"#\">" . $row->UserListsName . " list<br>";
                                    echo "<div class='friend-referral-comment'>" . $row->firstName . " " . $row->lastName . " says " . "\"" . $row->ReferralsComment . "\"";
                                    echo "</div>";
                                echo "</a>";
                            echo "</div>";
                            echo "<div class='list-row'>" . $row->firstName . " " . $row->lastName; // add all list detail here
                            echo "</div>";
                        }
                    }
                ?>

                </div>
                </div>
            </div>
            <!--                                a button you can press to load more rows-->
 <div class="load-more-button">Load more..</div>    
        </div>
        
        
    </body>
</html>
