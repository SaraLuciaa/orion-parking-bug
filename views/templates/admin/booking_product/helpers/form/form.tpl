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

<div class="panel">
  <div class="panel-heading">
    {if isset($edit)}
      <i class="icon-pencil"></i>
      {l s='Edit' mod='psbooking'}
    {else}
      <i class="icon-plus-circle"></i>
      {l s='Add' mod='psbooking'}
    {/if}
  </div>
  <div class="panel-content wk-tabs-panel">
    <div class="form-group">
				{if $total_languages > 1}
					<div class="col-lg-12">
						<label class="control-label">{l s='Choose language' mod='psbooking'}</label>
						<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
            <button type="button" id="seller_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
							<span id="seller_lang_btn_text">{$current_lang.name|escape:'htmlall':'UTF-8'}</span>
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu wk_language_menu" style="left:14%;top:32px;">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showProdLangField('{$language.name|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">
										{$language.name|escape:'htmlall':'UTF-8'}
									</a>
								</li>
							{/foreach}
						</ul>
						<p class="help-block">{l s='Change language for updating information in multiple language.' mod='psbooking'}</p>
					</div>
				{/if}
    </div>
    {if !isset($edit)}
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#information" data-toggle="tab" class="wk_text_capitalize">
            <i class="icon-info-sign"></i>
            {l s='Information' mod='psbooking'}
          </a>
        </li>
      </ul>
    {else}
      <ul class="nav nav-tabs">
        <li {if isset($active_tab) && $active_tab == 'information'}class="active{/if}">
          <a href="#information" data-toggle="tab" class="wk_text_capitalize">
            <i class="icon-info-sign"></i>
            {l s='Information' mod='psbooking'}
          </a>
        </li>
        <li {if isset($active_tab) && $active_tab == 'booking_product_images'}class="active{/if}">
          <a href="#booking_product_images" data-toggle="tab" class="wk_text_capitalize">
            <i class="icon-image"></i>
            {l s='Images' mod='psbooking'}
          </a>
        </li>
        <li {if isset($active_tab) && $active_tab == 'booking_product_location'}class="active{/if}">
          <a href="#booking_product_location" data-toggle="tab" class="wk_text_capitalize">
            <i class="icon-location-arrow"></i>
            {l s='Location' mod='psbooking'}
          </a>
        </li>
        {if isset($productBookingType) && $productBookingType==$type_time_slot}
          <li {if isset($active_tab) && $active_tab == 'booking_configuration'}class="active{/if}">
            <a href="#booking_configuration" data-toggle="tab" class="wk_text_capitalize">
              <i class="icon-cogs"></i>
              {l s='Booking Time Slots' mod='psbooking'}
            </a>
          </li>
        {/if}
        {if isset($productBookingType) && $productBookingType==$type_event}
          <li {if isset($active_tab) && $active_tab == 'booking_event'}class="active{/if}">
            <a href="#booking_event" data-toggle="tab" class="wk_text_capitalize">
              <i class="icon-comments"></i>
              {l s='Event Info' mod='psbooking'}
            </a>
          </li>
        {/if}
        {if isset($productBookingType) && $productBookingType!=$type_event}
          <li {if isset($active_tab) && $active_tab == 'booking_disable_dates'}class="active{/if}">
            <a href="#booking_disable_dates" data-toggle="tab" class="wk_text_capitalize">
              <i class="icon-calendar"></i>
              {l s='Disable Dates/Slots' mod='psbooking'}
            </a>
          </li>
        {/if}
        <li {if isset($active_tab) && $active_tab == 'booking_availability_info'}class="active{/if}">
          <a href="#booking_availability_info" data-toggle="tab" class="wk_text_capitalize">
            <i class="icon-AdminParentStats"></i>
            {l s='Availability & Rates' mod='psbooking'}
          </a>
        </li>
      </ul>
    {/if}
    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&{if isset($edit)}id={$idBookingProductInformation|escape:'htmlall':'UTF-8'}&update{$table|escape:'htmlall':'UTF-8'}{else}add{$table|escape:'htmlall':'UTF-8'}{/if}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
      <div class="tab-content panel collapse in">
        <div class="tab-pane active" id="information">
          <div style="padding-top: 14px;">
            <div class="form-group">
              <label class="col-lg-3 control-label required" for="product_name" >
                {l s='Product name ' mod='psbooking'}
                {include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}
              </label>
              <div class="col-lg-8">
                <input type="hidden" value="{$idBookingProductInformation|escape:'htmlall':'UTF-8'}" name="idTable" id="idTable">
                {foreach from=$languages item=language}
                  {assign var="name" value="name`$language.id_lang`"}
                  <input type="text"
                  id="product_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                  name="name{$language.id_lang|escape:'htmlall':'UTF-8'}"
                  value="{if isset($smarty.post.$name)}{$smarty.post.$name|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$bookingProduct->name[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
                  class="form-control product_name_all wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                  maxlength="128"
                  {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
                {/foreach}
              </div>
            </div>
            <div class="form-group">
              <label for="short_description" class="col-lg-3 control-label">
                {l s='Short description' mod='psbooking'}
                {include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}
              </label>
              <div class="col-lg-8">
                {foreach from=$languages item=language}
                  {assign var="description_short" value="description_short`$language.id_lang`"}
                  <div id="short_desc_div_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
                    <textarea
                      name="description_short{$language.id_lang|escape:'htmlall':'UTF-8'}"
                      id="short_description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                      cols="2" rows="3"
                      class="wk_tinymce form-control">
                    {if isset($smarty.post.$description_short)}{$smarty.post.$description_short|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$bookingProduct->description_short[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}
                      </textarea>
                  </div>
                {/foreach}
              </div>
            </div>
            <div class="form-group">
              <label for="product_description" class="col-lg-3 control-label">
                {l s='Description' mod='psbooking'}
                {include file="$self/../../views/templates/admin/_partials/mp-form-fields-flag.tpl"}
              </label>
              <div class="col-lg-8">
                {foreach from=$languages item=language}
                    {assign var="description" value="description`$language.id_lang`"}
                    <div id="product_desc_div_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
                      <textarea
                      name="description{$language.id_lang|escape:'htmlall':'UTF-8'}"
                      id="description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                      cols="2" rows="3"
                      class="wk_tinymce form-control">{if isset($smarty.post.$description)}{$smarty.post.$description|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$bookingProduct->description[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    </div>
                {/foreach}
              </div>
            </div>
            <div class="form-group">
              <label for="product_condition" class="control-label col-lg-3">
                {l s='Condition ' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                  <select class="form-control" name="condition">
                    <option value="new" {if isset($edit)}{if $bookingProduct->condition == 'new'}Selected="Selected"{/if}{/if}>
                      {l s='New' mod='psbooking'}
                    </option>
                    <option value="used" {if isset($edit)}{if $bookingProduct->condition == 'used'}Selected="Selected"{/if}{/if}>
                      {l s='Used' mod='psbooking'}
                    </option>
                    <option value="refurbished" {if isset($edit)}{if $bookingProduct->condition == 'refurbished'}Selected="Selected"{/if}{/if}>
                      {l s='Refurbished' mod='psbooking'}
                    </option>
                  </select>
                </div>
            </div>
            <div class="form-group">
              <label for="booking_type" class="control-label col-lg-3">
                {l s='Type ' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <select class="form-control" name="booking_type" id="booking_type">
                  <option value="{$type_date_range|escape:'htmlall':'UTF-8'}" {if isset($productBookingType) && $productBookingType=={$type_date_range|escape:'htmlall':'UTF-8'}}selected{/if}>
                    {l s='Date range' mod='psbooking'}
                  </option>
                  <option value="{$type_time_slot|escape:'htmlall':'UTF-8'}" {if isset($productBookingType) && $productBookingType=={$type_time_slot|escape:'htmlall':'UTF-8'}}selected{/if}>
                    {l s='Time slots' mod='psbooking'}
                  </option>
                  <option value="{$type_event|escape:'htmlall':'UTF-8'}" {if isset($productBookingType) && $productBookingType=={$type_event|escape:'htmlall':'UTF-8'}}selected{/if}>
                    {l s='Event' mod='psbooking'}
                  </option>
                  <option value="{$type_rental|escape:'htmlall':'UTF-8'}" {if isset($productBookingType) && $productBookingType=={$type_rental|escape:'htmlall':'UTF-8'}}selected{/if}>
                    {l s='Rental' mod='psbooking'}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-3 control-label required" for="product_price">
                {l s='Price' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <div class="input-group">
                  <div class="input-group-addon">
                    {$adminCurrency->sign|escape:'htmlall':'UTF-8'}
                  </div>
                  <input type="text" class="form-control" id="product_price" name="price"
                  value="{if isset($smarty.post.price) && $smarty.post.price}{$smarty.post.price|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($bookingProduct->price)}{$bookingProduct->price|escape:'htmlall':'UTF-8'|string_format:"%.2f"}{/if}" />
                  <div class="input-group-addon">/
                    <span class="booking_price_period">
                      {if isset($productBookingType) && $productBookingType==$type_date_range}
                        {l s='Day' mod='psbooking'}
                      {else if isset($productBookingType) && $productBookingType==$type_time_slot}
                        {l s='Slot' mod='psbooking'}
                      {else if isset($productBookingType) && $productBookingType==$type_event}
                        {l s='Event' mod='psbooking'}
                      {else if isset($productBookingType) && $productBookingType==$type_rental}
                        {l s='Day' mod='psbooking'}
                      {else}
                        {l s='Day' mod='psbooking'}
                      {/if}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group ">
              <label class="col-lg-3 control-label" for="id_tax_rules_group">
                {l s='Tax rate' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <select name="id_tax_rules_group" id="id_tax_rules_group" class="form-control form-control-select" data-action="input_excl">
                    <option value="0">{l s='No tax' mod='psbooking'}</option>
                    {foreach $tax_rules_groups as $tax_rule}
                      <option value="{$tax_rule.id_tax_rules_group|escape:'html':'UTF-8'}"
                      {if isset($bookingProduct->id_tax_rules_group)}{if $bookingProduct->id_tax_rules_group == $tax_rule.id_tax_rules_group} selected="selected"{/if}{else}{if $defaultTaxRuleGroup == $tax_rule.id_tax_rules_group} selected="selected" {/if}{/if}>
                        {$tax_rule.name|escape:'html':'UTF-8'}
                      </option>
                    {/foreach}
                  </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-3 control-label" for="product_make_this_product_booking_before">
                {l s='Booking before' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <div class="input-group">
                  <input type="text" class="form-control" id="make_this_product_booking_before" name="booking_before"
                  value="{if isset($smarty.post.booking_before) && $smarty.post.booking_before}{$smarty.post.booking_before|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($booking_before)}{$booking_before|escape:'htmlall':'UTF-8'}{/if}" />
                  <div class="input-group-addon">
                    <span>
                      {l s='hours' mod='psbooking'}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group day_range_field" {if isset($productBookingType) && ($productBookingType == $type_time_slot || $productBookingType == $type_event)}style="display:none"{/if}>
              <label class="col-lg-3 control-label">
                {l s='Minimum' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <div class="input-group">
                  <input type="text" class="form-control" name="min_days_booking"
                  value="{if isset($smarty.post.min_days_booking) && $smarty.post.min_days_booking}{$smarty.post.min_days_booking|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($min_days_booking)}{$min_days_booking|escape:'htmlall':'UTF-8'}{/if}" />
                  <div class="input-group-addon">
                    <span>
                      {l s='days' mod='psbooking'}
                    </span>
                  </div>
                </div>
                <p class="help-block">
                  {l s='Set to 0 if you do not want it.' mod='psbooking'}
                </p>
              </div>
            </div>
            <div class="form-group day_range_field" {if isset($productBookingType) && ($productBookingType == $type_time_slot || $productBookingType == $type_event)}style="display:none"{/if}>
              <label class="col-lg-3 control-label" for="product_make_this_product_booking_before">
                {l s='Maximum' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <div class="input-group">
                  <input type="text" class="form-control" name="max_days_booking"
                  value="{if isset($smarty.post.max_days_booking) && $smarty.post.max_days_booking}{$smarty.post.max_days_booking|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($max_days_booking)}{$max_days_booking|escape:'htmlall':'UTF-8'}{/if}" />
                  <div class="input-group-addon">
                    <span>
                      {l s='days' mod='psbooking'}
                    </span>
                  </div>
                </div>
                <p class="help-block">
                  {l s='Set to 0 if you do not want it.' mod='psbooking'}
                </p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-3 control-label required" for="product_quantity">
                {l s='Quantity ' mod='psbooking'}
              </label>
              <div class="col-lg-3">
                <input type="text" class="form-control" id="product_quantity" name="product_quantity"
                value="{if isset($smarty.post.product_quantity) && $smarty.post.product_quantity}{$smarty.post.product_quantity|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($productQuantity)}{$productQuantity|escape:'htmlall':'UTF-8'}{/if}" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-3 control-label required" for="product_category">
                {l s='Category' mod='psbooking'}
              </label>
              <div class="col-lg-6">
                <div id="categorycontainer"></div>
                <input type="hidden" name="product_category" id="product_category" value="{if isset($catIdsJoin)}{$catIdsJoin|escape:'htmlall':'UTF-8'}{/if}" />
              </div>
            </div>
            <div class="form-group" id="default_category_div">
              <label class="col-lg-3 control-label required" for="default_category">
                {l s='Default product category ' mod='psbooking'}
              </label>
              <div class="col-lg-6">
                <select name="default_category" class="fixed-width-xl" id="default_category">
                  {if isset($defaultCategory)}
                    {foreach $defaultCategory as $category}
                      <option id="default_cat{$category.id_category|escape:'htmlall':'UTF-8'}" value="{$category.id_category|escape:'htmlall':'UTF-8'}" {if isset($defaultIdCategory)}{if $defaultIdCategory == $category.id_category} selected {/if}{/if}>
                        {$category.name|escape:'htmlall':'UTF-8'}
                      </option>
                    {/foreach}
                  {else}
                    <option id="default_cat2" value="2">Home</option>
                  {/if}
                </select>
              </div>
            </div>
            <div class="form-group">
              <input type="hidden" name="id_selected_lang" id="id_selected_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
              <input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab|escape:'htmlall':'UTF-8'}{/if}" id="active_tab">
              <input type="hidden" id="id_ps_product" {if isset($edit) && isset($bookingProduct->id)} value="{$bookingProduct->id|escape:'htmlall':'UTF-8'}" {/if}>
              <label class="col-lg-3 control-label">{l s='Enable' mod='psbooking'}</label>
              <div class="col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                  <input type="radio" value="1" id="product_active_on" name="active" {if isset($edit)} {if $bookingProduct->active==1} checked="checked" {/if} {else}checked="checked"{/if}>
                  <label for="product_active_on">{l s='Yes' mod='psbooking'}</label>
                  <input type="radio" value="0" id="product_active_off" name="active" {if isset($edit) && $bookingProduct->active==0} checked="checked"{/if}>
                  <label for="product_active_off">{l s='No' mod='psbooking'}</label>
                  <a class="slide-button btn"></a>
                </span>
              </div>
            </div>
          </div>
          <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
              <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
            </a>
            <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
              <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
            </button>
            <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
              <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
            </button>
          </div>
        </div>
        <div class="tab-pane" id="booking_product_images">
          <div style="padding-top: 14px;">
            <div class="alert alert-info" style="margin-left: 75px;">{$uploadMb|escape:'htmlall':'UTF-8'} {l s='MB is maximum file size allowed by the server' mod='psbooking'}.
            <br>
            {l s='Allowed image extensions are jpg, png, gif, and jpeg' mod='psbooking'}.</div>
            {if isset($edit)}
                <div class="form-group">
                  <div class="wk_upload_product_image">
                    <input type="file" name="productimages[]" class="uploadimg_container" data-jfiler-name="productimg">
                    </div>
                </div>
                <div id="file_size_reached" style="margin-left: 75px;color: red;"></div>
                {include file="$self/../../views/templates/admin/_partials/imageedit.tpl"}
            {else}
              <div class="alert alert-danger">
                {l s='You must save this product before adding images.' mod='psbooking'}
              </div>
            {/if}
          </div>
          <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
              <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
            </a>
            <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
              <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
            </button>
            <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
              <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
            </button>
          </div>
        </div>
        <div class="tab-pane" id="booking_product_location">
          {if isset($wk_booking_api_key) && !empty($wk_booking_api_key)}
            <div style="padding-top: 14px;">
              <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Display map' mod='psbooking'}</label>
                <div class="col-lg-6">
                  <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" id="show_map_on" name="show_map" {if isset($edit)} {if isset($smarty.post.show_map)} {if $smarty.post.show_map == 1}checked="checked"{/if}{elseif $show_map==1} checked="checked" {/if}{/if}>
                    <label for="show_map_on">{l s='Yes' mod='psbooking'}</label>
                    <input type="radio" value="0" id="show_map_off" name="show_map" {if isset($edit)} {if isset($smarty.post.show_map)} {if $smarty.post.show_map == 0}checked="checked"{/if}{elseif $show_map==0} checked="checked" {/if}{/if}>
                    <label for="show_map_off">{l s='No' mod='psbooking'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
              </div>
              <div class="show_map_div" {if isset($show_map)}{if isset($smarty.post.show_map)} {if $smarty.post.show_map == 0}style="display: none;"{/if}{elseif $show_map==0} style="display: none;" {/if}{/if}>
                <div class="form-group">
                  <label class="col-lg-3 control-label required">{l s='Address' mod='psbooking'}</label>
                  <div class="col-lg-5">
                    <input type="text" class="form-control" id="address" name="address"
                    value="{if isset($smarty.post.address) && $smarty.post.address}{$smarty.post.address|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($address)}{$address|escape:'htmlall':'UTF-8'}{/if}" />
                  </div>
                </div>
                <div class="row wk-margin-20">
                  <input type="hidden" class="form-control" id="latitude" name="latitude"
                    value="{if isset($smarty.post.latitude) && $smarty.post.latitude}{$smarty.post.latitude|escape:'htmlall':'UTF-8'}{elseif isset($edit) && !empty($latitude)}{$latitude|escape:'htmlall':'UTF-8'}{/if}" />
                  <input type="hidden" class="form-control" id="longitude" name="longitude"
                    value="{if isset($smarty.post.longitude) && $smarty.post.longitude}{$smarty.post.longitude|escape:'htmlall':'UTF-8'}{elseif isset($edit) && !empty($longitude)}{$longitude|escape:'htmlall':'UTF-8'}{/if}" />
                  <div class="col-sm-12">
                    <input id="pac-input" class="controls" type="text" name="booking_location_input"
                      value="{if isset($smarty.post.booking_location_input)}{$smarty.post.booking_location_input|escape:'htmlall':'UTF-8'}{elseif isset($edit) && isset($address)}{$address|escape:'htmlall':'UTF-8'}{/if}"
                      placeholder="{l s='Enter location' mod='psbooking'}">
                    <div id="map-canvas"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
              </a>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
              </button>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
              </button>
            </div>
          {else}
            <div style="padding-top: 14px;">
              <div class="alert alert-info">{l s='Please set google map API key form module ' mod='psbooking'}
                <a target="_blank" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=psbooking&page=mapSetting">{l s='configuration' mod='psbooking'}</a>
              </div>
            </div>
          {/if}
        </div>
        {if isset($edit)}
          {* {$type_time_slot|dump} *}
          {if isset($productBookingType) && $productBookingType == $type_time_slot}
            <div class="tab-pane" id="booking_configuration">
              <div style="padding-top: 14px;">
                {include file='./_partials/booking_product_time_slots_information.tpl'}
              </div>
              <div class="panel-footer">
                <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                  <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
                </a>
                <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
                  <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
                </button>
                <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
                  <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
                </button>
              </div>
            </div>
          {/if}
          {if isset($productBookingType) && $productBookingType == $type_event}
            <div class="tab-pane" id="booking_event">
              <div style="padding-top: 14px;">
                {include file='./_partials/booking_event.tpl'}
              </div>
              <div class="panel-footer">
                <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                  <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
                </a>
                <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
                  <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
                </button>
                <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
                  <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
                </button>
              </div>
            </div>
          {/if}
          <div class="tab-pane" id="booking_disable_dates">
            <div style="padding-top: 14px;">
              {include file='./_partials/booking_disable_dates_info.tpl'}
            </div>
            <div class="panel-footer">
              <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
              </a>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
              </button>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
              </button>
            </div>
          </div>
          <div class="tab-pane" id="booking_availability_info">
            {include file='./_partials/availablity_rates_info.tpl'}
            <div class="panel-footer">
              <a href="{$link->getAdminLink('AdminBookingProduct')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                <i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
              </a>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i>{l s='Save' mod='psbooking'}
              </button>
              <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right submitBookingProduct">
                <i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
              </button>
            </div>
          </div>
        {/if}
      </div>
    </form>
  </div>
</div>

{if isset($edit)}
  {strip}
    {addJsDef module_dir = $module_dir}
    {addJsDef defaultIdCategory = $defaultIdCategory}
  {/strip}
{/if}
{strip}
  {addJsDefL name='slot_text'}{l s='Slot' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='event_text'}{l s='Event' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='avl_qty_txt'}{l s='Avail qty' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='choose_category_txt'}{l s='choose category' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='date_txt'}{l s='Date' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='date_from_txt'}{l s='Date from' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='date_to_txt'}{l s='Date to' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='slot_time_from_txt'}{l s='Slot time from' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='slot_time_to_txt'}{l s='Slot time to' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='status_txt'}{l s='Status' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='add_more_slots_txt'}{l s='Add more slots' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_select_days_txt'}{l s='Please select days.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_select_time_from_txt'}{l s='Please select slot time from.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_select_time_to_txt'}{l s='Please select slot time to.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_enter_price_txt'}{l s='Please enter price (tax excl.)' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_enter_valid_price_txt'}{l s='Please enter valid price (tax excl.)' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_enter_qty_txt'}{l s='Please enter quantity' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='please_enter_valid_qty_txt'}{l s='Please enter valid quantity' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='price_txt'}{l s='Price (tax excl.)' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='booked_qty_txt'}{l s='Booked' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='status_txt'}{l s='Status' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='day_text'}{l s='Day' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDef admin_booking_product_url = $link->getAdminlink('AdminBookingProduct')}
  {addJsDef type_date_range = $type_date_range}
  {addJsDef type_time_slot = $type_time_slot}
  {addJsDef type_event = $type_event}
  {addJsDef type_rental = $type_rental}

  {addJsDef defaultCurrencySign = $defaultCurrencySign}
  {addJsDef imageUploader = $link->getAdminlink('AdminBookingProduct')}
  {addJsDef actionpage = 'product'}

  {addJsDef backend_controller = 1}
  {addJsDef deleteaction = 'jFiler-item-trash-action'}

  {if isset($edit)}
    {addJsDef actionIdForUpload = $bookingProduct->id}
    {addJsDef bookingCalendarData=$bookingCalendarData}
    {if isset($disabledDays)}
      {addJsDef disabledDays = $disabledDays}
    {/if}
    {if isset($disabledDates)}
      {addJsDef disabledDates = $disabledDates}
    {/if}
    {addJsDef booking_type = $productBookingType}
  {else}
    {addJsDef actionIdForUpload = ''}
  {/if}
  {addJsDef wk_image_dir = $wk_image_dir}
  {addJsDefL name='choosefile'}{l s='Choose images' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='choosefiletoupload'}{l s='Choose images to upload' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='imagechoosen'}{l s='Images were chosen' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='dragdropupload'}{l s='Drop file here to Upload' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='confirm_delete_msg'}{l s='Are you sure want to delete this image?' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='deleteSuccess'}{l s='Successfully deleted.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='only'}{l s='Only' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='imagesallowed'}{l s='Images are allowed to be uploaded.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='onlyimagesallowed'}{l s='Images are allowed to be uploaded.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='imagetoolarge'}{l s='is too large! Please upload image up to' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='imagetoolargeall'}{l s='Images you have choosed are too large! Please upload images up to' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name='error_msg'}{l s='Some error occurs while deleting image' js=1 mod='psbooking'}{/addJsDefL}


  {addJsDef calendarDate=$date_from|date_format:"%d-%m-%Y"}
  {addJsDefL name=no_info_found_txt}{l s='No information found.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=no_slots_avail_txt}{l s='No time slot available.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=no_results_found_cond}{l s='No results found for this search.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_from_req_err}{l s='Please select date from.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_to_req_err}{l s='Please select date to.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_from_less_current_date_err}{l s='date from can not be before current date.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_to_more_date_from_err}{l s='date to must be greater than date from.' js=1 mod='psbooking'}{/addJsDefL}

  {addJsDefL name=no_slot_selected_err}{l s='No slots selected. Please select at least one slot.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=all_slots_disable_warning}{l s='In selected date range time slots of more than one date ranges are there. So in this case all the time slots will be disabled in this date range.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_from_req}{l s='Date from is missing.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_to_req}{l s='Date to is missing.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=to_txt}{l s='To' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=date_range_already_added}{l s='Disable date range already added.' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=add_category_wk}{l s='Add category' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=add_lang_wk}{l s='Add language' js=1 mod='psbooking'}{/addJsDefL}
  {addJsDefL name=add_age_group_wk}{l s='Add age group' js=1 mod='psbooking'}{/addJsDefL}

{/strip}

<script type="text/javascript">
  // for tiny mce setup
  var iso = "{$iso|escape:'htmlall':'UTF-8'}";
  var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}";
  var ad = "{$ad|escape:'htmlall':'UTF-8'}";
  $(document).ready(function(){
    {block name="autoload_tinyMCE"}
      tinySetup({
        editor_selector :"wk_tinymce",
        {* width : 700 *}
      });
    {/block}
  });
</script>