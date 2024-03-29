<!DOCTYPE html>
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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_mike_css.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_andy_css.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../assets/css/home_kim_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../assets/js/facebook.js" ></script>
        <script type="text/javascript" src="../assets/js/home.js" ></script>
        <script type="text/javascript" src="../assets/js/date.format.js"></script>
<!--        <script type="text/javascript" src="../assets/js/home_mike_js.js"></script>-->
<!--        <script type="text/javascript" src="../assets/js/home_andy_js.js"></script>-->
<!--        <script type="text/javascript" src="../assets/js/home_kim_js.js"></script>-->
<!--        <script type="text/javascript" src="../assets/js/fixedSplit.js"></script>-->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>
        <script type="text/javascript" src="../assets/js/dialogs.js"></script>
        <script type="text/javascript" src="../assets/js/lists.js"></script>
        <script type="text/javascript" src="../assets/js/accordions.js"></script>
        <script type="text/javascript" src="../assets/js/search.js"></script>
        <script type="text/javascript" src="../assets/js/date.time.stamp.js"></script>
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
        <?php include "dateTimeDiff.php" ?>
        <link rel="icon" href="https://twimg0-a.akamaihd.net/profile_images/1796614844/piggyback512x512_reasonably_small.png" type="image/x-png">
    </head>
    <body>
        </div>
        <div id='currentUserNameWrapper'>
            <span id="currentUserName" class="none">
                <?php echo $currentFirstName . ' ' . $currentLastName; ?>
            </span>
        </div>
        <div id='current-fbid-wrapper'>
            <span id="current-fbid" class="none">
                <?php echo $currentFBID; ?>
            </span>
        </div>
        <div id='current-uid-wrapper'>
            <span id='current-uid' class='none'>
                <?php echo $currentUID; ?>
            </span>
        </div>
        <div id='fuzz'></div>
        <div id="fb-root"></div>
        <script>
            loadFbApiForHomeClosedBeta();
        </script>
        <div id="top-bar">
                <h1 id="logo">
                    <a id="logo-container" href="home">
                        <img src="../assets/images/piggyback_logo175x75.gif" alt="Piggyback" />
                    </a>
                </h1>
            <div id="top-nav-bar" class='rounded-corners'>
                <div id="search">
                    <form id="searchform" action="#" method="post">
<!--                        <label for="search-box" id="search-box-label" class='round-element'>Search for: </label>-->
                        <input type="text" value="search for..." name="searchText" size="20" maxlength="64" class="box round-element placeholder rounded-corners" id="search-box"/>
<!--                        <label for="search-location" id="search-location-label">Near: </label>-->
                        <input type="text" value="near..." name ="searchLocation" size="20" maxlength="64" class="box round-element placeholder rounded-corners" id="search-location"/>
                        <button id="searchbutton" name="submitSearch" class="btn" title="Submit Search">search</button>
                    </form>
                </div>
                <div id="current-pic">
                    <?php echo '<img src="https://graph.facebook.com/' . $currentFBID . '/picture" class="rounded-corners">' ?>
                </div>
                <div id="logout">
                    logout
                </div>
            </div>
        </div>
        <div id="main">
            <div id="left-list-pane">
                <div id="left-list-pane-header" class='top-rounded-corners'>
                    My Lists
<!--                    <img id="add-list-button" src="../assets/images/piggyback_button_add_f1.png" alt="+" />-->
                    <a id="add-list-button" alt="+" role="button" href="#"><span>   </span></a>
                </div>
                <div id="scrollable-sections-holder">
                    <div id="scrollable-sections">
                        <div id="lists-container">
                            <ul id="lists">
                                <?php
                                if (count($myLists) == 0) {
                                    echo "<div id='no-list-message'>You currently have no lists!</div>";
                                } else {
                                    echo "<div id='no-list-message' class='none'>You currently have no lists!</div>";
                                }
                                // add each list as its own <li>
                                foreach ($myLists as $list) {
                                  //  echo "<li id='my-list-lid--" . $list->lid . "'><span id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'>x</span>" . $list->name . "</li>";
                                    echo "<li class='my-list-wrapper name-wrapper'><img src='../assets/images/piggyback_button_close_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_close_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_f1.png'\" id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'></img>";

//                                    echo "<li class='my-list-wrapper name-wrapper'><span id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'>x</span>";
                                    $modifiedName = htmlspecialchars($list->name, ENT_QUOTES);
//                                    if (strlen($modifiedName) > 17) {
//                                        $modifiedName = substr($modifiedName, 0, 17) . '...';
//                                    }
                                    echo "<span id='my-list-lid--" . $list->lid . "' class='my-list list-name' title='" . $modifiedName . "'>" . $modifiedName . "</span>";
                                    echo "<span class='refer-my-list-wrapper'><img src='../assets/images/piggyback_button_refer_small_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_refer_small_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_refer_small_f1.png'\" id='refer-my-list-lid--" . $list->lid . "' class='refer-my-list refer-list-popup-link'></img></span></li>";
//                                    echo "<span id='refer-my-list-lid--" . $list->lid . "' class='refer-my-list refer-list-popup-link'>refer</span></li>";
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
                                        <div class="ui-tabs ui-widget ui-widget-content ui-corner-top" id="tabs">
                                            <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-top">
                                                <li id="inbox-tab" class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#inbox-content" onClick=loadReferralItemsFromTab(this);><span>&nbsp;</span></a></li>
                                                <li id="friend-activity-tab" class="ui-state-default ui-corner-top"><a href="#friend-activity-content" onClick=loadReferralItemsFromTab(this);><span>&nbsp;</span></a></li>
                                                <li id="referral-tracking-tab" class="ui-state-default ui-corner-top"><a href="#referral-tracking-content" onClick=loadReferralItemsFromTab(this);><span>&nbsp;</span></a></li>
                                                <li id="search-tab" class="ui-state-default ui-corner-top none"><a href="#search-content"></a></li>
                                                <li id="list-tab" class="ui-state-default ui-corner-top none"><a href="#list-content"></a></li>
                                            </ul>
<!--                                            <div id='content'>-->
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="inbox-content">
                                                <div id="accordion-inbox">
                                                    <?php
                                                    $none = ""; 
                                                    $showMore = "none";
                                                    if( count($inboxItems) > 3 ) {
                                                        $showMore = "";
                                                        array_pop( $inboxItems );
                                                    }
                                                    ?>
                                                    <?php foreach ($inboxItems as $row):?>
                                                        <div class="referral-item-wrapper ui-corner-all accordion-object">
                                                            <?php if ($row->isCorrupted == ""): ?>
                                                                <?php
                                                                    $none = "none";
                                                                
                                                                    // get some vital variables ready
                                                                    // the count of likes
                                                                    $tempArray = $row->LikesList;
                                                                    $likesArray = $tempArray['LikesList'];
                                                                    $tempArray = $row->CommentsList;
                                                                    $commentsArray = $tempArray['CommentsList'];

                                                                    $likesArrayCount = count($likesArray);
                                                                    
//                                                                    echo $likesArray[0]->rid;     // how to get the likes
                                                                    
                                                                    $likeNumber = "0";
                                                                    if ($likesArrayCount>0) {
                                                                        $likeNumber = "$likesArrayCount";
//                                                                        if ($likesArrayCount == 1) {
//                                                                            $likeNumber = $likeNumber . " person likes this.";
//                                                                        } else {
//                                                                            $likeNumber = $likeNumber . " people like this.";
//                                                                        }
                                                                    }
                                                                    // if uid does not exist in the Likes rid list
                                                                    if ($row->alreadyLiked == 1) {
//                                                                        $likeStatus = "Unlike";
//                                                                        $likeClass = " is-liked";
                                                                        $likeStatus = "../assets/images/piggyback_button_like_counter_red_f1.png";
//                                                                        $otherLikeStatus = "../assets/images/piggyback_button_like_counter_red_f1.png";
//                                                                        $otherLikeStatus = "../assets/images/piggyback_button_like_counter_f1.png";
                                                                    } else {
//                                                                        $likeStatus = "Like";
//                                                                        $likeClass = "";
                                                                        $likeStatus = "../assets/images/piggyback_button_like_counter_f1.png";
//                                                                        $likeStatus = "../assets/images/piggyback_button_like_counter_f1.png";
//                                                                        $otherLikeStatus = "../assets/images/piggyback_button_like_counter_f1.png";
                                                                    }

                                                                    $VendorDetails = $row->VendorList['VendorList'][0][0];

                                                                    if ( $row->lid == 0 ) {
                                                                        $recommendationComment = "<b>" . $row->firstName . " " . $row->lastName . "</b> recommended you <span class='vendor-name'>" . $VendorDetails->name . "</span>";
                                                                    } else {
                                                                        $recommendationComment = "<b>" . $row->firstName . " " . $row->lastName . "</b> recommended you the \"<span class='list-name'>" . $row->UserList[0]->name . "</span>\" list";
                                                                    }

                                                                    $senderComment = "<i><span class='referral-comment'><q class='comment-wrapper'>" . $row->comment . "</q></span></i>";
                                                                    if ($row->comment == "") {
                                                                        $senderComment = "<i><span class='referral-comment'><q class='comment-wrapper empty-quote'></q></span></i>";
                                                                    }

                                                                    $data_ref = date('Y-m-d H:i:s', strtotime($row->date));

                                                                    $dateOfRecord = dateTimeDiff($data_ref);

                                                                    if ($dateOfRecord == "") {
                                                                        $dateOfRecord = date("g:ia, l, F jS, Y", strtotime($row->date));
                                                                    }
                                                                ?>
                                                                <!-- determine if $row is a list or single vendor -->
                                                                <?php if ( $row->lid == 0 ):?>

                                                                <!-- SINGLE VENDOR HERE -->

                                                                <!-- BEGINNING OF HEADER HERE of the ACCORDION    -->
                                                                    <div class="single-wrapper accordion-header name-wrapper">
                                                                        <a>
                                                                            <div class='referral-date time-stamp'>
                                                                                <?php echo $dateOfRecord; ?>
                                                                            </div>
                                                                            <div class="friend-pic">
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture" class="ui-corner-all">' ?>
                                                                            </div>
                                                                            <div class="friend-referral">
                                                                                <?php echo $recommendationComment; ?>
                                                                                <br>
                                                                                <?php echo $senderComment; ?>
                                                                            </div>
                                                                            <div class="button-row no-accordion">
                                                                                <img src="../assets/images/piggyback_button_close_big_f1.png" alt="refer" id="referrals-remove-button-id--<?php echo $row->rid; ?>" class='referrals-remove-button' data-rid='<?php echo $row->rid; ?>' onmouseover="this.src='../assets/images/piggyback_button_close_big_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_close_big_f1.png'">
                                                                                </img>
                                                                                <img src="../assets/images/piggyback_button_refer_f1.png" alt="refer" id="refer-to-friends-single-referral-id--<?php echo $VendorDetails->id; ?>" class="refer-popup-link dialog_link" onmouseover="this.src='../assets/images/piggyback_button_refer_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_refer_f1.png'">
                                                                                </img>
                                                                                <img id="add-to-list-single-referral-id--<?php echo $VendorDetails->id; ?>" alt="+" src="../assets/images/piggyback_button_add_f1.png" class="add-to-list-popup-link dialog_link" onmouseover="this.src='../assets/images/piggyback_button_add_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_add_f1.png'">
                                                                                </img>
                                                                                <div class="number-of-likes">
                                                                                    <div class='number-of-likes-inner'><?php echo $likeNumber; ?></div>
                                                                                </div>
                                                                                <img src='<?php echo $likeStatus; ?>' alt="like" class="click-to-like">
<!--                                                                                <img src='<?php echo $likeStatus; ?>' onmouseover="this.src='<?php echo $otherLikeStatus; ?>'" onmouseout="this.src='<?php echo $likeStatus; ?>'" alt="like" class="click-to-like <?php echo $likeClass; ?>" >-->
                                                                                </img>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                                                                    <!-- vendor details here (among anything else)-->
                                                                    <div class="drop-down-details accordion-content">
                                                                        <?php echo $VendorDetails->addr . "<br>"; // add all list detail here
                                                                        echo $VendorDetails->addrCity . " " . $VendorDetails->addrState . " " . $VendorDetails->addrZip . "<br>";
                                                                        echo $VendorDetails->phone;
                                                                        if ($VendorDetails->website != '') {
                                                                            echo "<BR><a href='" . $VendorDetails->website . "' class ='website-link' target='_blank'>" . $VendorDetails->website . "</a>";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                <?php else: ?>

                                                                    <!-- LIST HERE -->
                                                                    <div class="single-wrapper accordion-header name-wrapper">
                                                                        <a>
                                                                            <div class='referral-date time-stamp'>
                                                                                <?php echo $dateOfRecord; ?>
                                                                            </div>
                                                                            <div class="friend-pic">
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture" class="ui-corner-all">' ?>
                                                                            </div>
                                                                            <div class="friend-referral">
                                                                                <?php echo $recommendationComment; ?>
                                                                                <br>
                                                                                <?php echo $senderComment; ?>
                                                                            </div>
                                                                            <div class="button-row no-accordion">
                                                                                <img src="../assets/images/piggyback_button_close_big_f1.png" alt="refer" id='referrals-remove-button-id--<?php echo $row->rid; ?>' class='referrals-remove-button' data-rid='<?php echo $row->rid; ?>' onmouseover="this.src='../assets/images/piggyback_button_close_big_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_close_big_f1.png'">
                                                                                </img>
                                                                                <img alt="refer" src="../assets/images/piggyback_button_refer_f1.png" id="refer-to-friends-list-referral-id--<?php echo $row->lid; ?>" class="refer-list-popup-link dialog_link" onmouseover="this.src='../assets/images/piggyback_button_refer_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_refer_f1.png'">
                                                                                </img>
                                                                                <img id="add-to-list-list-referral-id--<?php echo $row->lid; ?>" alt="+" src="../assets/images/piggyback_button_add_f1.png" class="add-list-to-list-popup-link dialog_link" onmouseover="this.src='../assets/images/piggyback_button_add_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_add_f1.png'">
                                                                                </img>
                                                                                <div class="number-of-likes">
                                                                                    <div class='number-of-likes-inner'><?php echo $likeNumber; ?></div>
                                                                                </div>
                                                                                <img src='<?php echo $likeStatus; ?>' alt="like" class="click-to-like">
<!--                                                                                <img src='<?php echo $likeStatus; ?>' onmouseover="this.src='<?php echo $otherLikeStatus; ?>'" onmouseout="this.src='<?php echo $likeStatus; ?>'" alt="like" class="click-to-like <?php echo $likeClass; ?>">-->
                                                                                </img>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                                                                    <!-- vendor details here (among anything else)-->
                                                                    <div class="drop-down-details accordion-content">
                                                                        <?php foreach($row->VendorList['VendorList'] as $vendorRow): ?>
                                                                        <?php
                                                                            $singleComment = "<span class='referral-comment'><span class='comment-wrapper'></span></span>";
                                                                            if ($vendorRow['senderComment'] != "") {
                                                                                $singleComment = "<i><span class='referral-comment'><q class='comment-wrapper'>" . $vendorRow['senderComment'] . "</q></span></i>";
                                                                            }
                                                                        ?>

                                                                        <div class="subaccordion-object name-wrapper">
                                                                            <div class="subaccordion-header">
                                                                                <span class="vendor-name referral-list-vendor-name"><?php echo $vendorRow[0]->name; ?></span><br>
                                                                                <?php echo $singleComment; ?>
                                                                                <div class='subaccordion-button-row no-accordion'>
                                                                                <img alt="refer" src="../assets/images/piggyback_button_refer_f1.png" id="refer-to-friends-single-referral-id--<?php echo $vendorRow[0]->id; ?>" class="refer-popup-link dialog_link referral-list-vendor-refer" onmouseover="this.src='../assets/images/piggyback_button_refer_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_refer_f1.png'">
                                                                                </img>
                                                                                <img id="add-to-list-single-referral-id--<?php echo $vendorRow[0]->id; ?>" alt="+" src="../assets/images/piggyback_button_add_f1.png" class="add-to-list-popup-link dialog_link referral-list-vendor-add-to-list" onmouseover="this.src='../assets/images/piggyback_button_add_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_add_f1.png'">
                                                                                </img>
                                                                                </div>
                                                                            </div>
                                                                            <div class="subaccordion-content">
                                                                                <!-- vendor details here -->
                                                                                <?php
                                                                                    echo $vendorRow[0]->addr . "<br>";
                                                                                    echo $vendorRow[0]->addrCity . " " . $vendorRow[0]->addrState . " " . $vendorRow[0]->addrZip . "<br>";
                                                                                    echo $vendorRow[0]->phone;
                                                                                    if ($vendorRow[0]->website != '') {
                                                                                        echo "<BR><a href='" . $vendorRow[0]->website . "' class ='website-link' target='_blank'>" . $vendorRow[0]->website . "</a>";
                                                                                    }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                    <div class="accordion-footer">
                                                                        <!-- new div for like/comment button, comment fields, etc like button here -->
                                                                        <div id="row-rid--<?=$row->rid?>" class="row" data-rid=<?php echo $row->rid; ?>>
<!--                                                                            <div class="click-to-like no-accordion" data-likeCounts=<?php echo $likeNumber; ?>>
                                                                                <?php //echo $likeStatus; ?>
                                                                            </div>-->
<!--                                                                            <div class="number-of-likes">
                                                                                //<?php echo $likeNumber; ?>
                                                                            </div>-->
                                                                            <!-- create the divs to show other peoples comments-->
                                                                            <div class="comments">
                                                                                <div class="comments-body">
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

                                                                                        if($line->uid == $row->uid2) {
                                                                                            $removeButtonHTML = "<button id='remove-comment-button-cid--" . $line->cid . "' class='remove-comment-button' data-cid=" . $line->cid . "></button>";
                                                                                        } else {
                                                                                            $removeButtonHTML = "";
                                                                                        }
                                                                                        $data_ref = date('Y-m-d H:i:s', strtotime($line->date));

                                                                                        $commentDate = dateTimeDiff($data_ref);

                                                                                        if ($commentDate == "") {
                                                                                            $commentDate = date("g:ia, l, F jS, Y", strtotime($line->date));
                                                                                        }
                                                                                        ?>

                                                                                        <?php if($commentsCountdown==count($commentsArray)-1): ?>
                                                                                        <div class="show-all-comments-button <?php echo $needShowAllButton; ?>">
                                                                                            View all <?php echo count($commentsArray);?> comments.
                                                                                        </div>
                                                                                        <?php endif; ?>
                                                                                        <div class='single-comment <?php echo $showStatus; ?>'>
                                                                                            <div class="commenter-pic">
                                                                                                <?php echo '<img src="https://graph.facebook.com/' . $line->fbid . '/picture" class="ui-corner-all">' ?>
                                                                                            </div>
                                                                                            <div class="comment-wrapper-text">
                                                                                                <div class="comments-content">
                                                                                                    <b>
                                                                                                        <?php echo $line->firstName . " " . $line->lastName; ?>
                                                                                                    </b><br>
                                                                                                    <?php echo $line->comment; ?>
                                                                                                </div>
                                                                                                <div class="comment-date time-stamp">
                                                                                                    <?php echo $commentDate; ?>
                                                                                                </div>
                                                                                            </div>
                                                                                            <?php echo $removeButtonHTML; ?>
                                                                                        </div>
                                                                                    <?php endforeach; ?>
                                                                                </div>
                                                                                <div class="comment-box">
                                                                                    <form name="form-comment" class="form-comment" method="post">
                                                                                        <div class="commenter-pic">
                                                                                            <?php echo '<img src="https://graph.facebook.com/' . $currentFBID . '/picture" class="ui-corner-all commenter-pic"></img>'; ?>
                                                                                        </div>
                                                                                        <input type="text" value="Write a comment..." title="Write a comment..." class="comment-input placeholder"/>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div id="like-list-dialog--<?php echo $row->rid?>" class="like-dialog-modeless none">
                                                                    <?php
                                                                        foreach( $likesArray as $i ) {
                                                                            echo $i->firstName . " " . $i->lastName;
                                                                        }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <div id="inbox-load-more-button--<?php echo count($inboxItems); ?>" class="load-more-button <?php echo $showMore; ?>">
                                                       Show older..
                                                </div>
                                                <div id="empty-inbox-message" class="<?php echo $none;?>">Your inbox is empty! Better make some friends who want to share cool things with you.</div>
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="search-content">
                                            </div>  
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="list-content">
                                            </div>
                                            <div class='ui-tabs-panel ui-widget-content ui-corner-bottom' id='friend-activity-content'>
                                                <div id='accordion-friend-activity'>
                                                </div>
                                                <div id="friend-activity-load-more-button--0" class="load-more-button none">
                                                       Show older..
                                                </div>
                                                <div id="empty-friend-activity-message" class="none">There is no friend activity.</div>
                                            </div>
                                            <div class='ui-tabs-panel ui-widget-content ui-corner-bottom' id='referral-tracking-content'>
                                                <div id='accordion-referral-tracking'>
                                                </div>
                                                <div id="referral-tracking-load-more-button--0" class="load-more-button none">
                                                       Show older..
                                                </div>
                                                <div id="empty-referral-tracking-message" class="none">You haven't recommended anything to anyone yet. Recommend something today!</div>
                                            </div>
                                            <div class="hidden-list-content none" id="empty-list-content">
                                                List is empty!
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
        
        <div id='add-list-dialog' class='none'>
            <form id='add-list-form' method='post' onsubmit='return false;'>
                <label for='add-list-name'>Name your new list: </label>
                <br>
                <input type='text' id='add-list-name' maxlength="32" name='add-list-name'>
                <br>
                <br>
                <div id='add-list-button-wrapper'>
                    <input type='submit' id='add-list-submit' value=''>
                </div>
            </form>
        </div>
        <div id='edit-list-comment-dialog' class='none'>
            <form id='edit-list-comment-form' method='post' onsubmit='return false;'>
                <label for='edit-list-comment-value'>New comment: </label>
                <br>
                <input type='text' class='edit-list-comment-value no-enter-submit' name='edit-list-comment-value'>
            </form>
        </div>
        <div id='dialog' class='none'>
            <div id='friends-refer-upper' class='corner'>
                <div id='friends-refer-left' class='corner'>
                    <div id='friends-refer-search' class='search-corner'>
                        <form id='addFriend' method='post' onsubmit='return false;'>
                            <label id='referLabel' for='tags'></label>
                            <input type='text' id='tags' autocomplete='off' name='friend'><img src='../assets/images/piggyback_button_close_f1.png' onmouseover="this.src='../assets/images/piggyback_button_close_f2.png'" onmouseout="this.src='../assets/images/piggyback_button_close_f1.png'" class='clear-friend-name'></img>
                            <input type='submit' id='searchFriendsButton' value='Add to List'/>
                        </form>
                    </div>
                    <div id='friends-refer-display' class='display-corner'>
                        <div id='friends-refer-display-left' class='corner'>
                        </div>
                        <div id='friends-refer-display-right' class='corner'>
                        </div>
                    </div>
                </div>
                <div id='friends-refer-right' class='corner'>
                </div>
            </div>
            <div id='friends-refer-comment' class='corner'>
                 <label for='comment-box'>Add a comment with your referral!</label>
                 <textarea name='comment' id='comment-box'></textarea>
            </div>
        </div>

        <div id='addToListDialog' class='none'>
             <div id='add-to-existing-list'>
             </div>
             <div id='add-to-new-list'>
             </div>
             <div id='add-to-list-comment'>
             </div>
        </div>
        <div id='confirmDeleteDialog' class='none'>
            <div id='confirm-delete-msg'>
                Are you sure you want to delete this?
            </div>
        </div>
        <div id='email-dialog' class='none'>
            <form id='email-dialog-form' method='post' onsubmit='return false;'>
<!--                <label for='email-bug-name'>Your name:</label><BR>
                <input type="text" id='email-bug-name' name='email-bug-name'></input><BR>
                <label for='email-bug-email'>Your email:</label><BR>
                <input type="text" id='email-bug-email' name='email-bug-email'></input><BR>-->
                <label for='email-bug-body'>Describe the bug:</label><BR>
                <textarea id="email-content-body" name="email-bug-body" rows="8" cols="20"></textarea>
                <div id='email-dialog-wrapper'>
                    <input type='submit' id='email-submit-button' value=''>
                </div>
<!--                <br>
                <em id="empty-email-alert" class="none">Email content is empty.</em>-->
            </form>
<!--            <div id="email-confirmation-alert" class="none"><br><em>Message sent!</em></div>-->
        </div>
        <div id="loading">
          <p><img src="../assets/images/loading.gif" /><BR>Please Wait</p>
        </div>
        <div id="footer">
            <div id="email-button">Something not working? <em>Click here to tell us about it!</em></div>
            <div id="tempfooter">Piggyback</div>
        </div>
    </body>
</html>
