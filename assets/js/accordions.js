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

    //TODO: Optimize binding calls with Andy -- currently rebinding ALL accordions
    
    
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
    });
}

// override accordion click handler when clicking Like, Comment, pressing enter or space
function overrideAccordionEvent() {
//    $(document).on("click", ".no-accordion", function(e) {
//        console.log(e);
//        e.preventDefault();
//        e.stopPropagation();
//    });
    $(".no-accordion").click(function(e) {
        //e.preventDefault();
        e.stopPropagation();
        //return false;
    });
    
//    $('.accordion-header').click(function(e) {
//        if($(e.target).closest('.no-accordion').length) {
//            return false;
//        } 
//    });

    // override the space and enter key from performing accordion action
    $('.comment-input').keydown(function(e){
        if(e.which==32 || e.which==13 || e.which==9){
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

    $(document).on("click", ".show-load-comments-button", function(e) {
        $(this).closest('.comments-body').find('.hide-comment').show();
        $(this).hide();
        console.log(e);
    });

//    $('.show-load-comments-button').click(function() {
//        $(this).closest('.comments-table').find('.hide-comment').show();
//        $(this).hide();
//    });
}

function initCommentInputDisplay() {
    // hide all comment boxes
//    $('.comment-box').hide();

    // show comment div upon click
    $(document).on("click", ".click-to-comment", function() {
        $(this).closest('.row').find('.comment-box').toggle();
        // clear input field
        $(this).closest('.row').find('.comment-input').val('');
    });
}

function initLike() {
    //$(document).on("click", ".click-to-like", function () {
    $('.click-to-like').click(function () {
        var ridString = $(this).closest('.single-wrapper').next().next().find('.row').attr("id");
        var referId = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);
        
        var likes = $(this).closest('.button-row').find('.number-of-likes-inner');
        var likeElem = this;
//        var ridString = $(this).closest('.row').attr("id");
//        var referId = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);
//        var likes = $(this).closest('.row').find('.number-of-likes');
//        var likeStatus = $(this);

        jQuery.post("http://192.168.11.28/referrals/perform_like_action", {
            rid: referId
        }, function(){
            // parse the existing number of likes from the front end html div
            var likeNumber = likes.text().trim();
            likeNumber = parseInt(likeNumber);
//            likeNumber = likeNumber.substring(0, likeNumber.indexOf(' ')).trim();
//            var a = parseInt(likeNumber) || 0;
            
            var likeStatus = likeElem.src;
//            var likeStatus = $(this).hasClass('is-liked');
//            alert($(this).closest('.button-row').find("img[alt='like']").hasClass("is-liked"));
            likeStatus = likeStatus.substring(likeStatus.indexOf('like_counter_') + 'like_counter_'.length, likeStatus.indexOf('.png'));
            var likedImg = "";
            
            // liked
            if(likeStatus != 'f1'){
                likedImg = "../assets/images/piggyback_button_like_counter_f1.png";
                // user has liked
                // like count++
                // change text to 'unlike'
                likeNumber = likeNumber - 1;
//                $(this).removeClass('is-liked');
            // not liked
            } else {
                likedImg = "../assets/images/piggyback_button_like_counter_red_f1.png";
                // user has unliked
                // like count--
                // change text to 'like'
                likeNumber = likeNumber + 1;
//                $(this).addClass('is-liked');
            }
            $(likeElem).attr("src", likedImg);
            likes.text(likeNumber.toString());
//            if ( a > 0 ) {
////                if(a == 1) {
////                    likes.text(a.toString() + " person likes this.");
////                } else {
////                    likes.text(a.toString() + " people like this.");
////                }
//            } else {
////                likes.text("");
//            }
        });
    });
}


function initComment() {
    $(document).on("focus", ".comment-input", function() {
        if (this.value == this.title) {
            $(this).val("");
            $(this).addClass('not-placeholder');
            $(this).removeClass('placeholder');
        }
    });
    $(document).on("blur", ".comment-input", function() {
        if (this.value == "") {
            $(this).val(this.title);
            $(this).addClass('placeholder');
            $(this).removeClass('not-placeholder');
        }
    });
    
    $(document).on("keypress", ".comment-input", function(e) {
        if (e.which == 13) {
            var name = jQuery.trim($(this).val());
            var ridString = $(this).closest('.row').attr("id");
            var referId = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);

            // reset comment box
            $(this).val("Write a comment...");
            $(this).addClass('placeholder');
            $(this).removeClass('not-placeholder');
            $(this).blur();
            
            var lastRow = $(this).closest('.comments').find('.comments-body');
            

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
                        '<div class="single-comment show-comment">' +
                            '<div class="commenter-pic">' +
                                '<img src="https://graph.facebook.com/' + currentFBID + '/picture" class="ui-corner-all">' +
                            '</div>' +
                            '<div class="comment-wrapper-text">' +
                                '<div class="comments-content">' +
                                    '<b>' +
                                        currentUserName + ': ' +
                                    '</b>' +
                                    name +
                                '</div>' +
                                '<div class="comment-date time-stamp">' +
                                    "Just now" +
                                '</div>' +
                            '</div>' +
                            '<button id="remove-comment-button-cid--' + data + '" class="remove-comment-button" data-cid=' + data + '>' +
                            '</button>' +   
                        '</div>'
                    );
                });
            }
            return false; // prevents page from refreshing
        }
    });
}

function initRemoveComment() {
    $(document).on("click", ".remove-comment-button", function() {
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
    //$(document).on("click", ".referrals-remove-button", function() {
    $('.referrals-remove-button').click( function() {
        $("#fuzz").fadeIn();
        $('#confirmDeleteDialog').dialog('open');

        var ridString = $(this).attr('id');
        var rid = ridString.substring(ridString.indexOf('id--') + 'id--'.length);
        var itemType = $(this).closest('.ui-widget-content').attr('id');
        var itemType = itemType.substring(0, itemType.indexOf('-content'));
        var refElem = $(this).closest('.single-wrapper');
        
        $('#confirmDeleteDialog').dialog('option','buttons', {
            "Delete": {
                text: '',
                id: 'delete-button',
                click: function() {
                    $( this ).dialog( "close" );
                    jQuery.post('referrals/flag_delete_referral_item', {
                        rid: rid,
                        itemType: itemType
                    }, function(data) {
                        // remove referral from html
                        //TODO: Talk to Andy -- make this its own function so i can use it for my accordion
                        removeReferral(refElem);
                        // see below 'removeReferral'
                    });
                }
            },
            "Cancel": {
                text: '',
                id: 'cancel-delete-button',
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
}

function reInitCommentEvents() {
    //$('.comment-box').hide();
    $('.hide-load-comments-button').hide();
    $('.show-load-comments-button').show();
    $('.hide-comment').hide();
    $('.show-comment').show();
}

function reBindAccordion() {
    $('.click-to-like').unbind();
    $('.referrals-remove-button').unbind();
    $('.refer-popup-link').unbind();
    $('.add-to-list-popup-link').unbind();
    $('.refer-list-popup-link').unbind();
    $('.add-list-to-list-popup-link').unbind();
    
    initLike();
    initRemoveReferralButton();
    
    bindAccordion();
    overrideAccordionEvent();
    bindReferDialogButton();
    bindAddToListButton();
    
    reInitCommentEvents();
}

function reBindAccordionFromSearch(vendorData) {
    $('.click-to-like').unbind();
    $('.referrals-remove-button').unbind();
    $('.refer-popup-link').unbind();
    $('.add-to-list-popup-link').unbind();
    $('.refer-list-popup-link').unbind();
    $('.add-list-to-list-popup-link').unbind();
    
    initLike();
    initRemoveReferralButton();
    
    bindAccordion();
    overrideAccordionEvent();
    
    bindReferDialogButtonFromSearch(friendList,vendorData);
    bindAddToListButtonFromSearch(vendorData);
    
    reInitCommentEvents();
}

function removeReferral(elem) {
    elem.next().next().remove();
    elem.next().remove();
    elem.remove();
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
    
    // parse out the '-selected' part of the ID
    var itemType = $(elem).closest('li').attr('id').toString();
    if (itemType.indexOf("-selected") >= 0) {
        itemType = itemType.substring(0, itemType.indexOf("-selected"));
    }
    
//    var id = $(elem).attr("href");
//    alert(id);
    
    jQuery.post('referrals/get_referral_items', {
        rowStart:0,
        itemType: itemType
    }, function(data){
        resetReferralContent(itemType);
        var parsedJSON = jQuery.parseJSON(data);
//        for (var i = 0; i < parsedJSON.length; i++) {
//            alert(JSON.stringify(parsedJSON[i]));
//        }
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
    //  list-tab
    if (itemType == 'list-tab') {
        itemType = '#list-content';
    } else if (itemType == 'search-tab') {
        itemType = '#search-content';
    } else {
        itemType = "#accordion-" + itemType.substring(0, itemType.indexOf('-tab'));
    }
    
    // itemType:
    //  #accordion-inbox
    //  #accordion-friend-activity
    //  #accordion-referral-tracking
    //  #accordion-list
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
    //TODO: Optimize destroy to specific accordion. Code review with Andy. maybe even remove other accordions when tab is not selected (minimize html?)
//    if (itemType == 'inbox-tab') {
//        $('#accordion-inbox').accordionCustom('destroy');
//    } else if (itemType == 'friend-activity-tab') {
//        $('#accordion-friend-activity').accordionCustom('destroy');
//    } else if (itemType == 'referral-tracking-tab') {
//        $('#accordion-referral-tracking').accordionCustom('destroy');
//    }
    $('.accordion-object').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');

    var accordionName = "#accordion-" + itemType.substring(0, itemType.length-4);
    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        // first only allow non-corrupted data (consistent table servers)
        if(row.isCorrupted != 1) {
            displayReferralsHTMLString = createReferralsHTMLString(row, itemType);
            //TODO: Talk to Andy -- move variable declaration outside of for loop to avoid redundant instructions
            $(displayReferralsHTMLString).appendTo(accordionName);
        }
    }
    reBindAccordion();
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
    //TODO: Discuss global javascript variables with Andy -- variables without 'var' keyword are global (green colorcode)'
    var displayReferralsHTMLString = "<div class='referral-item-wrapper'>";
    if ( row.lid == 0 ) {
        displayReferralsHTMLString += createReferralsHeaderHTMLString(row, itemType, row.lid) +
        // details of the row
           "<div class='drop-down-details accordion-content'>" +
               createReferralsDetailsHTMLString(row.VendorList['VendorList'][0][0]) +
           "</div>";
    } else {
        //var userListDetails = row.UserList[0];
        displayReferralsHTMLString += createReferralsHeaderHTMLString(row, itemType, 1) +
            "<div class='drop-down-details accordion-content'>";
        
        for(var j = 0; j<row.VendorList['VendorList'].length; j++) {
            var SubVendorDetails = row.VendorList['VendorList'][j][0];
            var singleComment = "<span class='referral-comment'></span>";
            if (row.VendorList['VendorList'][j]['senderComment'] != "") {
                singleComment = "<i><span class='referral-comment'>\"" + row.VendorList['VendorList'][j]['senderComment'] +
                    "\"</span></i>";
            }
            displayReferralsHTMLString +=
                "<div class='subaccordion-object name-wrapper'>" +
                    "<div class='subaccordion-header'>" +
                        "<span class='vendor-name referral-list-vendor-name'>" +
                            SubVendorDetails.name +
                        "</span> <br> " +
                        singleComment + 
                        createReferralsReferButtonsHTMLString(SubVendorDetails.id) +
                    "</div>" +
                    "<div class='subaccordion-content'>" +
                        createReferralsDetailsHTMLString(SubVendorDetails) +
                    "</div>" +
                "</div>";
        }

        displayReferralsHTMLString +=
            "</div>";
    }
    displayReferralsHTMLString += createReferralsFooterHTMLString(row) +
        "</div>";
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
    var genName, genID, recipientName, senderName, senderComment;
    var userReferralString;
    
    // from search
    
    // if list or single
    if (listOrSingle == 0) {
        // if single
        var VendorDetails = row.VendorList['VendorList'][0][0];
        genName = "<span class='vendor-name'>" + VendorDetails.name + "</span>";;
        genID = VendorDetails.id;
    } else {
        // if list
        var userListDetails = row.UserList[0];
        genName = "the \"<span class='list-name'>" + userListDetails.name + "</span>\" list";
        genID = row.lid;
    }
    
    senderName = "<b>" + row.firstName + " " + row.lastName + "</b>";
    senderComment = "";
    if (row.ReferralsComment != "") {
        senderComment = "<i><span class='referral-comment'>\"" + row.ReferralsComment + "\"</span></i>";
    }
    
    // logic based on what tab, what default 'comment' to show as well as fb picture
    if (itemType != 'inbox-tab') {
        var RecipientDetails = row.RecipientDetails['RecipientDetails'][0];
        recipientName = "<b>" + RecipientDetails.firstName + " " + RecipientDetails.lastName + "</b>";
        if (itemType == 'friend-activity-tab') {
            userReferralString = senderName + " recommended " + genName + " to " + recipientName;
            referralID = -1;
            fbidPicture = row.fbid;
        } else if (itemType == 'referral-tracking-tab') {
            userReferralString = "You recommended " + genName + " to " + recipientName;
            fbidPicture = RecipientDetails.fbid;
        }
    } else {
        userReferralString = senderName + " recommended you " + genName;
        fbidPicture = row.fbid;
    }
    
    var referralsHeaderHTMLString =
        "<div class='single-wrapper accordion-header name-wrapper'>" +
            "<a>" +
                "<div class='referral-date time-stamp'>" +
                    timeStamp +
                "</div>" +
                "<div class='friend-pic'>" +
                    "<img src='https://graph.facebook.com/" + fbidPicture + "/picture' class='ui-corner-all'>" +
                "</div>" +
                "<div class='friend-referral'>" +
                    userReferralString +
                    "<br>" +
                    senderComment +
                "</div>" +
                "<div class='button-row no-accordion'>" +
                    createReferralsRemoveButtonHTMLString(referralID) +
                    createReferralsReferButtonsHTMLString(genID, listOrSingle) +
                    createLikeButtonHTMLString(row) +
                "</div>" +
            "</a>" +
        "</div>";
    return referralsHeaderHTMLString;
}

function createLikeButtonHTMLString(row) {
    var imgsrc = "";
    var likeNumber = row.LikesList['LikesList'].length;

    if (row.alreadyLiked==1) {
        imgsrc = "../assets/images/piggyback_button_like_counter_red_f1.png";
    } else {
        imgsrc = "../assets/images/piggyback_button_like_counter_f1.png";
    }
    var likeButtonHTMLString =
        "<div class='number-of-likes'>" +
            "<div class='number-of-likes-inner'>" + 
                likeNumber +
            "</div>" +
        "</div>" + 
        "<img src='" + imgsrc + "' alt='like' class='click-to-like'></img>";
        
    return likeButtonHTMLString;
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
//    var likeNumber = row.LikesList['LikesList'].length;
//    var likeStatus = "";
    
//    if(likeNumber>0) {
//        if(likeNumber == 1) {
//            likeNumber = likeNumber + " person likes this.";
//        } else {
//            likeNumber = likeNumber + " people like this.";
//        }
//    }
//    else {
//        likeNumber = "";
//    }
    
    // footer comments
    var referralsFooterHTMLString = 
        "<div class='accordion-footer'>" +
            "<div id='row-rid--" + row.rid + "' class='row' data-rid=" + row.rid + ">" +
                "<div class='comments'>" +
                    "<div class='comments-body'>" +
                        // loop comments for the 'view all comments'
                        createCommentsHTMLString(row.CommentsList['CommentsList']) +
                    "</div>" +
                    "<div class='comment-box'>" +
                        "<form name='form-comment' class='form-comment' method='post'>" +
                            "<div class='commenter-pic'>" +
                                $('#current-pic').html() +
                            "</div>" +
                                "<input type='text' value='Write a comment...' title='Write a comment...' class='comment-input placeholder'/>" +
                        "</form>" +
                    "</div>" +
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
    var idName = "single-referral-id--";
    if (listOrSingle == 1) {
        // if list,
        referClassName = "refer-list-popup-link";
        addClassName = "add-list-to-list-popup-link";
        idName = "list-referral-id--";
    }
    //TODO: Talk to Andy -- elements can't have the same IDs // fixed
    var referralsReferButtonsHTMLString =
        "<img alt='refer' src='../assets/images/piggyback_button_refer_f1.png' id='refer-to-friends-" + idName + genID + "' class='" + referClassName + " dialog_link referral-list-vendor-refer' onmouseover=\"this.src='../assets/images/piggyback_button_refer_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_refer_f1.png'\">" +
        "</img>" +
        "<img alt='+' src='../assets/images/piggyback_button_add_f1.png' id='add-to-list-" + idName + genID + "' class='" + addClassName + " dialog_link referral-list-vendor-add-to-list' onmouseover=\"this.src='../assets/images/piggyback_button_add_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_add_f1.png'\">" +
        "</img>";
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
//           "<button id='referrals-remove-button-id--" + referralID + "' class='referrals-remove-button' data-rid=" +referralID + ">" +
//           "</button>";
           "<img src='../assets/images/piggyback_button_close_big_f1.png' alt='refer' id='referrals-remove-button-id--" + referralID + "' class='referrals-remove-button' data-rid=" + referralID + " onmouseover=\"this.src='../assets/images/piggyback_button_close_big_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_big_f1.png'\"></img>";

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
//    alert(details.website);
    var referralsDetailsHTMLString =
        details.addrNum + " " + details.addrStreet + "<br>" +
        details.addrCity + " " + details.addrState + " " + details.addrZip + "<br>" +
        details.phone;
    if (details.website != '') {
        referralsDetailsHTMLString += "<BR><a href='" + details.website + "' class ='website-link' target='_blank'>" + details.website + "</a>";
    }
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
    var commentTimeStamp;
    
    for(var j=0; j<commentList.length; j++) {
        commentTimeStamp = commentList[j].date.toFuzzyElapsedTime();
        
        if(commentsCountdown < 3) {
            showStatus = "show-comment";
        } else {
            showStatus = "hide-comment";
            needShowAllButton = "show-load-comments-button";
        }
        commentsCountdown--;

        if(commentsCountdown == commentList.length-1) {
            commentsHTMLString +=
            "<div class='show-all-comments-button " + needShowAllButton + "'>" +
                "View all " + commentList.length + " comments." +
            "</div>";
        }
        
        // comments here
        commentsHTMLString +=
           "<div class='single-comment " + showStatus + "'>" +
                "<div class='commenter-pic'>" +
                    "<img src='https://graph.facebook.com/" + commentList[j].fbid + "/picture' class='ui-corner-all'>" +
                "</div>" +
                "<div class='comment-wrapper-text'>" +
                    "<div class='comments-content'>" +
                        "<b>" +
                            commentList[j].firstName + " " + commentList[j].lastName +
                        "</b><br>" +
                        commentList[j].comment +
                    "</div>" +
                    "<div class='comment-date time-stamp'>" +
                        commentTimeStamp +
                    "</div>" +
                "</div>";
                    
        if (myUID == commentList[j].uid) {
            commentsHTMLString +=
                "<button id='remove-comment-button-cid--" + commentList[j].cid + 
                "' class='remove-comment-button' data-cid=" + commentList[j].cid +
                       "></button>";
        }
        
        commentsHTMLString = commentsHTMLString +
           "</div>";
    }
    return commentsHTMLString;
}

/**
 * @mikegao functions for list accordion
 */
function displayListItems(parsedJSON, itemType, lid) {    
    resetReferralContent('list-tab');
    // moreRows is a parsedJSON object
    // create a string that captures all HTML required to write the next referral
    var displayListHTMLString = "";
    var accordionName = "#accordion-" + itemType.substring(0, itemType.length-4);
//    var accordionName = '#list-content';
    
    // destroy the accordion first
    $('#accordion-list').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');
    
    var tempHTML = "<div id='accordion-list' class='accordion-object'><div class='none' id='accordion-list-lid'>" + lid + "</div>";
    $(tempHTML).appendTo('#list-content');

    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        
        // first only allow non-corrupted data (consistent table servers)
        if(row.isCorrupted != 1) {
            displayListHTMLString = createListHTMLString(row, itemType);
            // itemType has to be 'list-tab' for now
            $(displayListHTMLString).appendTo(accordionName);
        }
    }
    $("</div>").appendTo('#list-content');
//    bindAccordion();
//    overrideAccordionEvent();

    reBindAccordion();
}

function createListHTMLString(row, itemType) {
    var displayListHTMLString = "<div class='list-item-wrapper'>";
//    var displayListHTMLString = "";
    
    displayListHTMLString += createListHeaderHTMLString(row, itemType, 0) +
    // details of the row
       "<div class='drop-down-details accordion-content'>" +
           createListDetailsHTMLString(row) +
       "</div>";


    displayListHTMLString +=
        "</div></div>";
//        "</div>";

//    displayReferralsHTMLString += createReferralsFooterHTMLString(row);
    return displayListHTMLString;
}

function createListHeaderHTMLString(row, itemType, listOrSingle) {
    var listHeaderHTMLString =
        "<div class='single-wrapper accordion-header name-wrapper'>" +
            "<a>" +
                "<div class='vendor-name'>" +
                    row.name +
                "</div>" + 
                "<div class='button-row no-accordion'>" +
                    "<span id='accordion-remove-vid--" + row.vid + "' class='accordion-remove'>" +
                        "remove" +
                    "</span>" +
                    createReferralsReferButtonsHTMLString(row.vid, 0) +
                "</div>" +
                "<div class='comment-and-edit-block'>" +
                    "<span class='vendor-list-comment'>" +
                        "<q>" + row.comment + "</q>" + 
                    "</span>" +
                    "<span id='accordion-edit-comment-vid--" + row.vid + "' class='accordion-edit-comment'>" +
                        "Edit Comment" +
                    "</span>" +
                "</div>" +
            "</a>" +
        "</div>";
    
    return listHeaderHTMLString;
}

// TODO: same code as andy's function createReferralsDetailsHTMLString
function createListDetailsHTMLString(details) {
    // vendor details are here
    var listDetailsHTMLString =
        details.addrNum + " " + details.addrStreet + "<br>" +
        details.addrCity + " " + details.addrState + " " + details.addrZip + "<br>" +
        details.phone;
    if (details.website != '') {
        listDetailsHTMLString += "<BR><a href='" + details.website + "' class ='website-link' target='_blank'>" + details.website + "</a>";
    }
    return listDetailsHTMLString;
}

// FOR KIM'S SEARCH
function displaySearchItems(parsedJSON, itemType, lid) {    
    resetReferralContent('search-tab');

    var displaySearchHTMLString = "";
    
    $('#accordion-search').accordionCustom('destroy');
//    $('.subaccordion-object').accordionCustom('destroy');
    
    var tempHTML = "<div id='accordion-search' class='accordion-object'>";
    $(tempHTML).appendTo('#search-content');
    
    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        
        // first only allow non-corrupted data (consistent table servers)
//        if(row.isCorrupted != 1) {
            displaySearchHTMLString = createSearchHTMLString(row, itemType);
            // itemType has to be 'list-tab' for now
            $(displaySearchHTMLString).appendTo('#accordion-search');
//        }
    }
    $("</div>").appendTo('#search-content');

    reBindAccordionFromSearch(parsedJSON);
    
}

function createSearchHTMLString(row, itemType) {
    var displaySearchHTMLString = "<div class='list-item-wrapper'>";
//    var displayListHTMLString = "";
    
    displaySearchHTMLString += createSearchHeaderHTMLString(row, itemType, 0) +
    // details of the row
       "<div class='drop-down-details accordion-content'>" +
           createListDetailsHTMLString(row) +
       "</div>";


    displaySearchHTMLString +=
        "</div></div>";
//        "</div>";

//    displayReferralsHTMLString += createReferralsFooterHTMLString(row);
    return displaySearchHTMLString;
}

function createSearchHeaderHTMLString(row, itemType, listOrSingle) {

    var listHeaderHTMLString =
        "<div class='single-wrapper accordion-header name-wrapper'>" +
            "<a>" +
                "<div class='vendor-name'>" +
                    row.name +
                "</div>" + 
                "<div class='button-row no-accordion'>" +
                    createReferralsReferButtonsHTMLString(row.id, 0) +
                "</div>" +
            "</a>" +
        "</div>";
    
    return listHeaderHTMLString;
}