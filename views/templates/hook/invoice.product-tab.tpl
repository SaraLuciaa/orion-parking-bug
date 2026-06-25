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

<table class="product" width="100%" cellpadding="4" cellspacing="0">
  <thead>
    <tr>
      <th class="product header small" width="{$layout.reference.width|escape:'htmlall':'UTF-8'}%">{l s='Reference' mod='psbooking'}</th>
      <th class="product header small" width="{$layout.product.width - 8|escape:'htmlall':'UTF-8'}%">{l s='Product' mod='psbooking'}</th>
      <th class="product header small" width="{$layout.tax_code.width|escape:'htmlall':'UTF-8'}%">{l s='Tax Rate' mod='psbooking'}</th>

      {if isset($layout.before_discount)}
        <th class="product header small" width="{$layout.unit_price_tax_excl.width|escape:'htmlall':'UTF-8'}%">{l s='Base price' mod='psbooking'} <br /> {l s='(Tax excl.)' mod='psbooking'}</th>
      {/if}

      <th class="product header-right small" width="{$layout.unit_price_tax_excl.width|escape:'htmlall':'UTF-8'}%">{l s='Unit Price' mod='psbooking'} <br /> {l s='(Tax excl.)' mod='psbooking'}</th>
      <th class="product header small" width="16%">{l s='Qty' mod='psbooking'}</th>
      <th class="product header-right small" width="{$layout.total_tax_excl.width|escape:'htmlall':'UTF-8'}%">{l s='Total' mod='psbooking'} <br /> {l s='(Tax excl.)' mod='psbooking'}</th>
    </tr>
  </thead>

  <tbody>

  <!-- PRODUCTS -->
  {foreach $order_details as $order_detail}
    {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
    {if isset($order_detail.isBookingProduct) && $order_detail.isBookingProduct}
      {if isset($order_detail.booking_product_data) && $order_detail.booking_product_data}
        {foreach from=$order_detail.booking_product_data item=bookingProduct}
          <tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}">
            <td class="product center">
              {$order_detail.product_reference|escape:'htmlall':'UTF-8'}
            </td>
            <td class="product left">
              {if $display_product_images}
                <table width="100%">
                  <tr>
                    <td width="15%">
                      {if isset($order_detail.image) && $order_detail.image->id}
                        {$order_detail.image_tag|escape:'htmlall':'UTF-8'}
                      {/if}
                    </td>
                    <td width="5%">&nbsp;</td>
                    <td width="80%">
                      {$order_detail.product_name|escape:'htmlall':'UTF-8'}
                    </td>
                  </tr>
                </table>
              {else}
                {$order_detail.product_name|escape:'htmlall':'UTF-8'}
              {/if}
            </td>
            <td class="product center">
              {$order_detail.order_detail_tax_label nofilter}
            </td>
            {if isset($layout.before_discount)}
              <td class="product center">
                {if isset($order_detail.unit_price_tax_excl_before_specific_price)}
                  {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_before_specific_price}
                {else}
                  --
                {/if}
              </td>
            {/if}
            <td class="product right">
              {displayPrice currency=$order->id_currency price=$bookingProduct['unit_feature_price_tax_excl']}
            </td>
            <td class="product center">
              {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}<br/>{l s='to' mod='psbooking'}<br/>{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
              {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}<br/>
                {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
              {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}
                {if $bookingProduct['time_from']!='00:00'}
                  {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}
                {/if}
                {if $bookingProduct['date_to'] != '0000-00-00 00:00:00'}
                  <br/>{l s='to' mod='psbooking'}<br/>{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                  {if $bookingProduct['time_to']!='00:00'}
                    {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                  {/if}
                {/if}
              {/if}
              <br/>
              [{l s='Quantity' mod='psbooking'} - {$bookingProduct['quantity']|escape:'htmlall':'UTF-8'}]
            </td>
            <td  class="product right">
              {displayPrice currency=$order->id_currency price=$bookingProduct['total_range_feature_price_tax_excl']}
            </td>
          </tr>
        {/foreach}
      {/if}
    {else}
      <tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}">

        <td class="product center">
          {$order_detail.product_reference|escape:'htmlall':'UTF-8'}
        </td>
        <td class="product left">
          {if $display_product_images}
            <table width="100%">
              <tr>
                <td width="15%">
                  {if isset($order_detail.image) && $order_detail.image->id}
                    {$order_detail.image_tag|escape:'htmlall':'UTF-8'}
                  {/if}
                </td>
                <td width="5%">&nbsp;</td>
                <td width="80%">
                  {$order_detail.product_name|escape:'htmlall':'UTF-8'}
                </td>
              </tr>
            </table>
          {else}
            {$order_detail.product_name|escape:'htmlall':'UTF-8'}
          {/if}

        </td>
        <td class="product center">
          {$order_detail.order_detail_tax_label}
        </td>

        {if isset($layout.before_discount)}
          <td class="product center">
            {if isset($order_detail.unit_price_tax_excl_before_specific_price)}
              {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_before_specific_price}
            {else}
              --
            {/if}
          </td>
        {/if}

        <td class="product right">
          {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_including_ecotax}
          {if $order_detail.ecotax_tax_excl > 0}
            <br>
            <small>{displayPrice currency=$order->id_currency price=$order_detail.ecotax_tax_excl}</small>
          {/if}
        </td>
        <td class="product center">
          {$order_detail.product_quantity|escape:'htmlall':'UTF-8'}
        </td>
        <td  class="product right">
          {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl_including_ecotax}
        </td>
      </tr>

      {foreach $order_detail.customizedDatas as $customizationPerAddress}
        {foreach $customizationPerAddress as $customizationId => $customization}
          <tr class="customization_data {$bgcolor_class|escape:'htmlall':'UTF-8'}">
            <td class="center"> &nbsp;</td>

            <td>
              {if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
                <table style="width: 100%;">
                  {foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
                    <tr>
                      <td style="width: 30%;">
                        {$customization_infos.name|escape:'htmlall':'UTF-8'}
                      </td>
                      <td>{if (int)$customization_infos.id_module}{$customization_infos.value|escape:'htmlall':'UTF-8'}{else}{$customization_infos.value|escape:'htmlall':'UTF-8'}{/if}</td>
                    </tr>
                  {/foreach}
                </table>
              {/if}

              {if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
                <table style="width: 100%;">
                  <tr>
                    <td style="width: 70%;">{l s='image(s):' mod='psbooking'}</td>
                    <td>{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])|escape:'htmlall':'UTF-8'}</td>
                  </tr>
                </table>
              {/if}
            </td>

            <td class="center">
              ({if $customization.quantity == 0}1{else}{$customization.quantity|escape:'htmlall':'UTF-8'}{/if})
            </td>

            {assign var=end value=($layout._colCount-3)}
            {for $var=0 to $end}
              <td class="center">
                --
              </td>
            {/for}

          </tr>
          <!--if !$smarty.foreach.custo_foreach.last-->
        {/foreach}
      {/foreach}
    {/if}
  {/foreach}
  <!-- END PRODUCTS -->

  <!-- CART RULES -->

  {assign var="shipping_discount_tax_incl" value="0"}
  {foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
    {if $smarty.foreach.cart_rules_loop.first}
    <tr class="discount">
      <th class="header" colspan="{$layout._colCount|escape:'htmlall':'UTF-8'}">
        {l s='Discounts' mod='psbooking'}
      </th>
    </tr>
    {/if}
    <tr class="discount">
      <td class="white right" colspan="{$layout._colCount - 1|escape:'htmlall':'UTF-8'}">
        {$cart_rule.name|escape:'htmlall':'UTF-8'}
      </td>
      <td class="right white">
        - {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
      </td>
    </tr>
  {/foreach}

  </tbody>

</table>
