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
    $(document).on("focus", "#search_date_from, #search_date_to", function() {
        var dlocale = prestashop.language.iso_code;
        if (typeof($.datepicker.regional[dlocale]) == 'undefined') {
            dlocale = '';
        }
        $("#search_date_from").datepicker({
            firstDay: wk_monday_first_day_week,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            changeYear: true,
            changeMonth: true,
            onSelect: function(selectedDate) {
                var date_format = selectedDate.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                $("#search_date_to").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#search_date_to").datepicker({
            firstDay: wk_monday_first_day_week,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            changeYear: true,
            changeMonth: true,
            beforeShow: function(input, instance) {
                $("#search_date_from").removeClass('hasDatepicker');
                var minDateTo = $('#search_date_from').val();
                var date_format = minDateTo.split("-");
                var minDateTo = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                $(".search_date_to").datepicker("option", "minDate", minDateTo);
            },
        });
        $.datepicker.setDefaults( $.datepicker.regional[dlocale] );
    });
});