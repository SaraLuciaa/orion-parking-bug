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

<div class="row disable_dates_information_block">
    {*disable special days*}
    <div class="alert alert-info">
        {l s='If you want to disable bookings for some special days then enable the option and select the days for which you want to disable the bookings. For Ex.- you can disable bookings for every sunday by selecting Sun.' mod='psbooking'}
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Disable special days' mod='psbooking'}</label>
        <div class="col-lg-6">
            <span class="switch prestashop-switch fixed-width-lg">
                <input class="is_disabled_week_days_exists" type="radio" value="1" id="disable_special_days_active_on" name="disable_special_days_active" {if isset($DISABLE_SPECIAL_DAYS_ACTIVE) && $DISABLE_SPECIAL_DAYS_ACTIVE} checked="checked"{/if}>
                <label for="disable_special_days_active_on">{l s='Yes' mod='psbooking'}</label>
                <input class="is_disabled_week_days_exists" type="radio" value="0" id="disable_special_days_active_off" name="disable_special_days_active" {if !isset($DISABLE_SPECIAL_DAYS_ACTIVE) || !$DISABLE_SPECIAL_DAYS_ACTIVE} checked="checked"{/if}>
                <label for="disable_special_days_active_off">{l s='No' mod='psbooking'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group disabled_week_days" {if !isset($DISABLE_SPECIAL_DAYS_ACTIVE) || !$DISABLE_SPECIAL_DAYS_ACTIVE} style="display:none;" {/if}>
        <label class="col-sm-3 control-label required" for="date_to" >
            {l s='Select special days' mod='psbooking'}
        </label>
        <div class="col-sm-7">
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="1" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('1', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Mon' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="2" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('2', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Tue' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="3" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('3', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Wed' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="4" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('4', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Thu' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="5" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('5', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Fri' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="6" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('6', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Sat' mod='psbooking'}</p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" name="disabled_special_days[]" value="0" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('0', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                <p>{l s='Sun' mod='psbooking'}</p>
            </div>
        </div>
    </div>
    <hr>
    {*disable specific days*}
    <div class="alert alert-info">
        {l s='If you want to disable bookings for some specific dates then enable the option and select the dates for which you want to disable the bookings. For Ex.- you can disable bookings for 25 december then select the date to disable the bookings.' mod='psbooking'}
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Disable specific dates' mod='psbooking'}</label>
        <div class="col-lg-6">
            <span class="switch prestashop-switch fixed-width-lg">
                <input class="is_disabled_specific_dates_exists" type="radio" value="1" id="disable_specific_days_active_on" name="disable_specific_days_active" {if isset($DISABLE_SPECIFIC_DAYS_ACTIVE) && $DISABLE_SPECIFIC_DAYS_ACTIVE} checked="checked"{/if}>
                <label for="disable_specific_days_active_on">{l s='Yes' mod='psbooking'}</label>
                <input class="is_disabled_specific_dates_exists" type="radio" value="0" id="disable_specific_days_active_off" name="disable_specific_days_active" {if !isset($DISABLE_SPECIFIC_DAYS_ACTIVE) || !$DISABLE_SPECIFIC_DAYS_ACTIVE} checked="checked"{/if}>
                <label for="disable_specific_days_active_off">{l s='No' mod='psbooking'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group disabled_specific_dates" {if !isset($DISABLE_SPECIFIC_DAYS_ACTIVE) || !$DISABLE_SPECIFIC_DAYS_ACTIVE} style="display:none;" {/if}>
        <div class="form-group pull-right">
            <button id="datepickerExpand" class="btn btn-default" type="button">
                <i class="icon-calendar-empty"></i>
                <span class="hidden-xs">
                    {l s='From' mod='psbooking'}
                    <strong class="text-info" id="datepicker-from-info">{$date_from|escape:'htmlall':'UTF-8'}</strong>
                    {l s='To' mod='psbooking'}
                    <strong class="text-info" id="datepicker-to-info">{$date_to|escape:'htmlall':'UTF-8'}</strong>
                    <strong class="text-info" id="datepicker-diff-info"></strong>
                </span>
                <i class="icon-caret-down"></i>
            </button>
        </div>
        {$calendar}
        <input type="hidden" name="disabled_specific_dates_json" id="disabled_specific_dates_json" {if isset($bookingDisableDates['disabled_dates_slots']) && $bookingDisableDates['disabled_dates_slots']} value="{$bookingDisableDates['disabled_dates_slots']|escape:'html':'UTF-8'}"{else} value="" {/if}/>
        <div style="clear:both;"></div>
        <div class="row">
            <div class="row selected_disabled_dates">
                {if isset($bookingDisableDates['disabled_dates_slots_array']) && $bookingDisableDates['disabled_dates_slots_array']}
                    {foreach $bookingDisableDates['disabled_dates_slots_array'] as $key => $disabedRange}
                        <div class="col-sm-3">
                            {if isset($productBookingType) && $productBookingType == 1}
                                <div class="disabled_date_container">
                                    <span>{$disabedRange['date_from']|escape:'htmlall':'UTF-8'} &nbsp; {l s='To' mod='psbooking'} &nbsp; {$disabedRange['date_to']|escape:'htmlall':'UTF-8'}</span><span  class="remove_disable_date" remove-date-index="{$key|escape:'htmlall':'UTF-8'}"><i class="icon-times-circle"></i></span>
                                </div>
                            {else if isset($productBookingType) && $productBookingType == 2}
                                <div class="disabled_date_container">
                                    <span><span>{$disabedRange['date_from']|escape:'htmlall':'UTF-8'} &nbsp; {l s='To' mod='psbooking'} &nbsp; {$disabedRange['date_to']|escape:'htmlall':'UTF-8'}</span>
                                    <span class="remove_disable_date" remove-date-index="{$key|escape:'htmlall':'UTF-8'}">
                                        <i class="icon-times-circle"></i>
                                    </span>
                                    <span date_end="{$disabedRange['date_to']|escape:'htmlall':'UTF-8'}" date_start="{$disabedRange['date_from']|escape:'htmlall':'UTF-8'}" class="edit_disable_date_slots">
                                        <i class="icon-pencil"></i>
                                    </span>
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>
</div>

{*Disable Dates Model*}
<div class="modal fade" id="disableTimeSlotsModal" tabindex="-1" role="dialog" aria-labelledby="disableTimeSlotsLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><i class="icon-clock-o"></i>&nbsp; {l s='Disable selected slots' mod='psbooking'}</h4>
			</div>
			<div class="modal-body booking-disable-slots-content">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default disableSlotsModalSubmit">{l s='Done' mod='psbooking'}</button>
			</div>
		</div>
	</div>
</div>
{*END*}