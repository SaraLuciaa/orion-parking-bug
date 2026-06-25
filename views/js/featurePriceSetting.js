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
    //date picker for date ranges
    $("#feature_plan_date_from").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date(date_format[2], date_format[1] - 1, date_format[0]);
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#feature_plan_date_to").datepicker("option", "minDate", selectedDate);
        },
    });

    $("#feature_plan_date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: getMinDate($("#feature_plan_date_from").val()),
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date(date_format[2], date_format[1] - 1, date_format[0]);
            selectedDate.setDate(selectedDate.getDate() - 1);
            $("#feature_plan_date_from").datepicker("option", "maxDate", selectedDate);
        }
    });

    $("#specific_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

    $(".is_special_days_exists").on('click', function() {
        if ($(this).is(':checked')) {
            $('.week_days').show();
        } else {
            $('.week_days').hide();
        }
    });

    if ($(".is_special_days_exists").length > 0) {
        if ($(".is_special_days_exists")[0].checked) {
            $('.week_days').show();
        } else {
            $('.week_days').hide();
        }
    }

    $('#price_impact_type').on('change', function() {
        showHideImpactType();
    });
    showHideImpactType();
    var ajax_pre_check_var = '';
    $('.booking_product_search_results_ul').hide();

    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }

    $(document).on('keyup', "#booking_product_name", function(event) {
        if (($('.booking_product_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40) {
                $(".booking_product_search_results_ul li:first").focus();
            } else if (event.which == 38) {
                $(".booking_product_search_results_ul li:last").focus();
            }
        } else {
            $('.booking_product_search_results_ul').empty().hide();
            if ($(this).val() != '' && $(this).val().length > 2) {
                abortRunningAjax();
                ajax_pre_check_var = $.ajax({
                    url: booking_product_price_plans_url,
                    data: {
                        room_type_name: $(this).val(),
                        action: 'SearchBookingProductByName',
                        ajax: true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        var html = '';
                        if (data.status != 'failed') {
                            $.each(data, function(key, booking_product) {
                                let single_event = 0;
                                if (typeof(booking_product.single_event) != 'undefined' && booking_product.single_event) {
                                    single_event = 1;
                                }
                                html += '<li data-id_product="' + booking_product.id_product + '" data-single_event="' + single_event + '">' + booking_product.name + '</li>';
                            });
                            $('.booking_product_search_results_ul').html(html);
                            $('.booking_product_search_results_ul').show();
                            $('.error-block').hide();
                        } else {
                            $('.error-block').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.booking_product_search_results_ul li', function(event) {
        if ($(this).data('single_event')) {
            $('#single_event_hide').hide();
            $('#single_event_input').val(1);
        } else {
            $('#single_event_hide').show();
            $('#single_event_input').val(0);
        }
        $('#booking_product_name').val($(this).html());
        $('#product_id').val($(this).data('id_product'));

        $('.booking_product_search_results_ul').empty().hide();
    });

    $('#date_selection_type').on('change', function() {
        showHideOnDateSelectionType();
    });

    showHideOnDateSelectionType();

});

function showHideImpactType()
{
    if ($('#price_impact_type').val() == 2) {
        $(".payment_type_icon").text(defaultcurrency_sign);
    } else if ($('#price_impact_type').val() == 1) {
        $(".payment_type_icon").text('%');
    } else {
        if (typeof(defaultcurrency_sign) != 'undefined') {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    }
}

function showHideOnDateSelectionType()
{
    if ($('#date_selection_type').val() == 2) {
        $(".specific_date_type").show();
        $(".date_range_type").hide();
        $(".special_days_content").hide();
    } else if ($('#date_selection_type').val() == 1) {
        $(".specific_date_type").hide();
        $(".date_range_type").show();
        $(".special_days_content").show();
    } else {
        $(".specific_date_type").hide();
        $(".date_range_type").show();
        $(".special_days_content").show();
    }
}

function getMinDate(date) {
    var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date)));
    selectedDate.setDate(selectedDate.getDate() + 1);
    return selectedDate;
}

function highlightDateBorder(elementVal, date) {
    if (elementVal) {
        var currentDate = date.getDate();
        var currentMonth = date.getMonth() + 1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        if (currentDate < 10) {
            currentDate = '0' + currentDate;
        }
        dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
        var date_format = elementVal.split("-");
        var check_in_time = (date_format[2]) + '-' + (date_format[1]) + '-' + (date_format[0]);
        if (dmy == check_in_time) {
            return [true, "selectedCheckedDate", "Check-In date"];
        } else {
            return [true, ""];
        }
    } else {
        return [true, ""];
    }
}