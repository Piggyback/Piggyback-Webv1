/* 
 * Document     : accordions.js
 * Created on   : Jan 4, 2011, 1:03 AM
 * Author       : @andyjiang
 * Description  :
 *   functions:
 *   
 *      -- accordion specific functions --
 *   
 *      bindAccordion
 *      overrideAccordionEvent
 *      
 *      -- initialize elements functions (to be called once) --
 *      
 *      initLoadMoreComment
 *      initCommentInputDisplay
 *      initLike
 *      initComment
 *      initRemoveComment
 *      initRemoveReferralButton
 *      
 *      -- initialize whenever destroy/rebind accordion --
 *      
 *      reInitCommentEvents
 *      resetReferralContent
 *      
 *      -- monster ajax call to server --
 *      
 *      loadReferralItems
 *      
 *      -- html generating functions --
 *      
 *      displayReferralItems
 *      
 *      createReferralsHeaderHTMLString
 *      createReferralsReferButtonsHTMLString
 *      createReferralsRemoveButtonHTMLString
 *      createReferralsDetailsHTMLString
 *      createReferralsHTMLString
 *      createCommentsHTMLString
 *      
 */

function bindAccordion() {
//    $("#accordion-object").addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
//        .find("div.accordion-header")
//        .addClass("ui-accordion-header ui-helper-reset ui-corner-all ui-state-default")
//        .prepend('<span class="ui-icon ui-icon-triangle-1-e"/>')
//        .click(function() {
//            $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
//                        .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
//                .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
//                .end().next().toggle().toggleClass("ui-accordion-content-active");
//            return false;
//        })
//        .next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();
    $( ".accordion-object" ).accordionCustom({
        header: 'div.accordion-header',
        content: 'div.accordion-content',
        footer: 'div.accordion-footer',
        collapsible: true,
        autoHeight: false,
        navigation: true,
        active: 'none'
    });
    $( ".subaccordion-object" ).accordionCustom({
        header: 'div.subaccordion-header',
        content: 'div.subaccordion-content',
        footer: 'div.subaccordion-footer',
        collapsible: true,
        autoHeight: false,
        navigation: true,
        active: 'none'
    })
}

// override accordion click handler when clicking Like, Comment, pressing enter or space
function overrideAccordionEvent() {
//    $(".no-accordion").click(function(e) {
//        e.stopPropagation();
//    });

    // override the space and enter key from performing accordion action
    $('.comment-input').keydown(function(e){
        if(e.which==32 || e.which==13){
            e.stopPropagation();
        }
    });
}


/*
 * figure out where to put the Load More button
 */
//function initLoadMoreButton() {
//    var inboxLoadStart = 3;
//    var itemType = $(this).closest('#inbox-content').attr('id');
//    
//    alert(itemType);
//    
//    itemType = itemType.substring(0, itemType.indexOf("content")) + "tab";
//    
//    // call more data from mysql ('load more' button action)
//    $('#load-more-inbox-content-button').click(function(){
//        // jquery post to retrieve more rows
//        jQuery.post("http://192.168.11.28/referrals/get_referral_items", {
//            rowStart: inboxLoadStart,
//            itemType: itemType
//        }, function(data) {
//            var parsedJSON = jQuery.parseJSON(data);
//            displayReferralItems(parsedJSON, itemType);
//            inboxLoadStart = inboxLoadStart+3;
//        });
//    });
//}

function initLoadMoreComments() {
    $('.hide-load-comments-button').hide();
    $('.show-load-comments-button').show();
    $('.hide-comment').hide();
    $('.show-comment').show();

    $(document).on("click", ".show-load-comments-button", function() {
        $(this).closest('.comments-table').find('.hide-comment').show();
        $(this).hide();
    });

//    $('.show-load-comments-button').click(function() {
//        $(this).closest('.comments-table').find('.hide-comment').show();
//        $(this).hide();
//    });
}

function initCommentInputDisplay() {
    // hide all comment boxes
    $('.comment-box').hide();

    // show comment div upon click
    $(document).on("click", ".click-to-comment", function() {
        $(this).closest('.row').find('.comment-box').toggle();
        // clear input field
        $(this).closest('.row').find('.comment-input').val('');
    });
}

function initLike() {
    $(document).on("click", ".click-to-like", function () {
        var ridString = $(this).closest('.row').attr("id");
        var referId = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);
        var likes = $(this).closest('.row').find('.number-of-likes');
        var likeStatus = $(this);

        jQuery.post("http://192.168.11.28/referrals/perform_like_action", {
            rid: referId
        }, function(likeCount){
            // parse the existing number of likes from the front end html div
            likeNumber = likes.text();
            likeNumber = likeNumber.substring(0, likeNumber.indexOf(' '));
            var a = parseInt(likeNumber) || 0;
            
            // toggle Like and Unlike
            if(jQuery.trim(likeStatus.text())=="Like")
            {
                // user has liked
                // like count++
                // change text to 'unlike'
                a = a + 1;
                likeStatus.text("Unlike");
            } else {
                // user has unliked
                // like count--
                // change text to 'like'
                a = a - 1;
                likeStatus.text("Like");
            }
            if ( a > 0 ) {
                if(a == 1) {
                    likes.text(a.toString() + " person likes this.");
                } else {
                    likes.text(a.toString() + " people like this.");
                }
            } else {
                likes.text("");
            }
        });
    });
}

function initComment() {
    $(document).on("click", ".submit-comment-button", function() {
        var name = jQuery.trim($(this).closest('.row').find('.comment-input').val());
        //var referId = $(this).closest('.row').data("rid");

        var ridString = $(this).closest('.row').attr("id");
        var referId = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);

        // toggle comment box
        $(this).closest('.row').find('.comment-box').toggle();
        var lastRow = $(this).closest('.row').find('.comments-table-tbody');

        // if the comment is not empty, then proceed with ajax
        if(name) {
            jQuery.post("http://192.168.11.28/referrals/add_new_comment", {
                comment: name,
                rid: referId
            }, function(data){
                // add comment to table using javascript
                var currentUserName = jQuery.trim($("#currentUserName").text());
                var currentFBID = jQuery.trim($("#current-fbid").text());
                lastRow.append(
                    '<tr class="single-comment">' +
                        '<td class="commenter-pic">' +
                            '<img src="https://graph.facebook.com/' + currentFBID + '/picture">' +
                        '</td>' +
                        '<td class="comments-name">' +
                            currentUserName + ': ' +
                        '</td>' +
                        '<td class="comments-content">' +
                            name +
                        '</td>' +
                        '<td>' +
                            '<button id="delete-comment-button-cid--' + data + '" class="delete-comment" data-cid=' + data + '>' +
                                'x' +
                            '</button>' +
                        '</td>' +
                '</tr>'
                );
            });
        }
        return false;
    });
}

function initRemoveComment() {
    $(document).on("click", ".delete-comment", function() {
        var cidString = $(this).attr('id');
        var cid = cidString.substring(cidString.indexOf('cid--') + 'cid--'.length);
        var singleComment = $(this).closest('.single-comment');
        
        jQuery.post("http://192.168.11.28/referrals/remove_comment", {
            cid: cid
        }, function(data) {
            singleComment.remove();
        });
    });
}

function initRemoveReferralButton() {
    $(document).on("click", ".referrals-remove-button", function() {
        var ridString = $(this).attr('id');
        var rid = ridString.substring(ridString.indexOf('id--') + 'id--'.length);
        var itemType = $(this).closest('.ui-widget-content').attr('id');
        var itemType = itemType.substring(0, itemType.indexOf('-content'));
        var refElem = $(this).closest('.single-wrapper');
        jQuery.post('referrals/flag_delete_referral_item', {
            rid: rid,
            itemType: itemType
        }, function(data) {
            // remove referral from html
            refElem.next().next().remove();
            refElem.next().remove();
            refElem.remove();
        });
    });
}

function reInitCommentEvents() {
    $('.comment-box').hide();
    $('.hide-load-comments-button').hide();
    $('.show-load-comments-button').show();
    $('.hide-comment').hide();
    $('.show-comment').show();
}

/*
 * loads the appropriate referral content (inbox, friend-activity, referral-tracking)
 * 
 * input:
 *      elem (the element that is calling this function)
 */
function loadReferralItems(elem) {
    // itemType:
    //      inbox-tab
    //      friend-activity-tab
    //      referral-tracking-tab
    var itemType = $(elem).closest('li').attr('id');
    
    jQuery.post('referrals/get_referral_items', {
        rowStart:0,
        itemType: itemType
    }, function(data){
        resetReferralContent(itemType);
        var parsedJSON = jQuery.parseJSON(data);
        displayReferralItems(parsedJSON, itemType);
    });
}

/*
 * reset the content given the input of what the itemType is
 * 
 * input:
 *      itemType: 'inbox-tab', 'friend-activity-tab', 'referral-tracking-tab'
 */
function resetReferralContent(itemType) {
    // itemType:
    //  inbox-tab
    //  friend-activity-tab
    //  referral-tracking-tab
    itemType = "#accordion-" + itemType.substring(0, itemType.indexOf('-tab'));
    
    // itemType:
    //  #accordion-inbox
    //  #accordion-friend-activity
    //  #accordion-referral-tracking
    $(itemType).empty();
}

/*
 * this function will call a function to create the referral item HTML and then add it to
 * the appropriate html element, then perform the necessary binds to get the html to function
 * 
 * input:
 *      parsedJSON (obj array for all referral items)
 *      itemType: inbox-tab, friend-activity-tab, referral-tracking-tab
 * 
 * output: none
 * 
 */
function displayReferralItems(parsedJSON, itemType) {
    // moreRows is a parsedJSON object
    // create a string that captures all HTML required to write the next referral
    var displayReferralsHTMLString = "";
    
    // destroy the accordion first
    $('.accordion-object').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');

    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        
        // first only allow non-corrupted data (consistent table servers)
        if(row.isCorrupted != 1) {
            displayReferralsHTMLString = createReferralsHTMLString(row, itemType);
            var accordionName = "#accordion-" + itemType.substring(0, itemType.length-4);
            $(displayReferralsHTMLString).appendTo(accordionName);
        }
    }
    bindAccordion();
    overrideAccordionEvent();
    reInitCommentEvents();
}

/*
 * this function creates the html for one referral item (accordion header, content, and footer)
 * 
 * input:
 *      row (obj array) that contains all details regarding the referral item
 *      itemType (string): 'inbox-tab', 'friend-activity-tab', 'referral-tracking-tab'
 * 
 * output:
 *      one referral item's html
 */
function createReferralsHTMLString(row, itemType) {
    displayReferralsHTMLString = "";
    if ( row.lid == 0 ) {
        displayReferralsHTMLString += createReferralsHeaderHTMLString(row, itemType, row.lid) +//createReferralsHeaderHTMLString(timeStamp, fbidPicture, userReferralString, VendorDetails.name, VendorDetails.id, refID, 0);
        // details of the row
           "<div class='drop-down-details accordion-content'>" +
               createReferralsDetailsHTMLString(row.VendorList['VendorList'][0][0]) +
           "</div>";
    } else {
        //var userListDetails = row.UserList[0];
        displayReferralsHTMLString += createReferralsHeaderHTMLString(row, itemType, 1) +//createReferralsHeaderHTMLString(timeStamp, fbidPicture, userReferralString, userListDetails.name, row.lid, refID, 1);
            "<div class='drop-down-details accordion-content'>";
        
        for(var j = 0; j<row.VendorList['VendorList'].length; j++) {
            SubVendorDetails = row.VendorList['VendorList'][j][0];
            displayReferralsHTMLString +=
                "<div class='subaccordion-object'>" +
                    "<div class='subaccordion-header'>" +
                        "<table class='formatted-table'>" +
                            "<tr>" +
                                "<td>" +
                                    SubVendorDetails.name +
                                "</td>" +
                                "<td>" +
                                    createReferralsReferButtonsHTMLString(SubVendorDetails.id) +
                                "</td>" +
                            "</tr>" +
                        "</table>" +
                    "</div>" +
                    "<div class='subaccordion-content'>" +
                        createReferralsDetailsHTMLString(SubVendorDetails) +
                    "</div>" +
                "</div>";
        }

        displayReferralsHTMLString +=
            "</div>";
    }
    displayReferralsHTMLString += createReferralsFooterHTMLString(row);
    return displayReferralsHTMLString;
}

/*
 * returns html for the header part of the referral item
 * 
// row:
//      object array with all referral information
//
// itemType (string):
//      inbox-tab
//      friend-activity-tab
//      referral-tracking-tab
//
// listOrSingle (integer):
//      0 = single
//      1 = list
 *      
 * outputs:
 *      String (html) for the accordion-header of the referral
 * 
 */
function createReferralsHeaderHTMLString(row, itemType, listOrSingle) {
    var timeStamp = row.refDate.toFuzzyElapsedTime();
    var referralID = row.rid;
    var fbidPicture = "";
    var genName, genID;
    
    // logic based on what tab, what default 'comment' to show as well as fb picture
    if (itemType != 'inbox-tab') {
        var RecipientDetails = row.RecipientDetails['RecipientDetails'][0];
        if (itemType == 'friend-activity-tab') {
            userReferralString = row.firstName + " " + row.lastName + " recommended to " + RecipientDetails.firstName + " " + RecipientDetails.lastName;
            referralID = -1;
        } else if (itemType == 'referral-tracking-tab') {
            userReferralString = "You recommended to " + RecipientDetails.firstName + " " + RecipientDetails.lastName;
        }
        if (row.ReferralsComment != "") {
            userReferralString = userReferralString + ": \"" + row.ReferralsComment + "\"";
        }
        fbidPicture = RecipientDetails.fbid;
    } else {
        if (row.ReferralsComment == "") {
            userReferralString = row.firstName + " " + row.lastName + " thinks you'll love this!";
        } else {
            userReferralString = row.firstName + " " + row.lastName + " says \"" + row.ReferralsComment + "\"";
        }
        fbidPicture = row.fbid;
    }
    
    // if list or single
    if (listOrSingle == 0) {
        // if single
        var VendorDetails = row.VendorList['VendorList'][0][0];
        genName = VendorDetails.name;
        genID = VendorDetails.id;
    } else {
        // if list
        var userListDetails = row.UserList[0];
        genName = userListDetails.name;
        genID = row.lid
    }
    
    var referralsHeaderHTMLString =
        "<div class='single-wrapper accordion-header list-name-wrapper'>" +
            "<a>" +
                "<table class='formatted-table'>" +
                    "<tr>" +
                        "<td>" +
                            "<div class='referral-date'>" +
                                timeStamp +
                            "</div>" +
                        "</td>" +
                        "<td>" +
                            createReferralsRemoveButtonHTMLString(referralID) +
                        "</td>" +
                    "</tr>" +
                    "<tr>" +
                        "<td class='list-name vendor-name'>" +
                            genName +
                        "</td>" +
                    "</tr>" +
                    "<tr>" +
                        "<td>" +
                            "<div class='friend-referral-comment-wrapper'>" +
                                "<div class='friend-pic'>" +
                                    "<img src='https://graph.facebook.com/" + fbidPicture + "/picture'>" +
                                "</div>" +
                                "<div class='friend-referral'>" +
                                    userReferralString +
                                "</div>" +
                            "</div>" +
                        "</td>" +
                        "<td>" +
                            createReferralsReferButtonsHTMLString(genID, listOrSingle) +
                        "</td>" +
                    "</tr>" +
                "</table>" +
            "</a>" +
        "</div>";
    return referralsHeaderHTMLString;
}

/*
 * this function returns the html string to create the accordion footer
 *      the footer includes the Likes and Comment button, as well as the comments
 *      
 * inputs:
 *      row (object array)
 * 
 * output
 *      String
 */
function createReferralsFooterHTMLString(row) {
    var likeNumber = row.LikesList['LikesList'].length;
    var likeStatus = "";
    
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

    if (row.alreadyLiked==1) {
        likeStatus = "Unlike";
    } else {
        likeStatus = "Like";
    }
    
    // footer comments
    referralsFooterHTMLString = 
        "<div class='accordion-footer'>" +
            "<div id='row-rid--" + row.rid + "' class='row' data-rid=" + row.rid + ">" +
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
                    "<table class='comments-table'>" +
                        "<tbody class='comments-table-tbody'>" +
                            // loop comments for the 'view all comments'
                            createCommentsHTMLString(row.CommentsList['CommentsList']) +
                        "</tbody>" +
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
        "</div>";
    return referralsFooterHTMLString;
}

/*
 * this function will add 'refer' and 'add to list' buttons
 *
 * inputs:
 *      genID (integer):
 *          id of either the list or the single referral. it is needed so the button can pass the id data to the server
 *      listOrSingle (1,0):
 *          necessary as the class names will be different for each
 *          1 = list,
 *          0 = single
 *          
 * outputs:
 *      String (html)
 */
function createReferralsReferButtonsHTMLString(genID, listOrSingle) {
    var referClassName = "refer-popup-link";
    var addClassName = "add-to-list-popup-link";
    if (listOrSingle == 1) {
        referClassName = "refer-list-popup-link";
        addClassName = "add-list-to-list-popup-link";
    }
    var referralsReferButtonsHTMLString =
            "<p>" +
                "<a href='#' id='referral-item-id--" + genID + "' class='" + referClassName + " dialog_link ui-state-default ui-corner-all no-accordion'>" +
                    "<span class='ui-icon ui-icon-plus'></span>" +
                        "Refer to Friends" +
                "</a>" +
            "</p>" +
            "<p>" +
                "<a href='#' id='referral-item-id--" + genID + "' class='" + addClassName + " dialog_link ui-state-default ui-corner-all no-accordion'>" +
                    "<span class='ui-icon ui-icon-plus'></span>" +
                        "Add to List" +
                "</a>" +
            "</p>";
    return referralsReferButtonsHTMLString;
}

/*
 * this function will add remove referral button
 *
 * inputs:
 *      referralID (integer):
 *          rid, so the button can pass the rid to the server for it to remove
 *          if rid = -1, then user is unable to remove it (ex. referral is in friend activity)
 *          
 * outputs:
 *      String (html)
 */
function createReferralsRemoveButtonHTMLString(referralID) {
    if (referralID > -1) {
        var referralsRemoveButtonHTMLString =
           "<button id='referrals-remove-button-id--" + referralID + "' class='referrals-remove-button no-accordion' data-rid=" +referralID + ">" +
              "x" +
           "</button>";
        return referralsRemoveButtonHTMLString;
    } else {
        return "";
    }
}

/*
 * this function will add vendor details
 *
 * inputs:
 *      details (object array):
 *          all vendor details from vendor table is in this array
 *          
 * outputs:
 *      String (html)
 */
function createReferralsDetailsHTMLString(details) {
    // vendor details are here
    var referralsDetailsHTMLString =
        details.addrNum + " " + details.addrStreet + "<br>" +
        details.addrCity + " " + details.addrState + " " + details.addrZip + "<br>" +
        details.phone + "<br>" +
        details.website;
    return referralsDetailsHTMLString;
}

/*
 * this function creates the html for comments
 *  including view all comments button and the list
 *  of all proceeding comments and remove individual
 *  comment button
 *  
 *  input:
 *      commentList (array)
 *      
 *  output:
 *      html string for comments
 */
function createCommentsHTMLString(commentList) {
    // re-write all html for entire comments table
    var commentsCountdown = commentList.length;
    var needShowAllButton = "hide-load-comments-button";
    var showStatus = "show-comment";
    var commentsHTMLString = "";
    
    for(var j=0; j<commentList.length; j++) {
        if(commentsCountdown < 3) {
            showStatus = "show-comment";
        } else {
            showStatus = "hide-comment";
            needShowAllButton = "show-load-comments-button";
        }
        commentsCountdown--;

        if(commentsCountdown == commentList.length-1) {
            commentsHTMLString = commentsHTMLString +
                "<tr>" +
                    "<td class='show-all-comments-button no-accordion " + needShowAllButton + "'>" +
                        "View all " + commentList.length + " comments." +
                    "</td>" +
                "</tr>";
        }

        // comments here
        commentsHTMLString = commentsHTMLString +
           "<tr class='single-comment " + showStatus + "'>" +
                "<td class='commenter-pic'>" +
                    "<img src='https://graph.facebook.com/" + commentList[j].fbid + "/picture'>" +
                "</td>" +
                "<td class='comments-name'>" +
                    commentList[j].firstName + " " + commentList[j].lastName + ": " +
                "</td>" +
                "<td class='comments-content'>" +
                    commentList[j].comment +
                "</td>";
            
        if (myUID == commentList[j].uid) {
            commentsHTMLString = commentsHTMLString +
                "<td>" +
                  "<button id='delete-comment-button-cid--" + commentList[j].cid + "' class='delete-comment' data-cid=" +
                     commentList[j].cid +
                       ">x</button>" +
                "</td>";
        }
        
        commentsHTMLString = commentsHTMLString +
           "</tr>";
    }
    return commentsHTMLString;
}