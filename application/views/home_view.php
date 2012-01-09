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
        <script type="text/javascript" src="../assets/js/home_andy_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_kim_js.js"></script>
        <script type="text/javascript" src="../assets/js/jQuery.corner.js"></script>
        <script type="text/javascript" src="../assets/js/fixedSplit.js"></script>
        <script type="text/javascript" src="../assets/js/dialogs.js"></script>
        <script type="text/javascript" src="../assets/js/lists.js"></script>
        <script type="text/javascript" src="../assets/js/accordions.js"></script>
        <script type="text/javascript" src="../assets/js/search.js"></script>
        <script type="text/javascript" src="../assets/js/date.time.stamp.js"></script>
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
        <?php include "dateTimeDiff.php" ?>
    </head>
    <body>
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
            loadFbApiForHome();
        </script>
        <div id="top-bar">
                <h1 id="logo">
                    <a id="logo-container" href="home">
                        <img src="../assets/images/piggyback_logo175x75.gif" alt="Piggyback" />
                    </a>
                </h1>
            <div id="top-nav-bar" class='round-element'>
                <div id="search">
                    <form id="searchform" action="#" method="post">
<!--                        <label for="search-box" id="search-box-label" class='round-element'>Search for: </label>-->
                        <input type="text" value="search for..." name="searchText" size="20" class="box round-element placeholder" id="search-box"/>
<!--                        <label for="search-location" id="search-location-label">Near: </label>-->
                        <input type="text" value="near..." name ="searchLocation" size="20" class="box round-element placeholder" id="search-location"/>
                        <button id="searchbutton" name="submitSearch" class="btn" title="Submit Search">search</button>
                    </form>
                </div>
                <div id="current-pic">
                    <?php echo '<img src="https://graph.facebook.com/' . $currentFBID . '/picture" class="round-element">' ?>
                </div>
                <div id="logout">
                    logout
                </div>
            </div>
        </div>
        <div id="main">
            <div id="left-list-pane">
                <div id="left-list-pane-header">
                    <div id="my-lists-heading">
                        My lists
                        <button id="add-list-button" onClick=clickAddListButton();>+</button>
                    </div>
                </div>
                <div id="scrollable-sections-holder">
                    <div id="scrollable-sections">
                        <div id="lists-container">
                            <ul id="lists">
                                <?php
                                // add each list as its own <li>
                                foreach ($myLists as $list) {
                                  //  echo "<li id='my-list-lid--" . $list->lid . "'><span id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'>x</span>" . $list->name . "</li>";
                                    echo "<li class='my-list-wrapper name-wrapper'><span id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'>x</span>";
                                    echo "<span id='my-list-lid--" . $list->lid . "' class='my-list list-name'>" . $list->name . "</span>";
                                    echo "<span id='refer-my-list-lid--" . $list->lid . "' class='refer-my-list refer-list-popup-link'>refer</span></li>";
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
                                                <li id="inbox-tab" class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#inbox-content" onClick=loadReferralItems(this);>Inbox</a></li>
                                                <li id="friend-activity-tab" class="ui-state-default ui-corner-top"><a href="#friend-activity-content" onClick=loadReferralItems(this);>Friend Activity</a></li>
                                                <li id="referral-tracking-tab" class="ui-state-default ui-corner-top"><a href="#referral-tracking-content" onClick=loadReferralItems(this);>Referral Tracking</a></li>
                                                <li id="search-tab" class="ui-state-default ui-corner-top none"><a href="#search-content"></a></li>
                                                <li id="list-tab" class="ui-state-default ui-corner-top none"><a href="#list-content"></a></li>
                                            </ul>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="inbox-content">
                                                <div id="accordion-inbox" class="accordion-object">
                                                    <?php foreach ($inboxItems as $row):?>
                                                        <div class="referral-item-wrapper">
                                                            <?php if ($row->isCorrupted == 0): ?>
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

                                                                    if ( $row->lid == 0 ) {
                                                                        $recommendationComment = "<span class='name'>" . $row->firstName . " " . $row->lastName . "</span> recommends you <span class='vendor-name list-name'>" . $VendorDetails->name . "</span>";
                                                                    } else {
                                                                        $recommendationComment = "<span class='name'>" . $row->firstName . " " . $row->lastName . "</span> recommends you <span class='vendor-name list-name'>" . $row->UserList[0]->name . "</span>";
                                                                    }

                                                                    $senderComment = "\"" . $row->ReferralsComment . "\"";

                                                                    $data_ref = date('Y-m-d H:i:s', strtotime($row->refDate));

                                                                    $dateOfRecord = dateTimeDiff($data_ref);

                                                                    if ($dateOfRecord == "") {
                                                                        $dateOfRecord = date("g:ia, l, F dS, Y", strtotime($row->refDate));
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
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                                                            </div>
                                                                            <div class="friend-referral">
                                                                                <?php echo $recommendationComment; ?>
                                                                                <br>
                                                                                <?php echo $senderComment; ?>
                                                                            </div>
                                                                            <div class="button-row">
                                                                                <button id='referrals-remove-button-id--<?php echo $row->rid; ?>' class='referrals-remove-button no-accordion' data-rid='<?php echo $row->rid; ?>'>
                                                                                    x
                                                                                </button>
                                                                                <button id="referral-item-id--<?php echo $VendorDetails->id; ?>" class="refer-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Refer
                                                                                </button>
                                                                                <button id="referral-item-id--<?php echo $VendorDetails->id; ?>" class="add-to-list-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Add
                                                                                </button>
                                                                            </div>
                                                                        </a>
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

                                                                    <!-- LIST HERE -->
                                                                    <div class="single-wrapper accordion-header name-wrapper">
                                                                        <a>
                                                                            <div class='referral-date time-stamp'>
                                                                                <?php echo $dateOfRecord; ?>
                                                                            </div>
                                                                            <div class="friend-pic">
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                                                            </div>
                                                                            <div class="friend-referral">
                                                                                <?php echo $recommendationComment; ?>
                                                                                <br>
                                                                                <?php echo $senderComment; ?>
                                                                            </div>
                                                                            <div class="button-row">
                                                                                <button id='referrals-remove-button-id--<?php echo $row->rid; ?>' class='referrals-remove-button no-accordion' data-rid='<?php echo $row->rid; ?>'>
                                                                                    x
                                                                                </button>
                                                                                <button id="referral-item-id--<?php echo $row->lid; ?>" class="refer-list-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Refer
                                                                                </button>
                                                                                <button id="referral-item-id--<?php echo $row->lid; ?>" class="add-list-to-list-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Add
                                                                                </button>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <!-- END HEADER OF ACCORDION HERE ENDS HERE -->
                                                                    <!-- vendor details here (among anything else)-->
                                                                    <div class="drop-down-details accordion-content">
                                                                        <?php foreach($row->VendorList['VendorList'] as $vendorRow): ?>

                                                                        <div class="subaccordion-object name-wrapper">
                                                                            <div class="subaccordion-header">
                                                                                <span class="vendor-name">
                                                                                    <!-- vendor name here -->
                                                                                    <?php echo $vendorRow[0]->name; ?>
                                                                                </span>
                                                                                <button id="referral-item-id--<?php echo $vendorRow[0]->id; ?>" class="refer-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Refer
                                                                                </button>
                                                                                <button id="referral-item-id--<?php echo $vendorRow[0]->id; ?>" class="add-to-list-popup-link dialog_link ui-state-default ui-corner-all">
                                                                                    <span class="ui-icon ui-icon-plus"></span>
                                                                                    Add
                                                                                </button>
                                                                            </div>
                                                                            <div class="subaccordion-content">
                                                                                <!-- vendor details here -->
                                                                                <?php
                                                                                    echo $vendorRow[0]->addrNum . " " . $vendorRow[0]->addrStreet . "<br>";
                                                                                    echo $vendorRow[0]->addrCity . " " . $vendorRow[0]->addrState . " " . $vendorRow[0]->addrZip . "<br>";
                                                                                    echo $vendorRow[0]->phone . "<br>";
                                                                                    echo $vendorRow[0]->website;
                                                                                ?>
                                                                                <?php echo "<span class='referral-comment'>" . $vendorRow['senderComment'] . "</span>"; ?>
                                                                            </div>
                                                                        </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                    <div class="accordion-footer">
                                                                        <!-- new div for like/comment button, comment fields, etc like button here -->
                                                                        <div id="row-rid--<?=$row->rid?>" class="row" data-rid=<?php echo $row->rid; ?>>
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
                                                                                        $removeButtonHTML = "<button id='remove-comment-button-cid--" . $line->cid . "' class='remove-comment-button' data-cid=" . $line->cid . ">x</button>";
                                                                                    } else {
                                                                                        $removeButtonHTML = "";
                                                                                    }
                                                                                    $data_ref = date('Y-m-d H:i:s', strtotime($line->date));

                                                                                    $commentDate = dateTimeDiff($data_ref);

                                                                                    if ($commentDate == "") {
                                                                                        $commentDate = date("g:ia, l, F dS, Y", strtotime($line->date));
                                                                                    }
                                                                                    ?>

                                                                                    <?php if($commentsCountdown==count($commentsArray)-1): ?>
                                                                                    <div class="show-all-comments-button no-accordion <?php echo $needShowAllButton; ?>">
                                                                                        View all <?php echo count($commentsArray);?> comments.
                                                                                    </div>
                                                                                    <?php endif; ?>

                                                                                    <div class='single-comment'>
                                                                                        <div class="commenter-pic">
                                                                                            <?php echo '<img src="https://graph.facebook.com/' . $line->fbid . '/picture">' ?>
                                                                                        </div>
                                                                                        <div class="comment-wrapper-text">
                                                                                            <div class="comments-content">
                                                                                                <span class="name">
                                                                                                    <?php echo $line->firstName . " " . $line->lastName . ": "; ?>
                                                                                                </span>
                                                                                                <?php echo $line->comment; ?>
                                                                                            </div>
                                                                                            <div class="comment-date time-stamp">
                                                                                                <?php echo $commentDate; ?>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="remove-comment-button-wrapper">
                                                                                            <?php echo $removeButtonHTML; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endforeach; ?>
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
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
<!--                                                <div id="load-more-inbox-content-button" class="load-more-button">
                                                    Load more..
                                                </div>-->
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="search-content">
                                                <p> search </p>
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="list-content">
<!--                                                <div id='accordion-list' class='accordion-object'>
                                                </div>-->
                                            </div>
                                            <div class='ui-tabs-panel ui-widget-content ui-corner-bottom' id='friend-activity-content'>
                                                <div id='accordion-friend-activity' class='accordion-object'>
                                                </div>
                                            </div>
                                            <div class='ui-tabs-panel ui-widget-content ui-corner-bottom' id='referral-tracking-content'>
                                                <div id='accordion-referral-tracking' class='accordion-object'>
                                                </div>
                                            </div>
                                            <div class="hidden-list-content none" id="empty-list-content">
                                                <p> List is empty! </p>
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
                <label for='add-list-name'>Name: </label>
                <br>
                <input type='text' id='add-list-name' name='add-list-name'>
                <br>
                <br>
                <input type='submit' id='add-list-submit' value='Create new list!'>
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
            <div id='friends-refer-upper'>
                <div id='friends-refer-left'>
                    <div id='friends-refer-search'>
                        <form id='addFriend' method='post' onsubmit='return false;'>
                            <label for='tags'><B>Who do you want to refer to?</b><BR></label>
                            <input type='text' id='tags' autocomplete='off' name='friend'>
                            <input type='submit' id='searchFriendsButton' value='Add to List'/>
                        </form>
                    </div>
                    <div id='friends-refer-display'>
                        <div id='friends-refer-display-left'>
                        </div>
                        <div id='friends-refer-display-right'>
                        </div>
                    </div>
                </div>
                <div id='friends-refer-right'>
                </div>
            </div>
            <div id='friends-refer-comment'>
                 <label for='comment-box'><B>Add a comment with your referral!</B></label>
                 <textarea name='comment' id='comment-box'></textarea>
            </div>
        </div>

        <div id='addToListDialog' class='none'>
             <div id='add-to-existing-list'>
             </div>
             <div id='add-to-new-list'>
             </div>
             <div id='add-to-list-comment'>
<!--                  <label for='add-to-list-comment-box'><B>Add a comment to remember what you like about this place!</B></label>
                  <textarea name='addToListComment' id='add-to-list-comment-box'></textarea>-->
             </div>
        </div>
        <div id="footer">
            <div id="tempfooter">Piggyback</div>
        </div>
    </body>
</html>
