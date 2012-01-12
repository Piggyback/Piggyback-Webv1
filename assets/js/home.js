/**
 * @global
 * 01/07/2012
 * 
 * All functions dealing with the home page:
 *      initScrollHeight
 *      initTabs
 *      initEnterDialogForm
 *      initSearchAJAX
 * 
 */

$(document).ready(function() {
    $('#searchform')[0].reset();
    // these functions are defined below
//    $('#loadingDiv')
//    .hide()  // hide it initially
//    .ajaxStart(function() {
//        $(this).show();
//    })
//    .ajaxStop(function() {
//        $(this).hide();
//    });
    // general 
    var myUID;
    var allFriends;
    getFriends();
    
    initScrollHeight();
    initTabs();
    initEnterDialogForm();
    initSearchAJAX();
    
    // initialize and bind 'refer' dialog
    bindFuzz();
    friendList = [];
    bindAddFriend();
    bindAutoComplete();
    bindReferDialog();
    bindReferDialogButton();
    
    // initialize and bind 'add to list' dialog
    bindAddToListDialog();
    bindAddToListButton();
        
    // these functions are defined in other files
    overrideAccordionEvent();   //TODO: Merge with Andy
    bindAccordion();        //TODO: Merge with Andy
    initClickAddList();
    initGetListContent();
    initDeleteList();
    bindAddListDialog();
    bindEditCommentDialog();
    bindFuzzMike();
    initAddList();
    
    // accordion content initialization
    initCommentInputDisplay();
    initLike();
    initComment();
    initRemoveComment();
    initLoadMoreComments();
    initDatePrototype();
    initRemoveReferralButton();
});

/**
 *  @mikegao
 *  div heights for content frame and list sidebar are resized if window is resized
 */
function initScrollHeight() {
    // set height of scrollable divs depending on window size
    $('#scrollable-sections').height($(window).height()-150);
    $('#viewer-page-container').height($(window).height()-92);

    $(window).resize(function() {
        $('#scrollable-sections').height($(window).height()-150)
        $('#viewer-page-container').height($(window).height()-92)
    });
}

/**
 *  @mikegao
 *  initialize jqueryUI tabs
 */
function initTabs() {
    $( "#tabs" ).tabs({
        spinner: "<img src='../assets/images/ajax-loader.gif' />",
        select: function(event, ui) {
            // deselect all lists
            $('#lists li').each(function() {
                $(this).removeClass('selected-list');
            });
            
            resetTabsStates();
            var id = $(ui.tab).closest('li').attr("id").toString();
            $(ui.tab).closest('li').attr("id", id + "-selected");
            
//            id = "#" + id.substring(0, id.indexOf("-tab")) + "-content";
//            alert(id);
        },
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html(
                    "Couldn't load this tab. We'll try to fix this as soon as possible.");
            }
        }
    });
    
    $( '#inbox-tab' ).attr("id", "inbox-tab-selected");     // initial state of home view
    $( "ul.ui-tabs-nav" ).removeClass( "ui-corner-all" ); // gets rid of the round bottom for ui nav bar
    
}

function resetTabsStates() {
    $('#tabs li').each(function() {
        var id = $(this).attr("id").toString();
        if (id.indexOf("-selected") >= 0) {
            $(this).attr("id", id.substring(0, id.indexOf("-selected")));   // remove -selected from id
        }
    });
}

/**
 * @mikegao
 * only applies to 'edit comment' in lists right now. 
 * prevents enter from linking to the form input. instead clicks dialog's submit button
 */
function initEnterDialogForm() {
    $('.no-enter-submit').keypress(function(e){
        if (e.which == 13) {
            e.preventDefault();
        }
    });

     $('#edit-list-comment-dialog').keyup(function(e) {
        if (e.keyCode == 13) {
            $('#edit-comment-submit').trigger('click');
        }
    });
}

/**
 *  @kimhsiao
 *  call perform_search when the search submit is pressed
 *  TODO: Move to search.js
 */
function initSearchAJAX() {
    // initialize placeholder text for search input fields
    var addEventItem = function(elem, type, fn) { // Simple utility for cross-browser event handling
        if (elem.addEventListener) elem.addEventListener(type, fn, false);
        else if (elem.attachEvent) elem.attachEvent('on' + type, fn);
    },
        textField = document.getElementById('search-box'),
        placeholderItem = 'search for...'; // The placeholder text

        addEventItem(textField, 'focus', function() {
            if (this.value === placeholderItem) {
                this.value = '';
//                $(this).css('color', '#000000');
                $(this).addClass('not-placeholder');
                $(this).removeClass('placeholder');
                
            }
        });
        addEventItem(textField, 'blur', function() {
            if (this.value === '') {
                this.value = placeholderItem;
//                $(this).css('color', '#969696');
                $(this).addClass('placeholder');
                $(this).removeClass('not-placeholder');
            }
    });
    
    var addEventLocation = function(elem, type, fn) { // Simple utility for cross-browser event handling
        if (elem.addEventListener) elem.addEventListener(type, fn, false);
        else if (elem.attachEvent) elem.attachEvent('on' + type, fn);
    },
        textField = document.getElementById('search-location'),
        placeholderLocation = 'near...'; // The placeholder text

        addEventLocation(textField, 'focus', function() {
            if (this.value === placeholderLocation) {
                this.value = '';
//                $(this).css('color', '#000000');
                $(this).addClass('not-placeholder');
                $(this).removeClass('placeholder');
            }
        });
        addEventLocation(textField, 'blur', function() {
            if (this.value === '') {
                this.value = placeholderLocation;
//                $(this).css('color', '#969696');
                $(this).addClass('placeholder');
                $(this).removeClass('not-placeholder');
            }
    }); 
    
    // merge kim's search with home shell
    $('#searchform').submit(function() {
        $('#search-content').empty();
        var $inputs = $('#searchform :input');
        var values = {};
        $inputs.each(function() {
            if ($(this).hasClass('placeholder')) {
                values[this.name] = '';
            } else {
                values[this.name] = $(this).val();
            }
        });

        jQuery.post('searchvendors/perform_search', {
            searchLocation: values['searchLocation'],
            searchText: values['searchText']
            }, function(data) {
                var parsedJSON = jQuery.parseJSON(data);
                var results = parsedJSON.searchResults;
                if (results[0] == "error") {
                    var errorType;
                    if (results[1] == "locationError") {
                            errorType = "Error with Location";
                    }

                    // error with the search
                    else if (results[1] == "searchError") {
                        errorType = "Error with Search";
                    }

                    // print out error message
                    switch (results[2]) {
                        case "ZERO_RESULTS":
                            alert(errorType + ": No results were found");
                            break;
                        case "OVER_QUERY_LIMIT":
                            alert(errorType + ": You are over the API query limit");
                            break;
                        case "REQUEST_DENIED":
                            alert(errorType + ": Your request was denied");
                            break;
                        case "INVALID_REQUEST":
                            alert(errorType + ": Your request is invalid");
                            break;
                        default:
                            alert(errorType);
                    }
                }
                else {
                    var vendorData = getVendorData(parsedJSON);
                    displaySearchResults(vendorData);
                }
            });

        $('#search-content').removeClass("ui-tabs-hide");
        $('#inbox-content, #friend-activity-content, #referral-tracking-content, #list-content').addClass("ui-tabs-hide")
        $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active-tab");

        return false;
    });
}

// store all friends upon log on
function getFriends() {
    jQuery.post('searchvendors/get_friends', function(data) {
          var parsedJSON = jQuery.parseJSON(data);
          myUID = parsedJSON.myUID;
          friends = parsedJSON.allFriendsArray;
          allFriends = new Array();
          for (var i = 0; i < friends.length; i++) {
              var oneFriend = new Array();
              oneFriend['uid'] = friends[i][0];
              oneFriend['fbid'] = friends[i][1];
              oneFriend['email'] = friends[i][2];
              oneFriend['firstName'] = friends[i][3];
              oneFriend['lastName'] = friends[i][4];
              allFriends.push(oneFriend);
          }
          displayAutoCompleteResults(allFriends);
     });
}

// TODO: Merge with Andy
// @kimhsiao
// create accordion for search results -- can display many open rows at once
//function bindAccordion() {
//$("#accordion-search").addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
//.find("h3")
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
//}
//
