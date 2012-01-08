/**
 * @mikegao
 * 01/04/2012
 * 
 * All functions dealing with the list feature:
 *      initGetListContent
 *      initDeleteList
 *      bindDeleteVendorFromList
 *      addContentToAccordionTemplate
 *      getVendorDataList
 *      overrideListAccordionEvent
 *      bindAccordionList
 *      clickAddListButton
 *      bindAddListDialog
 *      bindEditCommentDialog
 *      bindFuzzMike
 *      initAddList
 * 
 */

/**
 * when list is clicked for the first time, list contents are retrieved and stored in HTML.
 * further list clicks retrieve HTML vs. making calls to the database
 */
function initGetListContent() {
    $(document).on("click", ".my-list", function() {
	// parse lid from id
	var lid_string = $(this).attr('id');
	var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var htmlString;

        // check if the specific list content div already exists
	if ($('#list-content-lid--' + lid).length) {
            // list content was already added to the html; display contents in content frame
            htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
            
            //TODO: Merge with Andy
            htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(htmlString));

            // if list is empty, display empty list message
            if (htmlString.length == 0) {
                // added surrounding div for empty content for delete list purposes
                htmlString = "<div id='empty-list-content-lid--" + lid + "'>";
                htmlString = htmlString + jQuery.trim($('#empty-list-content').html());
                htmlString = htmlString + "</div>";
            }
            
            // place list contents in content frame
            $('#list-content').html(htmlString);
                        
//            var parsedJSON = jQuery.parseJSON(jQuery.trim($('#list-content-lid--' + lid).html()));
//            var vendorData = getVendorDataList(parsedJSON);

//            bindReferDialogButton(friendList);
//              bindReferDialogButton(friendList,0,lid,fdfsdf);

            // bind accordion to #list-content
            //TODO: Merge with Andy
            bindAccordionList();
            //TODO: Merge with Andy -- note: stopPropagation prevents by event delegations
            overrideListAccordionEvent();
            //TODO: Fix accordion code with Andy -- no need to rebind if we use event delegations (jquery .on)
            bindDeleteVendorFromList();
	} else {
            jQuery.post('list_controller/get_list_content', {
                lid: lid
            }, function(data) {
                //var parsedJSON = jQuery.parseJSON(data);
		// div does not exist; must add contents to a div
		htmlString = "<div id='list-content-lid--" + lid + "' class='none'>" + data + "</div>";
		// add div under empty-list-content
		$('#empty-list-content').after(htmlString);

		// reset htmlString to html inside of div (does not include class .none)
//		htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
                
                //TODO: Merge with Andy
		htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(data));

		if (htmlString.length == 0) {
                    // added surrounding div for empty content for delete list purposes
                    htmlString = "<div id='empty-list-content-lid--" + lid + "'>";
                    htmlString = htmlString + jQuery.trim($('#empty-list-content').html());
                    htmlString = htmlString + "</div>";
                    //TODO: Do not need duplicate statement below once we move to event delegations
                    $('#list-content').html(htmlString);

                } else {
                    $('#list-content').html(htmlString);
                    // bind accordion to #list-content
                    // from home_kim_js.js
                    
                    //TODO: Merge with Kim -- why is this called here??
                    displayAutoCompleteResults(allFriends);
                    // initialize popup box for referring friends to a vendor
//					var friendList = [];

//					var vendorData = getVendorDataList(parsedJSON);
//					bindReferDialog(friendList);
//					bindAddFriend();
//					bindReferDialogLink(friendList, vendorData);
//                    bindReferDialogButton(friendList);
//					bindAutoComplete();
                    // bind accordion to #list-content
                    //TODO: Merge with Andy
                    bindAccordionList();
                    //TODO: Merge with Andy -- note: stopPropagation prevents by event delegations
                    overrideListAccordionEvent();
                    //TODO: Fix accordion code with Andy -- no need to rebind if we use event delegations (jquery .on)
                    bindDeleteVendorFromList();
		}
            });
        }

        // ensure only the list content is showing and all other tabs are hidden
	$('#list-content').removeClass("ui-tabs-hide");
	$('#inbox-content, #friend-activity-content, #referral-tracking-content, #search-content').addClass("ui-tabs-hide")
    	$('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

        // why do i return false here? am i preventing any 'default aciton' to occur from the click? what other bindings am i affecting? think about it conceptually
//	return false;
    });
}

function initDeleteList() {
    $(document).on("click", ".delete-my-list", function() {
	var lid_string = $(this).attr('id');
	var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);

	// delete list from database
	jQuery.post('list_controller/delete_list', {
            lid: lid
	});

	// delete list from HTML
	$(this).parent().remove();

	// if current content is specified list content, then replace with inbox content
        if (!($('#list-content').hasClass('ui-tabs-hide'))) {
            //bug if list is empty
            var current_lid_string = $('#list-content div:first-child').attr('id');
            var current_lid = current_lid_string.substring(current_lid_string.indexOf('lid--') + 'lid--'.length);
            if (current_lid == lid) {
                $('#inbox-content').removeClass("ui-tabs-hide");
                $('#list-content, #friend-activity-content, #referral-tracking-content, #search-content').addClass("ui-tabs-hide")
                $("#inbox-tab").addClass("ui-tabs-selected ui-state-active");
            }
        }

        // why do i return false here? am i preventing any 'default aciton' to occur from the click? what other bindings am i affecting? think about it conceptually
//	return false;
    });
}

function bindDeleteVendorFromList() {
    // TODO: Currently rebinding each time -- will fix together with Andy
    $('.accordion-remove').on('click', function() {
//    $(document).on("click", ".accordion-remove", function() {        
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

    $('.accordion-edit-comment').on('click', function() {
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

//function getVendorDataList(parsedJSON) {
//    var vendorData = new Array();
//    for (var i=0; i<parsedJSON.length; i++) {
//	var singleVendor = new Array();
//
//	singleVendor['name'] = parsedJSON[i].name;
//	singleVendor['reference'] = parsedJSON[i].reference;
//	singleVendor['id'] = parsedJSON[i].id;
//	singleVendor['lat'] = parsedJSON[i].lat;
//	singleVendor['lng'] = parsedJSON[i].lng;
//	singleVendor['phone'] = parsedJSON[i].phone;
//	singleVendor['addr'] = parsedJSON[i].addr;
//	singleVendor['addrNum'] = parsedJSON[i].addrNum;
//	singleVendor['addrStreet'] = parsedJSON[i].addrStreet;
//	singleVendor['addrCity'] = parsedJSON[i].addrCity;
//	singleVendor['addrState'] = parsedJSON[i].addrState;
//	singleVendor['addrCountry'] = parsedJSON[i].addrCountry;
//	singleVendor['addrZip'] = parsedJSON[i].addrZip;
//	singleVendor['website'] = parsedJSON[i].website;
//	singleVendor['icon'] = parsedJSON[i].icon;
//	singleVendor['rating'] = parsedJSON[i].rating;
//	singleVendor['vicinity'] = parsedJSON[i].vicinity;
//
//	vendorData[i] = singleVendor;
//    }
//
//    return vendorData;
//}

function overrideListAccordionEvent() {
//    $(".no-accordion").click(function(e) {
//        e.stopPropagation();
//    });
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
                // add list to HTML
                var newListData = jQuery.parseJSON(data);
		if (newListData.length == 0) {
                    alert("List was not added successfully");
		} else if (newListData.length > 1) {
                    alert("Multiple lists were returned");
		} else {
//                  alert("lid: " + newListData[0].lid + "and name: " + newListData[0].name);
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