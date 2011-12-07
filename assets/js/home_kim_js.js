// kimhsiao

$(document).ready(function() {
    var myUID;
    var allFriends;
    getFriends();
    bindHoverOver();
    bindFuzz();
});

/* functions for $(document).ready */
function bindHoverOver() {
    $(document).on("mouseenter", ".dialog_link", function() {
        $(this).addClass('ui-state-hover');
    });
    
    $(document).on("mouseleave", ".dialog_link", function() {
         $(this).removeClass('ui-state-hover');
    });
}

function bindFuzz() {
    $(document).on("click", "#fuzz", function(){
         $('#dialog').dialog("close"); 
    });
}

function bindDialog(friendList) {    
    // Dialog			
    $('#dialog').dialog({
            autoOpen: false,
            width: 750,
            closeOnEscape: true,
            show: 'drop',
            hide: 'drop',
            resizable: false,
            beforeClose: function() {
                // reset all values in pop up to blank
                document.getElementById("explanation").value = '';
                document.getElementById("addedFriends").innerHTML = '';
                document.getElementById("tags").value = '';
                friendList.length = 0;

                // fade out dark background
                $("#fuzz").fadeOut();  
            }
    });
}

function bindAddFriendSubmit(friendList) {
    $(document).on("submit", "#addFriend", function(){
          var submittedFriend = document.forms["addFriend"]["friend"].value
          var submittedFriendFormatted = submittedFriend.toLowerCase();
          document.forms["addFriend"]["friend"].value = '';
          var fullName = "";
          var isFriendFlag = 0;
          var addedAlreadyFlag = 0;

              // set autocomplete list
              // TODO: autocomplete not working
    //              $('#tags').autocomplete("option","source", friendTags);

              // make sure added friend is actually a friend and has not been added yet
              for (var i = 0; i < allFriends.length; i++) {
                  fullName = allFriends[i]['firstName'] + " " + allFriends[i]['lastName'];

                  if (fullName == submittedFriendFormatted) {
                      isFriendFlag = 1;

                      if (friendList.indexOf(allFriends[i]) != -1) {
                          alert("You have already added " + submittedFriend);
                      }
                      else {
                          friendList.push(allFriends[i]);
                          displayAddedFriends(friendList);
                      }
                  }
              }

              // flag was not set because friend was not found with matching name
              if (!isFriendFlag) {
                    alert("You are not friends with " + submittedFriend);
              }
         return false;
     });
     
     $(document).on("click", "table tr img.delete", function() {
         var friendNameToRemove = $(this).parent().prev().html(); 
         alert(friendNameToRemove);
         $(this).parent().parent().remove();
         var indexOfRemovedFriend;
         
         for (var i = 0; i < friendList.length; i++) {
             if (friendList[i]['name'] == friendNameToRemove) {
                 indexOfRemovedFriend = i;
             }
         }
         friendList.splice(indexOfRemovedFriend,1);         
     });
}

function bindDialogLink(friendList, vendorData) {
    // Dialog Link
    $('.dialog_link').click(function(){
        
        // get id of the vendor, which is the id of the pop up button
        var vendorID = $(this).attr('id');
        var vendorName;

        for(var i = 0; i < vendorData.length; i++) {
            if (vendorData[i].id == vendorID) {
                vendor = vendorData[i];
            }
        }

        $("#fuzz").fadeIn();  
        $('#dialog').dialog("option","title","Refer Friends to " + vendor.name);

        $('#dialog').dialog("option","buttons", {
            "Refer!": function() { 
                if (friendList.length < 1) {
                    alert("You did not select any friends to refer. Please try again.");
                }
                else {
                    // create query for inserting row
                    var addReferralQuery = "INSERT INTO Referrals VALUES ";
                    for (var i = 0; i < friendList.length; i++){
                        var now = new Date();
                        now = now.format("yyyy-mm-dd HH:MM:ss");
                        var comment = document.getElementById('explanation').value;
                        var friendUID = friendList[i]['uid'];
                        addReferralQuery = addReferralQuery + "(NULL," + myUID + "," + friendUID + ",\"" + now + "\",0,\"" + comment + "\"),";
                        alert(addReferralQuery);
                }
                    addReferralQuery = addReferralQuery.slice(0,-1);

                    // perform query to add referrals to database
                    jQuery.post('searchvendors/add_referral',{
                        q: addReferralQuery,
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
                    }, function() {
                        $('#dialog').dialog("close");
                    });
                }
            }
        });

        $('#dialog').dialog('open');
        return false;
    });
}

function bindAutoComplete() {

    // create json object with friend data for auto complete
    var autoCompleteSource = [];
    var fullName;
    for (var i = 0; i < allFriends.length; i++) {
        fullName = allFriends[i]['firstName'] + " " + allFriends[i]['lastName'];
        autoCompleteSource.push({value: allFriends[i]['uid'], label: fullName, email: allFriends[i]['email'], icon: '../../assets/images/mgao.jpg'});
    }
    
    $("#tags").autocomplete({
            minLength: 0,
            source: autoCompleteSource,
            focus: function( event, ui ) {
                    $( "#tags" ).val(ui.item.label);
                    return false;
            },
            select: function( event, ui ) {
                    $( "#tags" ).val(ui.item.label);
                    return false;
            }
    })
    .data( "autocomplete" )._renderItem = function( ul, item ) {
            return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a><div><img class='autoCompleteImg' style='width:15%; float:left;' src='" + item.icon + "'><div class='autoCompleteDiv' style='width:80%; float:right; font-size:12px;'><B>" + item.label + "</b><br>" + item.email + "</div></div></a>" )
                    .appendTo( ul );
    };
}

/* helper functions */
function getFriends() {
    jQuery.post('searchvendors/get_friends', function(data) {
          var parsedJSON = jQuery.parseJSON(data);
          //var friendTags = parsedJSON.friendTags;
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

function getVendorData(parsedJSON) {

    var results = parsedJSON.searchResults;
    var srcLat = parsedJSON.srcLat;
    var srcLng = parsedJSON.srcLng;

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

function displaySearchResults(vendorData) {
    // add rows to accordion
    var htmlString = "<div id='accordion-search'>";

    for (var i=0; i<vendorData.length; i++) {
        var addr = vendorData[i].vicinity.split(",");

        htmlString = htmlString + 
            "<div>" + 
                "<h3><a href='#'>" + vendorData[i].name + "</a></h3>" +
                "<div> <table class='formatted-table'>" + 
                    "<td class='formatted-table-info'>" +
//                    vendorData[i].addrNum + " " + vendorData[i].addrStreet + "</br>" +
//                    vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip + "</br>" +
//                    vendorData[i].phone + "</td>" +
                    addr[0] + "<BR>" + addr[1] + ", " + vendorData[i].addrState + " " + vendorData[i].addrZip +
                    "<td class='formatted-table-button' align='right'>" +
                    "<p><a href='#' id=" + vendorData[i].id + " class='dialog_link ui-state-default ui-corner-all'>" +
                    "<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p>" + 
                    "</td>" + 
                "</table></div>" + 
            "</div>";
    }

    // close accordion div
    htmlString = htmlString + "</div>";

    // create pop up div
    htmlString = htmlString + 
        "<div id='dialog'>" +
            "<div id='popup-widget' class='ui-widget'>" +
                "<form id='addFriend' method='post'>" +
                    "<label for='tags'><B>Search for friends to refer</b><BR></label>" +
                    "<input type='search' size='35' name='friend' id='tags'>" +
                    "<input type='submit' value='Add to List'/>" +
                "</form>" +
            "</div>" +
            "<div id='right-col'>" +
                "<div id='addedFriends' style='width:300px; height:100px; clear:both; overflow:auto;'>" +
                "</div>" +
            "</div>" +
            "<div id='comment-area'>" +
                "<label for='explanation'><B>Add a comment with your referral</B></label>" +
                "<textarea name='comment' id='explanation' rows='4' cols='100%'></textarea>" +
            "</div>" +
        "</div>";

    // fill in content div with search results
    $('#search-content').html(htmlString);

    // TODO: OPTIMIZE BELOW JQUERY BINDING
    // Accordion
    $("#accordion-search").accordion({ header: "h3" });

    // initialize popup box for referring friends to a vendor
    var friendList = [];
    bindDialog(friendList);
    bindAddFriendSubmit(friendList);
    bindDialogLink(friendList, vendorData);
    bindAutoComplete();
}

function displayAddedFriends(friendList) {
    var displayFriends = "<div id='referredFriendsHeader'>List of friends to refer<BR></div><table>";
    for (var i = 0; i < friendList.length; i++) {
        displayFriends = displayFriends + "<tr><td><img src='../../assets/images/mgao.jpg'></td>" + 
                "<td class='referredFriend'>" + friendList[i]['firstName'] + " " + friendList[i]['lastName'] + 
                "</td><td><img class=\"delete\" src='../../assets/jquery-ui-1.8.16.custom/css/custom-theme/images/del.png'/></td>" + 
                "</tr>";
    }
    displayFriends = displayFriends + "</table>";
    document.getElementById("addedFriends").innerHTML = displayFriends; 
}