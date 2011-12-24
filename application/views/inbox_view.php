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
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
        <?php include "dateTimeDiff.php"; ?>
        
    </head>
    
    <body>
        <div id="inbox">
            <div id="accordion-inbox" class="accordion-object">
                <div id="inbox-wrapper">
                <?php foreach ($inboxItems as $row):?>    
                    <?php
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
                        
                        $VendorDetails = $row->VendorList['VendorList'][0][0];
                        
                        if ($row->ReferralsComment == "") {
                            $recommendationComment = $row->firstName . " " . $row->lastName . " thinks you'll love this!";
                        } else {
                            $recommendationComment = $row->firstName . " " . $row->lastName . " says \"" . $row->ReferralsComment . "\"";
                        }
                        
                        $data_ref = date('Y-m-d H:i:s', strtotime($row->refDate));
                        
                        $dateOfRecord = dateTimeDiff($data_ref);
                        
                        if ($dateOfRecord == "") {
                            $dateOfRecord = date("g:ia, l, F d, y", strtotime($row->refDate));
                        }
                        
                    ?>
                    <!-- determine if $row is a list or single vendor -->
                    <?php if ( $row->lid == 0 ):?>

                    <!-- comments for andy
                        -Add proper indenting to HTML
                        -Like text has lots of whitespace; use jQuery.trim
                        -Make comment font smaller
                        -Clean up code
                    -->

                    <!-- BEGINNING OF HEADER HERE of the ACCORDION    -->
                        <div class="inbox-single-wrapper accordion-header">
                            <div class="referral-date">
                                <?php echo $dateOfRecord; ?>
                            <a> <?php echo $VendorDetails->name; ?>
                                <!-- sub title here-->
                                <div class="friend-referral-comment-wrapper">
                                    <div class="inbox-friend-pic">
                                        <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                    </div>
                                    <div class="inbox-friend-referral">
                                        <?php echo $recommendationComment; ?>
                                    </div>
                                </div>
                            </a>
                            </div>
                        </div>
                        <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                        <!-- vendor details here (among anything else)-->
                        <div class="drop-down-details accordion-content">
                            <?php echo $VendorDetails->addrNum . " " . $VendorDetails->addrStreet . "<br>"; // add all list detail here
                            echo $VendorDetails->addrCity . " " . $VendorDetails->addrState . " " . $VendorDetails->addrZip . "<br>";
                            echo $VendorDetails->phone . "<br>";
                            echo $VendorDetails->website; ?>
                        </div>

                    <?php else: ?>
                        
                        <?php
                            $userListDetails = $row->UserList[0];
                        ?>
                        
                        <div class="inbox-single-wrapper accordion-header">
                            <div class="referral-date">
                                <?php echo $dateOfRecord; ?>
                            </div>
                            <a> <?php echo $userListDetails->name; ?>
                                <!-- sub title here-->
                                <div class="friend-referral-comment-wrapper">
                                    <div class="inbox-friend-pic">
                                        <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                    </div>
                                    <div class="inbox-friend-referral">
                                        <?php echo $recommendationComment; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                        <!-- vendor details here (among anything else)-->
                        <div class="drop-down-details accordion-content">
                            <?php foreach($row->VendorList['VendorList'] as $vendorRow): ?>
                            
                            <div class="subaccordion-object">
                                <div class="subaccordion-header">
                                    <!-- vendor name here -->
                                    <?php echo $vendorRow[0]->name; ?>
                                </div>
                                <div class="subaccordion-content">
                                    <!-- vendor details here -->
                                    <?php
                                        echo $vendorRow[0]->addrNum . " " . $vendorRow[0]->addrStreet . "<br>";
                                        echo $vendorRow[0]->addrCity . " " . $vendorRow[0]->addrState . " " . $vendorRow[0]->addrZip . "<br>";
                                        echo $vendorRow[0]->phone . "<br>";
                                        echo $vendorRow[0]->website;
                                    ?>
                                </div>
                            </div>
                            
                            <?php endforeach; ?>
                            
                        </div>
                        
                    <?php endif; ?>
                        
                        <div class="accordion-footer">

                            <!-- new div for like/comment button, comment fields, etc like button here -->
                            <div id="row-rid-<?=$row->rid?>" class="row" data-rid=<?php echo $row->rid; ?>>
                                <div class="click-to-like no-accordion" data-likeCounts=<?php echo $likeNumber; ?>>
                                    <?php echo $likeStatus; ?>
                                </div>

                                <!--comment button here -->
                                <div class="click-to-comment no-accordion">
                                    Comment
                                </div>
                                <div class="number-of-likes no-accordion">
                                    <?php echo $likeNumber; ?>
                                </div>

                                <!-- create the divs to show other peoples comments-->
                                <div class="comments">
                                    <table class="comments-table">
                                        <tbody class="comments-table-tbody">
                                            <?php $commentsCountdown = count($commentsArray);
                                            // default is that we do not need to show all (0)
                                            $needShowAllButton = "hide-load-comments-button"; ?>
                                            <?php foreach($commentsArray as $line): ?>
                                                <?php
                                                if($commentsCountdown < 3) {
                                                    $showStatus = 'show-comment';
                                                } else {
                                                    // more than two comments, will hide them
                                                    // also turn on the showAllButton flag
                                                    $showStatus = 'hide-comment';
                                                    $needShowAllButton = 'show-load-comments-button';
                                                }
                                                $commentsCountdown--;
                                                ?>

                                                <?php if($commentsCountdown==count($commentsArray)-1): ?>
                                                <tr>
                                                    <td class="show-all-comments-button no-accordion <?php echo $needShowAllButton; ?>">
                                                        View all <?php echo count($commentsArray);?> comments.
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php endif; ?>

                                                <tr class='inbox-single-comment'>
                                                    <td class="commenter-pic">
                                                        <?php echo '<img src="https://graph.facebook.com/' . $line->fbid . '/picture">' ?>
                                                    </td>
                                                    <td class="comments-name">
                                                        <?php echo $line->firstName . " " . $line->lastName . ": "; ?>
                                                    </td>
                                                    <td class="comments-content">
                                                        <?php echo $line->comment; ?>
                                                    </td>
                                                    <td>
                                                        <button id="delete-comment-button-cid-<?=$line->cid?>" class="delete-comment" data-cid=<?php echo $line->cid;?>>x</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
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
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div id="load-more-inbox-content-button" class="load-more-button">
                Load more..
            </div>  
        </div>
    </body>
</html>
