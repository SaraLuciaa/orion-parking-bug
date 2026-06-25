/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
$(document).ready(function() {
    if (typeof google === 'object' && typeof google.maps === 'object') {
        initMap();
    }
});
function initMap() {
    if (typeof(wk_booking_show_map) != 'undefined' && wk_booking_show_map != "0") {
        const mapOptions = {
            zoom: 8,
            center: { lat: parseFloat(wk_booking_latitude), lng: parseFloat(wk_booking_longitude) },
        };
        map = new google.maps.Map(document.getElementById("map"), mapOptions);
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(wk_booking_latitude), lng: parseFloat(wk_booking_longitude) },
            map: map,
        });
        link = 'http://maps.google.com/maps?';
        link += '&amp;daddr=(' + wk_booking_latitude + ', ' + wk_booking_longitude + ')';
        let html = "<p>" + wk_booking_address + "</p>";
        html += '<div class=""><a class="btn btn-primary wk-direction-btn"';
        html += ' href="' + link + '" target="_blank">' + getDirections + '</a></div>';
        const infowindow = new google.maps.InfoWindow({
            content: html,
        });
        google.maps.event.addListener(marker, "click", () => {
            infowindow.open(map, marker);
        });
    }
  }