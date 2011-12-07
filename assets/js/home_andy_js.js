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
});

/* functions for $(document).ready */
//initialize the accordion features for inbox
function bindAccordionInbox() {
    $( "#accordion-inbox" ).accordion({
                    header: 'div.inbox-single-wrapper',    
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
        var likes = $(this).closest('.row').find('.number-of-likes');
        var likeStatus = $(this);

        jQuery.post("http://192.168.11.28/test/perform_like_action", {
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
        $(this).parents('.row').children('.comment-box').toggle();
//        var lastRow = $(this).closest('.row').find('.inbox-single-comment').last();
        var lastRow = $(this).closest('.row').find('tbody');
        // if the comment is not empty, then proceed with ajax 
        if(name) {
            jQuery.post("http://192.168.11.28/test/add_new_comment", {
                comment: name,
                rid: referId             
            }, function(data){
                // add comment to table using javascript
                if (data == "success") {
                    var currentUserName = jQuery.trim($("#currentUserName").text());
//                    lastRow.after('<tr class="inbox-single-comment"><td class="comments-name">' + currentUserName + ': </td><td class="comments-content">' + name + '</td></tr>');
                    lastRow.append('<tr class="inbox-single-comment"><td class="comments-name">' + currentUserName + ': </td><td class="comments-content">' + name + '</td></tr>');
                }
            });
        }
        //}

        // now do something to confirm the comment
        
        return false;
    });
}