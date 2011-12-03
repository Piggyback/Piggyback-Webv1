/**
    Document   : home_js.php
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
                // data will be JSON-encoded
//                        alert(data);
                var parsedJSON = jQuery.parseJSON(data);
                var vendorData = getVendorData(parsedJSON);
                displaySearchResults(vendorData);
//                        alert(parsedJSON.searchResults[0].result.formatted_address);  // THIS FRACKIN' WORKS!
            });

        $('#search-content').removeClass("ui-tabs-hide");
        $('#inbox-content, #ui-tabs-1, #ui-tabs-2').addClass("ui-tabs-hide")
        $('#inbox-tab, #friend-activity-tab, #referral-tracking-tab').removeClass("ui-tabs-selected ui-state-active");

        return false;
    });
}