/* 
 * Document     : dialogs.js
 * Created on   : Jan 4, 2011, 1:03 AM
 * Author       : @kimhsiao
 * Description  :
 * 
 * getVendorData                store vendor data returned from Google API in a javascript array
 * displaySearchResults         display search results in accordion
 * addVendorToDB                add vendor to local database when added to a list or referred to a friend
 * 
 */

function showLoading() {
  $("#loading").show();
}

function hideLoading() {
  $("#loading").hide();
}

// get vendor data from foursquare api search and store results
function getVendorData(results) {    
    var vendorData = new Array();
    
    for(var i = 0; i < results.length; i++) {
        var singleVendor = new Array();
        var addrComponents;

        // get values if they exist, otherwise set to default value
        singleVendor['name'] = results[i].response.venue.name;
        if (singleVendor['name'] == undefined) {
            singleVendor['name'] = "";
        }
        singleVendor['id'] = results[i].response.venue.id;
        if (singleVendor['id'] == undefined) {
            singleVendor['id'] = "";
        }
        singleVendor['phone'] = results[i].response.venue.contact.formattedPhone;
        if (singleVendor['phone'] == undefined) {
            singleVendor['phone'] = "";
        }
        singleVendor['lat'] = results[i].response.venue.location.lat;
        if (singleVendor['lat'] == undefined) {
            singleVendor['lat'] = 0;
        }
        singleVendor['lng'] = results[i].response.venue.location.lng;
        if (singleVendor['lng'] == undefined) {
            singleVendor['lng'] = 0;
        }
        singleVendor['addr'] = results[i].response.venue.location.address;
        if (singleVendor['addr'] == undefined) {
            singleVendor['addr'] = "";
        }
        singleVendor['addrCrossStreet'] = results[i].response.venue.location.crossStreet;
        if (singleVendor['addrCrossStreet'] == undefined) {
            singleVendor['addrCrossStreet'] = "";
        }
        singleVendor['addrCity'] = results[i].response.venue.location.city;
        if (singleVendor['addrCity'] == undefined) {
            singleVendor['addrCity'] = "";
        }
        singleVendor['addrState'] = results[i].response.venue.location.state;
        if (singleVendor['addrState'] == undefined) {
            singleVendor['addrState'] = "";
        }
        singleVendor['addrCountry'] = results[i].response.venue.location.country;
        if (singleVendor['addrCountry'] == undefined) {
            singleVendor['addrCountry'] = "";
        }
        singleVendor['addrZip'] = results[i].response.venue.location.postalCode;
        if (singleVendor['addrZip'] == undefined) {
            singleVendor['addrZip'] = "";
        }
        singleVendor['formattedAddress'] = results[i].response.venue.location.formattedAddress;
        if (singleVendor['formattedAddress'] == undefined) {
            singleVendor['formattedAddress'] = "";
        }
        singleVendor['website'] = results[i].response.venue.url;
        if (singleVendor['website'] == undefined) {
            singleVendor['website'] = "";
        }

        var tags = [];
        if (results[i].response.venue.tags != undefined) {
            for(var j=0; j<results[i].response.venue.tags.length; j++) {
                tags[j] = results[i].response.venue.tags[j];
            }
        } 
        singleVendor['tags'] = tags;
        
        var categories = {};
        if (results[i].response.venue.categories != undefined) {
            for(var j=0; j<results[i].response.venue.categories.length; j++) {
                categories[j] = {};
                categories[j]['cid'] = results[i].response.venue.categories[j].id;
                categories[j]['categoryName'] = results[i].response.venue.categories[j].name;
            }
            singleVendor['categories'] = categories;
        }

        var counter = 0;
        var photos = {};
        if (results[i].response.venue.photos != undefined) {
            for(var j=0; j<results[i].response.venue.photos.groups.length; j++) {
                for (var k = 0; k < results[i].response.venue.photos.groups[j].items.length; k++) {
                    photos[counter] = {};
                    photos[counter]['pid'] = results[i].response.venue.photos.groups[j].items[k].id;
                    photos[counter]['photoURL'] = results[i].response.venue.photos.groups[j].items[k].url;
                    counter++;
                }
            }
            singleVendor['photos'] = photos;
        }

        
        // add singleVendor to vendorData array
        vendorData[i] = singleVendor;
    }
    return vendorData;
}

// retrieve vendor data from google API request for specific search and store results
//function getVendorData(results) {
//
////    var results = parsedJSON.searchResults;
////    var srcLat = parsedJSON.srcLat;
////    var srcLng = parsedJSON.srcLng;
//
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


// display vendor search results in accordion
//function displaySearchResults(vendorData) {
//    // mike's code
//    resetTabsStates();  // reset tabs to default setting
//    displaySearchItems(vendorData);
//    
////    $('#search-content').removeClass("ui-tabs-hide");
////    $('#inbox-content, #friend-activity-content, #referral-tracking-content, #list-content').addClass("ui-tabs-hide")
////    $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");
//    
////    bindReferDialogButtonFromSearch(friendList,vendorData);
////    bindAddToListButtonFromSearch();
//    
//    // kim's code
//    // add search result rows to accordion
////    var htmlString = "<div id='accordion-search'>";
////
////    var displayAddr;
////    for (var i=0; i<vendorData.length; i++) {
////        // use formatted address if available
////        if (vendorData[i].addr != "") {
////            var addr = vendorData[i].addr.split(",");
////            displayAddr = addr[0];
////            if (addr[1] != undefined) {
////                displayAddr = displayAddr + "<br>" + addr[1];
////                if (addr[2] != undefined) {
////                    displayAddr = displayAddr + ", " + addr[2];
////                }
////            }
////        }
////        // otherwise, use vicinity if available
////        else {
////            if (vendorData[i].vicinity != "") {
////                var addr = vendorData[i].vicinity.split(",");
////                if (addr[1] != undefined) {
////                    displayAddr = addr[0] + "<BR>" + addr[1] + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
////                }
////                else {
////                    displayAddr = addr[0] + "<BR>" + vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
////                }
////            }
////            // otherwise, use individual address components
////            else {
////                displayAddr =  vendorData[i].addrNum + " " + vendorData[i].addrStreet + "<BR>" + 
////                                vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip;
////            }
////        }
////
////        htmlString = htmlString +
////            "<div>" +
////                "<h3><a href='#'><div class='vendor-name'>" + vendorData[i].name + "</div></a></h3>" +
////                "<div class='name-wrapper'> <table class='formatted-table'>" +
////                    "<tr>" +
////                        "<td class='formatted-table-info'>" +
////                            displayAddr + 
////                        "</td>" +
////                        "<td class='formatted-table-button' align='right'>" +
////                            "<p><a href='#' id=search-refer-vid--" + vendorData[i].id + " class='refer-popup-link dialog_link ui-state-default ui-corner-all'>" +
////                            "<span class='ui-icon ui-icon-plus'></span>Refer to Friends</a></p>" +
////                            "<p><a href='#' id=search-add-vid--" + vendorData[i].id + " class='add-to-list-popup-link dialog_link ui-state-default ui-corner-all'>" +
////                            "<span class='ui-icon ui-icon-plus'></span>Add to List</a></p>" +
////                        "</td>" +
////                    "</tr>" +
////                "</table></div>" +
////            "</div>";
////    }
////
////    // close accordion div
////    htmlString = htmlString + "</div>";
////
////    // fill in content div with search results
////    $('#search-content').html(htmlString);
////
////    // initialize popup box for referring friends to a vendor
////    bindReferDialogButtonFromSearch(friendList, vendorData);
////    bindSearchAccordion();
////
////    // initialize pop up box for adding vendor to list
////    bindAddToListButtonFromSearch(vendorData);
////    displayListDropDown();
//}

function bindSearchAccordion() {
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


// add vendor to db whenever the vendor is added to a list or is referred to a friend
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