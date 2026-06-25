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
    {*To Show search form in the page*}
    <div id="stats_search_form" class="row">
        <div class="form-group col-sm-12">
            <div class="col-sm-2">
                <label class="pull-right contrl-label required form-control-static">{l s='Select duration' mod='psbooking'} </label>
            </div>
            <div class="col-sm-3">
                <input id="search_date_from" {if isset($date_from) && $date_from}value="{$date_from|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control" autocomplete="off" placeholder="From" name="date_from" readonly>
            </div>
            <div class="col-sm-3">
                <input id="search_date_to" {if isset($availablity_date_to) && $availablity_date_to}value="{$availablity_date_to|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control" autocomplete="off" placeholder="From" name="date_to" readonly>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary" name="availability-search-submit" id="availability-search-submit">
                    <span>{l s='Search' mod='psbooking'}</span>
                </button>
            </div>
        </div>
    </div>
    <hr>
    {*To Show calender in the page*}
    <div id="stats-calendar-info" class="row">
        <div id="stats-calendar" class="col-sm-12">
        </div>
    </div>
    <hr class="hr_style col-sm-8">
    {*To Show rooms representation colors*}
    <div id="rooms_presentation" class="row">
        <div class="col-sm-12 presentation_div">
            <div class="row">
                <div class="col-sm-6">
                    <p><i class="icon-circle" style="color:#7EC77B;"></i>&nbsp;&nbsp;{l s='If minimum one quantity of product/any slot on date is available' mod='psbooking'}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <p><i class="icon-circle" style="color:#CD5D5D;"></i>&nbsp;&nbsp;{l s='If no quantity is available of product/any slot on date' mod='psbooking'}</p>
                </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
                {if isset($productBookingType) && $productBookingType == 2}
                    <i><p>** {l s='Faded dates are indicating all slots in the date are disabled by admin.' mod='psbooking'}</p></i>
                {else}
                    <i><p>** {l s='Faded dates are indicating the disabled dates or disabled days by admin.' mod='psbooking'}</p></i>
                {/if}
            </div>
          </div>
        </div>
    </div>
</div>