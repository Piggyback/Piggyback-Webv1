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
                    <h1 id="my-lists-heading">
                        My lists
                    </h1>
                </div>
                <div id="scrollable-sections-holder">
                    <div id="scrollable-sections">
                        <div id="lists-container">
                            <ul id="lists">
                                <?php
                                // add each list as its own <li>
                                foreach ($myLists as $list) {
                                   // echo ("<li>" . $list->name . "</li>");
                                    echo "<li id='my-list-lid--" . $list->lid . "'>" . $list->name . "</li>";
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
                                                <li id="referral-tracking-tab" class="ui-state-default ui-corner-top"><a href="ajax/content3.html">Referral Tracking</a></li>
                                                <li id="search-tab" class="ui-state-default ui-corner-top none"><a href="#search-content"></a></li>
                                                <li id="list-tab" class="ui-state-default ui-corner-top none"><a href="#list-content"></a></li>
                                            </ul>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="inbox-content">
                                                <div id="inbox">
                                                    <div id="accordion-inbox">

                                                    <?php foreach ($inboxItems as $row):?>
<!--                                                    determine if $row is a list or single vendor-->
                                                        <?php if ( $row->lid == 0 ):?>
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
                                                            ?>

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
                                                                <a> <?php echo $row->name; ?>
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
                                                                <?php echo $row->addrNum . " " . $row->addrStreet . "<br>"; // add all list detail here
                                                                echo $row->addrCity . " " . $row->addrState . " " . $row->addrZip . "<br>";
                                                                echo $row->phone . "<br>";
                                                                echo $row->website; ?>
                                                            </div>

                                                            <?php endif; ?>

                                                            <div class="accordion-footer">

                                                                <!-- new div for like/comment button, comment fields, etc like button here -->
                                                                <div class="row" data-rid=<?php echo $row->rid; ?>>
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
                                                                                <?php foreach($commentsArray as $line): ?>
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
                                                                                            <button class="delete-comment" data-cid=<?php echo $line->cid;?>>x</button>
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
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="search-content">
                                                <p> search </p>
                                            </div>
                                            <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="list-content">
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
        <div id="footer">
            <div id="tempfooter">Piggyback</div>
        </div>
    </body>
</html>
