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
        $(this).parents('.row').children('.comment-box').toggle();
        // clear input field
        $(this).parents('.row').find('.comment-input').val('');
    });
}

// perform like action upon click
function initLike() {
    $('.click-to-like').click(function(){
        var referId = $(this).closest('.row').data("rid");
        var rid = $(this).closest('row').attr();
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
        var referId = $(this).closest('.row').data("rid");
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
							'<button class="delete-comment" data-cid=' + data + '>' +
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
        var cid = $(this).data('cid');
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
                "<td class='comments-name'>" +
                    commentList[j].firstName + " " + commentList[j].lastName + ": " +
                "</td>" +
                "<td class='comments-content'>" +
                    commentList[j].comment +
                "</td>" +
                "<td>" +
                  "<button class='delete-comment' data-cid=" +
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
