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
	<h3>{l s='Priority' mod='psbooking'}</h3>
	<div class="alert alert-info">
		{l s='Sometimes one customer can fit into multiple booking price rules. In this case priorities allow you to define which rule applies to the booking product.' mod='psbooking'}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		<div class="form-group">
			<label class="control-label col-lg-3" for="featurePricePriority">{l s='Booking price calculation priorities' mod='psbooking'}  </label>
			<div class="input-group col-lg-9">
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[0]=='specific_date'}selected="selected"{/if}>{l s='Specific date' mod='psbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[0]=='special_day'}selected="selected"{/if}>{l s='Special days' mod='psbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[0]=='date_range'}selected="selected"{/if}>{l s='Date ranges' mod='psbooking'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[1]=='specific_date'}selected="selected"{/if}>{l s='Specific date' mod='psbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[1]=='special_day'}selected="selected"{/if}>{l s='Special days' mod='psbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[1]=='date_range'}selected="selected"{/if}>{l s='Date ranges' mod='psbooking'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[2]=='specific_date'}selected="selected"{/if}>{l s='Specific date' mod='psbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[2]=='special_day'}selected="selected"{/if}>{l s='Special days' mod='psbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[2]=='date_range'}selected="selected"{/if}>{l s='Date ranges' mod='psbooking'}</option>
				</select>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitAddFeaturePricePriority" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='psbooking'}
			</button>
		</div>
	</form>
</div>
