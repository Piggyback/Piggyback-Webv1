<!-- the URL for this TEST SANDBOX is ../test -->

<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="../../assets/js/date.format.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
                <script type="text/javascript" src="../assets/js/date.format.js"></script>
        <meta charset="utf-8">
	
    </head>
    <body>
        <div class="single-wrapper accordion-header name-wrapper">
            <a>
                <table class='formatted-table'>
                    <tr>
                        <td>
                            <div class='referral-date'>
                                <?php echo $dateOfRecord; ?>
                            </div>
                        </td>
                        <td>
                            <button id='referrals-remove-button-id--<?php echo $row->rid; ?>' class='referrals-remove-button no-accordion' data-rid='<?php echo $row->rid; ?>'>
                                x
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="list-name vendor-name">
                            <?php echo $VendorDetails->name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class='friend-referral-comment-wrapper'>
                                <div class="friend-pic">
                                    <?php echo '<img class="round-element" src="https://graph.facebook.com/' . $row->fbid . '/picture">' ?>
                                </div>
                                <div class="friend-referral">
                                    <?php echo $recommendationComment; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p>
                                <a href="#" id="referral-item-id--<?php echo $VendorDetails->id; ?>" class="refer-popup-link dialog_link ui-state-default ui-corner-all">
                                    <span class="ui-icon ui-icon-plus"></span>
                                    Refer to Friends
                                </a>
                            </p>
                            <p>
                                <a href="#" id="referral-item-id--<?php echo $VendorDetails->id; ?>" class="add-to-list-popup-link dialog_link ui-state-default ui-corner-all">
                                    <span class="ui-icon ui-icon-plus"></span>
                                    Add to List
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </a>
        </div>


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

                    if($line->uid == $row->uid2) {
                        $removeButtonHTML = "<button id='delete-comment-button-cid--" . $line->cid . " class='delete-comment' data-cid=" . $line->cid . ">x</button>";
                    } else {
                        $removeButtonHTML = "";
                    }

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

                    <tr class='single-comment'>
                        <td class="commenter-pic">
                            <?php echo '<img src="https://graph.facebook.com/' . $line->fbid . '/picture">' ?>
                        </td>
                        <td class="comments-name">
                            <?php echo $line->firstName . " " . $line->lastName . ": "; ?>
                        </td>
                        <td class="comments-content">
                            <?php echo $line->comment; ?>
                        </td>
                        <td class="remove-comment-button">
                            <?php echo $removeButtonHTML; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>
