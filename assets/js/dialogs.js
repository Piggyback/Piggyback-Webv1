/* 
 * Document     : dialogs.js
 * Created on   : Jan 4, 2011, 1:03 AM
 * Author       : @kimhsiao
 * Description  :
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
//        var vendor_name = $.trim($(this).parents('a:first').html().split('<')[0]);
        bindReferVendorButton(vid,"vendor_name");
    });
    
    // refer LIST to friends from inbox and from sidebar
    $('body').on('click','.refer-list-popup-link', function() {
//    $('.refer-list-popup-link').on('click', function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        var list_name = jQuery.trim($(this).closest('.list-name-wrapper').find('.list-name').html());
//        var list_name = jQuery.trim($(this).parents('a:first').html().split('<')[0]);
        bindReferListButton(lid, list_name);
    });
    
    // refer list to friends from sidebar
//    $('body').on('click','.refer-my-list', function() {
////    $('.refer-my-list').on('click', function() {
//        var lid_string = $(this).attr('id');
//        var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
//        var list_name = jQuery.trim($(this).prev('.my-list').html());
//        bindReferListButton(lid, list_name);
//    });
    
}

// refer vendor functionality
function bindReferVendorButton(vid, vendor_name) {
    $("#fuzz").fadeIn();
    $('#dialog').dialog("option","title","Refer Friends to " + vendor_name);
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
    $('#dialog').dialog('option', 'title', 'Refer Friends to ' + list_name);
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
        var vendorID = $(this).attr('id');
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
            width: 650,
            height: 465,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: true
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
        var vid = $(this).attr('id');
        var vendor;
        
        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vid) {
                vendor = vendorData[i];
            }
        }
        
        $("#fuzz").fadeIn();
        $('#addToListDialog').dialog("option","title","Add " + vendor.name + " to a List");
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
                                addVendorToList(newListData[0].lid, vid);
                                addVendorToDB(vendor);
                            }
                    });
                }

                // add vendor to existing list
                else if (selectedList != 'none') {
                    addVendorToList(selectedList, vid);
                    addVendorToDB(vendor);
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
//    $('.add-to-list-popup-link').click(function(){
        var vid_string = $(this).attr('id');
        var vid = vid_string.substring(vid_string.indexOf('id--') + 'id--'.length);
        bindAddToList(vid, "singleVendor");
    });
    
    // add list to list -- no vendor data necessary
    $('body').on('click','.add-list-to-list-popup-link', function() {
//    $('.add-list-to-list-popup-link').click(function() {
        var lid_string = $(this).attr('id');
        var lid = lid_string.substring(lid_string.indexOf('id--') + 'id--'.length);
        bindAddToList(lid, "list");
    });
}

function bindAddToList(id, type) {
    $("#fuzz").fadeIn();
    
    $('#addToListDialog').dialog("option","title","Add " + "vendor-name" + " to a List");
    
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
                                addVendorToList(newListData[0].lid, id);
                            }
                            
                            else if (type == "list") {
                                addListToList(newListData[0].lid, id);
                            }
                        }
                });
            }

            // add vendor to existing list
            else if (selectedList != 'none') {
                if (type == "singleVendor") {
                    addVendorToList(selectedList, id);
                }

                else if (type == "list") {
                    addListToList(selectedList, id);
                }
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
            height: 375,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: true,
            beforeClose: function() {
                // reset all values in pop up to blank
                $('#selectList').val('none');
                $('#add-to-new-list').html('');
                $('#add-to-list-comment-box').val('');
                $('#new-list-name').val('');

                // fade out dark background
                $("#fuzz").fadeOut();
            }
    });
}

// display existing lists to add vendor to
function displayListDropDown() {
    var listDropDownHTML = "<b>Add to which list?</b><br>";
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
    $('#selectList').change(function() {
          if($('#selectList').val() == 'addNew') {
              var addNewHTML = "<b>What would you like to name your new list?</b><BR>" +
                               "<input type='text' name='newListName' class='box' id='new-list-name'/><BR><BR>";
              $('#add-to-new-list').html(addNewHTML);
          }
    });
}