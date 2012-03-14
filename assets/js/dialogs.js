/* 
 * Document     : dialogs.js
 * Created on   : Jan 4, 2011, 1:03 AM
 * Author       : @kimhsiao
 * Description  :
 * 
 * 
 * ***** REFER ******
 * bindFuzz     
 * bindReferDialogButton
 * bindReferVendorButton
 * bindReferListbutton
 * clearReferDialogOnClose
 * bindReferDialogButtonFromSearch
 * bindReferDialog
 * bindAutoComplete
 * displayAutoCompleteResults
 * bindAddFriend
 * displayAddedFriends
 * 
 * 
 * ***** ADD TO LIST *******
 * bindAddToListButtonFromSearch
 * bindAddToListButton
 * bindAddToList
 * bindAddToListDialog
 * displayListDropDown
 * bindDropDownChange
 * 
 */



/********************************* ALL DIALOG POP UPS ********************************/

// when you click on a button to bring up a dialog pop up, the background dims
function bindFuzz() {
    $(document).on("click", "#fuzz", function(){
         if($('#dialog').dialog('isOpen')) {
             $('#dialog').dialog("close");
         }
         if($('#addToListDialog').dialog('isOpen')) {
             $('#addToListDialog').dialog("close");
         }
         if($('#confirmDeleteDialog').dialog('isOpen')) {
             $('#confirmDeleteDialog').dialog("close");
         }
         if($('#email-dialog').dialog('isOpen')) {
             $('#email-dialog').dialog("close");
         }
    });
}


/******************************** REFER DIALOG POP UP ********************************/


// when you click on the refer vendor / refer list buttons, display pop up
function bindReferDialogButton() {    
    // refer SINGLE VENDOR from inbox or sidebar
//    $('body').on('click','.refer-popup-link', function() {
    $('.refer-popup-link').click(function() {
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor_name = jQuery.trim($(this).closest('.name-wrapper').find('.vendor-name').html());
        bindReferVendorButton(vid,vendor_name);
    });
    
    // refer LIST to friends from inbox and from sidebar
//    $('body').on('click','.refer-list-popup-link', function() {
    $('.refer-list-popup-link').click(function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        var list_name = jQuery.trim($(this).closest('.name-wrapper').find('.list-name').html()); 
        
        // if referring from inbox, get rid. otherwise, no rid is necessary
        var rid = 0;
        if (!$(this).is('.refer-my-list')) {
            var ridString = $(this).closest('.single-wrapper').next().next().find('.row').attr("id");
            var rid = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);
        }
        bindReferListButton(lid, list_name, rid);

    });
}

// refer vendor functionality
function bindReferVendorButton(vid, vendor_name) {
    $('#dialog').dialog("option","title","Refer to Friends!");
    $('#referLabel').html("Refer <B>\"" + vendor_name + "\"</B> to:")
    
    $('#dialog').dialog("option","buttons", {
        "Refer!": {
            text:'',
            id: 'refer-button-submit',
            click: function() {
                if (friendList.length < 1) {
                    alert("You did not select any friends to refer. Please try again.");
                }
                else {
                    var comment = $('#comment-box').val();

                    // create list of friend uid's to refer to
                    var uidFriendsObj = {};
                    for (var i = 0; i < friendList.length; i++) {
                        uidFriendsObj[i] = friendList[i].uid;
                    }
                    var uidFriendsStr = JSON.stringify(uidFriendsObj);

                    // perform query to add referrals to Referrals and ReferralDetails databases
                    jQuery.post('searchvendors/add_referral',{
                        myUID: myUID,
                        comment: comment,
                        numFriends: friendList.length,
                        uidFriends: uidFriendsStr,
                        id: vid
                    }, function(data) {
                        if (data == "Vendor referral could not be processed") {
                            alert("Vendor referral could not be processed");
                            console.log("add referral failed. fromUID: " + myUID + " vid: " + vid + " toFriends: " + uidFriendsStr);
                        }
                        else {
                            $('#dialog').dialog("close");
                        }
                    });
                }
            }
        }
    });

    $('#dialog').dialog('open');
    return false;
}

// refer list functionality
function bindReferListButton(lid, list_name, rid) {    
    $('#dialog').dialog('option', 'title', 'Refer to Friends!');
    $('#referLabel').html("Refer <B>\"" + list_name + "\"</B> to:")
//    clearReferDialogOnClose(friendList);

    $('#dialog').dialog('option', 'buttons', {
        "Refer!": {
            text: '',
            id: 'refer-button-submit',
            click: function() {
                if (friendList.length < 1) {
                    alert("You did not select any friends to refer. Please try again.");
                } else {
                    var uidFriendsObj = {};
                    for (var i=0; i<friendList.length; i++) {
                        uidFriendsObj[i] = friendList[i].uid;
                    }

                    var uidFriendsStr = JSON.stringify(uidFriendsObj);
                    var comment = $('#comment-box').val();
                    jQuery.post('list_controller/refer_list', {
                        lid: lid,
                        uid: myUID,
                        rid: rid,
                        numFriends: friendList.length,
                        uidFriends: uidFriendsStr,
                        comment: comment
                    }, function(data) {
                        if (data == "List referral could not be processed") {
                            alert("List referral could not be processed");
                            console.log("refer list failed. lid: " + lid + " fromUID: " + myUID + " rid: " + rid + " friendsToReferTo: " + uidFriendsStr + " comment: " + comment);
                        } else if (data == "Cannot refer an empty list!") {
                            alert("Cannot refer an empty list!");
                        } else {
                            $('#dialog').dialog('close');
                        }
                    });
                }
            }
        }
    });

    $('#dialog').dialog('open');
    return false;
}

//function clearReferDialogOnClose(friendList) {
//    // reset all values when dialog box closes
//    $('#dialog').bind('dialogbeforeclose', function(event, ui) {
////        $('#comment-box').val('');
////        $('#friends-refer-right').html('');
////        $('#tags').val('');
////        friendList.length = 0;
////        displayAutoCompleteResults(allFriends);
//
//        // fade out dark background
//        $('#fuzz').fadeOut();
//    });
//}

// store referral and vendor information in the database when referral is made from search page
function bindReferDialogButtonFromSearch(friendList, vendorData) {
    $('.refer-popup-link').click(function() {
        // get id of the vendor, which is the id of the pop up button
        var vid_string = $(this).attr('id');
        var vendorID = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor;

        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vendorID) {
                vendor = vendorData[i];
            }
        }
        
        $('#dialog').dialog("option","title","Refer to Friends!");
        $('#referLabel').html("Refer <B>\"" + vendor.name + "\"</B> to:")

        $('#dialog').dialog("option","buttons", {
            "Refer!": {
                text: '',
                id: 'refer-button-submit',
                click: function() {
                    if (friendList.length < 1) {
                        alert("You did not select any friends to refer. Please try again.");
                    }
                    else {
                        var comment = $('#comment-box').val();

                        // create list of friend uid's to refer to
                        var uidFriendsObj = {};
                        for (var i = 0; i < friendList.length; i++) {
                            uidFriendsObj[i] = friendList[i].uid;
                        }
                        var uidFriendsStr = JSON.stringify(uidFriendsObj);

                        // perform query to add referrals to Referrals and ReferralDetails databases
                        jQuery.post('searchvendors/refer_from_search',{
                            myUID: myUID,
                            comment: comment,
                            numFriends: friendList.length,
                            uidFriends: uidFriendsStr,
                            id: vendor.id, 
                            name: vendor.name,
                            reference: vendor.reference,
                            lat: vendor.lat,
                            lng: vendor.lng,
                            phone: vendor.phone,
                            addr: vendor.addr,
                            addrNum: vendor.addrNum,
                            addrStreet: vendor.addrStreet,
                            addrCity: vendor.addrCity,
                            addrState: vendor.addrState,
                            addrCountry: vendor.addrCountry,
                            addrZip: vendor.addrZip,
                            website: vendor.website,
                            icon: vendor.icon,
                            rating: vendor.rating,
                            vicinity: vendor.vicinity 
                        }, function(data) {
                            if (data == "Vendor referral could not be processed") {
                                alert("Vendor referral could not be processed");
                                console.log("refer from search failed. vid: " + vendorID + " friendsToReferTo: " + uidFriendsStr);
                            }
                            else {
                                $('#dialog').dialog("close");
                            }
                        });
                    }
                }
            }
        });

        $('#dialog').dialog('open');
        return false;
    });
}

// create the refer to friends popup box
function bindReferDialog() {
    $('#dialog').dialog({
            autoOpen: false,
            width: 632,
            height: 390,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: false,
            closeText: '',
            beforeClose: function() {
                $('#fuzz').fadeOut();
            },
            open: function() {
                $("#fuzz").fadeIn();
                
                $('#comment-box').val('');
                $('#friends-refer-right').html('');
                $('#tags').val('');
                friendList.length = 0;
                displayAutoCompleteResults(allFriends);
        
                // change refer button appearance
//                $('.ui-dialog-buttonpane').find('button:first').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
//                $('.ui-dialog-buttonpane').addClass('refer-button button-corner');
                
                // change close button appearance
                $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
            }
    });

    $(document).on("click", ".clear-friend-name", function() {
         $('#tags').val('');
         displayAutoCompleteResults(allFriends);
    });
}

// whenever a letter is typed, find matching friends and display filtered results
function bindAutoComplete() {
    $('#tags').keyup(function() {
        var typedString = document.forms["addFriend"]["friend"].value.toLowerCase();
        var matchingFriends = [];
        var fullName;

        for (var i = 0; i < allFriends.length; i++) {
            fullName = allFriends[i]['firstName'].toLowerCase() + " " + allFriends[i]['lastName'].toLowerCase();
            if (fullName.indexOf(typedString) == 0 || allFriends[i]['lastName'].toLowerCase().indexOf(typedString) == 0) {
                matchingFriends.push(allFriends[i]);
            }
        }
        displayAutoCompleteResults(matchingFriends);
    });
}

// dynamically update displayed list of friends from autocompleted friend search
function displayAutoCompleteResults(matchingFriends) {
    var displayAllFriendsLeft = "<table class='friendTable'>";
    var displayAllFriendsRight = "<table class='friendTable'>";

    for (var i = 0; i < matchingFriends.length; i++) {
        var picURL = "<img src='https://graph.facebook.com/" + matchingFriends[i]['fbid'] + "/picture'\>";
        var fullName = matchingFriends[i]['firstName'] + " " + matchingFriends[i]['lastName'];

        if (i%2 == 0) {
            displayAllFriendsLeft = displayAllFriendsLeft + "<tr><td class='friendPic'>" + picURL + "</td>";
            displayAllFriendsLeft = displayAllFriendsLeft + "<td class='friendName'>" + fullName + "</td></tr>";
        }

        if (i%2 == 1) {
            displayAllFriendsRight = displayAllFriendsRight + "<tr><td class='friendPic'>" + picURL + "</td>";
            displayAllFriendsRight = displayAllFriendsRight + "<td class='friendName'>" + fullName + "</td></tr>";
        }
    }
    displayAllFriendsLeft = displayAllFriendsLeft + "</table>";
    displayAllFriendsRight = displayAllFriendsRight + "</table>";

    $('#friends-refer-display-left').html(displayAllFriendsLeft);
    $('#friends-refer-display-right').html(displayAllFriendsRight);
}

// when you click on a friend in the friend search results, it adds them to the list of people to refer
function bindAddFriend() {
    $(document).on('click', '.friendTable tr', function() {
         var submittedFriend = $(this).html();
         var fbid = submittedFriend.split("/")[3];
         var name = submittedFriend.split(">")[4].split("<")[0];
         var isFriendFlag = 0;

          // make sure added friend is actually a friend and has not been added yet
          for (var i = 0; i < allFriends.length; i++) {
              if (fbid == allFriends[i]['fbid']) {
                  isFriendFlag = 1;

                  if (friendList.indexOf(allFriends[i]) != -1) {
                      alert("You have already added " + name);
                  }
                  else {
                      friendList.splice(0,0,allFriends[i]);
                      displayAddedFriends(friendList);
                  }
                  break;
              }
          }

          // flag was not set because friend was not found with matching name
          if (!isFriendFlag) {
                alert("You are not friends with " + submittedFriend);
          }
         return false;
    });
}

// show friends you have added to refer on side panel of pop up
function displayAddedFriends(friendList) {
    var displayFriends = "<table class='added-friends'>";
    for (var i = 0; i < friendList.length; i++) {
        var picURL = "<img src='https://graph.facebook.com/" + friendList[i]['fbid'] + "/picture'\>";
        displayFriends = displayFriends + "<tr><td>" + picURL + "</td>" +
                "<td class='referredFriend'>" + friendList[i]['firstName'] + " " + friendList[i]['lastName'] +
                "</td><td><img class=\"delete\" src='../assets/images/piggyback_button_close_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_close_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_f1.png'\"></img></td>" +
                "</tr>";
    }
    
    
    displayFriends = displayFriends + "</table>";
    $('#friends-refer-right').html(displayFriends);

    // bind x on added friends to delete row
    $('table tr img.delete').click(function() {
         var friendToRemove = $(this).parent().prev().prev().html();
         var fbid = friendToRemove.split("/")[3];

         $(this).parent().parent().remove();
         var indexOfRemovedFriend;

         for (var i = 0; i < friendList.length; i++) {
             if (fbid == friendList[i]['fbid']) {
                 indexOfRemovedFriend = i;
             }
         }
         friendList.splice(indexOfRemovedFriend,1);
     });
}


/******************************* ADD TO LIST DIALOG POP UP ******************************/

function bindAddToListButtonFromSearch(vendorData) {
    
    $('.add-to-list-popup-link').click(function(){
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor;
        
        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vid) {
                vendor = vendorData[i];
            }
        }
                
        // add comment box for adding to a list from the search
        var showCommentBoxHTML = "<label for='add-to-list-comment-box'>" + 
                                "Add a comment to remember what you like about this place!" +
                             "</label>" + 
                             "<textarea name='addToListComment' id='add-to-list-comment-box'></textarea>";
        $('#add-to-list-comment').html(showCommentBoxHTML);
        $('#addToListDialog').dialog('option', 'height', 245);
        
        displayListDropDown(vendor.name);

        $('#addToListDialog').dialog("option","title","Add to My Lists");
        
        $('#addToListDialog').dialog("option","buttons", {
            "Add!": {
                text: '',
                id: 'add-button-submit',
                click: function() {
                    // get value selected in dropdown and comment
                    var selectedList = $('#selectList').val();
                    var comment = $('#add-to-list-comment-box').val();

                    // create new list if specified, and add vendor to that new list
                    if (selectedList == 'addNew') {
                        var newListName = jQuery.trim($('#new-list-name').val());
                        if (newListName == '') {
                            alert('List name cannot be empty!');
                            
                        }
                        else {
                            jQuery.post('list_controller/add_to_new_list_from_search', {
                                newListName: newListName,
                                uid: myUID,
                                vid: vid,
                                comment: comment,
                                name: vendor.name,
                                reference: vendor.reference,
                                id: vendor.id,
                                lat: vendor.lat,
                                lng: vendor.lng,
                                phone: vendor.phone,
                                addr: vendor.addr,
                                addrNum: vendor.addrNum,
                                addrStreet: vendor.addrStreet,
                                addrCity: vendor.addrCity,
                                addrState: vendor.addrState,
                                addrCountry: vendor.addrCountry,
                                addrZip: vendor.addrZip,
                                website: vendor.website,
                                icon: vendor.icon,
                                rating: vendor.rating,
                                vicinity: vendor.vicinity  
                            }, function(data) {
                                var parsedJSON = jQuery.parseJSON(data);
                                if (parsedJSON == "Could not create list. Please try again!") {
                                    alert("Could not create list. Please try again!");
                                    console.log("add to new list from search failed. newListName: " + newListName + " myUID: " + myUID + " vid: " + vid + " comment: " + comment);
                                } else if (parsedJSON == "List already exists!") {
                                    alert("List already exists!");
                                } else {
                                    var newListData = parsedJSON.newList;
                                    var vendorObj = parsedJSON.vendor;      
                                    var lid = newListData[0].lid;
                                    var listName = newListData[0].name;
                                    
                                    // refresh sidebar that displays your lists
                                    updateListPanelHTML(lid, listName);

                                    // if the div exists for the list, then add on to the stored data for displaying
                                    updateListDiv(lid, vendorObj);
                                    if (!$('#no-list-message').hasClass('none')) {
                                        $('#no-list-message').addClass('none');
                                    }
                                    $('#addToListDialog').dialog("close");
                                }
                            });
                        }
                    }

                    // add vendor to existing list
                    else if (selectedList != 'none') {
                        jQuery.post('list_controller/add_to_existing_list_from_search', {
                                vid: vid,
                                lid: selectedList,
                                comment: comment,
                                name: vendor.name,
                                reference: vendor.reference,
                                id: vendor.id,
                                lat: vendor.lat,
                                lng: vendor.lng,
                                phone: vendor.phone,
                                addr: vendor.addr,
                                addrNum: vendor.addrNum,
                                addrStreet: vendor.addrStreet,
                                addrCity: vendor.addrCity,
                                addrState: vendor.addrState,
                                addrCountry: vendor.addrCountry,
                                addrZip: vendor.addrZip,
                                website: vendor.website,
                                icon: vendor.icon,
                                rating: vendor.rating,
                                vicinity: vendor.vicinity  
                            }, function(data) {
                                var vendorObj = jQuery.parseJSON(data);
                                if (vendorObj == "Could not add to list. Please try again!") {
                                    alert("Could not add to list. Please try again!");
                                    console.log("add to existing list from search failed. vid: " + vid + " lid: " + selectedList);
                                } else if (vendorObj == "List already exists!") {
                                        alert("List already exists!");
                                } else {
                                    updateListDiv(selectedList, vendorObj);
                                    $('#addToListDialog').dialog("close");
                                }
                          });
                    }

                    // no list was selected from dropdown
                    else {
                        alert("Please select a list");
                    }
                }
            }
        });

        $('#addToListDialog').dialog('open');
        return false;
    });
}


// add vendor to database if adding to list from search
function bindAddToListButton() {
    // add vendor to list -- get vendor data on specific vendor and pass through
//    $('body').on('click','.add-to-list-popup-link', function() {
    $('.add-to-list-popup-link').click(function() {
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor_name = jQuery.trim($(this).closest('.name-wrapper').find('.vendor-name').html());
        var comment = jQuery.trim($(this).closest('.name-wrapper').find('.comment-wrapper').html());
        bindAddToList(vid, vendor_name, "singleVendor", comment, null);
    });
    
    // add list to list -- no vendor data necessary
//    $('body').on('click','.add-list-to-list-popup-link', function() {
    $('.add-list-to-list-popup-link').click(function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        var list_name = jQuery.trim($(this).closest('.name-wrapper').find('.list-name').html());
        var comment = jQuery.trim($(this).closest('.name-wrapper').find('.referral-comment').html());
        var ridString = $(this).closest('.single-wrapper').next().next().find('.row').attr("id");
        var rid = ridString.substring(ridString.indexOf('rid--') + 'rid--'.length);
        bindAddToList(lid, list_name, "list", comment, rid);
    });
}

function bindAddToList(id, name, type, comment, rid) {    
    // dont need comment box for adding to a list from a referral
    $('#add-to-list-comment').html('');
    $('#addToListDialog').dialog('option', 'height', 135);
    displayListDropDown(name);
    
    $('#addToListDialog').dialog("option","title","Add to My Lists");
    
    $('#addToListDialog').dialog("option","buttons", {
        "Add!": {
            text: '',
            id: 'add-button-submit',
            click: function() {

                // get value selected in dropdown and comment
                var selectedList = $('#selectList').val();

                // create new list if specified, and add vendor to that new list
                if (selectedList == 'addNew') {
                    var newListName = jQuery.trim($('#new-list-name').val());
                    if (newListName == '') {
                        alert('List name cannot be empty!');
                    }
                    else {
                        if (type == "singleVendor") {
                            jQuery.post('list_controller/add_vendor_to_new_list_from_nonsearch', {
                                newListName: newListName,
                                uid: myUID,
                                vid: id,
                                comment: comment                      
                            }, function(data) {
                                    var parsedJSON = jQuery.parseJSON(data);
                                    if (parsedJSON == "Could not create list. Please try again!") {
                                        alert("Could not create list. Please try again!");
                                        console.log("add vendor to new list from nonsearch failed. newListName: " + newListName + " uid: " + myUID + " vid: " + id + " comment: " + comment);
                                    } else if (parsedJSON == "List already exists!") {
                                        alert("List already exists!");
                                    } else {
                                        if (!$('#no-list-message').hasClass('none')) {
                                            $('#no-list-message').addClass('none');
                                        }
                                        var newListData = parsedJSON.newList;
                                        var vendorObj = parsedJSON.vendor;      
                                        var lid = newListData[0].lid;
                                        var listName = newListData[0].name;

                                        // refresh sidebar that displays your lists
                                        updateListPanelHTML(lid, listName);

                                        // if the div exists for the list, then add on to the stored data for displaying
                                        updateListDiv(lid, vendorObj);
                                        
                                        $('#addToListDialog').dialog("close");
                                    }
                                });
                        } else if (type == "list") {
                            jQuery.post('list_controller/add_list_to_new_list_from_nonsearch', {
                                newListName: newListName,
                                uid: myUID,
                                lid: id,
                                rid: rid
                            }, function(data) {
                                var newLid = jQuery.parseJSON(data);
                                if (newLid == "Could not create list. Please try again!") {
                                    alert("Could not create list. Please try again!");
                                    console.log("add list to new list from search failed. newListName: " + newListName + " uid: " + myUID + " lidToBeAddedToNewList: " + id + "rid: " + rid);
                                } else if (newLid == "List already exists!") {
                                    alert("List already exists!");
                                }
                                else {
                                    if (!$('#no-list-message').hasClass('none')) {
                                        $('#no-list-message').addClass('none');
                                    }
                                    updateListPanelHTML(newLid, newListName);
                                    $('#addToListDialog').dialog("close");
                                }
                            });
                        }
                    }
                }

                // add vendor to existing list
                else if (selectedList != 'none') {
                    if (type == "singleVendor") {
                        addVendorToList(selectedList, id, comment);
                    }

                    else if (type == "list") {
                            jQuery.post('list_controller/add_list_to_existing_list', {
                                innerLid: id,
                                outerLid: selectedList,
                                rid: rid
                            }, function(data) {
                                var vendorObj = jQuery.parseJSON(data);
                                if (vendorObj == "Could not add to list. Please try again!") {
                                    alert("Could not add to list. Please try again!");
                                    console.log("add list to existing list failed. listToAdd: " + innerLid + " listToAddTo: " + outerLid + " rid: " + rid);
                                } else {
                                    if ($('#list-content-lid--' + selectedList).length) {
                                        $('#list-content-lid--' + selectedList).html(data);
                                    }
                                }
                            });
                    }

                    $('#addToListDialog').dialog("close");
                }

                // no list was selected from dropdown
                else {
                    alert("Please select a list");
                }
            }
        }
    });

    $('#addToListDialog').dialog('open');
    return false;
}

function updateListPanelHTML(newLid, newListName) {
    var htmlString = "<li class='my-list-wrapper name-wrapper'><img src='../assets/images/piggyback_button_close_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_close_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_close_f1.png'\" id='delete-my-list-lid--" + newLid + "' class='delete-my-list'></img>";
    htmlString = htmlString + "<span id='my-list-lid--" + newLid + "' class='my-list list-name'>" + newListName + "</span>";
    htmlString = htmlString + "<span class='refer-my-list-wrapper'><img src='../assets/images/piggyback_button_refer_small_f1.png' onmouseover=\"this.src='../assets/images/piggyback_button_refer_small_f2.png'\" onmouseout=\"this.src='../assets/images/piggyback_button_refer_small_f1.png'\" id='refer-my-list-lid--" + newLid + "' class='refer-my-list refer-list-popup-link'></img></span></li>";
    $('#lists').append(htmlString);

    bindReferDialogButton();
}

function updateListDiv(lid, vendor) {
    if ($('#list-content-lid--' + lid).length) {
        // get existing html that is stored in div
        var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());

        // get new html to add to div
        var newHtmlString = JSON.stringify(vendor);
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

// create the add to list popup box
function bindAddToListDialog() {
    $('#addToListDialog').dialog({
            autoOpen: false,
            width: 350,
//            height: 120,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: false,
            closeText: '',
            beforeClose: function() {
                $("#fuzz").fadeOut();
            },
            open: function() {
                // clear contents
                $('#add-to-new-list').html('');
                $('#selectList').val('none');
                $('#add-to-list-comment-box').val('');
                $('#new-list-name').val('');
               
                $("#fuzz").fadeIn();
                
                // change add button appearance
//                $('.ui-dialog-buttonpane').find('button:first').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
//                $('.ui-dialog-buttonpane').addClass('add-button button-corner');

                // change close button appearance
                $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
            }
    });
    
//    $('body').on('hover','.add-button',function(){
//        $('add-button').removeClass('ui-state-hover');
//    });
    
}

// display existing lists to add vendor to
function displayListDropDown(name) {
    var listDropDownHTML = "Add <b>\"" + name + "\"</b> to a list:</b><br>";
    listDropDownHTML = listDropDownHTML + "<select id='selectList'>" +
            "<option value='none'></option>" +
            "<option value='addNew'>Add to new list</option>";

    // pull list names from side panel
    var myListsHTML = $('#lists').html();
    // TODO: change display of existing lists depending on format of HTML on sidebar display of lists
    var myLists = myListsHTML.split("id=\"my-list-lid--");

    var listName;
    var lid;
    var temp;
    for (var i = 1; i < myLists.length; i++) {
        lid = myLists[i].split("\"")[0];
        listName = myLists[i].split(">")[1].split("<")[0];
        listDropDownHTML = listDropDownHTML + "<option value='" + lid + "'>" + listName + "</option>";
    }

    listDropDownHTML = listDropDownHTML + "</select>";

    $('#add-to-existing-list').html(listDropDownHTML);
    bindDropDownChange();
}

// display div for adding new list if you select to add vendor to a new list
function bindDropDownChange() {
    var origHeight = $('#addToListDialog').dialog('option','height');
    
    $('#selectList').change(function() {
          if($('#selectList').val() == 'addNew') {
              $('#addToListDialog').dialog('option', 'height', origHeight+65);
              var addNewHTML = "Name your new list:<BR>" +
                               "<input type='text' name='newListName' class='box' id='new-list-name'/>";
              $('#add-to-new-list').html(addNewHTML);
          }
          else {
              $('#add-to-new-list').html('');
              $('#addToListDialog').dialog('option', 'height', origHeight);
          }

    });
}

/******************************* DELETE DIALOG POP UP ******************************/

function bindDeleteDialog() {
    $('#confirmDeleteDialog').dialog({
        autoOpen: false,
        width: 350,
        height: 110,
        closeOnEscape: true,
        show: 'drop',
        hide: 'drop',
        resizable: false,
        closeText: '',
        title: 'Delete?',
        open: function() {
            $("#fuzz").fadeIn();
            // change add button appearance
//                $('.ui-dialog-buttonpane').find('button:first').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
//                $('.ui-dialog-buttonpane').find('button:last').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
            $('.ui-dialog-buttonpane').addClass('delete-button button-corner');

            // change close button appearance
            $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
        },
        beforeClose: function() {
            $("#fuzz").fadeOut();
        }
    });
}

/******************************** BUG SUBMISSION POP UP ************************************/
function bindEmailDialog() {
    $('#email-dialog').dialog({
        autoOpen: false,
        width: 300,
        height: 270,
        closeOnEscape: true,
        show: 'drop',
        hide: 'drop',
        resizable: false,
        closeText: '',
        title: 'Report a Bug',
        open: function() {
            $("#fuzz").fadeIn();
            
            // change close button appearance
            $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
        },
        beforeClose: function() {
            // say thank you!
            $("#fuzz").fadeOut();
            
            // empty form
            $("#email-content-body").val("");
        },
        close: function() {
            $("#email-dialog-form").removeClass("none");
            $("#email-confirmation-alert").addClass("none");
        }
    })
}

function bindEmailButton() {
    $(document).on("click", "#email-button", function () {
        $("#email-dialog").dialog("open");
    });
    $(document).on("click", "#email-submit-button", function () {
        var body = $("#email-content-body").val().trim();
        
        // check to make sure there is no empty values
        if ( body != "")  {
            $('#email-dialog').dialog("close");
            jQuery.post("home/send_email", {
                senderBody: body
            });
        } else {
            alert ('Description cannot be empty.');
        }
    });
    
    $(document).on("focus", "#email-content-body", function () {
        $("#empty-email-alert").addClass("none");
    });
    
    $(document).on("blur", "#email-content-body", function () {
        if( $("#email-content-body").val().trim() == "" ) {
            $("#empty-email-alert").removeClass("none");
        }
    });
    
}


/****************************** see who has liked this button pop up *********************************/
function initWhoLikesButton() {
    $(".number-of-likes").click(function() {
        var ridString = $(this).closest(".referral-item-wrapper").find(".accordion-footer").find(".row").attr("id");
        var rid = ridString.substring(ridString.indexOf("--") + "--".length);
        var likeNum = $(this).find(".number-of-likes-inner").html();
        var whoLikesDialogElem = "#like-list-dialog--" + rid;
        
        if (likeNum > 0) {
            $(whoLikesDialogElem).dialog("open");
        }
        
    });
}

function initWhoLikesDialog() {
    $(".like-dialog-modeless").dialog({
//        disabled:true,
        autoOpen: false,
        width: 300,
        height: 270,
        closeOnEscape: true,
        show: 'drop',
        hide: 'drop',
        resizable: false,
        closeText: '',
        modal: false,
        title: 'People who liked this recommendation',
        open: function() {
            
        },
        beforeClose: function() {
            
        },
        close: function() {
        }
    })
}