/**
 * @mikegao
 * 01/04/2012
 * 
 * All functions dealing with the list feature:
 *      initAddList
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

function initClickAddList() {
    $('#add-list-button').click(function() {
        $("#fuzz").fadeIn();
        $('#add-list-dialog').dialog("option", "title", "Create new list!");
        $('#add-list-dialog').dialog('open');
    });
}

/**
 * when list is clicked for the first time, list contents are retrieved and stored in HTML.
 * further list clicks retrieve HTML vs. making calls to the database
 */
function initGetListContent() {
    $(document).on("click", ".my-list", function() {
        // remove .selected-list class from all other lists
        $('#lists li').each(function() {
            $(this).removeClass('selected-list');
        });
        
        $(this).parent().addClass('selected-list');
        
	// parse lid from id
	var lid_string = $(this).attr('id');
	var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var htmlString;
        var parsedJSON;

        // check if the specific list content div already exists
	if ($('#list-content-lid--' + lid).length) {
            // list content was already added to the html; display contents in content frame
            htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
            
            //TODO: Merge with Andy
//            htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(htmlString));

            // if list is empty, display empty list message
            parsedJSON = jQuery.parseJSON(htmlString);
            if (parsedJSON.length == 0) {
                // added surrounding div for empty content for delete list purposes
                htmlString = "<div id='empty-list-content-lid--" + lid + "'>";
                htmlString = htmlString + jQuery.trim($('#empty-list-content').html());
                htmlString = htmlString + "</div>";
                //TODO: Do not need duplicate statement below once we move to event delegations
                $('#list-content').html(htmlString);
//                $(htmlString).appendTo('accordion-list');
            } else {
                displayListItems(parsedJSON, 'list', lid);
            }
            
            // place list contents in content frame
//            $('#list-content').html(htmlString);


            // bind accordion to #list-content
            //TODO: Merge with Andy
//            bindAccordionList();
            //TODO: Merge with Andy -- note: stopPropagation prevents by event delegations
//            overrideListAccordionEvent();
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

                
                //TODO: Merge with Andy
//		htmlString = addContentToAccordionTemplate(lid, jQuery.parseJSON(data));

                parsedJSON = jQuery.parseJSON(data);
		if (parsedJSON.length == 0) {
                    // added surrounding div for empty content for delete list purposes
                    htmlString = "<div id='empty-list-content-lid--" + lid + "'>";
                    htmlString = htmlString + jQuery.trim($('#empty-list-content').html());
                    htmlString = htmlString + "</div>";
                    //TODO: Do not need duplicate statement below once we move to event delegations
                    $('#list-content').html(htmlString);
//                    $(htmlString).appendTo('accordion-list');
                } else {
                    displayListItems(parsedJSON, 'list', lid);
                }
//
//                } else {
//                    $('#list-content').html(htmlString);
                    // bind accordion to #list-content
                    // from home_kim_js.js
                    
                    //TODO: Merge with Kim -- why is this called here??
                    displayAutoCompleteResults(allFriends);

                    // bind accordion to #list-content
                    //TODO: Merge with Andy
//                    bindAccordionList();
                    //TODO: Merge with Andy -- note: stopPropagation prevents by event delegations
//                    overrideListAccordionEvent();
                    //TODO: Fix accordion code with Andy -- no need to rebind if we use event delegations (jquery .on)
                    bindDeleteVendorFromList();
//		}
            });
        }

        // ensure only the list content is showing and all other tabs are hidden
        resetTabsStates();
	$('#list-content').removeClass("ui-tabs-hide");
	$('#inbox-content, #friend-activity-content, #referral-tracking-content, #search-content').addClass("ui-tabs-hide")
    	$('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

        // why do i return false here? am i preventing any 'default aciton' to occur from the click? what other bindings am i affecting? think about it conceptually
//	return false;
    });
}

function initDeleteList() {
    $(document).on("click", ".delete-my-list", function() {
        
        $("#fuzz").fadeIn();
        $('#confirmDeleteDialog').dialog('open');

	var lid_string = $(this).attr('id');
	var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        
        // to delete list from HTML
        var list_wrapper = $(this).parent();
 
        $('#confirmDeleteDialog').dialog('option','buttons', {
            "Delete": {
                text: '',
                id: 'delete-button',
                click: function() {
                    $( this ).dialog( "close" );
                    jQuery.post('list_controller/delete_list', {
                        lid: lid
                    });

                    list_wrapper.remove();
                    
                    if ($('#lists').find('li').length == 0) {
                        $('#no-list-message').removeClass('none');
                    }

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
        
//        
//	var lid_string = $(this).attr('id');
//	var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);

	// delete list from database
//	jQuery.post('list_controller/delete_list', {
//            lid: lid
//	});
//
//	// delete list from HTML
//	$(this).parent().remove();
//
//	// if current content is specified list content, then replace with inbox content
//        if (!($('#list-content').hasClass('ui-tabs-hide'))) {
//            //bug if list is empty
//            var current_lid_string = $('#list-content div:first-child').attr('id');
//            var current_lid = current_lid_string.substring(current_lid_string.indexOf('lid--') + 'lid--'.length);
//            if (current_lid == lid) {
//                $('#inbox-content').removeClass("ui-tabs-hide");
//                $('#list-content, #friend-activity-content, #referral-tracking-content, #search-content').addClass("ui-tabs-hide")
//                $("#inbox-tab").addClass("ui-tabs-selected ui-state-active");
//            }
//        }

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
//        var lid_string = $(this).closest(".accordion-list").attr("id");
//        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var lid = $(this).closest("#accordion-list").find("#accordion-list-lid").html();
        var accordionRow = $(this).closest(".single-wrapper");
//        var now = new Date();
//        now = now.format("yyyy-mm-dd HH:MM:ss");
        
        $('#confirmDeleteDialog').dialog('open');


        $('#confirmDeleteDialog').dialog('option','buttons', {
            "Delete": {
                text: '',
                id: 'delete-button',
                click: function() {
                    $( this ).dialog( "close" );
                    
                    jQuery.post('list_controller/delete_vendor_from_list', {
                        lid: lid,
                        vid: vid
                    }, function() {
                        accordionRow.next().remove();
                        accordionRow.remove();

                        // delete vendor from div
                        if ($('#list-content-lid--' + lid).length) {
                            var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
                            var parsedJSON = jQuery.parseJSON(htmlString);

                            for (var i=0; i<parsedJSON.length; i++) {
                                if (parsedJSON[i].vid == vid) {
                                    parsedJSON.splice(i,1);
                                }
                            }
                            var json_text = JSON.stringify(parsedJSON, null, 2);
                            $('#list-content-lid--' + lid).html(json_text);

                            // what if list is empty? redirect to list is empty text
                            if (parsedJSON.length == 0) {
                                $('#list-content').html(jQuery.trim($('#empty-list-content').html()));
                            }
                        }
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



        // delete vendor from database
//        jQuery.post('list_controller/delete_vendor_from_list', {
//            lid: lid,
//            vid: vid
////            now: now
//        });

        // delete vendor from HTML
//        $(this).closest(".single-wrapper").next().next().remove();
        
//        $(this).closest(".single-wrapper").next().remove();
//        $(this).closest(".single-wrapper").remove();
//
//        // delete vendor from div
//        if ($('#list-content-lid--' + lid).length) {
//            var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
//            var parsedJSON = jQuery.parseJSON(htmlString);
//
//            for (var i=0; i<parsedJSON.length; i++) {
//                if (parsedJSON[i].vid == vid) {
//                    parsedJSON.splice(i,1);
//                }
//            }
//            var json_text = JSON.stringify(parsedJSON, null, 2);
//            $('#list-content-lid--' + lid).html(json_text);
//            
//            // what if list is empty? redirect to list is empty text
//            if (parsedJSON.length == 0) {
//                $('#list-content').html(jQuery.trim($('#empty-list-content').html()));
//            }
//        }

        return false;
    });

    $('.accordion-edit-comment').on('click', function() {
        var editCommentObj = $(this);
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('vid--') + 'vid--'.length);
//        var lid_string = $(this).closest(".accordion-list").attr("id");
//        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
        var lid = $(this).closest('#accordion-list').find('#accordion-list-lid').html();
        var vendorName = jQuery.trim($(this).closest('.name-wrapper').find('.vendor-name').html());
        if (vendorName.length > 16) {
            vendorName = vendorName.substr(0,14) + "...";
        }

        $('#fuzz').fadeIn();
        $('#edit-list-comment-dialog').dialog('option', 'title', 'Edit comment for ' + vendorName);

        $('#edit-list-comment-dialog').dialog('option', 'buttons', {
            "Submit": {
                text: "",
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
                        var commentPrompt = "Add Comment";
                        var commentHTML = "<span class='comment-wrapper'>" + newComment + "</span>";
                        if (newComment != "") {
                            commentPrompt = "Edit Comment";
                            commentHTML = "<q class='comment-wrapper'>" + newComment + "</q>";
                        }
                        editCommentObj.prev('.vendor-list-comment').html(commentHTML);
                        editCommentObj.html(commentPrompt);
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
            "<div class='name-wrapper'>" +
                "<div id='accordion-list-header-row--" + i + "' class='accordion-list-header'>" +
                    "<a href='#' class='accordion-list-anchor'>" +
                        "<div class='vendor-name'>" +
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
                            parsedJSON[i].addr + "<br>" +
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

// Andy removed the need for this function. 1/24/2012
function bindAccordionList() {
    
//    $('.accordion-list').addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
//                        .find(".accordion-list-header")
//                        .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all")
//                        .prepend('<span class="ui-icon ui-icon-triangle-1-e"/>')
//                        .click(function() {
//                            $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
//                                   .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
//                                   .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
//			           .end().next().toggle().toggleClass("ui-accordion-content-active");
//                            return false;
//                        })
//                        .next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();
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
            width: 350,
            height: 145,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: false,
            closeText: '',
            beforeClose: function() {
              	// reset all values in pop up to blank
				$('#add-list-name').val('');

                // fade out dark background
                $("#fuzz").fadeOut();
            },
            open: function() {
                // change refer button appearance
                $('.ui-dialog-buttonpane').find('button:contains("Refer!")').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
                $('.ui-dialog-buttonpane').find('button:contains("Refer!")').text('');
                $('.ui-dialog-buttonpane').addClass('refer-button button-corner');
                
                // change close button
                $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');

            }
    });
}

function bindEditCommentDialog() {
    var windowHeight = window.innerHeight;
    var windwWidth = window.innerWidth;

    $('#edit-list-comment-dialog').dialog({
        autoOpen: false,
        width: 350,
        height: 140,
        closeOnEscape: true,
        show: 'drop',
        hide: 'drop',
        resizable: false,
        closeText: '',
        open: function() {
            // change add button appearance
            $('.ui-dialog-buttonpane').find('button:first').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
            $('.ui-dialog-buttonpane').addClass('add-button button-corner');

            // change close button appearance
            $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
        },
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
            var uid = jQuery.trim($('#current-uid').html());
            // add list to DB
            jQuery.post("list_controller/add_list", {
                newListName: newListName,
		uid: uid
            }, function(data) {
                var newListData = jQuery.parseJSON(data);
		if (newListData.length == 0) {
                    alert("List was not added successfully");
		} else if (newListData == "List already exists!") {
                    alert("List already exists!");
                } else if (newListData == "Could not add list") {
                    alert("Could not add list");
                } else {
                    if (!$('#no-list-message').hasClass('none')) {
                        $('#no-list-message').addClass('none');
                    }
                    // add list to sidebar HTML
                    var htmlString = "<li class='my-list-wrapper name-wrapper'><img src='../assets/images/piggyback_button_close_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_close_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_f1.png'\" id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'></img>";
                    htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list list-name'>" + newListData[0].name + "</span>";
                    htmlString = htmlString + "<span class='refer-my-list-wrapper'><img src='../assets/images/piggyback_button_refer_small_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_refer_small_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_refer_small_f1.png'\" id='refer-my-list-lid--" + newListData[0].lid + "' class='refer-my-list refer-list-popup-link'></img></span></li>";
                    $('#lists').append(htmlString);
                    $('#add-list-dialog').dialog("close");
                    
                    bindReferDialogButton();
		}
            });
        }

    return false;
    });
}

//// add list to list -- put lid1 into lid2
//// use rid to get timestamp to add list as it was when referred
//function addListToList(lid2, lid1, rid) {
//    // turn lid into an array, right now it is just an integer
//    jQuery.post('list_controller/get_add_to_list_content', {
//        lid: lid1,
//        rid: rid
//    }, function(data) {
//        var parsedJSON = jQuery.parseJSON(data);
//        for (var i = 0; i < parsedJSON.length; i++ ) {
//            addVendorToList(lid2, parsedJSON[i].vid, parsedJSON[i].comment);
//        }
//    });
//}


// update database and update mylists sidebar to reflect new vendor/list
function addVendorToList(lid, vid, comment) {

    // add vendor to list (new or old) and make it so that list div is updated to show new vendor
    jQuery.post('list_controller/add_vendor_to_list', {
        lid: lid,
        vid: vid,
        comment: comment
    }, function(data) {
        var vendorObj = jQuery.parseJSON(data);
        
        if (vendorObj == "Could not add to list") {
            alert("Could not add to list");
        } else if (vendorObj == "Already in list") {
            alert("Already in list");
        }
        else {
            // if the div exists for the list, then add on to the stored data for displaying
            if ($('#list-content-lid--' + lid).length) {                
                // get existing html that is stored in div
                var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
                
                // get new html to add to div
                var newHtmlString = JSON.stringify(vendorObj);
                newHtmlString = newHtmlString.slice(1,-1);

                // add a comma if there is now more than one object
                htmlString = htmlString.slice(0,-1);
                if (htmlString != "[") {
                    htmlString = htmlString + ",";
                }
                htmlString = htmlString + newHtmlString + "]";

                // save new json string to the appropriate list div
                $('#list-content-lid--' + lid).html(htmlString);

            }
        }
    });
}