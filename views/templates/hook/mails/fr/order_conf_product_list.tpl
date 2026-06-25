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

{foreach $list as $product}
  {if isset($product.isBookingProduct) && $product.isBookingProduct}
    {if isset($product.booking_product_data) && $product.booking_product_data}
      {foreach from=$product.booking_product_data item=bookingProduct}
        <tr>
          <td style="border:1px solid #D6D4D4;">
            <table class="table">
              <tr>
                <td width="10">&nbsp;</td>
                <td>
                  <font size="2" face="Open-sans, sans-serif" color="#555454">
                    {$product['reference']|escape:'htmlall':'UTF-8'}
                  </font>
                </td>
                <td width="10">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td style="border:1px solid #D6D4D4;">
            <table class="table">
              <tr>
                <td width="10">&nbsp;</td>
                <td>
                  <font size="2" face="Open-sans, sans-serif" color="#555454">
                    <strong>{$product['name']|escape:'htmlall':'UTF-8'}</strong>
                    {if count($product['customization']) == 1}
                      <br>
                      {foreach $product['customization'] as $customization}
                        {$customization['customization_text']|escape:'htmlall':'UTF-8'}
                      {/foreach}
                    {/if}

                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                  </font>
                </td>
                <td width="10">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td style="border:1px solid #D6D4D4;">
            <table class="table">
              <tr>
                <td width="10">&nbsp;</td>
                <td align="right">
                  <font size="2" face="Open-sans, sans-serif" color="#555454">
                    {$bookingProduct['unit_feature_price']|escape:'htmlall':'UTF-8'}
                  </font>
                </td>
                <td width="10">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td style="border:1px solid #D6D4D4;">
            <table class="table">
              <tr>
                <td width="10">&nbsp;</td>
                <td align="center">
                  <font size="2" face="Open-sans, sans-serif" color="#555454">
                    {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                      {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}</br>
                      Pour</br>
                      {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                      {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}</br>
                      {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                      {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'} {if $bookingProduct['time_from']!='00:00'}{$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}{/if}{if $bookingProduct['date_to'] != '0000-00-00 00:00:00'}</br> Pour</br>{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}{if $bookingProduct['time_to']!='00:00'}{$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}{/if}{/if}
                    {/if}
                    <br/>
                    [Quantité - {$bookingProduct['quantity']|escape:'htmlall':'UTF-8'}]
                  </font>
                </td>
                <td width="10">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td style="border:1px solid #D6D4D4;">
            <table class="table">
              <tr>
                <td width="10">&nbsp;</td>
                <td align="right">
                  <font size="2" face="Open-sans, sans-serif" color="#555454">
                    {$bookingProduct['total_range_feature_price_formated']|escape:'htmlall':'UTF-8'}
                  </font>
                </td>
                <td width="10">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
      {/foreach}
    {/if}
  {else}
    <tr>
      <td style="border:1px solid #D6D4D4;">
        <table class="table">
          <tr>
            <td width="10">&nbsp;</td>
            <td>
              <font size="2" face="Open-sans, sans-serif" color="#555454">
                {$product['reference']|escape:'htmlall':'UTF-8'}
              </font>
            </td>
            <td width="10">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td style="border:1px solid #D6D4D4;">
        <table class="table">
          <tr>
            <td width="10">&nbsp;</td>
            <td>
              <font size="2" face="Open-sans, sans-serif" color="#555454">
                <strong>{$product['name']|escape:'htmlall':'UTF-8'}</strong>
                {if count($product['customization']) == 1}
                  <br>
                  {foreach $product['customization'] as $customization}
                    {$customization['customization_text']|escape:'htmlall':'UTF-8'}
                  {/foreach}
                {/if}

                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
              </font>
            </td>
            <td width="10">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td style="border:1px solid #D6D4D4;">
        <table class="table">
          <tr>
            <td width="10">&nbsp;</td>
            <td align="right">
              <font size="2" face="Open-sans, sans-serif" color="#555454">
                {$product['unit_price']|escape:'htmlall':'UTF-8'}
              </font>
            </td>
            <td width="10">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td style="border:1px solid #D6D4D4;">
        <table class="table">
          <tr>
            <td width="10">&nbsp;</td>
            <td align="right">
              <font size="2" face="Open-sans, sans-serif" color="#555454">
                {$product['quantity']|escape:'htmlall':'UTF-8'}
              </font>
            </td>
            <td width="10">&nbsp;</td>
          </tr>
        </table>
      </td>
      <td style="border:1px solid #D6D4D4;">
        <table class="table">
          <tr>
            <td width="10">&nbsp;</td>
            <td align="right">
              <font size="2" face="Open-sans, sans-serif" color="#555454">
                {$product['price']|escape:'htmlall':'UTF-8'}
              </font>
            </td>
            <td width="10">&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    {if count($product['customization']) > 1}
      {foreach $product['customization'] as $customization}
        <tr>
        <td colspan="3" style="border:1px solid #D6D4D4;">
          <table class="table">
            <tr>
              <td width="10">&nbsp;</td>
              <td>
                <font size="2" face="Open-sans, sans-serif" color="#555454">
                  {$customization['customization_text']|escape:'htmlall':'UTF-8'}
                </font>
              </td>
              <td width="10">&nbsp;</td>
            </tr>
          </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
          <table class="table">
            <tr>
              <td width="10">&nbsp;</td>
              <td align="right">
                <font size="2" face="Open-sans, sans-serif" color="#555454">
                  {if count($product['customization']) > 1}
                    {$customization['customization_quantity']|escape:'htmlall':'UTF-8'}
                  {/if}
                </font>
              </td>
              <td width="10">&nbsp;</td>
            </tr>
          </table>
        </td>
        <td style="border:1px solid #D6D4D4;"></td>
      </tr>
      {/foreach}
    {/if}
  {/if}
{/foreach}
