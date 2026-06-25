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
    addTouchSpin();
    //if prestashop non booking product removed from the cart in checkout page
    $(document).on('click', '.remove-from-cart', function() {
        $(document).ajaxStop(function() {
            location.reload();
        });
    });

    $(document).on('click', '.product_blooking_time_slot', function() {
        var selectedSlots = new Array();
        var checkedSlots = parseInt($('.product_blooking_time_slot:checked').length);
        if (checkedSlots < 1) {
            $.growl.error({title: '', message: booking_one_slot_select_msg});
            return false;
        }
        $('.product_blooking_time_slot:checked').each(function() {
            selectedSlots.push({
                id_slot: $(this).val(),
                quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
            });
        });
        $(".booking_loading_img").show();
        $.ajax({
            url: wkBookingCartLink,
            data: {
                token: wk_static_token,
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $('#booking_time_slots_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
                id_product_attribute: $('#idProductAttribute').val(),
                booking_type: $('#booking_button').attr('booking_type')
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                $(".booking_loading_img").hide();
                if (result.status == 'ok') {
                    var qtyWanted = parseInt($('#booking_product_quantity_wanted').val());
                    if (typeof result.productPriceRegular != 'undefined') {
                        $('.booking_total_price').html('<strike>'+result.productPriceRegular+'</strike> ' +result.productPrice);
                    } else {
                        $('.booking_total_price').text(result.productPrice);
                    }
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors');
                    }
                } else {
                    $(".booking_product_errors").hide();
                }
            }
        });
    });

    $(document).on('click', '.remove-booking-product', function(e) {
        e.preventDefault();
        var $current = $(this);
        $.ajax({
            url: wkBookingCartLink,
            data: {
                token: wk_static_token,
                action: 'remove_booking_product_from_cart',
                booking_type: 1,
                id_cart_booking: $(this).attr('id-cart-booking'),
                id_product: $(this).attr('id-product'),
                id_product_attribute: $(this).attr('id-product-attribute'),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    location.reload();
                } else {
                    $.growl.error({title: '', message: result.msg});
                }
            }
        });
    });

    $(document).on('blur change', '.booking_time_slots_quantity_wanted', function() {
        var selectedSlots = new Array();
        var slot_max_avail_qty = $(this).closest('.time_slot_checkbox').find('.slot_max_avail_qty').val();
        var qty_wanted = $(this).val();
        if (qty_wanted == '' || !$.isNumeric(qty_wanted)) {
            $(this).val(1);
            qty_wanted = $(this).val();
        }
        $(this).val(parseInt(qty_wanted));
        if (parseInt(qty_wanted) < 1 || parseInt(qty_wanted) > slot_max_avail_qty) {
            $(this).val(slot_max_avail_qty);
        }
        $('.product_blooking_time_slot:checked').each(function() {
            selectedSlots.push({
                id_slot: $(this).val(),
                quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
            });
        });
        $.ajax({
            url: wkBookingCartLink,
            data: {
                token: wk_static_token,
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $(this).val(),
                id_product: $('#product_page_product_id').val(),
                id_product_attribute: $('#idProductAttribute').val(),
                booking_type: $('#booking_button').attr('booking_type')
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    if (typeof result.productPriceRegular != 'undefined') {
                        $('.booking_total_price').html('<strike>'+result.productPriceRegular+'</strike> ' +result.productPrice);
                    } else {
                        $('.booking_total_price').text(result.productPrice);
                    }
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors');
                    }
                } else {
                    $(".booking_product_errors").hide();
                }
            }
        });
    });

    $(document).on('click', '#booking_button', function(e) {
        e.preventDefault();
        $('#booking_button').attr('disabled', true);
        $(".booking_loading_img").show();
        $('#bookings_in_select_range').empty();
        var booking_type = $(this).attr('booking_type');
        if (booking_type == wk_type_date_range || booking_type == wk_type_rental || (booking_type == wk_type_event && $('.product_blooking_time_slot').length == 0)) {
            var quantity = $('#booking_product_quantity_wanted').val();
            if (quantity > 0) {
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        token: wk_static_token,
                        action: 'add_booking_product_to_cart',
                        booking_type: booking_type,
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: quantity,
                        event_multiple: 0,
                        id_product: $('#product_page_product_id').val(),
                        id_product_attribute: $('#idProductAttribute').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        $(".booking_loading_img").hide();
                        if (result.status == 'ok') {
                            $('#booking_button').attr('disabled', false);
                            $('#quantity_wanted').val(result.product_qty_to_cart);
                            $(".add-to-cart")[0].click();
                            if (result.available_qty <= 0) {
                                $('#booking_button').attr('disabled', 'disabled');
                                $('.unavailable_slot_err').show();
                            }
                            $('.product_max_avail_qty_display').text(result.available_qty);
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml).show();
                            }
                        } else {
                            $(".booking_product_errors").hide();
                        }
                    }
                });
            } else {
                $(".booking_product_errors").html(invalidQtyErr).show();
            }
        } else if (booking_type == wk_type_time_slot || (booking_type == wk_type_event && $('.product_blooking_time_slot').length > 0)) {
            var selectedSlots = new Array();
            $('.product_blooking_time_slot:checked').each(function() {
                selectedSlots.push({
                    id_slot: $(this).val(),
                    quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
                });
            });
            $.ajax({
                url: wkBookingCartLink,
                data: {
                    token: wk_static_token,
                    action: 'add_booking_product_to_cart',
                    selected_slots: selectedSlots,
                    booking_type: booking_type,
                    date: $('#booking_time_slot_date').val(),
                    quantity: $('#booking_time_slots_quantity_wanted').val(),
                    id_product: $('#product_page_product_id').val(),
                    id_product_attribute: $('#idProductAttribute').val(),
                    event_multiple: 1
                },
                method: 'POST',
                dataType: 'json',
                success: function(result) {
                    $(".booking_loading_img").hide();
                    if (result.status == 'ok') {
                        $('#booking_button').attr('disabled', false);
                        var qtyWanted = parseInt(result.totalQty);
                        $('#quantity_wanted').val(qtyWanted);
                        $(".add-to-cart")[0].click();
                        $('.product_max_avail_qty_display').text(result.available_qty);

                        // show this booking info on cart popup instead od normal product info
                        var popUpInfoHtml = '<div class="cart_pop_up_data range-period">';
                        popUpInfoHtml += '<span>' + dateText + ' - ' + $('#booking_time_slot_date').val() + '</span></br>';
                        $(result.timeSlotsInfo).each(function(key, slot) {
                            if (slot.quantity_avail == 0) {
                                $('#slot_checkbox_' + slot.slot_id).prop('checked', false);
                                $('#slot_quantity_container_' + slot.slot_id).empty();
                                $('#slot_quantity_container_' + slot.slot_id).html('<span class="booked_slot_text">' + slot_booked_text + '</span>');
                            }
                            $('#slot_max_avail_qty_' + slot.slot_id).val(slot.quantity_avail);
                            $('#qty_avail_' + slot.slot_id).html('/' + slot.quantity_avail);
                            popUpInfoHtml += '<span>' + slot.slot_from + ' - ' + slot.slot_to + ' , ' + qtyText + ' - ' + slot.quantity + '</span></br>';
                        });
                        popUpInfoHtml += '<span>' + total_price_text + ' - ' + result.totalPriceFormatted + '</span></br>';
                        popUpInfoHtml += '</div>';
                    }
                    if (result.errors != 'undefined') {
                        var errorHtml = '';
                        $(result.errors).each(function(key, error) {
                            errorHtml += error + '</br>';
                        });
                        if (errorHtml != '') {
                            $(".booking_product_errors").html(errorHtml).show();
                        }
                    } else {
                        $(".booking_product_errors").hide();
                    }
                }
            });
        }
    });

    $(document).on("focus", ".booking_date_from, .booking_date_to", function() {
        var dlocale = prestashop.language.iso_code;
        if (typeof($.datepicker.regional[dlocale]) == 'undefined') {
            dlocale = '';
        }
        $(".booking_date_from").datepicker({
            firstDay: wk_monday_first_day_week,
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: available_after,
            beforeShow: function(input, instance) {
                $(".booking_date_to").removeClass('hasDatepicker');
            },
            //for calender Css
            onSelect: function(selectedDate) {
                var date_format = selectedDate.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                if (considerDateToConfiguration == 0) {
                    if (min_days_booking <= 0) {
                        selectedDate.setDate(selectedDate.getDate() + 1);
                    } else {
                        selectedDate.setDate(selectedDate.getDate() + parseInt(min_days_booking));
                    }
                } else {
                    if (min_days_booking > 0) {
                        selectedDate.setDate(selectedDate.getDate() + parseInt(min_days_booking-1));
                    } else {
                        selectedDate.setDate(selectedDate.getDate());
                    }
                }
                $(".booking_date_to").datepicker("option", "minDate", selectedDate);
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        token: wk_static_token,
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_product_attribute: $('#idProductAttribute').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('#booking_button').attr('disabled', true);
                                $('#booking_button').hide();
                                $('.wk_total_booking_price_container').hide();
                                $('.unavailable_slot_err').show();
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').show();
                                $('.wk_total_booking_price_container').show();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            if (typeof result.productPriceRegular != 'undefined') {
                                $('.booking_total_price').html('<strike>'+result.productPriceRegular+'</strike> ' +result.productPrice);
                            } else {
                                $('.booking_total_price').text(result.productPrice);
                            }
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range_label + '</label>';
                                html += '<table class="table table-stripped">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>' + dateRangeText + '</th>';
                                html += '<th>' + priceText + '</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                $(result.dateRangesBookingInfo).each(function(key, rangeInfo) {

                                    html += '<tr>';
                                    html += '<td>';
                                    html += rangeInfo.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + rangeInfo.date_to;
                                    html += '</td>';
                                    html += '<td>';
                                    if (typeof rangeInfo.regular_price != 'undefined') {
                                        html += '<strike>'+rangeInfo.regular_price+'</strike> ' +rangeInfo.price;
                                    } else {
                                        html += rangeInfo.price;
                                    }
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody>';
                                html += '</table>';
                                $('#bookings_in_select_range').append(html);
                            }
                        }
                        if (result.errors != undefined) {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors');
                            }
                        } else {
                            $(".booking_product_errors").hide();
                        }
                    }
                });
            },
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_date_from').val()) {
                    var dateFromVal = $('.booking_date_from').val().split("-");
                    var dateFromVal = (dateFromVal[2]) + '-' + (dateFromVal[1]) + '-' + (dateFromVal[0]);
                    if (dateToWork == dateFromVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }
                if (typeof redBookedDate != 'undefined') {
                    if ($.inArray(dateToWork, redBookedDate) != -1) {
                        dateClass = 'redSlotBooked';
                    }
                }
                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                return [true,dateClass];
            },
        });
        $(".booking_date_to").datepicker({
            firstDay: wk_monday_first_day_week,
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: min_days_booking,
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_date_to').val()) {
                    var dateToVal = $('.booking_date_to').val().split("-");
                    var dateToVal = (dateToVal[2]) + '-' + (dateToVal[1]) + '-' + (dateToVal[0]);
                    if (dateToWork == dateToVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }
                if (typeof redBookedDate != 'undefined') {
                    if ($.inArray(dateToWork, redBookedDate) != -1) {
                        dateClass = 'redSlotBooked';
                    }
                }
                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                return [true, dateClass];
            },
            beforeShow: function(input, instance) {
                $(".booking_date_from").removeClass('hasDatepicker');
                var minDateTo = $('.booking_date_from').val();
                var date_format = minDateTo.split("-");
                var minDateTo = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                var maxDateTo = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                if (considerDateToConfiguration == 0) {
                    if (min_days_booking <= 0) {
                        minDateTo.setDate(minDateTo.getDate() + 1);
                    } else {
                        minDateTo.setDate(minDateTo.getDate() + parseInt(min_days_booking));
                    }
                    if (max_days_booking > 0) {
                        maxDateTo.setDate(maxDateTo.getDate() + parseInt(max_days_booking))
                    }
                } else {
                    var dateFrom = minDateTo.getDate();
                    if (min_days_booking > 0) {
                        minDateTo.setDate(dateFrom + parseInt(min_days_booking-1));
                    } else {
                        minDateTo.setDate(dateFrom);
                    }
                    if (max_days_booking > 0) {
                        maxDateTo.setDate(dateFrom + parseInt(max_days_booking-1))
                    }
                }
                $(".booking_date_to").datepicker("option", "minDate", minDateTo);
                if (max_days_booking > 0) {
                    $(".booking_date_to").datepicker("option", "maxDate", maxDateTo);
                }
            },
            onSelect: function(selectedDate) {
                var selectedDate = new Date($.datepicker.formatDate('dd-mm-yy', new Date(selectedDate)));
                if (considerDateToConfiguration == 0) {
                    selectedDate.setDate(selectedDate.getDate() - 1);
                }
                $(".booking_date_from").datepicker("option", "maxDate", selectedDate);
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        token: wk_static_token,
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_product_attribute: $('#idProductAttribute').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('.unavailable_slot_err').show();
                                $('#booking_button').hide();
                                $('.wk_total_booking_price_container').hide();
                                $('#booking_button').attr('disabled', true);
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').show();
                                $('.wk_total_booking_price_container').show();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            if (typeof result.productPriceRegular != 'undefined') {
                                $('.booking_total_price').html('<strike>'+result.productPriceRegular+'</strike> ' +result.productPrice);
                            } else {
                                $('.booking_total_price').text(result.productPrice);
                            }
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range_label + '</label>';
                                html += '<table class="table table-stripped">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>' + dateRangeText + '</th>';
                                html += '<th>' + priceText + '</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                $(result.dateRangesBookingInfo).each(function(key, rangeInfo) {

                                    html += '<tr>';
                                    html += '<td>';
                                    html += rangeInfo.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + rangeInfo.date_to;
                                    html += '</td>';
                                    html += '<td>';
                                    if (typeof rangeInfo.regular_price != 'undefined') {
                                        html += '<strike>'+rangeInfo.regular_price+'</strike> ' +rangeInfo.price;
                                    } else {
                                        html += rangeInfo.price;
                                    }
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody>';
                                html += '</table>';
                                $('#bookings_in_select_range').append(html);
                            }
                        }
                        if (result.errors != undefined) {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors');
                            }
                        } else {
                            $(".booking_product_errors").hide();
                        }
                    }
                });
            }
        });
        $.datepicker.setDefaults( $.datepicker.regional[dlocale] );
    });

    $(document).on("focus", ".booking_time_slot_date", function() {
        var selectedJsonDates = JSON.parse(selectedDatesJson);
        var dlocale = prestashop.language.iso_code;
        if (typeof($.datepicker.regional[dlocale]) == 'undefined') {
            dlocale = '';
        }
        $(".booking_time_slot_date").datepicker({
            firstDay: wk_monday_first_day_week,
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: available_after,
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_time_slot_date').val()) {
                    var dateVal = $('.booking_time_slot_date').val().split("-");
                    var dateVal = (dateVal[2]) + '-' + (dateVal[1]) + '-' + (dateVal[0]);
                    if (dateToWork == dateVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }
                if (typeof redBookedDate != 'undefined') {
                    if ($.inArray(dateToWork, redBookedDate) != -1) {
                        dateClass = 'redSlotBooked';
                    }
                }
                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof timeSlotDays != 'undefined' && timeSlotDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(currentDay, timeSlotDays) != -1) {

                        return [false, dateClass, disable_date_title];
                    }
                }

                if (typeof timeSlotType != 'undefined' && timeSlotType == 2) {
                    if ($.inArray(dateToWork, selectedJsonDates) == -1) {
                        return [false, dateClass];
                    } else {
                        return [true, dateClass];
                    }
                } else {
                    return [true, dateClass];
                }
            },
            //for calender Css
            onSelect: function(selectedDate) {
                $(".booking_loading_img").show();
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        token: wk_static_token,
                        action: 'booking_product_time_slots',
                        date: $('#booking_time_slot_date').val(),
                        quantity: $('#booking_time_slots_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_product_attribute: $('#idProductAttribute').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        $(".booking_loading_img").hide();
                        if (result.status == 'ok') {
                            var bookingTimeSlots = result.bookingTimeSlots;
                            var minimal_quantity = result.minimal_quantity;
                            var html = '';
                            if (bookingTimeSlots != 'empty') {
                                $.each(bookingTimeSlots, function(index, slot) {
                                    html += '<div class="time_slot_checkbox row">';
                                    html += '<label class="col-sm-9 form-control-static">';
                                    html += '<input type="checkbox" id="slot_checkbox_' + slot.id + '"';
                                    if (!slot.available_qty) {
                                        html += ' disabled="disabled ';
                                    }
                                    if (slot.checked) {
                                        html += ' checked="checked" ';
                                    }
                                    html += ' value="' + slot.id + '" class="product_blooking_time_slot">&nbsp;&nbsp;&nbsp';
                                    if (typeof slot.formated_slot_price_regular != 'undefined') {
                                        html += '<span class="time_slot_price"><strike>' + slot.formated_slot_price_regular  + '</strike> ' + slot.formated_slot_price + '&nbsp;</span>&nbsp;'+for_txt+'&nbsp;&nbsp;';
                                    } else {
                                        html += '<span class="time_slot_price">' + slot.formated_slot_price + '&nbsp;</span>&nbsp;'+for_txt+'&nbsp;&nbsp;';
                                    }
                                    html += '<span class="time_slot_range">' + slot.time_slot_from + ' &nbsp;-&nbsp;' + slot.time_slot_to + '</span>';
                                    html += '<span id="booking_product_available_qty"> <span class="product_max_avail_qty_display">' +slot_max_text+ ' - ' + slot.available_qty + '</span></span>';
                                    html += '</label>';
                                    if (slot.available_qty) {
                                        html += '<label class="col-sm-3" id="slot_quantity_container_' + slot.id + '">';
                                        html += '<input type="hidden" id="slot_max_avail_qty_' + slot.id + '" class="slot_max_avail_qty" value="' + slot.available_qty + '">';
                                        html += '<input type="text" class="booking_time_slots_quantity_wanted  form-control" value="'+ minimal_quantity +'"  min="'+ minimal_quantity +'">';
                                    } else {
                                        html += '<label class="col-sm-3 form-control-static" id="slot_quantity_container_' + slot.id + '">';
                                        html += '<span class="booked_slot_text">' + slot_booked_text + '</span>';
                                    }
                                    html += '</label>';
                                    html += '</div>';
                                });
                                $('#booking_button').attr('disabled', false);
                                $('#booking_product_time_slots').html(html);
                                addTouchSpin();
                                $(".unavailable_slot_err").hide();
                                $("#booking_button").show();
                                $(".wk_total_booking_price_container").show();
                            } else {
                                $('#booking_product_time_slots').html(no_slots_available_text);
                                $('.unavailable_slot_err').show();
                                $("#booking_button").hide();
                                $(".wk_total_booking_price_container").hide();
                                $('#booking_button').attr('disabled', true);
                            }
                            if (typeof result.productRegularTotalFeaturePriceFormated != 'undefined') {
                                $('.booking_total_price').html('<strike>'+result.productRegularTotalFeaturePriceFormated+'</strike> ' +result.productTotalFeaturePriceFormated);
                            } else {
                                $('.booking_total_price').text(result.productTotalFeaturePriceFormated);
                            }
                            if (result.totalSlotsQty !== 'undefined' && result.totalSlotsQty == 0) {
                                $('#booking_button').attr('disabled', true);
                                $("#booking_button").hide();
                                $(".wk_total_booking_price_container").hide();
                            }
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors');
                            }
                        } else {
                            $(".booking_product_errors").hide();
                        }
                    }
                });
            },
        });
        $.datepicker.setDefaults( $.datepicker.regional[dlocale] );
    });

    $(document).on('change', '#booking_product_quantity_wanted', function() {
        var qty_wanted = $('#booking_product_quantity_wanted').val();
        if (qty_wanted == '' || !$.isNumeric(qty_wanted)) {
            $('#booking_product_quantity_wanted').val(1);
            qty_wanted = $('#booking_product_quantity_wanted').val();
        }
        $('#booking_product_quantity_wanted').val(parseInt(qty_wanted));
        if (parseInt(qty_wanted) < 1 || parseInt(qty_wanted) > $('#max_available_qty').val()) {
            $.growl.error({title: '', message: invalidQtyErr});
            $('#booking_button').attr('disabled', true);
            return false;
            // $('#booking_product_quantity_wanted').val($('#max_available_qty').val());
        }
        $.ajax({
            url: wkBookingCartLink,
            data: {
                token: wk_static_token,
                action: 'booking_product_price_calc',
                date_from: $('#booking_date_from').val(),
                date_to: $('#booking_date_to').val(),
                quantity: $('#booking_product_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
                id_product_attribute: $('#idProductAttribute').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    if (result.max_avail_qty == 0) {
                        $('.unavailable_slot_err').show();
                        $('#booking_button').hide();
                        $('.wk_total_booking_price_container').hide();
                        $('#booking_button').attr('disabled', true);
                    } else {
                        $('.unavailable_slot_err').hide();
                        $('#booking_button').show();
                        $('.wk_total_booking_price_container').show();
                        $('#booking_button').attr('disabled', false);
                    }
                    $('.product_max_avail_qty_display').text(result.max_avail_qty);
                    $('#max_available_qty').val(result.max_avail_qty);
                    if (typeof result.productPriceRegular != 'undefined') {
                        $('.booking_total_price').html('<strike>'+result.productPriceRegular+'</strike> ' +result.productPrice);
                    } else {
                        $('.booking_total_price').text(result.productPrice);
                    }
                    // to show date ranges added in the selected date range
                    $('#bookings_in_select_range').empty();
                    if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                        html = '<label>' + bookings_in_select_range_label + '</label>';
                        html += '<table class="table table-stripped">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th>' + dateRangeText + '</th>';
                        html += '<th>' + priceText + '</th>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                        $(result.dateRangesBookingInfo).each(function(key, rangeInfo) {

                            html += '<tr>';
                            html += '<td>';
                            html += rangeInfo.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + rangeInfo.date_to;
                            html += '</td>';
                            html += '<td>';
                            if (typeof rangeInfo.regular_price != 'undefined') {
                                html += '<strike>'+rangeInfo.regular_price+'</strike> ' +rangeInfo.price;
                            } else {
                                html += rangeInfo.price;
                            }
                            html += '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody>';
                        html += '</table>';
                        $('#bookings_in_select_range').append(html);
                    }
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors');
                        $('#booking_button').attr('disabled', true);
                    }
                } else {
                    $(".booking_product_errors").hide();
                }
            }
        });
    });

});

function addTouchSpin() {

    if (!wk_hummingbird_theme) {
        $.each($('#booking_product_quantity_wanted'), function (index, spinner) {
            $(spinner).TouchSpin({
                verticalbuttons: true,
                verticalupclass: 'fa fa-angle-up material-icons touchspin-up',
                verticaldownclass: 'fa fa-angle-down material-icons touchspin-down',
                buttondown_class: 'btn btn-touchspin js-touchspin js-increase-product-quantity',
                buttonup_class: 'btn btn-touchspin js-touchspin js-decrease-product-quantity',
                min: parseInt($(spinner).attr('min'), 10),
                max: 1000000
            });
        });

        $.each($('.booking_time_slots_quantity_wanted'), function (index, spinner) {
            $(spinner).TouchSpin({
                verticalbuttons: true,
                verticalupclass: 'fa fa-angle-up material-icons touchspin-up',
                verticaldownclass: 'fa fa-angle-down material-icons touchspin-down',
                buttondown_class: 'btn btn-touchspin js-touchspin js-increase-product-quantity',
                buttonup_class: 'btn btn-touchspin js-touchspin js-decrease-product-quantity',
                min: parseInt($(spinner).attr('min'), 10),
                max: 1000000
            });
        });
    }
  }
