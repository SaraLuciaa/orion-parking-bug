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

{if isset($bookingProductCartInfo)}
  {foreach $bookingProductCartInfo as $key => $productBooking}
  <div class="cart_pop_up_data range-period">
      <div class="booking-dates">
        {if $productBooking['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $productBooking['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
          {Tools::displayDate($productBooking['date_from'])|escape:'htmlall':'UTF-8'}&nbsp;
          {l s='to' mod='psbooking'}&nbsp;
          {Tools::displayDate($productBooking['date_to'])|escape:'htmlall':'UTF-8'}
        {else if $productBooking['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
          {Tools::displayDate($productBooking['date_from'])|escape:'htmlall':'UTF-8'}&nbsp;
          {$productBooking['time_from']|escape:'htmlall':'UTF-8'} - {$productBooking['time_to']|escape:'htmlall':'UTF-8'}
        {else if $productBooking['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
          {Tools::displayDate($productBooking['date_from'])|escape:'htmlall':'UTF-8'} {if $productBooking['time_from']!='00:00'}{$productBooking['time_from']|escape:'htmlall':'UTF-8'}{/if}{if $productBooking['date_to'] != '0000-00-00 00:00:00'}</br> {l s='to' mod='psbooking'}</br>{Tools::displayDate($productBooking['date_to'])|escape:'htmlall':'UTF-8'}{if $productBooking['time_to']!='00:00'}{$productBooking['time_to']|escape:'htmlall':'UTF-8'}{/if}{/if}
        {/if}
      </div>
      <div class="booking-quantity">
        <span style="font-weight: bold;">{l s='Total quantity' mod='psbooking'}</span>&nbsp;&nbsp;-&nbsp;&nbsp;{$productBooking['quantity']|escape:'htmlall':'UTF-8'}
      </div>
      <div class="booking-price">
        <span style="font-weight: bold;">{l s='Total price' mod='psbooking'}</span>&nbsp;&nbsp;-&nbsp;&nbsp;{$productBooking['totalPriceTE']|escape:'htmlall':'UTF-8'} ({l s='tax excl.' mod='psbooking'})
      </div>
  </div>
  {/foreach}
{/if}
<style type="text/css">
  .product-name ~ p {
    display: none !important;
  }

  .booking-dates {
    font-weight:bold;
  }
  .cart_pop_up_data {
    font-size: 12px;
    color: #333;
    border-bottom:1px solid #333;
  }
</style>
