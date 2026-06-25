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
    <div class="row wk_search_row" >
        <div id="wrapper_header" class="col-md-12">
            <div class="wk-availability-heading wk-text-center">{l s='Availability' mod='psbooking'}</div>
            <div id="wrapper_header_right" class="row">
                <form method="post" action="{$link->getModuleLink('psbooking', 'bookingproduct')|escape:'htmlall':'UTF-8'}" style="width:100%;">
                    <div class="row" style="margin-left: 0px;margin-right: 0px;">
                        <div class="col-md-2 form-group">
                            <label>
                                {l s='Type' mod='psbooking'}
                            </label>
                            <div>
                                <select class="form-control" name="type">
                                    <option value="0">{l s='All' mod='psbooking'}</option>
                                    {foreach $booking_types as $key => $booking_type}
                                        <option value="{$key|escape:'htmlall':'UTF-8'}" {if ($type == $key)}selected{/if}>{$booking_type|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>
                                {l s='From' mod='psbooking'}
                            </label>
                            <div>
                            <input id="search_date_from" type="text" class="form-control datepicker-input" autocomplete="off" placeholder="{l s='From' mod='psbooking'}" name="date_from" readonly="" value="{if isset($date_from)}{$date_from|escape:'htmlall':'UTF-8'}{/if}">
                            </div>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>
                                {l s='To' mod='psbooking'}
                            </label>
                            <div>
                                <input id="search_date_to" type="text" class="form-control datepicker-input" autocomplete="off" placeholder="{l s='To' mod='psbooking'}" name="date_to" readonly="" value="{if isset($date_to)}{$date_to|escape:'htmlall':'UTF-8'}{/if}">
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>
                                {l s='Quantity' mod='psbooking'}
                            </label>
                            <div>
                                <input type="number" class="form-control" placeholder="{l s='Quantity' mod='psbooking'}" name="quantity" value="{if isset($quantity)}{$quantity|escape:'htmlall':'UTF-8'}{/if}">
                            </div>
                        </div>
                        <div class="col-md-2 wk_search_div" {if isset($wk_hummingbird_theme) && $wk_hummingbird_theme}style="margin-top:19px;"{/if}>
                            <button type="submit" class="btn btn-primary wk_btn_extra" id="wk_store_search">
                                <span>{l s='Search' mod='psbooking'}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {if isset($total_items) && !empty($total_items)}
        <div class="row wk_search_row">
            <div id="js-product-list-top" class="row products-selection">
                <div class="col-md-6 hidden-sm-down total-products">
                    {if $total_items > 1}
                        <p>{l s='There are %product_count% products.' mod='psbooking' sprintf=['%product_count%' => $total_items]}</p>
                    {elseif $total_items > 0}
                        <p>{l s='There is 1 product.' mod='psbooking'}</p>
                    {/if}
                </div>
                <div class="col-md-6">
                    <div class="row sort-by-row">
                        <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by' mod='psbooking'}:</span>
                        <div class="col-sm-12 col-xs-12 col-md-9 products-sort-order dropdown">
                            <button class="btn-unstyle select-title" rel="nofollow" data-toggle="dropdown" aria-label="Sort by selection" aria-haspopup="true" aria-expanded="false">
                            {if ($order_selected == 1)}
                                {l s='Name, A to Z' mod='psbooking'}
                            {elseif ($order_selected == 2)}
                                {l s='Name, Z to A' mod='psbooking'}
                            {elseif ($order_selected == 3)}
                                {l s='Price, low to high' mod='psbooking'}
                            {elseif ($order_selected == 4)}
                                {l s='Price, high to low' mod='psbooking'}
                            {else}
                                {l s='Select' mod='psbooking'}
                            {/if}
                                <i class="material-icons float-xs-right"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', array('orderby' => 'name', 'orderway' => 'asc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to))|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Name, A to Z' mod='psbooking'}
                            </a>
                                <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', array('orderby' => 'name', 'orderway' => 'desc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to))|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Name, Z to A' mod='psbooking'}
                            </a>
                                <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', array('orderby' => 'price', 'orderway' => 'asc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to))|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Price, low to high' mod='psbooking'}
                            </a>
                                <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', array('orderby' => 'price', 'orderway' => 'desc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to))|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Price, high to low' mod='psbooking'}
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="featured-products clearfix">
            <div class="products row">

                {if  count($products) > 0}
                    {foreach from=$products item="product"}
                    {include file="catalog/_partials/miniatures/product.tpl" product=$product productClasses="col-xs-12 col-lg-4 col-xl-3"}
                    {/foreach}
                    {block name="pagination_dustomer"}
                        {include file="module:psbooking/views/templates/front/_partials/pagination.tpl"}
                    {/block}
                {/if}
            </div>
        </section>
    {else}
        <div class="col-md-12 wk-text-center">
            <h2>{l s='No booking product found' mod='psbooking'}</h2>
        </div>
    {/if}
{/block}
