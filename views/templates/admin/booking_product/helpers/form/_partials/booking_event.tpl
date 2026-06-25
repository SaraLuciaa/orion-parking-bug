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
<div class="row event_block">
    <input type="hidden" value="1" name="event_info_save">
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label required"> {l s='Start date time' mod='psbooking'}</label>
                <div class="input-group">
                    <input autocomplete="off" class="event_start_date" type="text" name="event_start_date" value="{if isset($eventData->date_from)}{$eventData->date_from|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'} {$eventData->time_from|escape:'htmlall':'UTF-8'}{else}{$date_from|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y %H:%M'}{/if}" readonly>
                    <span class="input-group-addon">
                        <i class="icon-calendar"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="control-label required"> {l s='End date time' mod='psbooking'}</label>
                <div class="input-group">
                    <input autocomplete="off" class="event_end_date" type="text" name="event_end_date" value="{if isset($eventData->date_to)}{$eventData->date_to|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'} {$eventData->time_to|escape:'htmlall':'UTF-8'}{else}{$date_to|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y %H:%M'}{/if}" readonly>
                    <span class="input-group-addon">
                        <i class="icon-calendar"></i>
                    </span>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label">{l s='Language' mod='psbooking'}{include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}</label>
                {foreach from=$languages item=language}
                    <div class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
                        {assign var="event_lang" value="event_lang`$language.id_lang`"}
                        <input type="text"
                        id="event_lang_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        name="event_lang{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        value="{if isset($smarty.post.$event_lang)}{$smarty.post.$event_lang|escape:'htmlall':'UTF-8'}{elseif isset($eventData->language)}{$eventData->language[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                        class="form-control event_lang event_lang_all"
                        />
                    </div>
                {/foreach}
                {* <input class="event_lang" id="event_lang" type="text" name="event_lang" value="{if isset($eventData['language'])}{$eventData['language']}{/if}"> *}
            </div>
            <div class="col-md-6">
                <label class="control-label">{l s='Age group' mod='psbooking'}{include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}</label>
                {foreach from=$languages item=language}
                    <div class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
                        {assign var="age_group" value="age_group`$language.id_lang`"}
                        <input type="text"
                        id="age_group_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        name="age_group{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        value="{if isset($smarty.post.$age_group)}{$smarty.post.$age_group|escape:'htmlall':'UTF-8'}{elseif isset($eventData->age_group)}{$eventData->age_group[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                        class="form-control age_group age_group_all"
                        />
                    </div>
                {/foreach}
                {* <input class="age_group" id="age_group" type="text" name="age_group" value="{if isset($eventData['age_group'])}{$eventData['age_group']}{/if}"> *}
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label">{l s='Artist' mod='psbooking'}{include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}</label>
                {foreach from=$languages item=language}
                    {assign var="artist" value="artist`$language.id_lang`"}
                    <input type="text"
                    id="artist_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    name="artist{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    value="{if isset($smarty.post.$artist)}{$smarty.post.$artist|escape:'htmlall':'UTF-8'}{elseif isset($eventData->artist)}{$eventData->artist[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                    class="form-control artist_all wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
                {/foreach}
                {* <input class="artist" type="text" name="artist" value="{if isset($eventData['artist'])}{$eventData['artist']}{/if}"> *}
            </div>
            <div class="col-md-6">
                <label class="control-label">{l s='Organized by' mod='psbooking'}{include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}</label>
                {foreach from=$languages item=language}
                    {assign var="organized_by" value="organized_by`$language.id_lang`"}
                    <input type="text"
                    id="organized_by_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    name="organized_by{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    value="{if isset($smarty.post.$organized_by)}{$smarty.post.$organized_by|escape:'htmlall':'UTF-8'}{elseif isset($eventData->organized_by)}{$eventData->organized_by[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                    class="form-control organized_by_all wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
                {/foreach}
                {* <input class="organized_by" type="text" name="organized_by" value="{if isset($eventData['organized_by'])}{$eventData['organized_by']}{/if}"> *}
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label">{l s='Category' mod='psbooking'}{include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}</label>
                {foreach from=$languages item=language}
                    <div class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
                        {assign var="event_category" value="event_category`$language.id_lang`"}
                        <input type="text"
                        id="event_category_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        name="event_category{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        value="{if isset($smarty.post.$event_category)}{$smarty.post.$event_category|escape:'htmlall':'UTF-8'}{elseif isset($eventData->category)}{$eventData->category[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                        class="form-control event_category event_category_all"
                        />
                    </div>
                {/foreach}
                {* <input class="form-control updateCurrentText" id="event_category" type="text" name="event_category" value="{if isset($eventData['category'])}{$eventData['category']}{/if}"> *}
            </div>
            <div class="col-md-6">
                <label class="control-label">{l s='Banner image' mod='psbooking'}</label>
                <input id="event_banner" type="file" name="event_banner" class="hide" />
                <div class="dummyfile input-group">
                    <span class="input-group-addon"><i class="icon-file"></i></span>
                    <input id="event-banner-file-name" type="text" class="disabled" name="filename" readonly />
                    <span class="input-group-btn">
                        <button id="event-banner-file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                            <i class="icon-folder-open"></i> {l s='Choose a file' mod='psbooking'}
                        </button>
                    </span>
                </div>
                <div class="help-block">{l s='For best fit image size should be 830x400 ' mod='psbooking'}</div>
                {if isset($eventBannerImg)}
                    <div class="form-group event-banner-div">
                        <div class="col-lg-6" style="display: flex;">
                            <img
                                class="preview_banner" style="width: 100px;cursor: pointer;"
                                src="{$module_dir|escape:'htmlall':'UTF-8'}/psbooking/views/img/event/{$eventData->id_product|escape:'htmlall':'UTF-8'}/{$eventData->id_product|escape:'htmlall':'UTF-8'}_{$id_shop|escape:'htmlall':'UTF-8'}banner.jpg?time={$time|escape:'htmlall':'UTF-8'}"
                                title="{l s='Banner image' mod='psbooking'}">
                            <p style="margin-left:10px;">
                                <a href="#" class="btn btn-default" id="delete_event_banner">
                                    <i class="icon-trash"></i> {l s='Delete' mod='psbooking'}
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="modal fade" id="event_banner_delete_modal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="display: flex; border-bottom: none;">
                                    <h4 class="modal-title" style="font-size: 1rem; line-height: 1.5; font-weight: 700;">{l s='Delete banner image' mod='psbooking'}?</h4>
                                </div>
                                <div class="modal-footer" style="border-top: none;">
                                    <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
                                        {l s='No' mod='psbooking'}
                                    </button>
                                    <button type="button" value="confirm" class="btn btn-danger btn-lg" id-product="{$eventData->id_product|intval}">
                                        {l s='Yes' mod='psbooking'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="form-group">
                    <div class="col-lg-6">
                        <img
                            id="preview_banner" class="preview_banner" style="width: 100px;cursor: pointer;"
                            src="">
                    </div>
                </div>
                <div id="preview_modal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <img id="preview-modal-img" width="100%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <hr>
    <fieldset class="form-group">
        <div class="row">
            <div class="col-md-6 text-right">
                <label class="control-label">{l s='Have a multiple slot?' mod='psbooking'}</label>
            </div>
            <div class="col-md-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" id="multiple_slot_on" name="multiple_slot" {if isset($eventData->multiple_slot ) && $eventData->multiple_slot ==1} checked="checked"{/if}>
                    <label for="multiple_slot_on">{l s='Yes' mod='psbooking'}</label>
                    <input type="radio" value="0" id="multiple_slot_off" name="multiple_slot" {if isset($eventData->multiple_slot )} {if $eventData->multiple_slot ==0} checked="checked" {/if} {else}checked="checked"{/if}>
                    <label for="multiple_slot_off">{l s='No' mod='psbooking'}</label>
                    <a class="slide-button btn"></a>
                </span>
              </span>
            </div>
        </div>
    </fieldset>
    <hr>
    <div class="row event_time_slots_block">
        {if isset($bookingProductTimeSlots) && $bookingProductTimeSlots}
            <div class="time_slots_prices_content row">
                {assign var=date_ranges_count value=0}
                {foreach $bookingProductTimeSlots as $key => $dateRangesInfo}
                    <div class="single_date_range_slots_container" date_range_slot_num="{$date_ranges_count|escape:'htmlall':'UTF-8'}">
                        <div class="form-group table-responsive-row col-sm-3 booking_date_ranges">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th class="center">
                                        <span>{l s='Date' mod='psbooking'}</span>
                                    </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="form-control event_sloting_date" type="text" name="event_sloting_date[{$date_ranges_count|escape:'htmlall':'UTF-8'}]" value="{$dateRangesInfo['date_from']|escape:'htmlall':'UTF-8'}" change_date="event_sloting_date_{$key|escape:'htmlall':'UTF-8'}" change_key = "{$key|escape:'htmlall':'UTF-8'}" readonly>
                                            <span class="input-group-addon">
                                            <i class="icon-calendar"></i>
                                            </span>
                                        </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div  class="form-group table-responsive-row col-sm-9 time_slots_prices_table_div">
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
                                                <input type="text" name="slot_range_price{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['slot_price']|escape:'htmlall':'UTF-8'}">
                                                <span class="input-group-addon">{$defaultCurrencySign|escape:'htmlall':'UTF-8'}</span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="input-group">
                                                <input type="text" name="slot_range_qty{$date_ranges_count|escape:'htmlall':'UTF-8'}[]" value="{$timeSlots['slot_qty']|escape:'htmlall':'UTF-8'|round:2}">
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
                    <div class="form-group table-responsive-row col-sm-3 booking_date_ranges">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="center">
                                        <span>{l s='Date' mod='psbooking'}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="center">
                                <div class="input-group">
                                    <input autocomplete="off" class="form-control event_sloting_date" type="text" name="event_sloting_date[]" value="" change_date="event_sloting_date_1" change_key = "1" readonly>
                                    <span class="input-group-addon">
                                    <i class="icon-calendar"></i>
                                    </span>
                                </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div  class="form-group table-responsive-row col-sm-9 time_slots_prices_table_div">
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
                    {l s='Add more date' mod='psbooking'}
                </button>
            </div>
        </div>
    </div>
</div>