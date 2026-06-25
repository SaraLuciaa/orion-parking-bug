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

var calendar;
$(document).ready(function () {
    //Initialize calandar
    calendar = new tui.Calendar("#wk_calendar", {
        name: 'wk_calendar',
        defaultView: "month",
        taskView: false,
        scheduleView: true,
        usageStatistics: false,
        useCreationPopup: false,
        useDetailPopup: false,
        month: {
            isAlways6Week: false
        },
        template: {
            monthMoreClose: function () {
                return '<i class="icon-close"></i>';
            },
            allday: function(schedule) {
                var tem = '';
                wk_all_bookings.forEach(function(item) {
                    if (item.id == schedule.id) {
                        var color = '';
                        if (item.booking_type == wk_type_time_slot) {
                            color = '#03bd9e';
                        } else if (item.booking_type == wk_type_date_range) {
                            color = '#ff4040';
                        } else if (item.booking_type == wk_type_event) {
                            color = '#BBDC00';
                        } else if (item.booking_type == wk_type_rental) {
                            color = '#FF5583';
                        }

                        tem = '<span style="border-radius: 2px;color:#FFFFFF; background-color:' + color + ';display:block;overflow:hidden;padding: 0 5px;">' + item.ref + '</span>';
                    }
                });
                return tem;
            }
        }
    });
    //Initialize schedules
    if ((typeof wk_all_bookings != 'undefined') && (wk_all_bookings.length > 0)) {
        initializeSchedules(wk_all_bookings);
    }
    //Calendar events
    calendar.on({
        'clickSchedule': function (e) {
            showScheduleDetail(e);
        }
    });

    // --- FUNCIÓN PARA MOSTRAR MES/DÍA/AÑO EN EL TÍTULO ---
    function updateCalendarTitle() {
        var currentDate = calendar.getDate();
        var year = currentDate.getFullYear();
        // Sumamos 1 porque los meses en JavaScript van de 0 a 11
        var monthNumeric = currentDate.getMonth() + 1; 
        // Agrega un cero a la izquierda si el mes es menor a 10 (Ej: 06 en vez de 6)
        var formattedMonth = monthNumeric < 10 ? '0' + monthNumeric : monthNumeric;
        
        // Formato Mes/Año (Ya que la vista de mes no tiene un "día" específico seleccionado)
        // Si necesitas forzar un formato mm/dd/yyyy con el primer día del mes usa: formattedMonth + '/01/' + year
        var customDateFormat = formattedMonth + '/' + year;

        $('#wkcalendar_title span').text(customDateFormat);
    }

    // Ejecución inicial del título
    updateCalendarTitle();

    $('#wk_booking_monthview').on('click', function () {
        calendar.changeView('month');
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
    });
    $('#wk_booking_weekview').on('click', function () {
        calendar.changeView('week');
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
    });
    $('#wk_booking_dayview').on('click', function () {
        calendar.changeView('day');
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
    });

    // Actualiza el título al hacer clic para navegar entre meses (Anterior / Siguiente)
    $(document).on('click', function () {
        updateCalendarTitle();
    });

});

function initializeSchedules(wk_msp_schs) {
    var schs = [];
    $.each(wk_msp_schs, function (idx, booking) {
        var startTime = parseInt(booking.start_time);
        var endTime = parseInt(booking.end_time);
        var sched = {
            id: booking.id,
            calendarId: 1,
            title: booking.ref,
            category: 'allday',
            dueDateClass: '',
            color: '#FFFFFF',
            isAllDay: false,
            bgColor: '#9e5fff',
            start: new Date(startTime * 1000),
            isReadOnly: true,
            end: new Date((endTime * 1000))
        };
        schs.push(sched);
    });
    calendar.createSchedules(schs);
}
function showScheduleDetail(e) {
    wk_all_bookings.forEach(function(item) {
        if (item.id == e.schedule.id) {
            $('#wk_booking_product_name').text('' + item.product_name + '');
            $('#wk_booking_duration').text(''+item.duration+'');
            $('#wk_booking_quantity').text(''+item.quantity+'');
            $('#wk_booking_oder_view').attr('href', item.order_link);
            $('#wk_booking_detail_modal').modal('toggle');
        }
    });
}