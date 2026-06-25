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

{extends file=$layout}
{block name='content'}
    <div class="row wk-white-bckgrnd">
        <div class="col-md-12 col-xs-12 col-sm-12 mt-1">
            <div class="clearfix">
                {if isset($eventBannerImg)}
                    <img
                    src="{$banner_img|escape:'htmlall':'UTF-8'}" width="100%"/>
                {/if}
            </div>
        </div>
    </div>
    <div class="row wk-white-bckgrnd">
        <div class="col-md-8 col-xs-12 col-sm-12 p-1">
            <h1>{$product_name|escape:'htmlall':'UTF-8'}</h1>
            <div class="event_info">
                {if isset($e_language) && !empty($e_language)}
                    {$e_language|escape:'htmlall':'UTF-8'}
                {/if}
                {if isset($e_age_group) && !empty($e_age_group)}
                    | {$e_age_group|escape:'htmlall':'UTF-8'}
                {/if}
                {if isset($durationText) && !empty($durationText)}
                    | {$durationText|escape:'htmlall':'UTF-8'}
                {/if}
            </div>
            {if !$timeGone}
                <div class="product-price event_price">
                    {if isset($show_regular_price_after_discount) && $show_regular_price_after_discount && isset($productFeaturePriceRegular)}
                        <strike>{$productFeaturePriceRegular|escape:'htmlall':'UTF-8'}</strike>
                    {/if}
                    {$productFeaturePrice|escape:'htmlall':'UTF-8'}
                    {if isset($show_onwards) && $show_onwards}
                        <span class="onwards">{l s='onwards' mod='psbooking'}</span>
                    {/if}
                </div>
            {/if}
            {if isset($e_artist) && !empty($e_artist)}
                <div class="mt-1">
                    <span class="heading">{l s='Artist' mod='psbooking'}:</span>
                    <span>{$e_artist|escape:'htmlall':'UTF-8'}</span>
                </div>
            {/if}
            {if isset($summary) && !empty($summary)}
                <div class="mt-2">
                    <span class="heading">{l s='Summary' mod='psbooking'}:</span>
                    <div class="mt-1">{$summary nofilter}</div>
                </div>
            {/if}
            {if isset($description) && !empty($description)}
                <div class="mt-2">
                    <span class="heading">{l s='Description' mod='psbooking'}:</span>
                    <div class="mt-1">{$description nofilter}</div>
                </div>
            {/if}
        </div>
        <div class="col-md-4 col-xs-12 col-sm-12 p-1">
            <div class="wk-right-container">
                {* start form fields for ajax *}
                <div>
                    <input type="hidden" id="booking_date_from" value="{$e_date_from|escape:'htmlall':'UTF-8'}" />
                    <input type="hidden" id="booking_date_to" value="{$e_date_to|escape:'htmlall':'UTF-8'}" />
                    <form action="{$urls.pages.cart|escape:'htmlall':'UTF-8'}" method="post" id="add-to-cart-or-refresh" style="display:none;">
                        <input type="hidden" name="token" value="{$static_token|escape:'htmlall':'UTF-8'}">
                        <input type="hidden" name="id_product" value="{$idProduct|escape:'htmlall':'UTF-8'}" id="product_page_product_id">
                        <input type="hidden" name="id_customization" value="0" id="product_customization_id">
                        <input type="hidden" name="qty" id="quantity_wanted" value="1" min="1" class="">
                        <button class="add-to-cart" data-button-action="add-to-cart" type="submit" style="display:none;">
                        </button>
                    </form>
                </div>
                {* end form fields for ajax *}
                <div class="wk-right-container-sub">{l s='Date & Time' mod='psbooking'}:</div>
                <div class="wk-date-time-venue">
                    {Tools::displayDate($e_date_from)|escape:'htmlall':'UTF-8'}
                    {if $e_time_from != '00:00'}{$e_time_from|escape:'htmlall':'UTF-8'}{/if}
                    {l s='To' mod='psbooking'}
                    {Tools::displayDate($e_date_to)|escape:'htmlall':'UTF-8'}
                    {if $e_time_to != '00:00'}{$e_time_to|escape:'htmlall':'UTF-8'}{/if}
                </div>
                {if $e_addres}
                    <div class="mt-1 wk-right-container-sub">{l s='Venue' mod='psbooking'}:</div>
                    <div class="wk-date-time-venue">{$e_addres|escape:'htmlall':'UTF-8'} </div>
                    <div class="map-div">
                        <div id="map"></div>
                    </div>
                {/if}
                {if !$timeGone}
                    {if $multiple_slot == 1}
                        {assign var="checkedSlotPrice" value=0}
                        {assign var="checkedSlotPriceRegular" value=0}
                        <hr>
                        <div id="booking_product_time_slots" class="mt-1">
                            {if isset($bookingTimeSlots) && $bookingTimeSlots}
                                {foreach $bookingTimeSlots as $time_slot}
                                    {if $time_slot['checked']}
                                        {if isset($time_slot['formated_slot_price_regular'])}
                                            {assign var="checkedSlotPriceRegular" value=$time_slot['formated_slot_price_regular']}
                                        {/if}
                                        {assign var="checkedSlotPrice" value=$time_slot['formated_slot_price']}
                                    {/if}
                                    <div class="time_slot_checkbox row mb-1">
                                        <label class="col-md-12 col-lg-8 text-justify">
                                        <div class="product-price" style="display: inline;">
                                            <input {if !$time_slot['available_qty']}disabled="disabled"{/if} {if $time_slot['checked']|escape:'htmlall':'UTF-8'}checked="checked"{/if} type="checkbox" data-slot_price="{$time_slot['price']|escape:'htmlall':'UTF-8'}" value="{$time_slot['id']|escape:'htmlall':'UTF-8'}" class="product_blooking_time_slot">
                                                {if isset($time_slot['formated_slot_price_regular']) && $show_regular_price_after_discount}
                                                    <strike>{$time_slot['formated_slot_price_regular']|escape:'htmlall':'UTF-8'}</strike>
                                                {/if}
                                                {$time_slot['formated_slot_price']|escape:'htmlall':'UTF-8'}

                                            </div>
                                            <div style="margin-top: 5px;">
                                                {Tools::displayDate($time_slot['date_from'])|escape:'htmlall':'UTF-8'}
                                                {$time_slot['time_slot_from']|escape:'htmlall':'UTF-8'}
                                                {$time_slot['hours']|escape:'htmlall':'UTF-8'} {l s='Hr' mod='psbooking'} {if $time_slot['minutes']>0}{$time_slot['minutes']|escape:'htmlall':'UTF-8'} {l s='Mins' mod='psbooking'}{/if}
                                            </div>
                                            <div id="max_span" style="margin-top: 5px;">
                                                <span class="product_max_avail_qty_display">{l s='Max' mod='psbooking'} - {$time_slot['available_qty']|escape:'htmlall':'UTF-8'}</span>
                                            </div>
                                        </label>
                                        {if $time_slot['available_qty']}
                                            <label class="col-md-12 col-lg-4" id="slot_quantity_container_{$time_slot['id']|escape:'htmlall':'UTF-8'}">
                                            <input type="hidden" id="slot_max_avail_qty_{$time_slot['id']|escape:'htmlall':'UTF-8'}" class="slot_max_avail_qty" value="{$time_slot['available_qty']|escape:'htmlall':'UTF-8'}">
                                            <input type="text" class="booking_time_slots_quantity_wanted  form-control" value="{$minimal_quantity|escape:'htmlall':'UTF-8'}" min="{$minimal_quantity|escape:'htmlall':'UTF-8'}">
                                            </label>
                                        {else}
                                            <label class="col-md-12 col-lg-4 form-control-static" id="slot_quantity_container_{$time_slot['id']|escape:'htmlall':'UTF-8'}">
                                            <span class="booked_slot_text">{l s='Slot Booked' mod='psbooking'}!</span>
                                            </label>
                                        {/if}
                                    </div>
                                {/foreach}
                            {else}
                                {l s='No slots available' mod='psbooking'}
                            {/if}
                        </div>
                        <hr>
                        <div class="row mt-1">
                            <div class="col-md-6 product-price">
                                <span class="event_price booking_total_price" style="font-size: 15px;">
                                    {if $show_regular_price_after_discount}
                                        <strike>{$checkedSlotPriceRegular|escape:'htmlall':'UTF-8'}</strike>
                                    {/if}
                                    {$checkedSlotPrice|escape:'htmlall':'UTF-8'}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <p class="col-sm-12 alert-danger booking_product_errors"></p>
                                <img src="{$module_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/ajax-loader.gif" class="booking_loading_img" alt={l s='Not Found' mod='psbooking'}/>
                                {if !$is_catalog_mode}
                                    <button button class="btn btn-primary btn-lg btn-block" id="booking_button"  booking_type="3">{l s='Book Now' mod='psbooking'}</button>
                                {/if}
                            </div>
                        </div>
                    {else}
                        <div class="row mt-1">
                            <div class="col-md-6">
                                <input
                                    type="text"
                                    id="booking_product_quantity_wanted"
                                    value="{$minimal_quantity|escape:'htmlall':'UTF-8'}"
                                    class="input-group form-control"
                                    min="{$minimal_quantity|escape:'htmlall':'UTF-8'}"
                                    aria-label="{l s='Quantity' mod='psbooking'}"
                                >
                            </div>
                            <div class="col-md-6 product-price">
                                <span class="event_price booking_total_price">
                                    {if isset($show_regular_price_after_discount) && $show_regular_price_after_discount && isset($productFeaturePriceRegular)}
                                        <strike>{$productFeaturePriceRegular|escape:'htmlall':'UTF-8'}</strike>
                                    {/if}
                                    {$productFeaturePrice|escape:'htmlall':'UTF-8'}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12" style="text-align: center;">
                                <p class="col-sm-12 alert-danger booking_product_errors"></p>
                                <img src="{$module_dir|escape:'htmlall':'UTF-8'}psbooking/views/img/ajax-loader.gif" class="booking_loading_img" alt={l s='Not Found' mod='psbooking'}/>
                                {if !$is_catalog_mode}
                                    <button button class="btn btn-primary btn-lg btn-block" id="booking_button"  booking_type="3">{l s='Book Now' mod='psbooking'}</button>
                                {/if}
                            </div>
                        </div>
                    {/if}
                {else}
                    <div class="alert alert-danger mt-1" role="alert">
                    {l s='Ohh! Booking time has gone.' mod='psbooking'}
                    </div>
                {/if}
            </div>
            {if in_array(1, [$whatsapp_share, $facebook_share, $instagram_share, $twitter_share, $clipboard_share])}
                <div class="wk-right-container mt-1">
                    <div class="wk-right-container-sub">{l s='Share' mod='psbooking'}:</div>
                    <div class="share_btn">
                        {if $whatsapp_share}
                            <a href="whatsapp://send?text={$share_link|escape:'htmlall':'UTF-8'}" data-action="share/whatsapp/share" title="{l s='Share on whatsapp' mod='psbooking'}"
                            target="_blank">
                                <img src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon/whatsapp.png">
                            </a>
                        {/if}
                        {if $facebook_share}
                            <a href="https://www.facebook.com/sharer/sharer.php?u={$share_link|escape:'htmlall':'UTF-8'}"
                            onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"
                            target="_blank" title="{l s='Share on facebook' mod='psbooking'}">
                                <img src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon/fb.png">
                            </a>
                        {/if}
                        {if $instagram_share}
                            <a href="https://www.instagram.com/?url={$share_link|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener"  title="{l s='Share on instagram' mod='psbooking'}">
                                <img src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon/Group.png">
                            </a>
                        {/if}
                        {if $twitter_share}
                            <a href="https://twitter.com/share?url={$share_link|escape:'htmlall':'UTF-8'}&via=TWITTER_HANDLE"
                            onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"
                            target="_blank" title="{l s='Share on twitter' mod='psbooking'}">
                                <img src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon/twitter.png">
                            </a>
                        {/if}
                        {if $clipboard_share}
                            <a class="copy_text" data-toggle="tooltip" data-placement="right" title="{l s='Copy to clipboard' mod='psbooking'}" href="{$share_link|escape:'htmlall':'UTF-8'}"><img src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon/copy.png"></a>
                        {/if}
                    </div>
                </div>
            {/if}
        </div>
    </div>
    <div class="row wk-white-bckgrnd mb-1">
        {if $display_related_events}
                <div class="col-md-12 col-xs-12 col-sm-12">
                    {if isset($booking_shop_product) && !empty($booking_shop_product)}
                        <div class="wk-right-container mb-1">
                                <div class="box-account">
                                    <div class="box-content">
                                        <div class="row">
                                            <label class="col-md-6 wk_text_left">
                                                <div class="wk-right-container-sub">{l s='Related Events' mod='psbooking'}:</div>
                                            </label>
                                        </div>
                                        {if isset($booking_shop_product) && !empty($booking_shop_product)}
                                            <div id="product-slider_block_center" class="wk-product-slider">
                                                <ul class="mp-prod-slider {if $booking_shop_product|@count > 4}mp-bx-slider{/if}">
                                                    {foreach $booking_shop_product as $key => $product}
                                                        <li class="wk_relative{if $booking_shop_product|@count <= 4} wk-product-out-slider{/if}" {if $key == 3}style="margin-right:0;"{/if}>
                                                            <div class="related_box">
                                                                <div class="related_img_div">
                                                                    {if $product.image}
                                                                        <img width="100%" class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'home_default')|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'htmlall':'UTF-8'}">
                                                                    {else}
                                                                        <img width="100%" class="replace-2x img-responsive" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/psbooking/views/img/home-default.jpg" alt="{$product.name|escape:'htmlall':'UTF-8'}">
                                                                    {/if}
                                                                </div>
                                                                <div class="related_content_div">
                                                                    <div class="other_event_name">{$product.name|escape:'htmlall':'UTF-8'|truncate:50:"...":true}</div>
                                                                    <div class="event_info other_event_time">
                                                                        {Tools::displayDate($product.date_from)|escape:'htmlall':'UTF-8'}
                                                                        {if $product.time_from != '00:00'}{$product.time_from|escape:'htmlall':'UTF-8'}{/if}
                                                                        {l s='To' mod='psbooking'}
                                                                        {Tools::displayDate($product.date_to)|escape:'htmlall':'UTF-8'}
                                                                        {if $product.time_to != '00:00'}{$product.time_to|escape:'htmlall':'UTF-8'}{/if}
                                                                    </div>
                                                                    <div class="event_info other_event_add">
                                                                        {$product.address|escape:'htmlall':'UTF-8'|truncate:30:"...":true}
                                                                    </div>
                                                                    <div class="row mt-1">
                                                                        <div class="col-xs-6 product-price">
                                                                            <span class="event_price">{$product.price|escape:'htmlall':'UTF-8'}</span>
                                                                        </div>
                                                                        <div class="col-xs-6">
                                                                         <a href="{$link->getProductLink($product.objproduct)|escape:'htmlall':'UTF-8'}" target="_blank" class="wk_float_right btn btn-primary btn-lg">{l s='Book Now' mod='psbooking'}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                        </div>
                    {/if}
                </div>
        {/if}
    </div>
{/block}
