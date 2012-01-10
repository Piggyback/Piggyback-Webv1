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

$(document).ready(function() {
    var myUID;
    var allFriends;
    getFriends();
    bindFuzz();

    // initialize and bind 'refer' dialog
    friendList = [];
    bindAddFriend();
    bindAutoComplete();
    bindReferDialog();
    bindReferDialogButton();
    
    // initialize and bind 'add to list' dialog
    bindAddToListDialog();
    bindAddToListButton();
});


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
    });
}


/******************************** REFER DIALOG POP UP ********************************/


// when you click on the refer vendor / refer list buttons, display pop up
function bindReferDialogButton() {    
    // refer SINGLE VENDOR from inbox or sidebar
    $('body').on('click','.refer-popup-link', function() {
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor_name = jQuery.trim($(this).closest('.name-wrapper').find('.vendor-name').html());
        bindReferVendorButton(vid,vendor_name);
    });
    
    // refer LIST to friends from inbox and from sidebar
    $('body').on('click','.refer-list-popup-link', function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        var list_name = jQuery.trim($(this).closest('.name-wrapper').find('.list-name').html());
        bindReferListButton(lid, list_name);
    });
}

// refer vendor functionality
function bindReferVendorButton(vid, vendor_name) {
    $("#fuzz").fadeIn();
    $('#dialog').dialog("option","title","Refer to Friends!");
    $('#referLabel').html("Refer <B>\"" + vendor_name + "\"</B> to your friends:")
    clearReferDialogOnClose(friendList);
    
    $('#dialog').dialog("option","buttons", {
        "Refer!": function() {
            if (friendList.length < 1) {
                alert("You did not select any friends to refer. Please try again.");
            }
            else {
                var now = new Date();
                now = now.format("yyyy-mm-dd HH:MM:ss");
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
                    date: now,
                    comment: comment,
                    numFriends: friendList.length,
                    uidFriends: uidFriendsStr,
                    id: vid
                }, function(data) {
                    if (data) {
                        alert(data);
                    }
                    else {
                        $('#dialog').dialog("close");
                    }
                });
            }
        }
    });

    $('#dialog').dialog('open');
    return false;
}

// refer list functionality
function bindReferListButton(lid, list_name) {
    $('#fuzz').fadeIn();
    
    $('#dialog').dialog('option', 'title', 'Refer to Friends!');
    $('#referLabel').html("Refer <B>\"" + list_name + "\"</B> to your friends:")
    clearReferDialogOnClose(friendList);

    $('#dialog').dialog('option', 'buttons', {
        "Refer!": function() {
            if (friendList.length < 1) {
                alert("You did not select any friends to refer. Please try again.");
            } else {
                var now = new Date();
                now = now.format("yyyy-mm-dd HH:MM:ss");
                var uidFriendsObj = {};
                for (var i=0; i<friendList.length; i++) {
                    uidFriendsObj[i] = friendList[i].uid;
                }

                var uidFriendsStr = JSON.stringify(uidFriendsObj);
                jQuery.post('list_controller/refer_list', {
                    lid: lid,
                    uid: myUID,
                    numFriends: friendList.length,
                    uidFriends: uidFriendsStr,
                    date: now,
                    comment: $('#comment-box').val()
                }, function(data) {
                    if (data) {
                        alert(data);
                    }
                    else {
                        $('#dialog').dialog('close');
                    }
                });
            }
        }
    });

    $('#dialog').dialog('open');
    return false;
}

function clearReferDialogOnClose(friendList) {
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
}

// store referral and vendor information in the database when referral is made from search page
function bindReferDialogButtonFromSearch(friendList, vendorData) {
    $('.refer-popup-link').click(function() {
        // get id of the vendor, which is the id of the pop up button
        var vid_string = $(this).attr('id');
        var vendorID = vid_string.substring(vid_string.indexOf('vid--') + 'vid--'.length);
        var vendor;

        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vendorID) {
                vendor = vendorData[i];
            }
        }
        
        $("#fuzz").fadeIn();
        $('#dialog').dialog("option","title","Refer Friends to " + vendor.name);
        clearReferDialogOnClose(friendList);

        $('#dialog').dialog("option","buttons", {
            "Refer!": function() {
                if (friendList.length < 1) {
                    alert("You did not select any friends to refer. Please try again.");
                }
                else {
                    var now = new Date();
                    now = now.format("yyyy-mm-dd HH:MM:ss");
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
                        date: now,
                        comment: comment,
                        numFriends: friendList.length,
                        uidFriends: uidFriendsStr,
                        id: vendor.id
                    }, function(data) {
                        if (data) {
                            alert(data);
                        }
                        else {
                            addVendorToDB(vendor);
                            $('#dialog').dialog("close");
                        }
                    });
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
            height: 380,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: true,
            closeText: '',
            open: function() {
                // change refer button appearance
                $('.ui-dialog-buttonpane').find('button:contains("Refer!")').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
                $('.ui-dialog-buttonpane').find('button:contains("Refer!")').text('');
                $('.ui-dialog-buttonpane').addClass('refer-button button-corner');
                
                // change close button appearance
//                $('.ui-dialog-titlebar-close').removeClass('ui-dialog-titlebar-close ui-corner-all').addClass('dialog-close');
                $('.ui-dialog-titlebar-close').removeClass('ui-corner-all');
                $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick');
//                $('.ui-dialog-titlebar').find('.ui-icon').removeClass('ui-icon ui-icon-closethick').addClass('close-button');
            }
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
    var displayFriends = "<table>";
    for (var i = 0; i < friendList.length; i++) {
        var picURL = "<img src='https://graph.facebook.com/" + friendList[i]['fbid'] + "/picture'\>";
        displayFriends = displayFriends + "<tr><td>" + picURL + "</td>" +
                "<td class='referredFriend'>" + friendList[i]['firstName'] + " " + friendList[i]['lastName'] +
                "</td><td><img class=\"delete\" src='../../assets/jquery-ui-1.8.16.custom/css/custom-theme/images/del.png'/></td>" +
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
        var vid = vid_string.substring(vid_string.indexOf('vid--') + 'vid--'.length);
        var vendor;
        
        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vid) {
                vendor = vendorData[i];
            }
        }
        
        $("#fuzz").fadeIn();
        
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
            "Add!": function() {
                // get value selected in dropdown and comment
                var selectedList = $('#selectList').val();
                var comment = $('#add-to-list-comment-box').val();


                // create new list if specified, and add vendor to that new list
                if (selectedList == 'addNew') {
                    var newListName = $('#new-list-name').val();
                    jQuery.post('list_controller/add_list', {
                        newListName: newListName,
                        uid: myUID
                    }, function(data) {
                        var newListData = jQuery.parseJSON(data);
                            if (newListData.length == 0) {
                                alert("List was not added successfully");
                            } else if (newListData.length > 1) {
                                alert("Multiple lists were returned");
                            } else {
                                // refresh sidebar that displays your lists
                                var htmlString = "<li class='my-list-wrapper'><span id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'>x</span>";
                                htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list'>" + newListData[0].name + "</span></li>";                                $('#lists').append(htmlString);

                                // add single vendor or list to new list
                                addVendorToList(newListData[0].lid, vid, comment);
                                addVendorToDB(vendor);
                                $('#addToListDialog').dialog("close");

                            }
                    });
                }

                // add vendor to existing list
                else if (selectedList != 'none') {
                    addVendorToList(selectedList, vid, comment);
                    addVendorToDB(vendor);
                    $('#addToListDialog').dialog("close");
                }

                // no list was selected from dropdown
                else {
                    alert("Please select a list");
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
    $('body').on('click','.add-to-list-popup-link', function() {
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        var vendor_name = jQuery.trim($(this).closest('.name-wrapper').find('.vendor-name').html());
        var comment = jQuery.trim($(this).closest('.name-wrapper').find('.referral-comment').html());
        bindAddToList(vid, vendor_name, "singleVendor", comment, null);
    });
    
    // add list to list -- no vendor data necessary
    $('body').on('click','.add-list-to-list-popup-link', function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        var list_name = jQuery.trim($(this).closest('.name-wrapper').find('.list-name').html());
        var comment = jQuery.trim($(this).closest('.name-wrapper').find('.referral-comment').html());
        var ridString = jQuery.trim($(this).closest('.name-wrapper').find('.referrals-remove-button').attr('id'));
        var rid = ridString.substring(ridString.indexOf('id--') + 'id--'.length);
        bindAddToList(lid, list_name, "list", comment, rid);
    });
}

function bindAddToList(id, name, type, comment, rid) {
    $("#fuzz").fadeIn();
    
    // dont need comment box for adding to a list from a referral
    $('#add-to-list-comment').html('');
    $('#addToListDialog').dialog('option', 'height', 140);
    displayListDropDown(name);
    
    $('#addToListDialog').dialog("option","title","Add to My Lists");
    
    $('#addToListDialog').dialog("option","buttons", {
        "Add!": function() {
            // get value selected in dropdown and comment
            var selectedList = $('#selectList').val();

            // create new list if specified, and add vendor to that new list
            if (selectedList == 'addNew') {
                var newListName = $('#new-list-name').val();
                jQuery.post('list_controller/add_list', {
                    newListName: newListName,
                    uid: myUID
                }, function(data) {
                    var newListData = jQuery.parseJSON(data);
                        if (newListData.length == 0) {
                            alert("List was not added successfully");
                        } else if (newListData.length > 1) {
                            alert("Multiple lists were returned");
                        } else {
                            // refresh sidebar that displays your lists
                            var htmlString = "<li class='my-list-wrapper'><span id='delete-my-list-lid--" + newListData[0].lid + "' class='delete-my-list'>x</span>";
                            htmlString = htmlString + "<span id='my-list-lid--" + newListData[0].lid + "' class='my-list'>" + newListData[0].name + "</span></li>";                                $('#lists').append(htmlString);

                            // add single vendor or list to new list
                            if (type == "singleVendor") {
                                addVendorToList(newListData[0].lid, id, comment);
                            }
                            
                            else if (type == "list") {
                                addListToList(newListData[0].lid, id, rid);
                            }
                            
                            $('#addToListDialog').dialog("close");
                        }
                });
            }

            // add vendor to existing list
            else if (selectedList != 'none') {
                if (type == "singleVendor") {
                    addVendorToList(selectedList, id, comment);
                }

                else if (type == "list") {
                    addListToList(selectedList, id, rid);
                }
                
                $('#addToListDialog').dialog("close");
            }

            // no list was selected from dropdown
            else {
                alert("Please select a list");
            }
        }
    });

    $('#addToListDialog').dialog('open');

    return false;
}

// create the add to list popup box
function bindAddToListDialog() {
    $('#addToListDialog').dialog({
            autoOpen: false,
            width: 350,
//            height: 200,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: false,
            closeText: '',
            open: function() {
                // change add button appearance
                $('.ui-dialog-buttonpane').find('button:contains("Add!")').removeClass('ui-button ui-widget ui-state-default ui-button-text-only ui-corner-all');
                $('.ui-dialog-buttonpane').find('button:contains("Add!")').text('');
                $('.ui-dialog-buttonpane').addClass('add-button button-corner');

                // change close button appearance
            }
    });
    
    $('body').on('hover','.add-button',function(){
        $('add-button').removeClass('ui-state-hover');
    });
    
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
    $('#addToListDialog').dialog({
            beforeClose: function() {
                // reset all values in pop up to blank
                $('#selectList').val('none');
                $('#add-to-new-list').html('');
                $('#add-to-list-comment-box').val('');
                $('#new-list-name').val('');
                $('#addToListDialog').dialog('option', 'height', origHeight);

                // fade out dark background
                $("#fuzz").fadeOut();
            }
    });
    
    $('#selectList').change(function() {
          if($('#selectList').val() == 'addNew') {
              $('#addToListDialog').dialog('option', 'height', origHeight+50);
              var addNewHTML = "Name your new list:<BR>" +
                               "<input type='text' name='newListName' class='box' id='new-list-name'/>";
              $('#add-to-new-list').css('padding-top','8px');
              $('#add-to-new-list').html(addNewHTML);
          }
          else {
              $('#add-to-new-list').html('');
              $('#add-to-new-list').css('padding-top','0px');
              var height = $('#addToListDialog').dialog('option','height');
              $('#addToListDialog').dialog('option', 'height', origHeight);
          }
    });
}