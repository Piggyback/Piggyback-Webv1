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
    $(".no-accordion").click(function(e) {
        //e.preventDefault();
        e.stopPropagation();
        //return false;
    });

    // override the space and enter key from performing accordion action
    $('.comment-input').keydown(function(e){
        if(e.which==32 || e.which==13 || e.which==9){
            e.stopPropagation();
        }
    });
}

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

        jQuery.post("referrals/perform_like_action", {
            rid: referId
        }, function(likeNum){
            // set new number of likes for display
            likes.text(likeNum.toString());
            
            var likeStatus = likeElem.src;
            likeStatus = likeStatus.substring(likeStatus.indexOf('like_counter_') + 'like_counter_'.length, likeStatus.indexOf('.png'));
            var likedImg = "";
            
            // liked
            if(likeStatus != 'f1'){
                likedImg = "../assets/images/piggyback_button_like_counter_f1.png";
            // not liked
            } else {
                likedImg = "../assets/images/piggyback_button_like_counter_red_f1.png";
            }
            $(likeElem).attr("src", likedImg);
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
            
            // in PHP: $date = date("Y-m-d H:i:s");
            // client side date below
//            var date = new Date();
//            var dateString = date.toFormattedString('yyyy-mm-P H:i:S');
            
            // if the comment is not empty, then proceed with ajax
            if(name) {
                jQuery.post("referrals/add_new_comment", {
                    comment: name,
                    rid: referId
//                    date: dateString
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
                                        currentUserName +
                                    '</b><BR>' +
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
        
        jQuery.post("referrals/remove_comment", {
            cid: cid
        }, function(data) {
            singleComment.remove();
        });
    });
}

function initRemoveReferralButton() {
    //$(document).on("click", ".referrals-remove-button", function() {
    $('.referrals-remove-button').click( function() {
        $('#confirmDeleteDialog').dialog('open');

        var ridString = $(this).attr('id');
        var rid = ridString.substring(ridString.indexOf('id--') + 'id--'.length);
        var itemType = $(this).closest('.ui-widget-content').attr('id');
        itemType = itemType.substring(0, itemType.indexOf('-content'));
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
                        removeReferral(refElem);
                        refreshEmptyMessage(itemType);
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

function initLoadMoreButton() {
    $(document).on("click", ".load-more-button", function() {
        var a = $(this).attr('id').toString();
        var rs = a.substring(a.indexOf('--') + '--'.length);
        var it = a.substring(0, a.indexOf('-load'));
        
        loadReferralItems(it, rs, 3);
    });
}

function initScrollLoadMore() {
    $("#inbox-content, #friend-activity-content, #referral-tracking-content").scroll( function() {
        if( ($(this).scrollTop() > $(this)[0].scrollHeight - $(this).height() - 100) && !$(this).find('.load-more-button').hasClass('none'))  {
            var a = $(this).find('.load-more-button').attr('id').toString();
            var rs = a.substring(a.indexOf('--') + '--'.length);
            var it = a.substring(0, a.indexOf('-load'));
            loadReferralItems(it, rs, 3);
        }
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
    $('.number-of-likes').unbind();
    
    initLike();
    initRemoveReferralButton();
    initWhoLikesButton();
    
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
    
    bindReferDialogButton();
    bindReferDialogButtonFromSearch(friendList,vendorData);
    bindAddToListButtonFromSearch(vendorData);
    
    reInitCommentEvents();
}

function removeReferral(elem) {
//    elem.next().next().remove();
//    elem.next().remove();
//    elem.remove();
    elem.parent().remove(); // gets rid of parent div
}

/*
 * loads the appropriate referral content (inbox, friend-activity, referral-tracking)
 * 
 * input:
 *      elem (the element that is calling this function)
 */
function loadReferralItemsFromTab (elem) {
    // itemType:
    //      inbox
    //      friend-activity
    //      referral-tracking
    
    // get itemType
    var itemType = $(elem).closest('li').attr('id').toString();
    if (itemType.indexOf("-selected") >= 0) {
        itemType = itemType.substring(0, itemType.indexOf("-selected"));
    }
    itemType = itemType.substring(0, itemType.indexOf("-tab"));
    
    // get the rowsRequested
    var contentElem = "#" + itemType + "-content";
    var rrString = $(contentElem).find(".load-more-button").attr("id").toString();
    var rowsRequested = rrString.substring(rrString.indexOf("--") + "--".length);
    
    if ( rowsRequested < 3 ) {
        rowsRequested = 3;
    }
    
    var rowStart = 0;
    
    loadReferralItems(itemType, rowStart, rowsRequested)
    
}

function loadReferralItems (itemType, rowStart, rowsRequested) {
    var loadMoreElem = $("#" + itemType + "-content").find(".load-more-button");
    var accordionElem = "#accordion-" + itemType;
    jQuery.post('referrals/get_referral_items', {
        rowStart: rowStart,
        rowsRequested: rowsRequested,
        itemType: itemType
    }, function(data){
        if ( rowStart == 0 )
            resetReferralContent(itemType);
        
        var parsedJSON = jQuery.parseJSON(data);
        // see if length equals to expected length
        if (parsedJSON.length > rowsRequested) {
            // show the 'show older' button
            // remove the last item from the parsedJSON array
            $(loadMoreElem).removeClass("none");
            parsedJSON.splice(parsedJSON.length-1, 1);
        } else {
            // hide the 'show older' button
            $(loadMoreElem).addClass("none");
        }
        
        displayReferralItems(parsedJSON, itemType);
        var newCount = $(accordionElem + " > div").size();
        $(loadMoreElem).attr('id', itemType + '-load-more-button--' + newCount);
        
        refreshEmptyMessage(itemType);
    });
}

function refreshEmptyMessage(itemType) {
    // prepare specific accordion elem
    var accordionElem = "#accordion-" + itemType;
    // prepare empty element
    var emptyElem = "#empty-" + itemType + "-message";
    
    if ( $(accordionElem + " > div").size() == 0 ) {
        $(emptyElem).removeClass("none");   // show empty message
    } else {
        $(emptyElem).addClass("none");      // remove empty message
    }
}

/*
 * reset the content given the input of what the itemType is
 * 
 * input:
 *      itemType: 'inbox', 'friend-activity', 'referral-tracking'
 */
function resetReferralContent(itemType) {
    // itemType:
    //  inbox
    //  friend-activity
    //  referral-tracking
    //  list
    //  search
    if (itemType == 'list') {
        itemType = '#list-content';
    } else if (itemType == 'search') {
        itemType = '#search-content';
    } else {
        itemType = "#accordion-" + itemType;
    }
    
    // itemType:
    //  #accordion-inbox
    //  #accordion-friend-activity
    //  #accordion-referral-tracking
    //  #accordion-list
    //  #list-content
    //  #search-content
    $(itemType).empty();
    
    // TODO: use the ID of the direct parent DIV and then instead of EMPTY just add
    //       html to include the number of items loaded
}

/*
 * this function will call a function to create the referral item HTML and then add it to
 * the appropriate html element, then perform the necessary binds to get the html to function
 * 
 * input:
 *      parsedJSON (obj array for all referral items)
 *      itemType: inbox, friend-activity, referral-tracking
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
//    if (itemType == 'inbox') {
//        $('#accordion-inbox').accordionCustom('destroy');
//    } else if (itemType == 'friend-activity') {
//        $('#accordion-friend-activity').accordionCustom('destroy');
//    } else if (itemType == 'referral-tracking') {
//        $('#accordion-referral-tracking').accordionCustom('destroy');
//    }
    $('.accordion-object').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');

    var accordionName = "#accordion-" + itemType;
    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        // first only allow non-corrupted data (consistent table servers)
        if(row.isCorrupted == "") {
            displayReferralsHTMLString = createReferralsHTMLString(row, itemType);
            //TODO: Talk to Andy -- move variable declaration outside of for loop to avoid redundant instructions
            $(displayReferralsHTMLString).appendTo(accordionName);
        } else {
            // error log
            console.log(row.isCorrupted);
        }
    }
    reBindAccordion();
}

/*
 * this function creates the html for one referral item (accordion header, content, and footer)
 * 
 * input:
 *      row (obj array) that contains all details regarding the referral item
 *      itemType (string): 'inbox', 'friend-activity', 'referral-tracking'
 * 
 * output:
 *      one referral item's html
 */
function createReferralsHTMLString(row, itemType) {
    //TODO: Discuss global javascript variables with Andy -- variables without 'var' keyword are global (green colorcode)'
    var displayReferralsHTMLString = "<div class='referral-item-wrapper accordion-object'>";
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
            var singleComment = "<span class='referral-comment'><q class='comment-wrapper empty-quote'></q></span>";
            if (row.VendorList['VendorList'][j]['senderComment'] != "") {
                singleComment = "<i><span class='referral-comment'><q class='comment-wrapper'>" + row.VendorList['VendorList'][j]['senderComment'] +
                    "</q></span></i>";
            }
            displayReferralsHTMLString +=
                "<div class='subaccordion-object name-wrapper'>" +
                    "<div class='subaccordion-header'>" +
                        "<span class='vendor-name referral-list-vendor-name'>" +
                            SubVendorDetails.name +
                        "</span> <br> " +
                        singleComment + 
                        "<div class='subaccordion-button-row no-accordion'>" +
                        createReferralsReferButtonsHTMLString(SubVendorDetails.id) +
                        "</div>" + 
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
//      inbox
//      friend-activity
//      referral-tracking
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
    var timeStamp = row.date.toFuzzyElapsedTime();
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
    senderComment = "<i><span class='referral-comment'><q class='comment-wrapper'>" + row.comment + "</q></span></i>";
    if (row.comment == "") {
        senderComment = "<i><span class='referral-comment'><q class='comment-wrapper empty-quote'></q></span></i>";
    }
    
    // logic based on what tab, what default 'comment' to show as well as fb picture
    if (itemType != 'inbox') {
        var RecipientDetails = row.RecipientDetails['RecipientDetails'][0];
        recipientName = "<b>" + RecipientDetails.firstName + " " + RecipientDetails.lastName + "</b>";
        if (itemType == 'friend-activity') {
            userReferralString = senderName + " recommended " + genName + " to " + recipientName;
            referralID = -1;
            fbidPicture = row.fbid;
        } else if (itemType == 'referral-tracking') {
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

function createListRemoveButtonHTMLString(vid) {
    var listRemoveButtonHTMLString = "<img src='../assets/images/piggyback_button_close_big_f1.png' alt='refer' id='accordion-remove-vid--" + vid + "' class='accordion-remove' onmouseover=\"this.src='../assets/images/piggyback_button_close_big_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_big_f1.png'\"></img>";
    return listRemoveButtonHTMLString;
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
    resetReferralContent('list');
    // moreRows is a parsedJSON object
    // create a string that captures all HTML required to write the next referral
    var displayListHTMLString = "";
    var accordionName = "#accordion-" + itemType; //.substring(0, itemType.length-4);
//    var accordionName = '#list-content';

    // destroy the accordion first
    //$('#accordion-list').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');
    
    var tempHTML = "<div id='accordion-list'><div class='none' id='accordion-list-lid'>" + lid + "</div>";
    $(tempHTML).appendTo('#list-content');

    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        
        // first only allow non-corrupted data (consistent table servers)
        
        //alert(row.isCorrupted);
        // TODO: incorporate isCorrupted flag?
        //if(row.isCorrupted == "") {
            displayListHTMLString = createListHTMLString(row, itemType);
            // itemType has to be 'list' for now
            $(displayListHTMLString).appendTo(accordionName);
        //}
    }
    $("</div>").appendTo('#list-content');
//    bindAccordion();
//    overrideAccordionEvent();

    reBindAccordion();
}

function createListHTMLString(row, itemType) {
    var displayListHTMLString = "<div class='list-item-wrapper accordion-object'>";
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
    var commentPrompt = "Add Comment";
    var commentHTML = "<span class='comment-wrapper'>" + row.comment + "</span>";
    if (row.comment != "") {
        commentPrompt = "Edit Comment";
        commentHTML = "<q class='comment-wrapper'>" + row.comment + "</q>";
    }
    
    var listHeaderHTMLString =
        "<div class='single-wrapper accordion-header name-wrapper'>" +
            "<a>" +
                "<div class='vendor-name'>" +
                    row.name +
                "</div>" + 
                "<div class='button-row no-accordion'>" +
                    createListRemoveButtonHTMLString(row.vid) +
                    createReferralsReferButtonsHTMLString(row.vid, 0) +
                "</div>" +
                "<div class='comment-and-edit-block'>" +
                    "<span class='vendor-list-comment'>" +
                        commentHTML +
                    "</span>" +
                    "<span id='accordion-edit-comment-vid--" + row.vid + "' class='accordion-edit-comment'>" +
                        commentPrompt +
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
function displaySearchItems(parsedJSON) {    
    resetReferralContent('search');

    var displaySearchHTMLString = "";
    
    $('#accordion-search').accordionCustom('destroy');
//    $('.subaccordion-object').accordionCustom('destroy');
    
    var tempHTML = "<div id='accordion-search'>";
    $(tempHTML).appendTo('#search-content');
    
    for(var i=0; i<parsedJSON.length; i++) {
        var row = parsedJSON[i];
        
        // first only allow non-corrupted data (consistent table servers)
//        if(row.isCorrupted == "") {
            displaySearchHTMLString = createSearchHTMLString(row);
            // itemType has to be 'list' for now
            $(displaySearchHTMLString).appendTo('#accordion-search');
//        }
    }
    $("</div>").appendTo('#search-content');

    reBindAccordionFromSearch(parsedJSON);
    
}

function createSearchHTMLString(row) {
    var displaySearchHTMLString = "<div class='list-item-wrapper accordion-object'>";
//    var displayListHTMLString = "";
    
    displaySearchHTMLString += createSearchHeaderHTMLString(row) +
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

function createSearchHeaderHTMLString(row) {

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