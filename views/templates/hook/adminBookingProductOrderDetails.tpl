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

<div class="panel card mt-2">
	<div class="panel-heading card-header">
		<i class="icon-shopping-cart"></i>
		{l s='Booking product details' mod='psbooking'}
	</div>
	<div class="table-responsive card-body">
		<table class="table" id="orderProducts">
			<thead>
				<tr>
					<th>{l s='Image' mod='psbooking'}</th>
          <th>{l s='Product name' mod='psbooking'}</th>
          <th>{l s='Duration' mod='psbooking'}</th>
          <th>{l s='Quantity' mod='psbooking'}</th>
          <th>{l s='Unit price (tax excl.)' mod='psbooking'}</th>
          <th>{l s='Total price (tax excl.)' mod='psbooking'}</th>
				</tr>
			</thead>
			<tbody>
        {foreach from=$orderProducts item=product}
          {if isset($product.isBookingProduct) && $product.isBookingProduct}
            {if isset($product.booking_product_data) && $product.booking_product_data}
              {foreach from=$product.booking_product_data item=bookingProduct}
                <tr>
                  <td>
                    <span class="image">
                      {if isset($product.image) && $product.image->id}{$product.image_tag}{/if}
                    </span>
                  </td>
                  <td>
                    <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product['product_id']|intval, 'updateproduct' => '1'])}" target="_blank">
                      <span class="productName">{$product['product_name']|escape:'htmlall':'UTF-8'}</span>
                    </a>
                  </td>
                  <td>
                    {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                      {Tools::displayDate($bookingProduct['date_from']|escape:'htmlall':'UTF-8')}</br> {l s='to' mod='psbooking'} </br> {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                      {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}</br>
                      {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                      {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'} {if $bookingProduct['time_from']!='00:00'}{$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}{/if}{if $bookingProduct['date_to'] != '0000-00-00 00:00:00'}</br> {l s='to' mod='psbooking'}</br>{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'} {if $bookingProduct['time_to']!='00:00'}{$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}{/if}{/if}
                    {/if}
                  </td>
                  <td>{$bookingProduct['quantity']|escape:'htmlall':'UTF-8'}</td>
                  <td>{$bookingProduct['unit_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}</td>
                  <td>{$bookingProduct['total_range_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}</td>
                </tr>
              {/foreach}
            {/if}
          {/if}
        {/foreach}
			</tbody>
		</table>
	</div>
</div>
