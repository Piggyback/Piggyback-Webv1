/**
    Document   : home_andy_js.php
    Created on : Dec 5, 2011, 5:07 PM
    Author     : andyjiang
    Description:
        all javascript code for home page (related to andy's inbox) is here
*/
/**
   TO-DOs:
*/

$(document).ready(function() {
    bindAccordion();
    overrideAccordionEvent();
    initCommentInputDisplay();
    initLike();
    initComment();
    initRemoveComment();
    initLoadMoreComments();
    initLoadMoreButton();
    initDatePrototype();
    initRemoveReferralButton();

//    initAddAndReferButtons();

    // initialize round elements
    initRoundElements();
    
});

function initAddAndReferButtons() {
//    displayListDropDown();
//    bindAddToListButton();
//    bindAddToListDialog();
//    bindReferDialog();
//    bindReferDialogButton2(friendList);
}

/* functions for $(document).ready */
//initialize the accordion features for inbox
//function bindAccordionInbox() {
////    $("#accordion-object").addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
////        .find("div.accordion-header")
////        .addClass("ui-accordion-header ui-helper-reset ui-corner-all ui-state-default")
////        .prepend('<span class="ui-icon ui-icon-triangle-1-e"/>')
////        .click(function() {
////            $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
////                        .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
////                .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
////                .end().next().toggle().toggleClass("ui-accordion-content-active");
////            return false;
////        })
////        .next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();
//    $( ".accordion-object" ).accordionCustom({
//        header: 'div.accordion-header',
//        content: 'div.accordion-content',
//        footer: 'div.accordion-footer',
//        collapsible: true,
//        autoHeight: false,
//        navigation: true,
//        active: 'none'
//    });
//    $( ".subaccordion-object" ).accordionCustom({
//        header: 'div.subaccordion-header',
//        content: 'div.subaccordion-content',
//        footer: 'div.subaccordion-footer',
//        collapsible: true,
//        autoHeight: false,
//        navigation: true,
//        active: 'none'
//    })
//}

// override accordion click handler when clicking Like, Comment, pressing enter or space
//function overrideAccordionEvent() {
////    $(".accordion-object p a, .accordion-object .no-accordion").click(function(e) {
////        e.stopPropagation();
////    });
//
//    // override the space and enter key from performing accordion action
//    $('.comment-input').keydown(function(e){
//        if(e.which==32 || e.which==13){
//            e.stopPropagation();
//        }
//    });
//}

function initRoundElements() {
    $(".round-element").corner();
}

// init with comment input hidden; show upon click
//function initCommentInputDisplay() {
//    // hide all comment boxes
//    $('.comment-box').hide();
//
//    // show comment div upon click
//    $('.click-to-comment').click(function(){
//        $(this).closest('.row').find('.comment-box').toggle();
//        // clear input field
//        $(this).closest('.row').find('.comment-input').val('');
//    });
//}

// perform like action upon click
//function initLike() {
//    $('.click-to-like').click(function(){
//        //var referId = $(this).closest('.row').data("rid");
//        var ridString = $(this).closest('.row').attr("id");
//        var referId = ridString.substring(ridString.indexOf('rid-') + 'rid-'.length);
//
//        //alert(referId);
//
//        var likes = $(this).closest('.row').find('.number-of-likes');
//        var likeStatus = $(this);
//
//        jQuery.post("http://192.168.11.28/referrals/perform_like_action", {
//            rid: referId
//        }, function(likeCount){
//            // toggle Like and Unlike
//            if(jQuery.trim(likeStatus.text())=="Like")
//            {
//                likeStatus.text("Unlike");
//            } else {
//                likeStatus.text("Like");
//            }
//            if(likeCount>0) {
//                if(likeCount == 1) {
//                    likes.text(likeCount + " person likes this.");
//                } else {
//                    likes.text(likeCount + " people like this.");
//                }
//            }
//            else {
//                likes.text("");
//            }
//        });
//    });
//}

//function initComment() {
//    $('.submit-comment-button').click(function(){
//        var name = jQuery.trim($(this).closest('.row').find('.comment-input').val());
//        //var referId = $(this).closest('.row').data("rid");
//
//        var ridString = $(this).closest('.row').attr("id");
//        var referId = ridString.substring(ridString.indexOf('rid-') + 'rid-'.length);
//
//        //alert(referId);
//
//        // toggle comment box
//        $(this).closest('.row').find('.comment-box').toggle();
//        var lastRow = $(this).closest('.row').find('.comments-table-tbody');
//
//        // if the comment is not empty, then proceed with ajax
//        if(name) {
//            jQuery.post("http://192.168.11.28/referrals/add_new_comment", {
//                comment: name,
//                rid: referId
//            }, function(data){
//                // add comment to table using javascript
//                var currentUserName = jQuery.trim($("#currentUserName").text());
//                                    var currentFBID = jQuery.trim($("#current-fbid").text());
////                    lastRow.after('<tr class="inbox-single-comment"><td class="comments-name">' + currentUserName + ': </td><td class="comments-content">' + name + '</td></tr>');
//                lastRow.append('<tr class="inbox-single-comment">' +
//                                            '<td class="commenter-pic">' +
//                                                    '<img src="https://graph.facebook.com/' + currentFBID + '/picture">' +
//                                            '</td>' +
//                                            '<td class="comments-name">' +
//                            currentUserName + ': ' +
//                                            '</td>' +
//                                            '<td class="comments-content">' +
//                                                    name +
//                                            '</td>' +
//                                            '<td>' +
//                                                    '<button id="delete-comment-button-cid-' + data + '" class="delete-comment" data-cid=' + data + '>' +
//                                                            'x' +
//                                                    '</button>' +
//                                            '</td>' +
//                                    '</tr>');
//                initRemoveComment();
//            });
//        }
//        //}
//
//        // now do something to confirm the comment
//
//        return false;
//    });
//}

//function initRemoveComment() {
//    $('.delete-comment').click(function(){
////        var cid = $(this).data('cid');
//        var cidString = $(this).attr('id');
//        var cid = cidString.substring(cidString.indexOf('cid-') + 'cid-'.length);
//
//        var inboxSingleComment = $(this).closest('.inbox-single-comment');
//        jQuery.post("http://192.168.11.28/referrals/remove_comment", {
//            cid: cid
//        }, function(data) {
////            var parsedJSON = jQuery.parseJSON(data);
////            updateComments(parsedJSON, commentsTable);
//            inboxSingleComment.remove();
//        });
//    });
//}


//function initLoadMoreComments() {
//    $('.hide-load-comments-button').hide();
//    $('.show-load-comments-button').show();
//    $('.hide-comment').hide();
//    $('.show-comment').show();
//
//    $('.show-load-comments-button').click(function() {
//        $(this).closest('.comments-table').find('.hide-comment').show();
//        $(this).hide();
//    });
//}

function initLoadMoreButton() {
    var inboxLoadStart = 3;
    // call more data from mysql ('load more' button action)
    $('#load-more-inbox-content-button').click(function(){
        // jquery post to retrieve more rows
        jQuery.post("http://192.168.11.28/referrals/get_more_inbox", {
            rowStart: inboxLoadStart
        }, function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            displayMoreInbox(parsedJSON);
            inboxLoadStart = inboxLoadStart+3;
        });
    });

    var friendActivityLoadStart = 3;
    $('#load-more-friend-activity-content-button').click(function(){
        jQuery.post("http://192.168.11.28/referrals/get_more_friend_activity", {
            rowStart: friendActivityLoadStart
        }, function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            displayMoreFriendActivity(parsedJSON);
            friendActivityLoadStart = friendActivityLoadStart+3;
        });
    });
}


// private function
//function updateComments(commentList, commentsTable, collapse) {
//    var commentsHTMLString = updateCommentsHTMLString(commentList, collapse);
//
//    // update comments table
//    commentsTable.html(commentsHTMLString);
//
//    // rebind comment table elements
//    $('.show-load-comments-button').unbind();
//    initLoadMoreComments();
//    $('.delete-comment').unbind();
//    initRemoveComment();
//}

// private function
//function updateCommentsHTMLString(commentList, collapse) {
//    // re-write all html for entire comments table
//    var commentsCountdown = commentList.length;
//    var needShowAllButton = "hide-load-comments-button";
//    var showStatus = "show-comment";
//    var commentsHTMLString = "";
//
//    for(var j=0; j<commentList.length; j++) {
//        if(commentsCountdown < 3) {
//            showStatus = "show-comment";
//        } else {
//            showStatus = "hide-comment";
//            needShowAllButton = "show-load-comments-button";
//        }
//        commentsCountdown--;
//
//        if(commentsCountdown == commentList.length-1) {
//            commentsHTMLString = commentsHTMLString +
//                "<tr>" +
//                    "<td class='show-all-comments-button no-accordion " + needShowAllButton + "'>" +
//                        "View all " + commentList.length + " comments." +
//                    "</td>" +
//                "</tr>";
//        }
//
//        // comments here
//        commentsHTMLString = commentsHTMLString +
//           "<tr class='inbox-single-comment " + showStatus + "'>" +
//                "<td class='commenter-pic'>" +
//                    "<img src='https://graph.facebook.com/" + commentList[j].fbid + "/picture'>" +
//                "</td>" +
//                "<td class='comments-name'>" +
//                    commentList[j].firstName + " " + commentList[j].lastName + ": " +
//                "</td>" +
//                "<td class='comments-content'>" +
//                    commentList[j].comment +
//                "</td>" +
//                "<td>" +
//                  "<button id='delete-comment-button-cid-" + commentList[j].cid + "' class='delete-comment' data-cid=" +
//                     commentList[j].cid +
//                       ">x</button>" +
//                "</td>" +
//           "</tr>";
//    }
//    return commentsHTMLString;
//}

// private function
//function createReferralsHTMLString(row, userReferralString, fbidPicture) {
//    var VendorDetails = row.VendorList['VendorList'][0][0];
//    var likeNumber = 0;
//    var likeStatus = "";
//    var recommendationComment = "";
//
//    var t = row.refDate.split(/[- :]/);
//    var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
//    var timeStamp = d.getFuzzyTimeElapsed();
//
//    likeNumber = row.LikesList['LikesList'].length;
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
//
//    if (row.alreadyLiked==1) {
//        likeStatus = "Unlike";
//    } else {
//        likeStatus = "Like";
//    }
//
//    // allow for a little flexibility
//    if(fbidPicture == undefined) {
//        var fbidPicture = row.fbid;
//    }
//
//    displayReferralsHTMLString = "";
//    if ( row.lid == 0 ) {
//        displayReferralsHTMLString = displayReferralsHTMLString +
//            "<div class='inbox-single-wrapper accordion-header'>" +
//                "<div class='referral-date'>" +
//                    timeStamp +
//                "</div>" +
//                "<a>" +
//                    VendorDetails.name +
//                    "<div class='friend-referral-comment-wrapper'>" +
//                        "<table class='formatted-table'>" +
//                            "<tr>" +
//                                "<td class='formatted-table-info'>" +
//                                    "<div class='inbox-friend-pic'>" +
//                                        "<img src='https://graph.facebook.com/" + fbidPicture + "/picture'>" +
//                                    "</div>" +
//                                    "<div class='inbox-friend-referral'>" +
//                                        userReferralString +
//                                    "</div>" +
//                                "</td>" +
//                                "<td class='formatted-table-button' align='right'>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + VendorDetails.id + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Refer to Friends" +
//                                        "</a>" +
//                                    "</p>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + VendorDetails.id + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Add to List" +
//                                        "</a>" +
//                                    "</p>" +
//                                "</td>" +
//                            "</tr>" +
//                        "</table>" +
//                    "</div>" +
//                "</a>" +
//            "</div>" +
//
//       // details of the row
//           "<div class='drop-down-details accordion-content'>" +
//               VendorDetails.addrNum + " " + VendorDetails.addrStreet + "<br>" +
//               VendorDetails.addrCity + " " + VendorDetails.addrState + " " + VendorDetails.addrZip + "<br>" +
//               VendorDetails.phone + "<br>" +
//               VendorDetails.website +
//           "</div>";
//    } else {
//        var userListDetails = row.UserList[0];
//
//        displayReferralsHTMLString = displayReferralsHTMLString +
//            "<div class='inbox-single-wrapper accordion-header'>" +
//                "<div class='referral-date'>" +
//                    timeStamp +
//                "</div>" +
//                "<a>" + userListDetails.name +
//                    "<div class='friend-referral-comment-wrapper'>" +
//                        "<table class='formatted-table'>" +
//                            "<tr>" +
//                                "<td class='formatted-table-info'>" +
//                                    "<div class='inbox-friend-pic'>" +
//                                        "<img src='https://graph.facebook.com/" + fbidPicture + "/picture'>" +
//                                    "</div>" +
//                                    "<div class='inbox-friend-referral'>" +
//                                        userReferralString +
//                                    "</div>" +
//                                "</td>" +
//                                "<td class='formatted-table-button' align='right'>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + row.lid + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Refer to Friends" +
//                                        "</a>" +
//                                    "</p>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + row.lid + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Add to List" +
//                                        "</a>" +
//                                    "</p>" +
//                                "</td>" +
//                            "</tr>" +
//                        "</table>" +
//                    "</div>" +
//                "</a>" +
//            "</div>" +
//
//            "<div class='drop-down-details accordion-content'>";
//
//        for(var j = 0; j<row.VendorList['VendorList'].length; j++) {
//            SubVendorDetails = row.VendorList['VendorList'][j][0];
//            displayReferralsHTMLString = displayReferralsHTMLString +
//                "<div class='subaccordion-object'>" +
//                    "<div class='subaccordion-header'>" +
//                        "<table class='formatted-table'>" +
//                            "<tr>" +
//                                "<td>" +
//                                    SubVendorDetails.name +
//                                "</td>" +
//                                "<td class='formatted-table-button' align='right'>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + SubVendorDetails.id + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Refer to Friends" +
//                                        "</a>" +
//                                    "</p>" +
//                                    "<p>" +
//                                        "<a href='#' id=" + SubVendorDetails.id + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                                            "<span class='ui-icon ui-icon-plus'></span>" +
//                                                "Add to List" +
//                                        "</a>" +
//                                    "</p>" +
//                                "</td>" +
//                            "</tr>" +
//                        "</table>" +
//                    "</div>" +
//                    "<div class='subaccordion-content'>" +
//                        SubVendorDetails.addrNum + " " + SubVendorDetails.addrStreet + "<br>" +
//                        SubVendorDetails.addrCity + " " + SubVendorDetails.addrState + " " + SubVendorDetails.addrZip + "<br>" +
//                        SubVendorDetails.phone + "<br>" +
//                        SubVendorDetails.website +
//                    "</div>" +
//                "</div>";
//        }
//
//        displayReferralsHTMLString = displayReferralsHTMLString +
//            "</div>";
//
//    }
//
//    // footer comments
//    displayReferralsHTMLString = displayReferralsHTMLString +
//    "<div class='accordion-footer'>" +
//       "<div id='row-rid-" + row.rid + "' class='row' data-rid=" + row.rid + ">" +
//            "<div class='click-to-like no-accordion' data-likeCounts=" + likeNumber + ">" +
//                likeStatus +
//            "</div>" +
//            "<div class='click-to-comment no-accordion'>" +
//                "Comment" +
//            "</div>" +
//            "<div class='number-of-likes no-accordion'>" +
//                likeNumber +
//            "</div>" +
//            "<div class='comments'>" +
//                "<table class='comments-table'>" +
//                    "<tbody class='comments-table-tbody'>" +
//                        // loop comments for the 'view all comments'
//                        updateCommentsHTMLString(row.CommentsList['CommentsList']) +
//                    "</tbody>" +
//               "</table>" +
//           "</div>" +
//           "<div class='comment-box no-accordion'>" +
//                "<form name='form-comment' class='form-comment' method='post'>" +
//                    "<input type='text' class='comment-input'/>" +
//                    "<button type='submit' class='submit-comment-button'>" +
//                        "Submit" +
//                    "</button>" +
//                "</form>" +
//           "</div>" +
//       "</div>" +
//    "</div>";
//
//    return displayReferralsHTMLString;
//}


/*
 * date and time related functions begin here
 */
//function initDatePrototype () {
//
//    Date.prototype.toFormattedString = function (f)
//    {
//        var nm = this.getMonthName();
//        var nd = this.getDayName();
//        var ampm = 'am';
//        if ( this.getHours() > 12 ) {ampm = 'pm'};
//
//        f = f.replace(/x/g, ampm);
//        f = f.replace(/yyyy/g, this.getFullYear());
//        f = f.replace(/MMM/g, nm.substr(0,3).toUpperCase());
//        f = f.replace(/Mmm/g, nm.substr(0,3));
//        f = f.replace(/MM\*/g, nm.toUpperCase());
//        f = f.replace(/Mm\*/g, nm);
//        f = f.replace(/mm/g, String(this.getMonth()+1).padLeft('0', 2));
//        f = f.replace(/DDD/g, nd.substr(0,3).toUpperCase());
//        f = f.replace(/Ddd/g, nd.substr(0,3));
//        f = f.replace(/DD\*/g, nd.toUpperCase());
//        f = f.replace(/Dd\*/g, nd);
//        f = f.replace(/dd/g, String(this.getDate()).padLeft('0', 2));
//        f = f.replace(/d\*/g, this.getDate());
//        f = f.replace(/h/g, this.getHours() % 12);
//        f = f.replace(/i/g, String(this.getMinutes()).padLeft('0', 2));
//        f = f.replace(/z/g, this.getDayName());
//        f = f.replace(/q/g, this.getMonthName());
//        f = f.replace(/X/g, this.getSuffix());
//
//        return f
//    };
//
//    Date.prototype.getMonthName = function () {
//        switch(this.getMonth())
//        {
//            case 0:return 'January';
//            case 1:return 'February';
//            case 2:return 'March';
//            case 3:return 'April';
//            case 4:return 'May';
//            case 5:return 'June';
//            case 6:return 'July';
//            case 7:return 'August';
//            case 8:return 'September';
//            case 9:return 'October';
//            case 10:return 'November';
//            case 11:return 'December';
//        }
//    };
//
//    Date.prototype.getMonthString = function () {
//        switch(this.getMonth())
//        {
//            case 0:return '01';
//            case 1:return '02';
//            case 2:return '03';
//            case 3:return '04';
//            case 4:return '05';
//            case 5:return '06';
//            case 6:return '07';
//            case 7:return '08';
//            case 8:return '09';
//            case 9:return '10';
//            case 10:return '11';
//            case 11:return '12';
//        }
//    };
//
//    Date.prototype.getSuffix = function () {
//        switch(this.getDate())
//        {
//            case 1:
//            case 21:
//            case 31:
//                return 'st';
//            case 2:
//            case 22:
//                return 'nd';
//            case 3:
//            case 23:
//                return 'rd';
//            default:
//                return 'th';
//        }
//    }
//
//    Date.prototype.getDayName = function ()
//    {
//        switch(this.getDay())
//        {
//            case 0:return 'Sunday';
//            case 1:return 'Monday';
//            case 2:return 'Tuesday';
//            case 3:return 'Wednesday';
//            case 4:return 'Thursday';
//            case 5:return 'Friday';
//            case 6:return 'Saturday';
//        }
//    };
//
//    String.prototype.padLeft = function (value, size)
//    {
//        var x = this;
//        while (x.length<size) {x = value + x;}
//        return x;
//    };
//
//    Date.prototype.getFuzzyTimeElapsed = function ()
//    {
//        // Get the current date and reference date
//        var currentDate = new Date();
//        var refDate = new Date(this);
//        var dateOfRecord = "";
//
//        // Extract from currentDate
//        var currentYear = currentDate.getFullYear().toString();
//        var currentMonth = currentDate.getMonthString();
//
//        if (currentDate.getDate() < 10) {
//            currentDay = '0' + currentDate.getDate().toString();
//        } else {
//            currentDay = currentDate.getDate().toString();
//        }
//
//        // Extract from refDate
//        var refYear = refDate.getFullYear().toString();
//        var refMonth = refDate.getMonthString();
//
//        if (refDate.getDate() < 10) {
//            refDay = '0' + refDate.getDate().toString();
//        } else {
//            refDay = refDate.getDate().toString();
//        }
//
//        // Determine the difference in time
//
//        var tempMaxDate = currentYear + currentMonth + currentDay;
//        var tempDateRef = refYear + refMonth + refDay;
//        var diffInDays = parseInt(currentDay) - parseInt(refDay);
//
//        var tempDifference = parseInt(tempMaxDate) - parseInt(tempDateRef);
//
//        if (tempDifference > 7) {
//            // display regular time stamp
//            dateOfRecord = refDate.toFormattedString('h:ix, z, q ddX, yyyy');
//        } else {
//            var currentHour = currentDate.getHours();
//            var currentMin = currentDate.getMinutes();
//            var currentSec = currentDate.getSeconds();
//
//            var refHour = refDate.getHours();
//            var refMin = refDate.getMinutes();
//            var refSec = refDate.getSeconds();
//
//            var diffInHours = currentHour - refHour;
//            var diffInMin = currentMin - refMin;
//            var diffInSec = currentSec - refSec;
//
//            // show time difference
//            if (tempDifference < 1) {
//                if (diffInHours > 0) {
//                    if (diffInMin < 0) {
//                        diffInMin = 60 + diffInMin;
//                        diffInHours = diffInHours - 1;
//                        if (diffInHours == 0 ) {
//                            dateOfRecord = String(diffInMin) + ' min ago';
//                        } else {
//                            dateOfRecord = String(diffInHours) + ' hr ago';// + String(diffInMin) + ' min ago';
//                        }
//                    } else {
//                        dateOfRecord = String(diffInHours) + ' hr ago';// + String(diffInMin) + ' min ago';
//                    }
//                } else {
//                    if (diffInMin > 0) {
//                        if (diffInSec < 0) {
//                            diffInMin = diffInMin - 1;
//                            diffInSec = diffInSec + 60;
//                            if (diffInMin == 0) {
//                                dateOfRecord = String(diffInSec) + ' sec ago';
//                            } else {
//                                dateOfRecord = String(diffInMin) + ' min ago'; // + String(diffInSec) + ' sec ago';
//                            }
//                        } else {
//                            dateOfRecord = String(diffInMin) + ' min ago'; // + String(diffInSec) + ' sec ago';
//                        }
//                    } else {
//                        dateOfRecord = String(diffInSec) + ' sec ago';
//                    }
//                }
//            } else {
//                if (tempDifference > 1) {
//                    dateOfRecord = String(diffInDays) + ' days ago';
//                } else {
//                    if (diffInHours < 0) {
//                        diffInHours = diffInHours + 24;
//                        diffInDays = diffInDays - 1;
//                        if(diffInDays == 0) {
//                            dateOfRecord = String(diffInHours) + ' hr ago';
//                        } else {
//                            dateOfRecord = String(diffInDays) + ' day ago'; // + String(diffInHours) + ' hr ago';
//                        }
//                    } else {
//                        dateOfRecord = String(diffInDays) + ' day ago'; // + String(diffInHours) + ' hr ago';
//                    }
//                }
//            }
//        }
//
//        return dateOfRecord;
//
//    }
//
//}

function reInitReferralItems() {
    //$('.click-to-comment').unbind();
    //$('.click-to-like').unbind();
    //$('.submit-comment-button').unbind();
    //$('.delete-comment').unbind();
    //$('.show-load-comments-button').unbind();
    
//    initRemoveComment();
//    initComment();
//    initLike();
//    initCommentInputDisplay();
//    initLoadMoreComments();

//    $('.add-to-list-popup-link').unbind();
//    $('.refer-popup-link').unbind();
//
//    // are the following two bindings necessary?
//    $('#addToListDialog').unbind();
//    $('#dialog').unbind();
//
//    initAddAndReferButtons();
}


function displayMoreInbox(moreRows) {
    // moreRows is a parsedJSON object
    // create a string that captures all HTML required to write the next referral
    var displayReferralsHTMLString = "";

    // destroy the accordion first
    $('.accordion-object').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');

    for(var i=0; i<moreRows.length; i++) {
        var row = moreRows[i];

        if (row.ReferralsComment == "") {
            userReferralString = row.firstName + " " + row.lastName + " thinks you'll love this!";
        } else {
            userReferralString = row.firstName + " " + row.lastName + " says \"" + row.ReferralsComment + "\"";
        }

        displayReferralsHTMLString = createReferralsHTMLString(row, userReferralString);

        // append to inbox wrapper
        $(displayReferralsHTMLString).appendTo('#accordion-inbox');

        reInitReferralItems();
    }
    bindAccordionInbox();
    overrideAccordionEvent();
}



function displayMoreFriendActivity(moreRows) {
    var displayReferralsHTMLString = "";

    // destroy the accordion first
    $('.accordion-object').accordionCustom('destroy');
    $('.subaccordion-object').accordionCustom('destroy');

    for(var i=0; i<moreRows.length; i++) {
        var row = moreRows[i];
        var RecipientDetails = row.RecipientDetails['RecipientDetails'][0];

        userReferralString = row.firstName + " " + row.lastName + " recommended to " + RecipientDetails.firstName + " " + RecipientDetails.lastName;
        if (row.ReferralsComment != "") {
            userReferralString = userReferralString + ": \"" + row.ReferralsComment + "\"";
        }

        displayReferralsHTMLString = createReferralsHTMLString(row, userReferralString);
        $(displayReferralsHTMLString).appendTo('#accordion-friend-activity');
        reInitReferralItems();
    }
    bindAccordionInbox();
    overrideAccordionEvent();
}

function getVendorDetails(id, callback) {
    // ajax call to get array of details given only the id

    jQuery.post("http://192.168.11.28/referrals/get_vendor_details", {
        vendorID: id
    }, function(data) {
        var parsedJSON = jQuery.parseJSON(data);
        return callback(parsedJSON);
    });
}


//function bindAddToListButton() {
//    $('.add-to-list-popup-link').click(function() {
//
//        var vendor = {};
//        vendor['id'] = $(this).attr('id');
//
//        $('#fuzz').fadeIn();
//        $('#addToListDialog').dialog("option", "title", "Add to a List");
//        $('#addToListDialog').dialog("option", "buttons", {
//            "Add!": function() {
//
//                // get value selected in dropdown and comment
//                var selectedList = $('#selectList').val();
//
//                // create new list if specified and add vendor to that new list
//                if (selectedList == 'addNew') {
//                    var newListName = $('#new-list-name').val();
//                    jQuery.post('list_controller/add_list', {
//                        newListName: newListName,
//                        uid: myUID
//                    }, function(data) {
//                        var newListData = jQuery.parseJSON(data);
//                        if (newListData.length == 0) {
//                            alert("List was not added successfully");
//                        } else if (newListData.length > 1) {
//                            alert("Multiple lists were returned");
//                        } else {
//                            // refresh sidebar that displays your lists
//                            var htmlString = "<li class='my-list-wrapper'><span id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'>x</span>";
//                            htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list'>" + newListData[0].name + "</span></li>";                                $('#lists').append(htmlString);
//
//                            // set selectedList to the lid that was just created
//                            addVendorToList(newListData[0].lid, vendor);
//                        }
//                    });
//                }
//
//                // add vendor to existing list
//                else if (selectedList != 'none') {
//                    addVendorToList(selectedList, vendor);
//                }
//
//                // no list was selected from dropdown
//                else {
//                    alert("Please select a list");
//                }
//            }
//        });
//
//        $('#addToListDialog').dialog('open');
//        return false;
//    });
//    
//    $('.add-list-to-list-popup-link').click(function() {
//
//        var lid = $(this).attr('id');
//
//        $('#fuzz').fadeIn();
//        $('#addToListDialog').dialog("option", "title", "Add to a List");
//        $('#addToListDialog').dialog("option", "buttons", {
//            "Add!": function() {
//
//                // get value selected in dropdown and comment
//                var selectedList = $('#selectList').val();
//
//                // create new list if specified and add vendor to that new list
//                if (selectedList == 'addNew') {
//                    var newListName = $('#new-list-name').val();
//                    jQuery.post('list_controller/add_list', {
//                        newListName: newListName,
//                        uid: myUID
//                    }, function(data) {
//                        var newListData = jQuery.parseJSON(data);
//                        if (newListData.length == 0) {
//                            alert("List was not added successfully");
//                        } else if (newListData.length > 1) {
//                            alert("Multiple lists were returned");
//                        } else {
//                            // refresh sidebar that displays your lists
//                            var htmlString = "<li class='my-list-wrapper'><span id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'>x</span>";
//                            htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list'>" + newListData[0].name + "</span></li>";                                $('#lists').append(htmlString);
//
//                            // set selectedList to the lid that was just created
//                            // addVendorToList(newListData[0].lid, vendor);
//                            addListToList(newListData[0].lid, lid)
//                        }
//                    });
//                }
//
//                // add vendor to existing list
//                else if (selectedList != 'none') {
//                    // addVendorToList(selectedList, vendor);
//                    addListToList(selectedList, lid);
//                }
//
//                // no list was selected from dropdown
//                else {
//                    alert("Please select a list");
//                }
//            }
//        });
//
//        $('#addToListDialog').dialog('open');
//        return false;
//    });
//    
//}

//function bindReferDialogButton(friendList) {
//    $('.refer-popup-link').click(function(){
//        var vendor = {};
//        vendor['id'] = $(this).attr('id');
//
//        displayAutoCompleteResults(allFriends);
//        
//        $("#fuzz").fadeIn();
//        $('#dialog').dialog("option","title","Refer Friends");
//
//        // reset all values when dialog box closes
//        $( "#dialog" ).bind( "dialogbeforeclose", function(event, ui) {
//            $('#comment-box').val('');
//            $('#friends-refer-right').html('');
//            $('#tags').val('');
//            friendList.length = 0;
//            displayAutoCompleteResults(allFriends);
//
//            // fade out dark background
//            $("#fuzz").fadeOut();
//        });
//
//
//        $('#dialog').dialog("option","buttons", {
//            "Refer!": function() {
//                if (friendList.length < 1) {
//                    alert("You did not select any friends to refer. Please try again.");
//                }
//                else {
//                    var now = new Date();
//                    now = now.format("yyyy-mm-dd HH:MM:ss");
//                    var comment = $('#comment-box').val();
//
//                    // create list of friend uid's to refer to
//                    var uidFriendsObj = {};
//                    for (var i = 0; i < friendList.length; i++) {
//                        uidFriendsObj[i] = friendList[i].uid;
//                    }
//                    var uidFriendsStr = JSON.stringify(uidFriendsObj);
//
//                    // perform query to add referrals to Referrals and ReferralDetails databases
//                    jQuery.post('searchvendors/add_referral',{
//                        myUID: myUID,
//                        date: now,
//                        comment: comment,
//                        numFriends: friendList.length,
//                        uidFriends: uidFriendsStr,
//                        id: vendor.id
//                    }, function(data) {
//                        if (data) {
//                            alert(data);
//                        }
//                        else {
//                            $('#dialog').dialog("close");
//                        }
//                    });
//                }
//            }
//        });
//
//        $('#dialog').dialog('open');
//        return false;
//    });
//    
//    
//    $('.refer-list-popup-link').click(function(){
//
//        var lid = $(this).attr('id');
//        
//        displayAutoCompleteResults(allFriends);
//        
//        $("#fuzz").fadeIn();
//        $('#dialog').dialog("option","title","Refer Friends");
//
//        // reset all values when dialog box closes
//        $( "#dialog" ).bind( "dialogbeforeclose", function(event, ui) {
//            $('#comment-box').val('');
//            $('#friends-refer-right').html('');
//            $('#tags').val('');
//            friendList.length = 0;
//            displayAutoCompleteResults(allFriends);
//
//            // fade out dark background
//            $("#fuzz").fadeOut();
//        });
//
//
//        $('#dialog').dialog("option","buttons", {
//            "Refer!": function() {
//                if (friendList.length < 1) {
//                    alert("You did not select any friends to refer. Please try again.");
//                }
//                else {
//                    var now = new Date();
//                    now = now.format("yyyy-mm-dd HH:MM:ss");
//                    var comment = $('#comment-box').val();
//
//                    // create list of friend uid's to refer to
//                    var uidFriendsObj = {};
//                    for (var i = 0; i < friendList.length; i++) {
//                        uidFriendsObj[i] = friendList[i].uid;
//                    }
//                    var uidFriendsStr = JSON.stringify(uidFriendsObj);
//
//                    // perform query to add referrals to Referrals and ReferralDetails databases
//                    jQuery.post('searchvendors/add_referral',{
//                        myUID: myUID,
//                        date: now,
//                        comment: comment,
//                        numFriends: friendList.length,
//                        uidFriends: uidFriendsStr,
//                        id: lid
//                    }, function(data) {
//                        if (data) {
//                            alert(data);
//                        }
//                        else {
//                            $('#dialog').dialog("close");
//                        }
//                    });
//                }
//            }
//        });
//
//        $('#dialog').dialog('open');
//        return false;
//    });
//}
