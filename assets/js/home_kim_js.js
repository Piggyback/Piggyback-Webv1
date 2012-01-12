// kimhsiao

//$(document).ready(function() {
//    bindHoverOver();
//});

// when you hover over a button, the text turns pink
//function bindHoverOver() {
//    $(document).on("mouseenter", ".dialog_link", function() {
//        $(this).addClass('ui-state-hover');
//    });
//
//    $(document).on("mouseleave", ".dialog_link", function() {
//         $(this).removeClass('ui-state-hover');
//    });
//}

// TODO: fix bug with initial accordion state
//// create accordion for search results -- can display many open rows at once
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

//// store all friends upon log on
//function getFriends() {
//    jQuery.post('searchvendors/get_friends', function(data) {
//          var parsedJSON = jQuery.parseJSON(data);
//          myUID = parsedJSON.myUID;
//          friends = parsedJSON.allFriendsArray;
//          allFriends = new Array();
//          for (var i = 0; i < friends.length; i++) {
//              var oneFriend = new Array();
//              oneFriend['uid'] = friends[i][0];
//              oneFriend['fbid'] = friends[i][1];
//              oneFriend['email'] = friends[i][2];
//              oneFriend['firstName'] = friends[i][3];
//              oneFriend['lastName'] = friends[i][4];
//              allFriends.push(oneFriend);
//          }
//          displayAutoCompleteResults(allFriends);
//     });
//}

//// retrieve vendor data from google API request for specific search and store results
//function getVendorData(parsedJSON) {
//
//    var results = parsedJSON.searchResults;
//    var srcLat = parsedJSON.srcLat;
//    var srcLng = parsedJSON.srcLng;
//
//// TODO: consider changing array to json object {} - reference bookmarked stackoverflow
//    var vendorData = new Array();
//    for(var i=0; i<results.length; i++) {
//        var singleVendor = new Array();
//        var addrComponents;
//
//        // get values if they exist, otherwise set to default value
//        singleVendor['name'] = results[i].result.name;
//        if (singleVendor['name'] == undefined) {
//            singleVendor['name'] = "";
//        }
//        singleVendor['reference'] = results[i].result.reference;
//        if (singleVendor['reference'] == undefined) {
//            singleVendor['reference'] = "";
//        }
//        singleVendor['id'] = results[i].result.id;
//        if (singleVendor['id'] == undefined) {
//            singleVendor['id'] = "";
//        }
//        singleVendor['lat'] = results[i].result.geometry.location.lat;
//        if (singleVendor['lat'] == undefined) {
//            singleVendor['lat'] = 0;
//        }
//        singleVendor['lng'] = results[i].result.geometry.location.lng;
//        if (singleVendor['lng'] == undefined) {
//            singleVendor['lng'] = 0;
//        }
//        singleVendor['phone'] = results[i].result.formatted_phone_number;
//        if (singleVendor['phone'] == undefined) {
//            singleVendor['phone'] = "";
//        }
//        singleVendor['addr'] = results[i].result.formatted_address;
//        if (singleVendor['addr'] == undefined) {
//            singleVendor['addr'] = "";
//        }
//
//        singleVendor['addrNum'] = "";
//        singleVendor['addrStreet'] = "";
//        singleVendor['addrCity'] = "";
//        singleVendor['addrState'] = "";
//        singleVendor['addrCountry'] = "";
//        singleVendor['addrZip'] = "";
//        addrComponents = results[i].result.address_components;
//        if (addrComponents != undefined) {
//            for (var j = 0; j < addrComponents.length; j++) {
//                switch (addrComponents[j].types[0]) {
//                    case "street_number":
//                        singleVendor['addrNum'] = addrComponents[j].short_name;
//                        break;
//                    case "route":
//                        singleVendor['addrStreet'] = addrComponents[j].short_name;
//                        break;
//                    case "locality":
//                        singleVendor['addrCity'] = addrComponents[j].short_name;
//                        break;
//                    case "administrative_area_level_1":
//                        singleVendor['addrState'] = addrComponents[j].short_name;
//                        break;
//                    case "country":
//                        singleVendor['addrCountry'] = addrComponents[j].short_name;
//                        break;
//                    case "postal_code":
//                        singleVendor['addrZip'] = addrComponents[j].short_name;
//                        break;
//                }
//            }
//        }
//
//        singleVendor['website'] = results[i].result.website;
//        if (singleVendor['website'] == undefined) {
//            singleVendor['website'] = "";
//        }
//        singleVendor['icon'] = results[i].result.icon;
//        if (singleVendor['icon'] == undefined) {
//            singleVendor['icon'] = "";
//        }
//        singleVendor['rating'] = results[i].result.rating;
//        if (singleVendor['rating'] == undefined) {
//            singleVendor['rating'] = 0;
//        }
//        singleVendor['vicinity'] = results[i].result.vicinity;
//        if (singleVendor['vicinity'] == undefined) {
//            singleVendor['vicinity'] = "";
//        }
//
//        var types = new Array();
//        if (results[i].result.types != undefined) {
//            for(var j=0; j<results[i].result.types.length; j++) {
//                types[j] = results[i].result.types[j];
//            }
//            singleVendor['types'] = types;
//        }
//
//        // add singleVendor to vendorData array
//        vendorData[i] = singleVendor;
//    }
//    return vendorData;
//}
//
//
//// display vendor search results in accordion
//function displaySearchResults(vendorData) {
//    // add search result rows to accordion
//    var htmlString = "<div id='accordion-search'>";
//
//    var displayAddr;
//    for (var i=0; i<vendorData.length; i++) {
//        // use formatted address if available
//        if (vendorData[i].addr != "") {
//            var addr = vendorData[i].addr.split(",");
//            displayAddr = addr[0];
//            if (addr[1] != undefined) {
//                displayAddr = displayAddr + "<br>" + addr[1];
//                if (addr[2] != undefined) {
//                    displayAddr = displayAddr + ", " + addr[2];
//                }
//            }
//        }
//        // otherwise, use vicinity if available
//        else {
//            if (vendorData[i].vicinity != "") {
//                var addr = vendorData[i].vicinity.split(",");
//                if (addr[1] != undefined) {
//                    displayAddr = addr[0] + "<BR>" + addr[1] + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
//                }
//                else {
//                    displayAddr = addr[0] + "<BR>" + vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
//                }
//            }
//            // otherwise, use individual address components
//            else {
//                displayAddr =  vendorData[i].addrNum + " " + vendorData[i].addrStreet + "<BR>" + 
//                                vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
//            }
//        }
//
//        htmlString = htmlString +
//            "<div>" +
//                "<h3><a href='#'>" + vendorData[i].name + "</a></h3>" +
//                "<div> <table class='formatted-table'>" +
//                    "<tr>" +
//                        "<td class='formatted-table-info'>" +
//                            displayAddr + 
//                        "</td>" +
//                        "<td class='formatted-table-button' align='right'>" +
//                            "<p><a href='#' id=" + vendorData[i].id + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                            "<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p>" +
//                            "<p><a href='#' id=" + vendorData[i].id + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
//                            "<span class='ui-icon ui-icon-plus'></span>Add to List</a></p>" +
//                        "</td>" +
//                    "</tr>" +
//                "</table></div>" +
//            "</div>";
//    }
//
//    // close accordion div
//    htmlString = htmlString + "</div>";
//
//    // fill in content div with search results
//    $('#search-content').html(htmlString);
//
//    // initialize popup box for referring friends to a vendor
//    bindReferDialogButtonFromSearch(friendList, vendorData);
//    bindAccordion();
//
//    // initialize pop up box for adding vendor to list
////    bindAddToListDialog();
//    bindAddToListButtonFromSearch(vendorData);
//    displayListDropDown();
//}
//
//// add vendor to db whenever the vendor is added to a list or is referred to a friend
//function addVendorToDB(vendor) {
//    jQuery.post('searchvendors/add_vendor', {
//        name: vendor.name,
//        reference: vendor.reference,
//        id: vendor.id,
//        lat: vendor.lat,
//        lng: vendor.lng,
//        phone: vendor.phone,
//        addr: vendor.addr,
//        addrNum: vendor.addrNum,
//        addrStreet: vendor.addrStreet,
//        addrCity: vendor.addrCity,
//        addrState: vendor.addrState,
//        addrCountry: vendor.addrCountry,
//        addrZip: vendor.addrZip,
//        website: vendor.website,
//        icon: vendor.icon,
//        rating: vendor.rating,
//        vicinity: vendor.vicinity
//    }, function(data) {
//        // alert error if one occurred
//        if (data) {
//            alert(data);
//        }
//    });
//}

//// add list to list -- put lid1 into lid2
//function addListToList(lid2, lid1) {
//    // turn lid into an array, right now it is just an integer
//    jQuery.post('list_controller/get_list_content', {
//        lid: lid1
//    }, function(data) {
//        var parsedJSON = jQuery.parseJSON(data);
//        for (var i = 0; i < parsedJSON.length; i++ ) {
//            addVendorToList(lid2, parsedJSON[i].vid);
//        }
//    });
//}
//
//
//// update database and update mylists sidebar to reflect new vendor/list
//function addVendorToList(lid, vid) {
//
//    //get date
//    var now = new Date();
//    now = now.format("yyyy-mm-dd HH:MM:ss");
//
//    // get comment
//    var comment = $('#add-to-list-comment-box').val();
//
//    // add vendor to list (new or old) and make it so that list div is updated to show new vendor
//    jQuery.post('list_controller/add_vendor_to_list', {
//        lid: lid,
//        vid: vid,
//        date: now,
//        comment: comment
//    }, function(data) {
//        if (data == "Could not add to list") {
//            alert("Could not add to list");
//        }
//        // if there was no error, then vendor was added to list. close the dialog
//        else {
//            $('#addToListDialog').dialog("close");
//
//            // if the div exists for the list, then add on to the stored data for displaying
//            if ($('#list-content-lid--' + lid).length) {
//                var vendorObj = jQuery.parseJSON(data);
//                
//                // get existing html that is stored in div
//                var htmlString = jQuery.trim($('#list-content-lid--' + lid).html());
//                
//                // get new html to add to div
//                var newHtmlString = JSON.stringify(vendorObj);
//                newHtmlString = newHtmlString.slice(1,-1);
//
//                // add a comma if there is now more than one object
//                htmlString = htmlString.slice(0,-1);
//                if (htmlString != "[") {
//                    htmlString = htmlString + ",";
//                }
//                htmlString = htmlString + newHtmlString + "]";
//
//                // save new json string to the appropriate list div
//                $('#list-content-lid--' + lid).html(htmlString);
//
//            }
//        }
//    });
//}
//
