// kimhsiao

$(document).ready(function() {
    var myUID;
    var allFriends;
    getFriends();
    bindHoverOver();
    bindFuzz();
});

/* functions for $(document).ready */

// when you hover over a button, the text turns pink
function bindHoverOver() {
    $(document).on("mouseenter", ".dialog_link", function() {
        $(this).addClass('ui-state-hover');
    });

    $(document).on("mouseleave", ".dialog_link", function() {
         $(this).removeClass('ui-state-hover');
    });
}

// when you click on the refer button, the background dims
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

// create the refer friends popup box. when you close the box, all values reset to blank
function bindReferDialog() {
    var windowHeight = window.innerHeight;
    var windowWidth = window.innerWidth;

    // Dialog
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

// create the add to list popup box
function bindAddToListDialog() {
    var windowHeight = window.innerHeight;
    var windowWidth = window.innerWidth;

    // Dialog
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

// when you click on the refer button on search results, display pop up
// when you click on refer button in pop up, store referral and vendor information in the database
function bindReferDialogLink(friendList, vendorData) {
    // Dialog Link
    $('.refer-popup-link').click(function(){
        // get id of the vendor, which is the id of the pop up button
        var vendorID = $(this).attr('id');
        var vendorName;
        var vendor;

        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vendorID) {
                vendor = vendorData[i];
            }
        }

        $("#fuzz").fadeIn();
        $('#dialog').dialog("option","title","Refer Friends to " + vendor.name);

        // reset all values when dialog box closes
        $( "#dialog" ).bind( "dialogbeforeclose", function(event, ui) {
                $('#comment-box').val('');
                $('#friends-refer-right').html('');
                $('#tags').val('');
                friendList.length = 0;
                displayAutoCompleteResults(allFriends);

                // fade out dark background
                $("#fuzz").fadeOut();
        });


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

// when you click on the 'add to list' button, show the dialog
function bindAddToListDialogLink(vendorData) {
    $('.add-to-list-popup-link').click(function(){
        // get id of the vendor, which is the id of the pop up
        var vendorID = $(this).attr('id');
        var vendorName;
        var vendor;

        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vendorID) {
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

                                // set selectedList to the lid that was just created
                                addVendorToList(newListData[0].lid, vendor);

                                // add vendor to vendor table
                                addVendorToDB(vendor);
                            }
                    });
                }

                // add vendor to existing list
                else if (selectedList != 'none') {
                    addVendorToList(selectedList, vendor);

                    // add vendor to vendor table
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

// whenever a letter is typed, check which friends have that string included and add to the list of friends to display
function bindAutoComplete() {
    $('#tags').keyup(function() {
        var typedString = document.forms["addFriend"]["friend"].value
        var matchingFriends = [];
        var fullName;

        for (var i = 0; i < allFriends.length; i++) {
            fullName = allFriends[i]['firstName'] + " " + allFriends[i]['lastName'];
            if (fullName.indexOf(typedString) == 0 || allFriends[i]['lastName'].indexOf(typedString) == 0) {
                matchingFriends.push(allFriends[i]);
            }
        }
        displayAutoCompleteResults(matchingFriends);
    });
}

// TODO: fix bug with initial accordion state
// create accordion for search results -- can display many open rows at once
function bindAccordion() {
$("#accordion-search").addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
.find("h3")
        .addClass("ui-accordion-header ui-helper-reset ui-corner-all ui-state-default")
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

/* helper functions */

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
              oneFriend['firstName'] = friends[i][3].toLowerCase();
              oneFriend['lastName'] = friends[i][4].toLowerCase();
              allFriends.push(oneFriend);
          }
     });
}

// retrieve vendor data from google API request for specific search and store results
function getVendorData(parsedJSON) {

    var results = parsedJSON.searchResults;
    var srcLat = parsedJSON.srcLat;
    var srcLng = parsedJSON.srcLng;

// TODO: consider changing array to json object {} - reference bookmarked stackoverflow
    var vendorData = new Array();
    for(var i=0; i<results.length; i++) {
        var singleVendor = new Array();
        var addrComponents;

        // get values if they exist, otherwise set to default value
        singleVendor['name'] = results[i].result.name;
        if (singleVendor['name'] == undefined) {
            singleVendor['name'] = "";
        }
        singleVendor['reference'] = results[i].result.reference;
        if (singleVendor['reference'] == undefined) {
            singleVendor['reference'] = "";
        }
        singleVendor['id'] = results[i].result.id;
        if (singleVendor['id'] == undefined) {
            singleVendor['id'] = "";
        }
        singleVendor['lat'] = results[i].result.geometry.location.lat;
        if (singleVendor['lat'] == undefined) {
            singleVendor['lat'] = 0;
        }
        singleVendor['lng'] = results[i].result.geometry.location.lng;
        if (singleVendor['lng'] == undefined) {
            singleVendor['lng'] = 0;
        }
        singleVendor['phone'] = results[i].result.formatted_phone_number;
        if (singleVendor['phone'] == undefined) {
            singleVendor['phone'] = "";
        }
        singleVendor['addr'] = results[i].result.formatted_address;
        if (singleVendor['addr'] == undefined) {
            singleVendor['addr'] = "";
        }

        singleVendor['addrNum'] = "";
        singleVendor['addrStreet'] = "";
        singleVendor['addrCity'] = "";
        singleVendor['addrState'] = "";
        singleVendor['addrCountry'] = "";
        singleVendor['addrZip'] = "";
        addrComponents = results[i].result.address_components;
        if (addrComponents != undefined) {
            for (var j = 0; j < addrComponents.length; j++) {
                switch (addrComponents[j].types[0]) {
                    case "street_number":
                        singleVendor['addrNum'] = addrComponents[j].short_name;
                        break;
                    case "route":
                        singleVendor['addrStreet'] = addrComponents[j].short_name;
                        break;
                    case "locality":
                        singleVendor['addrCity'] = addrComponents[j].short_name;
                        break;
                    case "administrative_area_level_1":
                        singleVendor['addrState'] = addrComponents[j].short_name;
                        break;
                    case "country":
                        singleVendor['addrCountry'] = addrComponents[j].short_name;
                        break;
                    case "postal_code":
                        singleVendor['addrZip'] = addrComponents[j].short_name;
                        break;
                }
            }
        }

        singleVendor['website'] = results[i].result.website;
        if (singleVendor['website'] == undefined) {
            singleVendor['website'] = "";
        }
        singleVendor['icon'] = results[i].result.icon;
        if (singleVendor['icon'] == undefined) {
            singleVendor['icon'] = "";
        }
        singleVendor['rating'] = results[i].result.rating;
        if (singleVendor['rating'] == undefined) {
            singleVendor['rating'] = 0;
        }
        singleVendor['vicinity'] = results[i].result.vicinity;
        if (singleVendor['vicinity'] == undefined) {
            singleVendor['vicinity'] = "";
        }

        var types = new Array();
        if (results[i].result.types != undefined) {
            for(var j=0; j<results[i].result.types.length; j++) {
                types[j] = results[i].result.types[j];
            }
            singleVendor['types'] = types;
        }

        // add singleVendor to vendorData array
        vendorData[i] = singleVendor;
    }
    return vendorData;
}

// dynamically update displayed list of friends from friend search
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

// display vendor search results in accordion
function displaySearchResults(vendorData) {
    // add search result rows to accordion
    var htmlString = "<div id='accordion-search'>";

    for (var i=0; i<vendorData.length; i++) {
        var addr = vendorData[i].vicinity.split(",");

        htmlString = htmlString +
            "<div>" +
                "<h3><a href='#'>" + vendorData[i].name + "</a></h3>" +
                "<div> <table class='formatted-table'>" +
                    "<tr>" +
                        "<td class='formatted-table-info'>" +
                            addr[0] + "<BR>" + addr[1] + ", " + vendorData[i].addrState + " " + vendorData[i].addrZip +
                        "</td>" +
                        "<td class='formatted-table-button' align='right'>" +
                            "<p><a href='#' id=" + vendorData[i].id + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
                            "<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p>" +
                            "<p><a href='#' id=" + vendorData[i].id + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
                            "<span class='ui-icon ui-icon-plus'></span>Add to List</a></p>" +
                        "</td>" +
                    "</tr>" +
                "</table></div>" +
            "</div>";
    }

    // close accordion div
    htmlString = htmlString + "</div>";

    // fill in content div with search results
    $('#search-content').html(htmlString);

    displayAutoCompleteResults(allFriends);

    // initialize popup box for referring friends to a vendor
    bindReferDialogLink(friendList, vendorData);
    bindAccordion();

    // initialize pop up box for adding vendor to list
    bindAddToListDialog();
    bindAddToListDialogLink(vendorData);
    displayListDropDown();
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

// add vendor to db whenever the vendor is added to a list or is referred to a friend
function addVendorToDB(vendor) {
    jQuery.post('searchvendors/add_vendor', {
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
        // alert error if one occurred
        if (data) {
            alert(data);
        }
    });
}

// from search results, add vendor to an existing or new list
// update database
// update mylists sidebar to reflect new vendor/list
function addVendorToList(lid, vendor) {

    //get date
    var now = new Date();
    now = now.format("yyyy-mm-dd HH:MM:ss");

    // get comment
    var comment = $('#add-to-list-comment-box').val();

    // add vendor to list (new or old) and make it so that list div is updated to show new vendor
    jQuery.post('list_controller/add_vendor_to_list', {
        lid: lid,
        vid: vendor.id,
        date: now,
        comment: comment
    }, function(data) {
        // if there was an error, print out the error
        if (data) {
            alert(data);
        }
        // if there was no error, then vendor was added to list. close the dialog
        else {
            // if the div exists for the list, then add on to the stored data for displaying
            if ($('#list-content-lid--' + lid).length) {
                // get html
                var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());

                // convert vendor array to vendor json object so that it can be stringified (change into json string)
                // because div data is stored in json format
                var vendorObj = {};
                vendorObj.lid = lid;
                vendorObj.vid = vendor.id;
                vendorObj.date = now;
                vendorObj.comment = comment;
                vendorObj.name = vendor.name;
                vendorObj.reference = vendor.reference;
                vendorObj.id = vendor.id;
                vendorObj.lat = vendor.lat.toString();
                vendorObj.lng = vendor.lng.toString();
                vendorObj.phone = vendor.phone;
                vendorObj.addr = vendor.addr;
                vendorObj.addrNum = vendor.addrNum;
                vendorObj.addrStreet = vendor.addrStreet;
                vendorObj.addrCity = vendor.addrCity;
                vendorObj.addrCountry = vendor.addrCountry;
                vendorObj.addrZip = vendor.addrZip;
                vendorObj.vicinity = vendor.vicinity;
                vendorObj.website = vendor.website;
                vendorObj.icon = vendor.icon;
                vendorObj.rating = vendor.rating.toString();
                vendorObj.listsDate = now;
                var newHtmlString = JSON.stringify(vendorObj);

                // add a comma if there is now more than one object
                htmlString = htmlString.slice(0,-1);
                if (htmlString != "[") {
                    htmlString = htmlString + ",";
                }
                htmlString = htmlString + newHtmlString + "]";

                // save new json string to the appropriate list div
                $('#list-content-lid--' + lid).html(htmlString);

            }
            $('#addToListDialog').dialog("close");
        }
    });
}
