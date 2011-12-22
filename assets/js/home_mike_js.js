/**
    Document   : home_mike_js.php
    Created on : Dec 2, 2011, 6:43:47 PM
    Author     : gaobi
    Description:
        all javascript code for home page is here
*/
/**
   TO-DOs:
*/


$(document).ready(function() {
    initEnterForm();
    setScrollHeight();
    initTabs();
    initHoverTabs();
	getListContent();
	initDeleteList();
    friendList = [];
    bindAddFriend();
    bindReferDialog();
	bindAutoComplete();
	bindAddListDialog();
    bindEditCommentDialog();
	bindFuzzMike();
	initAddList();
    searchAJAX();

    bindReferDialogList(friendList);
});

function initEnterForm() {
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

/* functions for $(document).ready */
function setScrollHeight() {
    // set height of scrollable divs depending on window size
    $('#scrollable-sections').height($(window).height()-150);
    $('#viewer-page-container').height($(window).height()-92);

    $(window).resize(function() {
        $('#scrollable-sections').height($(window).height()-150)
        $('#viewer-page-container').height($(window).height()-92)
    });
}

function initTabs() {
    $( "#tabs" ).tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html(
                    "Couldn't load this tab. We'll try to fix this as soon as possible.");
            }
        }
    });
}

function initHoverTabs() {
    $('ul#icons li').hover(
        function() { $(this).addClass('ui-state-hover'); },
        function() { $(this).removeClass('ui-state-hover'); }
    );
}

function bindReferDialogList(friendList) {
    $('.refer-my-list').click(function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var list_name = jQuery.trim($(this).prev('.my-list').html());
        $('#fuzz').fadeIn();
        $('#dialog').dialog('option', 'title', 'Refer Friends to ' + list_name);

        displayAutoCompleteResults(allFriends);
        // reset all values when dialog box closes
        $('#dialog').bind('dialogbeforeclose', function(event, ui) {
            $('#comment-box').val('');
            $('#friends-refer-right').html('');
            $('#tags').val('');
            friendList.length = 0;
            displayAutoCompleteResults(allFriends);

            // fade out dark background
            $('#fuzz').fadeOut();
        });

        $('#dialog').dialog('option', 'buttons', {
            "Refer!": function() {
                if (friendList.length < 1) {
                    alert("You did not select any friends to refer. Please try again.");
                } else {
                    var now = new Date();
                    now = now.format("yyyy-mm-dd HH:MM:ss");
                    var uidFriendsObj = {};
                    var friendNum;
                    for (var i=0; i<friendList.length; i++) {
                        friendNum = "friend" + i.toString();
                        uidFriendsObj[friendNum] = friendList[i].uid;
                    }

                    var uidFriendsStr = JSON.stringify(uidFriendsObj);
                    jQuery.post('list_controller/refer_list', {
                        lid: lid,
                        uid: myUID,
                        numFriends: friendList.length,
                        uidFriends: uidFriendsStr,
                        date: now,
                        comment: $('#comment-box').val()
                    }, function() {
                        $('#dialog').dialog('close');
                    });
                }
            }
        });

        $('#dialog').dialog('open');
        return false;
    });
}

function getListContent() {
	$(document).on("click", ".my-list", function() {
//	$(document).on("click", "#lists li", function() {
 		// load list content when clicked
		// parse lid from id
		var lid_string = $(this).attr('id');
		var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
		var htmlString;

		// check if the specific list content div already exists
		if ($('#list-content-lid--' + lid).length) {
			// list content was already added to the html; display in div
			htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
			htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(htmlString));

			if (htmlString.length == 0) {
				htmlString = jQuery.trim($('#empty-list-content').html());
			}
			$('#list-content').html(htmlString);

                        var parsedJSON = jQuery.parseJSON(jQuery.trim($('#list-content-lid--' + lid).html()));
                        var vendorData = getVendorDataList(parsedJSON);

                        bindReferDialogLink(friendList, vendorData);


			// bind accordion to #list-content
			bindAccordionList();
            overrideListAccordionEvent();
            initDeleteVendorFromList();
		} else {
			jQuery.post('list_controller/get_list_content', {
				lid: lid
			}, function(data) {
				var parsedJSON = jQuery.parseJSON(data);
				// div does not exist; must add contents to a div
				htmlString = "<div id='list-content-lid--" + lid + "' class='none'>" + data + "</div>";

				// add div under empty-list-content
				$('#empty-list-content').after(htmlString);

				// reset htmlString to html inside of div (does not include class .none)
				htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
				htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(htmlString));

				if (htmlString.length == 0) {
					htmlString = jQuery.trim($('#empty-list-content').html());
					$('#list-content').html(htmlString);

				} else {
					$('#list-content').html(htmlString);
					// bind accordion to #list-content
					// from home_kim_js.js
					displayAutoCompleteResults(allFriends);
					// initialize popup box for referring friends to a vendor
//					var friendList = [];
					var vendorData = getVendorDataList(parsedJSON);
//					bindReferDialog(friendList);
//					bindAddFriend();
					bindReferDialogLink(friendList, vendorData);
//					bindAutoComplete();
					bindAccordionList();
                    overrideListAccordionEvent();
                    initDeleteVendorFromList();
				}
			});
		}

		$('#list-content').removeClass("ui-tabs-hide");
	    $('#inbox-content, #ui-tabs-1, #ui-tabs-2, #search-content').addClass("ui-tabs-hide")
    	$('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

		return false;
	});
}

function initDeleteList() {
	$(document).on("click", ".delete-my-list", function() {
		var lid_string = $(this).attr('id');
		var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);

		//alert("delete " + lid);
		// delete list from database
		jQuery.post('list_controller/delete_list', {
			lid: lid
		});

		// delete list from HTML
		$(this).parent().remove();

		// if current content is specified list content, then replace with inbox content (for now)
		$('#inbox-content').removeClass("ui-tabs-hide");
	    $('#list-content, #ui-tabs-1, #ui-tabs-2, #search-content').addClass("ui-tabs-hide")
		$("#inbox-tab").addClass("ui-tabs-selected ui-state-active");
    //	$('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");


		return false;
	});
}

function initDeleteVendorFromList() {
    $('.accordion-remove').click(function() {
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('vid--') + 'vid--'.length);
        var lid_string = $(this).closest(".accordion-list").attr("id");
        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);

        // delete vendor from database
        jQuery.post('list_controller/delete_vendor_from_list', {
            lid: lid,
            vid: vid
        });

        // delete vendor from HTML
        $(this).closest(".accordion-list-header").parent().remove();

        // delete vendor from div
        if ($('#list-content-lid--' + lid).length) {
            var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
            var parsedJSON = jQuery.parseJSON(htmlString);
//            parsedJSON.splice(row,1);
//            delete parsedJSON[row];
            for (var i=0; i<parsedJSON.length; i++) {
                if (parsedJSON[i].vid == vid) {
                    parsedJSON.splice(i,1);
                }
            }
            var json_text = JSON.stringify(parsedJSON, null, 2);
            $('#list-content-lid--' + lid).html(json_text);
        }

        // what if list is empty? redirect to list is empty text
        if (parsedJSON.length == 0) {
			$('#list-content').html(jQuery.trim($('#empty-list-content').html()));
        }

        return false;
    });

    $('.accordion-edit-comment').click(function() {
        var editCommentObj = $(this);
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('vid--') + 'vid--'.length);
        var lid_string = $(this).closest(".accordion-list").attr("id");
        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var vendorName = jQuery.trim($(this).prev().prev().prev(".list-vendor-name").html());

        $('#fuzz').fadeIn();
        $('#edit-list-comment-dialog').dialog('option', 'title', 'Edit comment for ' + vendorName);

        $('#edit-list-comment-dialog').dialog('option', 'buttons', {
            "Submit": {
                text: "Submit",
                id: 'edit-comment-submit',
                click: function() {
                    var newComment = jQuery.trim($('.edit-list-comment-value').val());
                    // change comment in database
                    jQuery.post('list_controller/edit_vendor_comment', {
                        lid: lid,
                        vid: vid,
                        newComment: newComment
                    }, function() {
                        // change comment in HTML
                        editCommentObj.prev('.vendor-list-comment').html(newComment);
                        var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
                        var parsedJSON = jQuery.parseJSON(htmlString);
                        for (var i=0; i<parsedJSON.length; i++) {
                            if (parsedJSON[i].vid == vid) {
                                parsedJSON[i].comment = newComment;
                            }
                        }
                        var json_text = JSON.stringify(parsedJSON, null, 2);
                        $('#list-content-lid--' + lid).html(json_text);

                        $('#edit-list-comment-dialog').dialog('close');
                    });
                }
            }
        });

        $('#edit-list-comment-dialog').dialog('open');

        return false;
    });
}

function addContentToAccordionTemplate(lid, parsedJSON) {
	if (parsedJSON.length == 0) {
		return "";
	} else {
		var htmlString = "<div id='accordion-list-lid--" + lid + "' class='accordion-list'>";
		for (var i=0; i<parsedJSON.length; i++) {
			htmlString = htmlString +
			"<div>" +
				"<div id='accordion-list-header-row--" + i + "' class='accordion-list-header'>" +
                    "<a href='#' class='accordion-list-anchor'>" +
                        "<div class='list-vendor-name'>" +
                            parsedJSON[i].name +
                        "</div>" +
                        "<div id='accordion-remove-vid--" + parsedJSON[i].vid + "' class='accordion-remove no-accordion'>" +
                            "remove" +
                        "</div>" +
                        "<div class='vendor-list-comment'>" + parsedJSON[i].comment + "</div>" +
                        "<div id='accordion-edit-comment-vid--" + parsedJSON[i].vid + "' class='accordion-edit-comment no-accordion'>" +
                            "edit comment" +
                        "</div>" +
                    "</a>" +
                "</div>" +
				"<div> <table class='formatted-table'>" +
					"<tr>" +
						"<td class='formatted-table-info'>" +
							parsedJSON[i].addrNum + " " + parsedJSON[i].addrStreet + "<br>" +
							parsedJSON[i].addrCity + " " + parsedJSON[i].addrState + " " + parsedJSON[i].addrZip + "<br>" +
							parsedJSON[i].phone +
						"</td>" +
						"<td class='formatted-table-button' align='right'>" +
							"<p><a href='#' id=" + parsedJSON[i].vid + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
							"<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p>" +
						"</td>" +
					"</tr>" +
				"</table></div>" +
			"</div>";
		}

		// close accordion div
		htmlString = htmlString + "</div>";

		return htmlString;
	}

	return;
}

function getVendorDataList(parsedJSON) {
	var vendorData = new Array();
	for (var i=0; i<parsedJSON.length; i++) {
		var singleVendor = new Array();

		singleVendor['name'] = parsedJSON[i].name;
		singleVendor['reference'] = parsedJSON[i].reference;
		singleVendor['id'] = parsedJSON[i].id;
		singleVendor['lat'] = parsedJSON[i].lat;
		singleVendor['lng'] = parsedJSON[i].lng;
		singleVendor['phone'] = parsedJSON[i].phone;
		singleVendor['addr'] = parsedJSON[i].addr;
		singleVendor['addrNum'] = parsedJSON[i].addrNum;
		singleVendor['addrStreet'] = parsedJSON[i].addrStreet;
		singleVendor['addrCity'] = parsedJSON[i].addrCity;
		singleVendor['addrState'] = parsedJSON[i].addrState;
		singleVendor['addrCountry'] = parsedJSON[i].addrCountry;
		singleVendor['addrZip'] = parsedJSON[i].addrZip;
		singleVendor['website'] = parsedJSON[i].website;
		singleVendor['icon'] = parsedJSON[i].icon;
		singleVendor['rating'] = parsedJSON[i].rating;
		singleVendor['vicinity'] = parsedJSON[i].vicinity;

		vendorData[i] = singleVendor;
	}

	return vendorData;
}

function overrideListAccordionEvent() {
    $(".no-accordion").click(function(e) {
        e.stopPropagation();
    });
}

function bindAccordionList() {
	$('.accordion-list').addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
		.find(".accordion-list-header")
	    .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all")
	    .prepend('<span class="ui-icon ui-icon-triangle-1-e"/>')
	    .click(function() {
		    $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
				    .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
					.find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
			        .end().next().toggle().toggleClass("ui-accordion-content-active");
			return false;
		})
		.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();
}

function clickAddListButton() {
	$("#fuzz").fadeIn();
	$('#add-list-dialog').dialog("option", "title", "Create new list!");
	$('#add-list-dialog').dialog('open');

	return false;
}

// create the refer friends popup box. when you close the box, all values reset to blank
function bindAddListDialog() {
    var windowHeight = window.innerHeight;
    var windowWidth = window.innerWidth;

    // Dialog
    $('#add-list-dialog').dialog({
            autoOpen: false,
            width: 400,
            height: 200,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: true,
            beforeClose: function() {
              	// reset all values in pop up to blank
				$('#add-list-name').val('');

                // fade out dark background
                $("#fuzz").fadeOut();
            }
    });
}

function bindEditCommentDialog() {
    var windowHeight = window.innerHeight;
    var windwWidth = window.innerWidth;

    $('#edit-list-comment-dialog').dialog({
        autoOpen: false,
        width: 400,
        height: 200,
        closeOnEscape: true,
        show: 'drop',
        hide: 'drop',
        resizable: true,
        beforeClose: function() {
            $('.edit-list-comment-value').val('');

            $('#fuzz').fadeOut();
        }
    });
}

function bindFuzzMike() {
	$(document).on("click", "#fuzz", function() {
		if($('#add-list-dialog').dialog('isOpen')) {
			$('#add-list-dialog').dialog("close");
		}
	});

    $(document).on('click', '#fuzz', function() {
        if($('#edit-list-comment-dialog').dialog('isOpen')) {
            $('#edit-list-comment-dialog').dialog('close');
        }
    });
}

function initAddList() {
	$('#add-list-submit').click(function() {
		var newListName = jQuery.trim($('#add-list-name').val());
		if (newListName == "") {
			alert('List name cannot be empty!');
		} else {
			// retrieve current uid
			var uid = jQuery.trim($('#current-uid').html());
			// add list to DB
                        $('#add-list-dialog').dialog("close");
			jQuery.post("list_controller/add_list", {
				newListName: newListName,
				uid: uid
			}, function(data) {
				//alert(data);
				// add list to HTML
				var newListData = jQuery.parseJSON(data);
				if (newListData.length == 0) {
					alert("List was not added successfully");
				} else if (newListData.length > 1) {
					alert("Multiple lists were returned");
				} else {
//					alert("lid: " + newListData[0].lid + "and name: " + newListData[0].name);
                    var htmlString = "<li class='my-list-wrapper'><span id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'>x</span>";
                    htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list'>" + newListData[0].name + "</span></li>";
		//			var htmlString = "<li id='my-list-lid--" + newListData[0].lid + "'>" + newListData[0].name + "</li>";
					$('#lists').append(htmlString);
					$('#add-list-dialog').dialog("close");
				}
			});
		}

		return false;
	});
}

function searchAJAX() {
    // merge kim's search with home shell
    $('#searchform').submit(function() {
        var $inputs = $('#searchform :input');
        var values = {};
        $inputs.each(function() {
            values[this.name] = $(this).val();
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
        $('#inbox-content, #ui-tabs-1, #ui-tabs-2, #list-content').addClass("ui-tabs-hide")
        $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

        return false;
    });
}

function loadReferralTracking() {
    jQuery.post('referral_tracking/get_referral_tracking', function(data){
        var parsedJSON = jQuery.parseJSON(data);

        displayReferralTracking(parsedJSON);
    });
}

function displayReferralTracking(parsedJSON) {
    var htmlString = "<div id='referral-tracking'><div id='accordion-referral-tracking' class='accordion-object'>";
    var likeNumber = 0;
    var likeStatus = "";

    for (var i=0; i<parsedJSON.length; i++) {
        likeNumber = parsedJSON[i].LikesList['LikesList'].length;
        if (likeNumber > 0) {
            if (likeNumber == 1) {
                likeNumber = likeNumber + " person likes this.";
            } else {
                likeNumber = likeNumber + " people like this.";
            }
        } else {
            likeNumber = "";
        }

        if (parsedJSON[i].alreadyLiked==1) {
            likeStatus = "Unlike";
        } else {
            likeStatus = "Like";
        }

        htmlString = htmlString +
            "<div class='inbox-single-wrapper accordion-header'>" +
                "<div class='referral-date'>" +
                    parsedJSON[i].refDate +
                "</div>" +
                "<a>" +
                    parsedJSON[i].name +
                "</a>" +
            "</div>";

    //    $('#referral-tracking-content').html(htmlString);
    }
    htmlString = htmlString + "</div></div>";
    $('#referral-tracking-content').html(htmlString);

    bindAccordionInbox();
    overrideAccordionEvent();
}

function fbAPI() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '251920381531962',
            status     : true,
            cookie     : true,
            xfbml      : true
        });

        // If user is not logged in, redirect user to login page
        FB.getLoginStatus(function(response) {
            if (response.status != "connected") {
                // logged in and connected user
                window.location = "http://192.168.11.28/login";
            } else {
                // do nothing
            }
        });

        $('#logout').click(function () {
            //logout when div is clicked
            FB.logout(function(response) {
                // user is now logged out of service AND facebook
                // return to login page
                window.location = "http://192.168.11.28/login";
            });
        });

    };
    (function(d){
        var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
}
