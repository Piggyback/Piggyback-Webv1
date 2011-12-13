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
    bindAccordionInbox();
    overrideAccordionEvent();
    initCommentInputDisplay();
    initLike();
    initComment();
    initRemoveComment();
    initLoadMoreComments();
    initLoadMoreButton();
});

/* functions for $(document).ready */
//initialize the accordion features for inbox
function bindAccordionInbox() {
    $( "#accordion-inbox" ).accordionCustom({
        header: 'div.inbox-single-wrapper',// div.inbox-list-wrapper',
        content: 'div.accordion-content',
        footer: 'div.accordion-footer',
        collapsible: true,
        autoHeight: true,
        navigation: true,
        active: 'none'
    });
}

// override accordion click handler when clicking Like, Comment, pressing enter or space
function overrideAccordionEvent() {
    $("#accordion-inbox p a, #accordion-inbox table, #accordion-inbox .no-accordion").click(function(e) {
        e.stopPropagation();
    });

    // override the space and enter key from performing accordion action
    $('.comment-input').keydown(function(e){
        if(e.which==32 || e.which==13){
            e.stopPropagation();
        }
    });
}

// init with comment input hidden; show upon click
function initCommentInputDisplay() {
    // hide all comment boxes
    $('.comment-box').hide();

    // show comment div upon click
    $('.click-to-comment').click(function(){
        $(this).closest('.row').find('.comment-box').toggle();
        // clear input field
        $(this).closest('.row').find('.comment-input').val('');
    });
}

// perform like action upon click
function initLike() {
    $('.click-to-like').click(function(){
        //var referId = $(this).closest('.row').data("rid");
        var ridString = $(this).closest('.row').attr("id");
        var referId = ridString.substring(ridString.indexOf('rid-') + 'rid-'.length);

        //alert(referId);

        var likes = $(this).closest('.row').find('.number-of-likes');
        var likeStatus = $(this);

        jQuery.post("http://192.168.11.28/referrals/perform_like_action", {
            rid: referId
        }, function(likeCount){
            // toggle Like and Unlike
            if(jQuery.trim(likeStatus.text())=="Like")
            {
                likeStatus.text("Unlike");
            } else {
                likeStatus.text("Like");
            }
            if(likeCount>0) {
                if(likeCount == 1) {
                    likes.text(likeCount + " person likes this.");
                } else {
                    likes.text(likeCount + " people like this.");
                }
            }
            else {
                likes.text("");
            }
        });
    });
}

function initComment() {
    $('.submit-comment-button').click(function(){
        var name = $(this).closest('.row').find('.comment-input').val();
        //var referId = $(this).closest('.row').data("rid");

        var ridString = $(this).closest('.row').attr("id");
        var referId = ridString.substring(ridString.indexOf('rid-') + 'rid-'.length);

        //alert(referId);

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
                if (data) {
                    var currentUserName = jQuery.trim($("#currentUserName").text());
					var currentFBID = jQuery.trim($("#current-fbid").text());
//                    lastRow.after('<tr class="inbox-single-comment"><td class="comments-name">' + currentUserName + ': </td><td class="comments-content">' + name + '</td></tr>');
                    lastRow.append('<tr class="inbox-single-comment">' +
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
							'<button id="delete-comment-button-cid-' + data + '" class="delete-comment" data-cid=' + data + '>' +
								'x' +
							'</button>' +
						'</td>' +
					'</tr>');
                    initRemoveComment();
                }
            });
        }
        //}

        // now do something to confirm the comment

        return false;
    });
}

function initRemoveComment() {
    $('.delete-comment').click(function(){
//        var cid = $(this).data('cid');
        var cidString = $(this).attr('id');
        var cid = cidString.substring(cidString.indexOf('cid-') + 'cid-'.length);

        //alert(cid);

        var commentsTable = $(this).closest('.comments-table-tbody');
        jQuery.post("http://192.168.11.28/referrals/remove_comment", {
            cid: cid
        }, function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            updateComments(parsedJSON, commentsTable);
        });
    });

}

// these are private functions
function updateComments(commentList, commentsTable, collapse) {
    var commentsHTMLString = updateCommentsHTMLString(commentList, collapse);

    // update comments table
    commentsTable.html(commentsHTMLString);

    // rebind comment table elements
    $('.show-load-comments-button').unbind();
    initLoadMoreComments();
    $('.delete-comment').unbind();
    initRemoveComment();
}

// private function
function updateCommentsHTMLString(commentList, collapse) {
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
           "<tr class='inbox-single-comment " + showStatus + "'>" +
                "<td class='commenter-pic'>" +
                    "<img src='https://graph.facebook.com/" + commentList[j].fbid + "/picture'>" +
                "</td>" +
                "<td class='comments-name'>" +
                    commentList[j].firstName + " " + commentList[j].lastName + ": " +
                "</td>" +
                "<td class='comments-content'>" +
                    commentList[j].comment +
                "</td>" +
                "<td>" +
                  "<button id='delete-comment-button-cid-" + commentList[j].cid + "' class='delete-comment' data-cid=" +
                     commentList[j].cid +
                       ">x</button>" +
                "</td>" +
           "</tr>";
    }
    return commentsHTMLString;
}

function initLoadMoreComments() {
    $('.hide-load-comments-button').hide();
    $('.show-load-comments-button').show();
    $('.hide-comment').hide();
    $('.show-comment').show();

    $('.show-load-comments-button').click(function() {
        $(this).closest('.comments-table').find('.hide-comment').show();
        $(this).hide();
    });
}

function initLoadMoreButton() {
    var loadStart = 3;
    // call more data from mysql ('load more' button action)
    $('.load-more-button').click(function(){
        // jquery post to retrieve more rows
        jQuery.post("http://192.168.11.28/referrals/get_more_inbox", {
            rowStart: loadStart
        }, function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            displayMoreReferrals(parsedJSON);
            loadStart = loadStart+3;
        });
    });
}

// display additional rows
function displayMoreReferrals(moreRows) {
    // moreRows is a parsedJSON object
    // create a string that captures all HTML required to write the next referral
    var displayReferralsHTMLString = "";
    var likeNumber = 0;
    var likeStatus = "";

    // destroy the accordion first
    $('#accordion-inbox').accordionCustom('destroy');

    for(var i=0; i<moreRows.length; i++) {
        likeNumber = moreRows[i].LikesList['LikesList'].length;
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

        if (moreRows[i].alreadyLiked==1) {
            likeStatus = "Unlike";
        } else {
            likeStatus = "Like";
        }

        displayReferralsHTMLString = "" +
        "<div class='inbox-single-wrapper accordion-header'>" +
            "<div class='referral-date'>" +
                moreRows[i].refDate +
            "</div>" +
            "<a>" +
                moreRows[i].name +
                "<div class='friend-referral-comment-wrapper'>" +
                    "<div class='inbox-friend-pic'>" +
                        "<img src='https://graph.facebook.com/" + moreRows[i].fbid + "/picture'>" +
                    "</div>" +
                    "<div class='inbox-friend-referral'>" +
                        moreRows[i].firstName + " " + moreRows[i].lastName + " says \"" + moreRows[i].ReferralsComment + "\"" +
                    "</div>" +
                "</div>" +
            "</a>" +
        "</div>" +

   // details of the row
       "<div class='drop-down-details accordion-content'>" +
           moreRows[i].addrNum + " " + moreRows[i].addrStreet + "<br>" +
           moreRows[i].addrCity + " " + moreRows[i].addrState + " " + moreRows[i].addrZip + "<br>" +
           moreRows[i].phone + "<br>" +
           moreRows[i].website +
       "</div>" +

   // footer comments
        "<div class='accordion-footer'>" +
           "<div id='row-rid-" + moreRows[i].rid + "' class='row' data-rid=" + moreRows[i].rid + ">" +
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
                            updateCommentsHTMLString(moreRows[i].CommentsList['CommentsList']) +
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

        // append to inbox wrapper
        $(displayReferralsHTMLString).appendTo('#inbox-wrapper');

        $('.click-to-comment').unbind();
        initCommentInputDisplay();
        $('.click-to-like').unbind();
        initLike();
        $('.submit-comment-button').unbind();
        initComment();
        $('.delete-comment').unbind();
        initRemoveComment();
        $('.show-load-comments-button').unbind();
        initLoadMoreComments();
    }
    bindAccordionInbox();
    overrideAccordionEvent();
}
