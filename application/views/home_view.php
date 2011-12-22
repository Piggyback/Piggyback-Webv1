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
        <script type="text/javascript" src="../assets/js/date.format.js"></script>
        <script type="text/javascript" src="../assets/js/home_mike_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_andy_js.js"></script>
        <script type="text/javascript" src="../assets/js/home_kim_js.js"></script>
        <script type="text/javascript" src="../assets/js/fixedSplit.js"></script>
        <script type="text/javascript" src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
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
            fbAPI();
        </script>
        <div id="top-bar">
            <a id="logo-container" href="home">
                <h1 id="logo">
<!--                    <span class="none">Piggyback</span>-->
                    <span>Piggyback</span>
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
                <div id="current-pic">
                    <?php echo '<img src="https://graph.facebook.com/' . $currentFBID . '/picture">' ?>
                </div>
                <div id="logout">
                    Logout
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
                                    echo "<li class='my-list-wrapper'><span id='delete-my-list-lid--" . $list->lid . "' class='delete-my-list'>x</span>";
                                    echo "<span id='my-list-lid--" . $list->lid . "' class='my-list'>" . $list->name . "</span>";
                                    echo "<span id='refer-my-list-lid--" . $list->lid . "' class='refer-my-list'>refer</span></li>";
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
                                                <li id="referral-tracking-tab" class="ui-state-default ui-corner-top"><a href="#referral-tracking-content" onClick=loadReferralTracking();>Referral Tracking</a></li>
                                                <li id="search-tab" class="ui-state-default ui-corner-top none"><a href="#search-content"></a></li>
                                                <li id="list-tab" class="ui-state-default ui-corner-top none"><a href="#list-content"></a></li>
                                            </ul>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="inbox-content">
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
                                                                        <?php echo $row->refDate; ?>
                                                                    </div>
                                                                    <a> <?php echo $VendorDetails->name; ?>
                                                                        <!-- sub title here-->
                                                                        <div class="friend-referral-comment-wrapper">
                                                                            <div class="inbox-friend-pic">
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                                                            </div>
                                                                            <div class="inbox-friend-referral">
                                                                                <?php echo $row->firstName . " " . $row->lastName; ?> says "<?php echo $row->ReferralsComment ?>"
                                                                            </div>
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

                                                            <?php else: ?>

                                                                <?php
                                                                    $userListDetails = $row->UserList[0];
                                                                ?>

                                                                <div class="inbox-single-wrapper accordion-header">
                                                                    <div class="referral-date">
                                                                        <?php echo $row->refDate; ?>
                                                                    </div>
                                                                    <a> <?php echo $userListDetails->name; ?>
                                                                        <!-- sub title here-->
                                                                        <div class="friend-referral-comment-wrapper">
                                                                            <div class="inbox-friend-pic">
                                                                                <?php echo '<img src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                                                            </div>
                                                                            <div class="inbox-friend-referral">
                                                                                <?php echo $row->firstName . " " . $row->lastName; ?> says "<?php echo $row->ReferralsComment ?>"
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

                                                            <?php endif; ?>

                                                        <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="load-more-button">
                                                    Load more..
                                                </div>  
                                            </div>
<!--                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="friend-activity-content">
                                                
                                            </div>-->
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="search-content">
                                                <p> search </p>
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="list-content">
                                            </div>
                                            <div class='ui-tabs-panel ui-widget-content ui-corner-bottom' id='referral-tracking-content'>
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
                  <label for='add-to-list-comment-box'><B>Add a comment!</B></label>
                  <textarea name='addToListComment' id='add-to-list-comment-box'></textarea>
             </div>
        </div>
        <div id="footer">
            <div id="tempfooter">Piggyback</div>
        </div>
    </body>
</html>
