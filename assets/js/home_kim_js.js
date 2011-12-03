// kimhsiao

$(document).ready(function() {
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

/* helper functions */
function getVendorData(parsedJSON) {
    //TODO: ERROR HANDLING
    //TODO: MAKE CODE ROBUST -- if there is a missing element or if multiple cities (for example): check for type
    var srcLat = parsedJSON.srcLat;
    var srcLng = parsedJSON.srcLng;

    var results = parsedJSON.searchResults;

    var vendorData = new Array();
    for(var i=0; i<results.length; i++) {
        var singleVendor = new Array();
        singleVendor['name'] = results[i].result.name;
        singleVendor['reference'] = results[i].result.reference;
        singleVendor['id'] = results[i].result.id;
        singleVendor['lat'] = results[i].result.geometry.location.lat;
        singleVendor['lng'] = results[i].result.geometry.location.lng;
        singleVendor['phone'] = results[i].result.formatted_phone_number;
        singleVendor['addr'] = results[i].result.formatted_address;
        singleVendor['addrNum'] = results[i].result.address_components[0].short_name;
        singleVendor['addrStreet'] = results[i].result.address_components[1].short_name;
        singleVendor['addrCity'] = results[i].result.address_components[2].short_name;
        singleVendor['addrState'] = results[i].result.address_components[3].short_name;
        singleVendor['addrCountry'] = results[i].result.address_components[4].short_name;
        singleVendor['addrZip'] = results[i].result.address_components[5].short_name;
        singleVendor['website'] = results[i].result.website;
        singleVendor['icon'] = results[i].result.icon;
        singleVendor['rating'] = results[i].result.rating;
        var types = new Array();
        // store types
//                alert("name:" + name + "\nreference:" + reference + "\nid:" + id + "\nlat:" + lat + "\nlng:" + lng + "\nphone:" + phone + "\naddr:" + addr + "\naddrNum:" + addrNum +
//                "\naddrStreet:" + addrStreet + "\naddrCity:" + addrCity + "\naddrState:" + addrState + "\naddrCountry:" + addrCountry + "\naddrZip:" + addrZip + "\nwebsite:" + website +
//                "\nicon:" + icon + "\nrating:" + rating);
        for(var j=0; j<results[i].result.types.length; j++) {
            types[j] = results[i].result.types[j];
//                    alert(types[j]);
        }
        singleVendor['types'] = types;

        // add singleVendor to vendorData array
        vendorData[i] = singleVendor;
    }
    return vendorData;
}

function displaySearchResults(vendorData) {
    // add rows to accordion
    var htmlString = "<div id='accordion'>";

    for (var i=0; i<vendorData.length; i++) {
        htmlString = htmlString + 
            "<div>" + 
                "<h3><a href='#'>" + vendorData[i].name + "</a></h3>" +
                "<div> <table class='formatted-table'>" + 
                    "<td class='formatted-table-info'>" +
                    vendorData[i].addrNum + " " + vendorData[i].addrStreet + "</br>" +
                    vendorData[i].addrCity + " " + vendorData[i].addrState + " " + vendorData[i].addrZip + "</br>" +
                    vendorData[i].phone + "</td>" +
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
    $("#accordion").accordion({ header: "h3" });

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
                //document.getElementById("explanation").value = '';
                //document.getElementById("addedFriends").innerHTML = '';
                //document.getElementById("tags").value = '';
                //friendList = [];

                // fade out dark background
                $("#fuzz").fadeOut();  
            },
            buttons: {
//                        "Refer!": function() { 
//                            if (friendList.length < 1) {
//                                alert("You did not select any friends to refer. Please try again.");
//                            }
//                            else {
//                                // create ajax request
//                                var ajaxRequest;
//
//                                // code for IE7+, Firefox, Chrome, Opera, Safari
//                                if (window.XMLHttpRequest) {
//                                      ajaxRequest = new XMLHttpRequest();
//                                }
//                                // code for IE6, IE5
//                                else {
//                                      ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
//                                }
//
//                                // Create a function that will receive data sent from the server
//                                ajaxRequest.onreadystatechange=function() {
//                                      if (ajaxRequest.readyState==4 && ajaxRequest.status==200) {
//                                        $('#dialog').dialog("close"); 
//                                      }
//                                } 
//
//                                // create query for inserting row
//                                var addReferralQuery = "INSERT INTO Referrals VALUES ";
//                                for (var i = 0; i < friendList.length; i++){
//                                    var now = new Date();
//                                    now = now.format("yyyy-mm-dd HH:MM:ss");
//                                    var comment = document.getElementById('explanation').value;
//                                    var friendUID = friendList[i][0];
//                                    addReferralQuery = addReferralQuery + "(NULL," + myUID + "," + friendUID + ",\"" + now + "\",0,\"" + comment + "\"),";
//                                }
//
//                                addReferralQuery = addReferralQuery.slice(0,-1);
//
//                                var addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
//                                var ajaxQueryString = "?q=" + addReferralQuery + "&vid=" + vendorID;
//                                    ajaxRequest.open("GET", "http://192.168.11.28/searchvendors/add_referral" + ajaxQueryString, true);
//                                    ajaxRequest.send(null); 
//                                }
//                        }
          }
    });

    // Dialog Link
    $('.dialog_link').click(function(){
            // get id of the vendor, which is the id of the pop up button
            vendorID = $(this).attr('id');
//
//                    for(var i = 0; i < vendorData.length; i++) {
//                        if (vendorData[i][0] == vendorID) {
//                            vendorName = vendorData[i][1];
//                        }
//                    }
//
//                    $('#dialog').dialog("option","title","Refer Friends to " + vendorName);
            $("#fuzz").fadeIn();  
            $('#dialog').dialog('open');
            return false;
    });
}