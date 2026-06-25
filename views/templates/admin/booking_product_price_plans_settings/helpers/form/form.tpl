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
			<i class='icon-pencil'></i>&nbsp{l s='Edit' mod='psbooking'}
		{else}
			<i class='icon-plus-circle'></i>&nbsp{l s='Add' mod='psbooking'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$featurePriceInfo->id|escape:'html':'UTF-8'}" name="id" />
		{/if}
		<input type="hidden" value="{if isset($single_event) && $single_event}1{else}
		0{/if}" name="single_event" id="single_event_input" />
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Search booking product ' mod='psbooking'}
			</label>
			<div class="col-sm-4">
				<div class="input-group">
					<input type="text" id="booking_product_name" name="booking_product_name" class="form-control" placeholder= "{l s='Search booking product' mod='psbooking'}" autocomplete="off"
					value="{if isset($productName)}{$productName|escape:'htmlall':'UTF-8'}{/if}"/>
					<span class="input-group-addon">
						<i class="icon-search"></i>
					</span>
				</div>
				<input type="hidden" id="product_id" name="product_id" class="form-control" value="{if isset($featurePriceInfo->id_product)}{$featurePriceInfo->id_product|escape:'htmlall':'UTF-8'}{else}0{/if}"/>
				<div class="dropdown">
	                <ul class="booking_product_search_results_ul"></ul>
	            </div>
				<p class="error-block" style="display:none; color: #CD5D5D;">{l s='No match found for this search. Please try with an existing name.' mod='psbooking'}</p>
			</div>
			<div class="col-sm-offset-3 col-sm-9 help-block">
				{l s='Type booking product name and select the product for which you are going to create this price plan.' mod='psbooking'}
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Booking price rule name ' mod='psbooking'}
			</label>
			<div class="col-sm-4">
				<input type="text" id="feature_price_name" name="feature_price_name" class="form-control"
				value="{if isset($smarty.post.feature_price_name) && $smarty.post.feature_price_name}{$smarty.post.feature_price_name|escape:'htmlall':'UTF-8'}{elseif isset($featurePriceInfo->feature_price_name)}{$featurePriceInfo->feature_price_name|escape:'htmlall':'UTF-8'}{/if}"/>
			</div>
		</div>
		<div id="single_event_hide" {if isset($single_event) && $single_event}style="display:none;"{/if}>
			<div class="form-group">
				<label for="date_selection_type" class="control-label col-lg-3">
				{l s='Date selection type ' mod='psbooking'}
				</label>
				<div class="col-lg-4">
					<select class="form-control" name="date_selection_type" id="date_selection_type">
						<option value="1" {if isset($smarty.post.date_selection_type) && $smarty.post.date_selection_type == 1}selected{elseif isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 1}selected = "selected"{/if}>
						{l s='Date range' mod='psbooking'}
						</option>
						<option value="2" {if isset($smarty.post.date_selection_type) && $smarty.post.date_selection_type == 2}selected{elseif isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}selected = "selected"{/if}>
						{l s='Specific date' mod='psbooking'}
						</option>
					</select>
				</div>
			</div>
			<div class="form-group specific_date_type" {if isset($edit) && $edit}{if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type != 2}style="display:none;"{/if}{else}style="display:none;"{/if}>
				<label class="col-sm-3 control-label required" for="specific_date" >
					{l s='Specific date' mod='psbooking'}
				</label>
				<div class="col-sm-4">
					<input type="text" id="specific_date" name="specific_date" class="form-control" value="{if isset($smarty.post.specific_date) && $smarty.post.specific_date}{$smarty.post.specific_date|escape:'htmlall':'UTF-8'}{elseif isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from|escape:'htmlall':'UTF-8'}{else}{$date_from|escape:'htmlall':'UTF-8'}{/if}" readonly/>
				</div>
			</div>

			<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
				<label class="col-sm-3 control-label required" for="date_form" >
					{l s='Date from' mod='psbooking'}
				</label>
				<div class="col-sm-4">
					<input type="text" id="feature_plan_date_from" name="date_from" class="form-control" value="{if isset($smarty.post.date_from) && $smarty.post.date_from}{$smarty.post.date_from|escape:'htmlall':'UTF-8'}{elseif isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}{else}{$date_from|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
				<label class="col-sm-3 control-label required" for="date_to" >
					{l s='Date to' mod='psbooking'}
				</label>
				<div class="col-sm-4">
					<input type="text" id="feature_plan_date_to" name="date_to" class="form-control" value="{if isset($smarty.post.date_to) && $smarty.post.date_to}{$smarty.post.date_to|escape:'htmlall':'UTF-8'}{elseif isset($featurePriceInfo->date_to)}{$featurePriceInfo->date_to|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}{else}{$date_to|escape:'htmlall':'UTF-8'|date_format:'%d-%m-%Y'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group special_days_content" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
				<label class="col-sm-3 control-label" for="date_to" >
					{l s='For special days' mod='psbooking'}
				</label>
				<div class="col-sm-2">
					<p class="checkbox">
						<label>
							<input class="is_special_days_exists pull-left" type="checkbox" name="is_special_days_exists" {if isset($smarty.post.is_special_days_exists) && $smarty.post.is_special_days_exists}checked="checked"{elseif isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}checked="checked"{/if}/>
							{l s='Check to select special days' mod='psbooking'}
						</label>
					</p>
				</div>
				<div class="col-sm-7 week_days" {if isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}style="display:block;"{/if}>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="mon" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('mon', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('mon', $special_days)}checked="checked"{/if}/>
						<p>{l s='Mon' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="tue" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('tue', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('tue', $special_days)}checked="checked"{/if}/>
						<p>{l s='Tue' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="wed" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('wed', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('wed', $special_days)}checked="checked"{/if}/>
						<p>{l s='Wed' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="thu" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('thu', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('thu', $special_days)}checked="checked"{/if}/>
						<p>{l s='Thu' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="fri" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('fri', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('fri', $special_days)}checked="checked"{/if}/>
						<p>{l s='Fri' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="sat" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('sat', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('sat', $special_days)}checked="checked"{/if}/>
						<p>{l s='Sat' mod='psbooking'}</p>
					</div>
					<div class="col-sm-1">
						<input type="checkbox" name="special_days[]" value="sun" {if isset($smarty.post.special_days) && $smarty.post.special_days && in_array('sun', $smarty.post.special_days)}checked="checked"{elseif isset($special_days) && $special_days && in_array('sun', $special_days)}checked="checked"{/if}/>
						<p>{l s='Sun' mod='psbooking'}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
            <label for="Price Impact Way" class="control-label col-lg-3">
              {l s='Impact way ' mod='psbooking'}
            </label>
            <div class="col-lg-4">
				<select class="form-control" name="price_impact_way" id="price_impact_way">
					<option value="1" {if isset($smarty.post.price_impact_way) && $smarty.post.price_impact_way == 1}selected{elseif isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 1}selected = "selected"{/if}>
					  {l s='Decrease price' mod='psbooking'}
					</option>
					<option value="2" {if isset($smarty.post.price_impact_way) && $smarty.post.price_impact_way == 2}selected{elseif isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 2}selected = "selected"{/if}>
					  {l s='Increase price' mod='psbooking'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Type" class="control-label col-lg-3">
              {l s='Impact type ' mod='psbooking'}
            </label>
            <div class="col-lg-4">
				<select class="form-control" name="price_impact_type" id="price_impact_type">
					<option value="1" {if isset($smarty.post.price_impact_type) && $smarty.post.price_impact_type == 1}selected{elseif isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 1}selected = "selected"{/if}>
					  {l s='Percentage' mod='psbooking'}
					</option>
					<option value="2" {if isset($smarty.post.price_impact_type) && $smarty.post.price_impact_type == 2}selected{elseif isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 2}selected = "selected"{/if}>
					  {l s='Fixed price' mod='psbooking'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Impact value' mod='psbooking'} ({l s='tax excl.' mod='psbooking'})
			</label>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $featurePriceInfo->impact_type==2}{$defaultcurrency_sign|escape:'htmlall':'UTF-8'}{else}%{/if}{else}%{/if}</span>
					<input type="text" id="impact_value" name="impact_value"
					value="{if isset($smarty.post.impact_value) && $smarty.post.impact_value}{$smarty.post.impact_value|escape:'htmlall':'UTF-8'}{elseif isset($featurePriceInfo->impact_value)}{$featurePriceInfo->impact_value|escape:'htmlall':'UTF-8'}{/if}"/>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">
				<span>
					{l s='Enable' mod='psbooking'}
				</span>
			</label>
			<div class="col-lg-9 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($smarty.post.enable_feature_price) && $smarty.post.enable_feature_price == 1}checked="checked"{elseif isset($edit) && $featurePriceInfo->active==1} checked="checked" {else}checked="checked"{/if} value="1" id="enable_feature_price_on" name="enable_feature_price">
					<label for="enable_feature_price_on">{l s='Yes' mod='psbooking'}</label>
					<input {if isset($smarty.post.enable_feature_price) && $smarty.post.enable_feature_price == 0}checked="checked"{elseif isset($edit) && $featurePriceInfo->active==0} checked="checked" {/if} type="radio" value="0" id="enable_feature_price_off" name="enable_feature_price">
					<label for="enable_feature_price_off">{l s='No' mod='psbooking'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminBookingProductPricePlansSettings')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='psbooking'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='psbooking'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='psbooking'}
			</button>
		</div>
	</form>
</div>

{strip}
	{addJsDef booking_product_price_plans_url = $link->getAdminLink('AdminBookingProductPricePlansSettings')}
	{addJsDef defaultcurrency_sign = $defaultcurrency_sign mod='psbooking'}
{/strip}