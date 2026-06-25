{**
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
*}
<style>
    .chosen-choices .search-field input {
        width: 100% !important;
    }

    .tui-full-calendar-allday-right.tui-full-calendar-right {
        -moz-margin-end: 10px;
    }
</style>
<div class="panel" style="padding:0;">
    <div id="wk_book_cal_menu">
        <div id="wk_book_cal_menu-navi">
            <button type="button" class="btn btn-default btn-sm move-today" onclick="calendar.today();"
                data-action="move-today">
                {l s='Today' mod='psbooking'}
            </button>
            <button type="button" class="btn btn-default btn-sm move-day" onclick="calendar.prev();"
                data-action="move-prev">
                <i class="icon-arrow-left" data-action="move-prev"></i>
            </button>
            <button type="button" class="btn btn-default btn-sm move-day" onclick="calendar.next();"
                data-action="move-next">
                <i class="icon-arrow-right" data-action="move-next"></i>
            </button>
        </div>
        <span id="renderRange" class="render-range"></span>
        <div id="wk_book_cal_menu-naviright" class="pull-right btn-group">
            <button type="button" class="btn btn-default active" id="wk_booking_monthview">
                {l s='Month' mod='psbooking'}
            </button>
            <button type="button" class="btn btn-default" id="wk_booking_weekview">
                {l s='Week' mod='psbooking'}
            </button>
            <button type="button" class="btn btn-default" id="wk_booking_dayview">
                {l s='Day' mod='psbooking'}
            </button>
        </div>
    </div>
    <h2 id="wkcalendar_title" class="text-center"><span></span></h2>
    <div id="wk_calendar"></div>
    <div id="wk_booking_detail_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="font-weight: 600;">{l s='Booking details' mod='psbooking'}</h4>
                </div>
                <div class="modal-body">
                    <div style="border: 1px solid #e5e5e5;border-width: 0 1px;">
                        <hr style="margin: 0;">
                        <div class="row wk_booking_detail_row">
                            <b class=" col-sm-6 text-right">{l s='Product name -' mod='psbooking'}</b>
                            <div class="col-sm-6" id="wk_booking_product_name">
                            </div>
                        </div>
                        <div class="row wk_booking_detail_row">
                            <b class=" col-sm-6 text-right">{l s='Duration -' mod='psbooking'}</b>
                            <div class="col-sm-6" id="wk_booking_duration">
                            </div>
                        </div>
                        <div class="row wk_booking_detail_row">
                            <b class=" col-sm-6 text-right">{l s='Quantity -' mod='psbooking'}</b>
                            <div class="col-sm-6" id="wk_booking_quantity">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a target="_blank" href="" class="btn btn-warning" id="wk_booking_oder_view">{l s='View order' mod='psbooking'}</a>
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal">{l s='Close' mod='psbooking'}</button>
                </div>
            </div>
        </div>
    </div>
</div>
