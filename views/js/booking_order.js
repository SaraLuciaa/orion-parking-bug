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

    var ajax_pre_check_var = '';
    //abort ajax
    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }
    //abort ajax
    $(document).on('keyup', "#customer-search-input", function(event) {
        if (($('.customer_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40) {
                $(".customer_search_results_ul li:first").focus();
            } else if (event.which == 38) {
                $(".customer_search_results_ul li:last").focus();
            }
        } else {
            $('.customer_search_results_ul').empty().hide();

            if ($(this).val() != '') {
                abortRunningAjax();
                ajax_pre_check_var = $.ajax({
                    url: booking_order_url,
                    data: {
                        customer: $(this).val(),
                        action: 'SearchCustomer',
                        ajax: true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        var html = '';
                        if (data.status != 'failed') {
                            $.each(data, function(key, customer) {
                                html += '<li data-id_customer="' + customer.id_customer + '">' + customer.cname + '('+customer.email+')'+'</li>';
                            });
                            $('.customer_search_results_ul').html(html);
                            $('.customer_search_results_ul').show();
                            $('.error-block-customer').hide();
                        } else {
                            $('.error-block-customer').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.customer_search_results_ul li', function(event) {
        var id_customer = $(this).data('id_customer');
        $('#customer-search-input').val($(this).html());
        $('#customer_id').val($(this).data('id_customer'));
        $('.customer_search_results_ul').empty().hide();
        $('.address_div').show();
        abortRunningAjax();
        ajax_pre_check_var = $.ajax({
            url: booking_order_url,
            data: {
                id_customer: id_customer,
                action: 'GetCustomerAddress',
                ajax: true,
            },
            method: 'POST',
            dataType: 'JSON',
            success: function(data) {
                if (data.status != 'failed') {
                    var addresses_delivery_options = '';
                    var address_delivery_detail = '';
                    $.each(data, function(key, address) {
                        if (key == 0) {
                            address_delivery_detail = address.formated_address;
                        }
                        addresses_delivery_options += '<option value="'+address.id_address+'" >'+address.alias+'</option>';
                    });
                    $('#id_address_delivery').html(addresses_delivery_options);
                    $('#address_delivery_detail').html(address_delivery_detail);
                    $('.card_panel').show();
                    $('.error-block-address').hide();
                } else {
                    $('.error-block-address').show();
                    $('.card_panel').hide();
                }
            }
        });
    });

    $("#id_address_delivery").change(function() {
        updateAddresses();
    });

    $(document).on('keyup', "#product-search-input", function(event) {
        if (($('.product_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40) {
                $(".product_search_results_ul li:first").focus();
            } else if (event.which == 38) {
                $(".product_search_results_ul li:last").focus();
            }
        } else {
            $('.product_search_results_ul').empty().hide();

            if ($(this).val() != '') {
                abortRunningAjax();
                ajax_pre_check_var = $.ajax({
                    url: booking_product_price_plans_url,
                    data: {
                        room_type_name: $(this).val(),
                        with_combi: true,
                        action: 'SearchBookingProductByName',
                        ajax: true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        var html = '';
                        if (data.status != 'failed') {
                            $.each(data, function(key, booking_product) {
                                html += '<li data-id_product="' + booking_product.id_product + '" data-id_product_attribute="' + booking_product.id_product_attribute + '">' + booking_product.name + '</li>';
                            });
                            $('.product_search_results_ul').html(html);
                            $('.product_search_results_ul').show();
                            $('.error-block-product').hide();
                        } else {
                            $('.error-block-product').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.product_search_results_ul li', function(event) {
        $('#product-search-input').val('');
        var id_product = $(this).data('id_product');
        var id_product_attribute = $(this).data('id_product_attribute');
        $('.product_search_results_ul').empty().hide();
        ajax_pre_check_var = $.ajax({
            url: booking_order_url,
            data: {
                id_product: id_product,
                id_product_attribute: id_product_attribute,
                action: 'GetProductForBooking',
                ajax: true,
                id_cart: $('#wk_id_cart').val(),
            },
            method: 'POST',
            success: function(data) {
                $('#booking_product_interface').html(data);
            }
        });
    });

    $(document).on("focus", ".booking_date_from, .booking_date_to", function() {
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
                    selectedDate.setDate(selectedDate.getDate() + 1);
                } else {
                    selectedDate.setDate(selectedDate.getDate());
                }
                $(".booking_date_to").datepicker("option", "minDate", selectedDate);
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_cart: $('#wk_id_cart').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('#booking_button').attr('disabled', true);
                                $('.unavailable_slot_err').show();
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            $('.booking_total_price').text(result.productPrice);
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range + '</label>';
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
                                    html += rangeInfo.price;
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
                            }
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
        });
        $(".booking_date_to").datepicker({
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
                if ($('.booking_date_to').val()) {
                    var dateToVal = $('.booking_date_to').val().split("-");
                    var dateToVal = (dateToVal[2]) + '-' + (dateToVal[1]) + '-' + (dateToVal[0]);
                    if (dateToWork == dateToVal) {
                        dateClass = 'selectedCheckedDate';
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
                if (considerDateToConfiguration == 0) {
                    minDateTo.setDate(minDateTo.getDate() + 1);
                } else {
                    minDateTo.setDate(minDateTo.getDate());
                }
                $(".booking_date_to").datepicker("option", "minDate", minDateTo);
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
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_cart: $('#wk_id_cart').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('.unavailable_slot_err').show();
                                $('#booking_button').attr('disabled', true);
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            $('.booking_total_price').text(result.productPrice);
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range + '</label>';
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
                                    html += rangeInfo.price;
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
                            }
                        }
                    }
                });
            }
        });
    });

    $(document).on("focus", ".booking_time_slot_date", function() {
        var selectedJsonDates = selectedDatesJson;
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
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'booking_product_time_slots',
                        date: $('#booking_time_slot_date').val(),
                        quantity: $('#booking_time_slots_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                        id_cart: $('#wk_id_cart').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            var bookingTimeSlots = result.bookingTimeSlots;
                            var html = '';
                            if (bookingTimeSlots != 'empty') {
                                $.each(bookingTimeSlots, function(index, slot) {
                                    html += '<div class="time_slot_checkbox row">';
                                    html += '<label class="col-sm-4 form-control-static">';
                                    html += '<input type="checkbox" id="slot_checkbox_' + slot.id + '"';
                                    if (!slot.available_qty) {
                                        html += ' disabled="disabled ';
                                    }
                                    if (slot.checked) {
                                        html += ' checked="checked" ';
                                    }
                                    html += ' value="' + slot.id + '" class="product_blooking_time_slot">&nbsp;&nbsp;&nbsp';
                                    html += '<span class="time_slot_price">' + slot.formated_slot_price + '&nbsp;</span>&nbsp;&nbsp;'+for_txt+'&nbsp;&nbsp;';
                                    html += '<span class="time_slot_range">' + slot.time_slot_from + ' &nbsp;-&nbsp;' + slot.time_slot_to + '</span>';
                                    html += '</label>';
                                    if (slot.available_qty) {
                                        html += '<label class="col-sm-4" id="slot_quantity_container_' + slot.id + '">';
                                        html += '<div class="input-group col-sm-6">';
                                        html += '<input type="hidden" id="slot_max_avail_qty_' + slot.id + '" class="slot_max_avail_qty" value="' + slot.available_qty + '">';
                                        html += '<input type="text" class="booking_time_slots_quantity_wanted  form-control" value="1" min="1">';
                                        html += '<div class="input-group-addon" id="qty_avail_' + slot.id + '">/' + slot.available_qty + '</div>';
                                        html += '</div>';
                                    } else {
                                        html += '<label class="col-sm-4 form-control-static" id="slot_quantity_container_' + slot.id + '">';
                                        html += '<span class="booked_slot_text">' + slot_booked_text + '</span>';
                                    }
                                    html += '</label>';
                                    html += '</div>';
                                });
                                $('#booking_button').attr('disabled', false);
                                $('#booking_product_time_slots').html(html);
                                $(".unavailable_slot_err").hide();
                            } else {
                                $('#booking_product_time_slots').html(no_slots_available_text);
                                $('.unavailable_slot_err').show();
                                $('#booking_button').attr('disabled', true);
                            }
                            $('.booking_total_price').text(result.productTotalFeaturePriceFormated);
                            if (result.totalSlotsQty !== 'undefined' && result.totalSlotsQty == 0) {
                                $('#booking_button').attr('disabled', true);
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
                        }
                    }
                });
            },
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
        $.ajax({
            url: wkBookingCartLink,
            data: {
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $('#booking_time_slots_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    $('.booking_total_price').text(result.productPrice);
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
                }
            }
        });
    });

    $(document).on('blur', '.booking_time_slots_quantity_wanted', function() {
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
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $(this).val(),
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    $('.booking_total_price').text(result.productPrice);
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
                }
            }
        });
    });

    $(document).on('blur', '#booking_product_quantity_wanted', function() {
        var qty_wanted = $('#booking_product_quantity_wanted').val();
        if (qty_wanted == '' || !$.isNumeric(qty_wanted)) {
            $('#booking_product_quantity_wanted').val(1);
            qty_wanted = $('#booking_product_quantity_wanted').val();
        }
        $('#booking_product_quantity_wanted').val(parseInt(qty_wanted));
        if (parseInt(qty_wanted) < 1 || parseInt(qty_wanted) > $('#max_available_qty').val()) {
            $('#booking_product_quantity_wanted').val($('#max_available_qty').val());
        }
        $.ajax({
            url: wkBookingCartLink,
            data: {
                // token: wk_static_token,
                action: 'booking_product_price_calc',
                date_from: $('#booking_date_from').val(),
                date_to: $('#booking_date_to').val(),
                quantity: $('#booking_product_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
                id_cart: $('#wk_id_cart').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    if (result.max_avail_qty == 0 || result.showBookings) {
                        $('.unavailable_slot_err').show();
                        $('#booking_button').attr('disabled', true);
                    } else {
                        $('.unavailable_slot_err').hide();
                        $('#booking_button').attr('disabled', false);
                    }
                    $('.product_max_avail_qty_display').text(result.max_avail_qty);
                    $('#max_available_qty').val(result.max_avail_qty);
                    $('.booking_total_price').text(result.productPrice);
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
                    url: booking_order_url,
                    data: {
                        action: 'AddBookingProduct',
                        booking_type: booking_type,
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: quantity,
                        event_multiple: 0,
                        id_product: $('#product_page_product_id').val(),
                        id_product_attribute: $('#product_page_product_attribute_id').val(),
                        id_address_delivery: $('#id_address_delivery').val(),
                        customer_id: $('#customer_id').val(),
                        id_cart: $('#wk_id_cart').val()
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        $(".booking_loading_img").hide();
                        if (result.status == 'ok') {
                            $('#booking_button').attr('disabled', false);
                            $('#wk_id_cart').val(result.id_cart)
                            $('#js-process-order-link').attr('href', result.order_link);
                            // var index = $('.added_product_tbody').find('tr').length;
                            $('#booking_product_interface').html('');
                            $('#added_product_div').show();
                            var html = '';
                            $.each(result.products, function(key, booking_product) {
                                html += '<tr>';
                                    html += '<td>';
                                        html += booking_product.p_name;
                                    html += '</td>';
                                    html += '<td>';
                                        if (booking_product.type == wk_type_date_range || booking_product.type == wk_type_rental) {
                                            html += booking_product.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + booking_product.date_to;
                                        }
                                        if (booking_product.type == wk_type_time_slot) {
                                            html += booking_product.date_from + ' &nbsp; &nbsp;' + booking_product.slot_from + '-' + booking_product.slot_to;
                                        }
                                        if (booking_product.type == wk_type_event) {
                                            html += booking_product.date_from + ' &nbsp;';
                                            if (booking_product.slot_from != '00:00') {
                                                html += booking_product.slot_from + ' &nbsp;';
                                            }
                                            if (booking_product.date_to != '0000-00-00 00:00:00') {
                                                html += To_txt + ' &nbsp;' + booking_product.date_to + ' &nbsp;';
                                            }
                                            if (booking_product.slot_to != '00:00') {
                                                html += booking_product.slot_to + ' &nbsp;';
                                            }

                                        }
                                    html += '</td>';
                                    html += '<td>';
                                        html += booking_product.quantity;
                                    html += '</td>';
                                    html += '<td>';
                                        html += booking_product.unit_price;
                                    html += '</td>';
                                    html += '<td>';
                                        html += booking_product.total_price;
                                    html += '</td>';
                                    html += '<td>';
                                        html += '<a id-cart-booking="'+booking_product.id_cart_booking+'" href="#" class="remove_added_product btn btn-default"><i class="icon-trash"></i></a>';
                                    html += '</td>';
                            });
                            $('.added_product_tbody').html(html);
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml).show();
                            }
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
                url: booking_order_url,
                data: {
                    action: 'AddBookingProduct',
                    selected_slots: selectedSlots,
                    booking_type: booking_type,
                    date: $('#booking_time_slot_date').val(),
                    quantity: $('#booking_time_slots_quantity_wanted').val(),
                    id_product: $('#product_page_product_id').val(),
                    event_multiple: 1,
                    customer_id: $('#customer_id').val(),
                    id_cart: $('#wk_id_cart').val(),
                    id_address_delivery: $('#id_address_delivery').val(),
                },
                method: 'POST',
                dataType: 'json',
                success: function(result) {
                    $(".booking_loading_img").hide();
                    if (result.status == 'ok') {
                        $('#booking_button').attr('disabled', false);
                        $('#wk_id_cart').val(result.id_cart);
                        $('#js-process-order-link').attr('href', result.order_link);
                        $('#booking_product_interface').html('');
                        $('#added_product_div').show();
                        var html = '';
                        $.each(result.products, function(key, booking_product) {
                            html += '<tr>';
                                html += '<td>';
                                    html += booking_product.p_name;
                                html += '</td>';
                                html += '<td>';
                                    if (booking_product.type == wk_type_date_range || booking_product.type == wk_type_rental) {
                                        html += booking_product.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + booking_product.date_to;
                                    }
                                    if (booking_product.type == wk_type_time_slot || booking_product.type == wk_type_event) {
                                        html += booking_product.date_from + ' &nbsp; &nbsp;' + booking_product.slot_from + '-' + booking_product.slot_to;
                                    }
                                html += '</td>';
                                html += '<td>';
                                    html += booking_product.quantity;
                                html += '</td>';
                                html += '<td>';
                                    html += booking_product.unit_price;
                                html += '</td>';
                                html += '<td>';
                                    html += booking_product.total_price;
                                html += '</td>';
                                html += '<td>';
                                    html += '<a id-cart-booking="'+booking_product.id_cart_booking+'" href="#" class="remove_added_product btn btn-default"><i class="icon-trash"></i></a>';
                                html += '</td>';
                        });
                        $('.added_product_tbody').html(html);
                    }
                    if (result.errors != 'undefined') {
                        var errorHtml = '';
                        $(result.errors).each(function(key, error) {
                            errorHtml += error + '</br>';
                        });
                        if (errorHtml != '') {
                            $(".booking_product_errors").html(errorHtml).show();
                        }
                    }
                }
            });
        }
    });

    //To send order link to mail to customer
    $(document).on('click', '#js-send-process-order-email-btn', function(e) {
        e.preventDefault();
        $.ajax({
            url: booking_order_url,
            data: {
                action: 'SendMailValidateOrder',
                id_cart: $('#wk_id_cart').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.errors) {
                    $.growl.error({title: '', message: result.msg});
                } else {
                    $.growl.notice({title: '', message: result.msg});
                }

            }
        });
    });
    //To remove a row created with add new time slots buttons
    $(document).on('click', '.remove_added_product', function(e) {
        e.preventDefault();
        $.ajax({
            url: booking_order_url,
            data: {
                action: 'RemoveFromCart',
                id_cart_booking: $(this).attr('id-cart-booking'),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                } else {
                    $.growl.error({title: '', message: result.msg});
                }
            }
        });
        if ($(this).closest('.added_product_tbody').find('.remove_added_product').length == 1) {
            $('#added_product_div').hide();
            $(this).closest('tr').remove();
        } else {
            $(this).closest('tr').remove();
        }
    });

});

function updateAddresses()
{
    $.ajax({
        type:"POST",
        url: booking_order_url,
        async: true,
        dataType: "json",
        data : {
            ajax: "1",
            action: "updateAddresses",
            id_address_delivery: $('#id_address_delivery option:selected').val(),
        },
        success : function(res)
        {
            $('#address_delivery_detail').html(res.formated_address);
        }
    });
}