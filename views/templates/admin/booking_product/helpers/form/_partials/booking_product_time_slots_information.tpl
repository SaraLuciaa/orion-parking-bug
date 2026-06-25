{*
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

<div class="row time_slots_information_block">
    <input type="hidden" value="1" name="time_slots_data_save">
    <input type="hidden" value="{$idBookingProductInformation|escape:'htmlall':'UTF-8'}" name="idTable">
    <input class="form-control id_booking_product" type="hidden" name="id_booking_product" {if isset($edit) && $bookingProduct->id} value="{$bookingProduct->id|escape:'htmlall':'UTF-8'}"{/if} >
    <!-- for time slots type bookings -->
    <div class="form-group">
        <label for="product_condition" class="control-label col-lg-3">
            {l s='Time slots type ' mod='psbooking'}
        </label>
        <div class="col-lg-3">
            <select class="form-control" name="time_slot_type" id="time_slot_type">
                <option value="{WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DAY|escape:'htmlall':'UTF-8'}" {if isset($edit)}{if $timeSlotType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DAY}Selected="Selected"{/if}{/if}>
                {l s='Day' mod='psbooking'}
                </option>
                <option value="{WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE|escape:'htmlall':'UTF-8'}" {if isset($edit)}{if $timeSlotType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE}Selected="Selected"{/if}{/if}>
                {l s='Date' mod='psbooking'}
                </option>
            </select>
        </div>
    </div>
    <div id ="time_slot_type_date" style="{if $timeSlotType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DAY}display:none; {/if}">
        {if isset($bookingProductTimeSlots) && $bookingProductTimeSlots && $timeSlotType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE}
            <div class="time_slots_prices_content">
                {assign var=date_ranges_count value=0}
                {foreach $bookingProductTimeSlots as $key => $dateRangesInfo}
                    <div class="single_date_range_slots_container" date_range_slot_num="{$date_ranges_count|escape:'htmlall':'UTF-8'}">
                        <div  class="form-group table-responsive-row col-sm-5 booking_date_ranges">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th class="center">
                                        <span>{l s='Date from' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Date to' mod='psbooking'}</span>
                                    </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="center">
                                            <div class="input-group">
                                                <input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[{$date_ranges_count|escape:'htmlall':'UTF-8'}]" value="{$dateRangesInfo['date_from']|escape:'htmlall':'UTF-8'}" change_date="sloting_date_from_{$key|escape:'htmlall':'UTF-8'}" change_key =
                                                {$key|escape:'htmlall':'UTF-8'} readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-calendar"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="input-group">
                                                <input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[{$date_ranges_count|escape:'htmlall':'UTF-8'}]" value="{$dateRangesInfo['date_to']|escape:'htmlall':'UTF-8'}" change_date="sloting_date_to_{$key|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'" change_key =
                                                {$key|escape:'htmlall':'UTF-8'} readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-calendar"></i>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div  class="form-group table-responsive-row col-sm-7 time_slots_prices_table_div">
                            <table class="table time_slots_prices_table">
                                <thead>
                                    <tr>
                                        <th class="center">
                                            <span>{l s='Slot time from' mod='psbooking'}</span>
                                        </th>
                                        <th class="center">
                                            <span>{l s='Slot time to' mod='psbooking'}</span>
                                        </th>
                                        <th class="center">
                                            <span>{l s='Price (tax excl.)' mod='psbooking'}</span>
                                        </th>
                                        <th class="center">
                                            <span>{l s='Qty' mod='psbooking'}</span>
                                        </th>
                                        <th class="center">
                                            <span>{l s='Status' mod='psbooking'}</span>
                                        </th>
                                    </tr>
                                </thead>
                            <tbody>
                                {assign var=key_time_slot value=0}
                                {foreach $dateRangesInfo.time_slots as $timeSlots}
                                    <tr>
                                        <td class="center">
                                            <div class="input-group">
                                                <input autocomplete="off" type="hidden" name="time_slot_id{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['id_slot']|escape:'htmlall':'UTF-8'}">
                                                <input autocomplete="off" class="booking_time_from" type="text" name="booking_time_from{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['time_from']|escape:'htmlall':'UTF-8'}" readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-clock-o"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="input-group">
                                                <input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['time_to']|escape:'htmlall':'UTF-8'}" readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-clock-o"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="input-group">
                                                <input type="text" name="slot_range_price{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['slot_price']|round:2|escape:'htmlall':'UTF-8'}">
                                                <span class="input-group-addon">{$defaultCurrencySign|escape:'htmlall':'UTF-8'}</span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="input-group">
                                                <input type="text" name="slot_range_qty{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['slot_qty']|round:2|escape:'htmlall':'UTF-8'}">
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="slot_status_div">
                                                <input type="hidden" value="{if $timeSlots['active']}1{else}0{/if}" name="slot_active{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" class="time_slot_status">
                                                <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$timeSlots['active']}style="display:none;"{/if}>
                                                <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $timeSlots['active']}style="display:none;"{/if}>
                                            </div>
                                        </td>
                                        {if $key_time_slot}
                                            <td class="center">
                                                <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                            </td>
                                        {else}
                                            <td class="center">
                                                <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                            </td>
                                        {/if}
                                    </tr>
                                    {assign var=key_time_slot value=$key_time_slot+1}
                                {/foreach}
                            </tbody>
                            </table>
                            <div class="form-group">
                                <div class="col-lg-12 text-right">
                                    <button class="btn btn-default add_more_time_slot_price" type="button" data-size="s" data-style="expand-right">
                                        <i class="icon-calendar-empty"></i>
                                        {l s='Add more slots' mod='psbooking'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {assign var=date_ranges_count value=$date_ranges_count+1}
                {/foreach}
            </div>
        {else}
            <div class="time_slots_prices_content row">
                <div class="single_date_range_slots_container" date_range_slot_num="0">
                    <div  class="form-group table-responsive-row col-sm-5 booking_date_ranges">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="center">
                                        <span>{l s='Date from' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Date to' mod='psbooking'}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="center">
                                <div class="input-group">
                                    <input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="{$date_from|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}" change_date="sloting_date_from_1" change_key = "1" readonly>
                                    <span class="input-group-addon">
                                    <i class="icon-calendar"></i>
                                    </span>
                                </div>
                                </td>

                                <td class="center">
                                <div class="input-group">
                                    <input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="{$date_to|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}" change_date="sloting_date_to_1" change_key = "1" readonly>
                                    <span class="input-group-addon">
                                    <i class="icon-calendar"></i>
                                    </span>
                                </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div  class="form-group table-responsive-row col-sm-7 time_slots_prices_table_div">
                        <table class="table time_slots_prices_table">
                            <thead>
                                <tr>
                                    <th class="center">
                                        <span>{l s='Slot time from' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Slot time to' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Price (tax excl.)' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Qty' mod='psbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Status' mod='psbooking'}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="booking_time_from" type="text" name="booking_time_from0[]" value="{$timeFrom|escape:'htmlall':'UTF-8'}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-clock-o"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to0[]" value="{$timeTo|escape:'htmlall':'UTF-8'}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-clock-o"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input type="hidden" value="{$idBookingProductInformation|escape:'htmlall':'UTF-8'}" name="idTable">
                                            <input type="text" name="slot_range_price0[]" value="{$bookingProduct->price|escape:'htmlall':'UTF-8'|round:2}">
                                            <span class="input-group-addon">{$defaultCurrencySign|escape:'htmlall':'UTF-8'}</span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input type="hidden" value="{$idBookingProductInformation|escape:'htmlall':'UTF-8'}" name="idTable">
                                            <input type="text" name="slot_range_qty0[]" value="{$productQuantity|escape:'htmlall':'UTF-8'}">
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="1" name="slot_active0[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-default add_more_time_slot_price" type="button" data-size="s" data-style="expand-right">
                                    <i class="icon-calendar-empty"></i>
                                    {l s='Add more slots' mod='psbooking'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <div class="form-group">
            <div class="col-lg-12">
                <button id="add_more_date_ranges" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
                    <i class="icon-calendar-empty"></i>
                    {l s='Add more date ranges' mod='psbooking'}
                </button>
            </div>
        </div>
    </div>
    <div id ="time_slot_type_day" style="{if $timeSlotType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE}display:none; {/if}">
        <div class="day_time_slots_prices_content row">
            <div class="single_date_range_slots_container" date_range_slot_num="0">
                <div  class="form-group table-responsive-row col-sm-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="center">
                                    <span>
                                        {l s='Days' mod='psbooking'}
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="center">
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="1">{l s='Mon' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="2">{l s='Tue' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="3">{l s='Wed' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="4">{l s='Thu' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="5">{l s='Fri' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="6">{l s='Sat' mod='psbooking'}</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="days" value="7">{l s='Sun' mod='psbooking'}</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div  class="form-group table-responsive-row col-sm-7 time_slots_prices_table_div">
                    <table class="table time_slots_prices_table">
                        <thead>
                            <tr>
                                <th class="center">
                                    <span>{l s='Slot time from' mod='psbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Slot time to' mod='psbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Price (tax excl.)' mod='psbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Qty' mod='psbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Status' mod='psbooking'}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="center">
                                    <div class="input-group">
                                        <input autocomplete="off" class="booking_time_from day_time_from" type="text" name="booking_time_day_from0[]" value="{$timeFrom|escape:'htmlall':'UTF-8'}" readonly>
                                        <span class="input-group-addon">
                                            <i class="icon-clock-o"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="input-group">
                                        <input autocomplete="off" class="form-control booking_time_to day_time_to" type="text" name="booking_time_day_to0[]" value="{$timeTo|escape:'htmlall':'UTF-8'}" readonly>
                                        <span class="input-group-addon">
                                            <i class="icon-clock-o"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="input-group">
                                        <input type="text" class="day_range_price" name="slot_range_day_price0[]" value="{$bookingProduct->price|escape:'htmlall':'UTF-8'|round:2}">
                                        <span class="input-group-addon">{$defaultCurrencySign|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="input-group">
                                        <input type="text" class="day_range_qty" name="slot_range_day_qty0[]" value="{$productQuantity|escape:'htmlall':'UTF-8'}">
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="slot_status_div">
                                        <input type="hidden" value="1" name="slot_active0[]" class="time_slot_status day_time_slot_status">
                                        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img">
                                        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="col-lg-6 text-right">
                            <button class="btn btn-default add_more_time_slot_price_day" type="button" data-size="s" data-style="expand-right">
                                <i class="icon-calendar-empty"></i>
                                {l s='Add more slots' mod='psbooking'}
                            </button>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button class="btn btn-default add_day_wise_slot" type="button" data-size="s" data-style="expand-right">
                                <i class="icon-calendar-empty"></i>
                                {l s='Add' mod='psbooking'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
       <div id="final_day_time_data">
        <div class="monday">
            <h2 class="text-center">{l s='Monday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="monday_tbody">
                        {if isset($bookingProductTimeSlots['1'])}
                            {if isset($bookingProductTimeSlots['1']['time_slots']) && !empty(isset($bookingProductTimeSlots['1']['time_slots']))}
                                {foreach $bookingProductTimeSlots['1']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from1[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to1[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day1[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day1[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day1[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tuesday">
            <h2 class="text-center">{l s='Tuesday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="tuesday_tbody">
                        {if isset($bookingProductTimeSlots['2'])}
                            {if isset($bookingProductTimeSlots['2']['time_slots']) && !empty(isset($bookingProductTimeSlots['2']['time_slots']))}
                                {foreach $bookingProductTimeSlots['2']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from2[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to2[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day2[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day2[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day2[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="wednesday">
            <h2 class="text-center">{l s='Wednesday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="wednesday_tbody">
                        {if isset($bookingProductTimeSlots['3'])}
                            {if isset($bookingProductTimeSlots['3']['time_slots']) && !empty(isset($bookingProductTimeSlots['3']['time_slots']))}
                                {foreach $bookingProductTimeSlots['3']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from3[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to3[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day3[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day3[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day3[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="thursday">
            <h2 class="text-center">{l s='Thursday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="thursday_tbody">
                        {if isset($bookingProductTimeSlots['4'])}
                            {if isset($bookingProductTimeSlots['4']['time_slots']) && !empty(isset($bookingProductTimeSlots['4']['time_slots']))}
                                {foreach $bookingProductTimeSlots['4']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from4[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to4[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day4[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day4[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day4[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="friday">
            <h2 class="text-center">{l s='Friday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="friday_tbody">
                        {if isset($bookingProductTimeSlots['5'])}
                            {if isset($bookingProductTimeSlots['5']['time_slots']) && !empty(isset($bookingProductTimeSlots['5']['time_slots']))}
                                {foreach $bookingProductTimeSlots['5']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from5[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to5[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day5[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                     <td class="center">
                                        <input type="hidden" name="slot_range_qty_day5[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day5[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="saturday">
            <h2 class="text-center">{l s='Saturday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="saturday_tbody">
                        {if isset($bookingProductTimeSlots['6'])}
                            {if isset($bookingProductTimeSlots['6']['time_slots']) && !empty(isset($bookingProductTimeSlots['6']['time_slots']))}
                                {foreach $bookingProductTimeSlots['6']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from6[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to6[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day6[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day6[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day6[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sunday">
            <h2 class="text-center">{l s='Sunday' mod='psbooking'}</h2>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time from' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Slot time to' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Price (tax excl.)' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Qty' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Status' mod='psbooking'}
                                </span>
                            </th>
                            <th class="center wk-text-bold">
                                <span>
                                    {l s='Action' mod='psbooking'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="sunday_tbody">
                        {if isset($bookingProductTimeSlots['7'])}
                            {if isset($bookingProductTimeSlots['7']['time_slots']) && !empty(isset($bookingProductTimeSlots['7']['time_slots']))}
                                {foreach $bookingProductTimeSlots['7']['time_slots'] as $slots}
                                <tr>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_from7[]" value="{$slots['time_from']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_from']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="booking_time_day_to7[]" value="{$slots['time_to']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['time_to']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_price_day7[]" value="{$slots['slot_price']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_price']|escape:'htmlall':'UTF-8'|round:2}</span>
                                    </td>
                                    <td class="center">
                                        <input type="hidden" name="slot_range_qty_day7[]" value="{$slots['slot_qty']|escape:'htmlall':'UTF-8'}">
                                        <span>{$slots['slot_qty']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $slots['active']}1{else}0{/if}" name="slot_active_day7[]" class="time_slot_status">
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$slots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $slots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                    </td>
                                </tr>
                                {/foreach}
                            {/if}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
       </div>
    </div>
</div>