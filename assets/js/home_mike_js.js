/**
    Document   : home_mike_js.php
    Created on : Dec 2, 2011, 6:43:47 PM
    Author     : gaobi
    Description:
        all javascript code for home page is here
*/
/**
   TO-DOs:
*/

$(document).ready(function() {
    setScrollHeight();
    initTabs();
    initHoverTabs();
	getListContent();
    searchAJAX();
});

/* functions for $(document).ready */
function setScrollHeight() {
    // set height of scrollable divs depending on window size
    $('#scrollable-sections').height($(window).height()-150);
    $('#viewer-page-container').height($(window).height()-92);

    $(window).resize(function() {
        $('#scrollable-sections').height($(window).height()-150)
        $('#viewer-page-container').height($(window).height()-92)
    });
}

function initTabs() {
    $( "#tabs" ).tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html(
                    "Couldn't load this tab. We'll try to fix this as soon as possible.");
            }
        }
    });
}

function initHoverTabs() {
    $('ul#icons li').hover(
        function() { $(this).addClass('ui-state-hover'); },
        function() { $(this).removeClass('ui-state-hover'); }
    );
}

function getListContent() {
	//TODO: Continue here
	$('#lists li').click(function () {
 		// load list content when clicked
		alert ($(this).attr('id'));
		// parse lid from id
		var lid_string = $(this).attr('id');
		var lid = lid_string.substring(lid_string.indexOf('lid--') + 'lid--'.length);
		jQuery.post('list_controller/get_list_content', {
			lid: lid
		}, function(data) {
//			alert(data);
			var parsedJSON = jQuery.parseJSON(data);
			alert(parsedJSON[0].listsDate);
		});
	});
}

function searchAJAX() {
    // merge kim's search with home shell
    $('#searchform').submit(function() {
        var $inputs = $('#searchform :input');
        var values = {};
        $inputs.each(function() {
            values[this.name] = $(this).val();
        });

        jQuery.post('searchvendors/perform_search', {
            searchLocation: values['searchLocation'],
            searchText: values['searchText']
            }, function(data) {
                var parsedJSON = jQuery.parseJSON(data);
                var results = parsedJSON.searchResults;
                if (results[0] == "error") {
                    var errorType;
                    if (results[1] == "locationError") {
                            errorType = "Error with Location";
                    }

                    // error with the search
                    else if (results[1] == "searchError") {
                        errorType = "Error with Search";
                    }

                    // print out error message
                    switch (results[2]) {
                        case "ZERO_RESULTS":
                            alert(errorType + ": No results were found");
                            break;
                        case "OVER_QUERY_LIMIT":
                            alert(errorType + ": You are over the API query limit");
                            break;
                        case "REQUEST_DENIED":
                            alert(errorType + ": Your request was denied");
                            break;
                        case "INVALID_REQUEST":
                            alert(errorType + ": Your request is invalid");
                            break;
                        default:
                            alert(errorType);
                    }
                }
                else {
                    var vendorData = getVendorData(parsedJSON);
                    displaySearchResults(vendorData);
                }
            });

        $('#search-content').removeClass("ui-tabs-hide");
        $('#inbox-content, #ui-tabs-1, #ui-tabs-2').addClass("ui-tabs-hide")
        $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

        return false;
    });
}

function fbAPI() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '251920381531962',
            status     : true,
            cookie     : true,
            xfbml      : true
        });

        // If user is not logged in, redirect user to login page
        FB.getLoginStatus(function(response) {
            if (response.status != "connected") {
                // logged in and connected user
                window.location = "http://192.168.11.28/login";
            } else {
                // do nothing
            }
        });

        $('#logout').click(function () {
            //logout when div is clicked
            FB.logout(function(response) {
                // user is now logged out of service AND facebook
                // return to login page
                window.location = "http://192.168.11.28/login";
            });
        });

    };
    (function(d){
        var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
}
