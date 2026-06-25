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

{extends file=$order_confirmation_template_file}
{block name='order_confirmation_table'}
  {block name='order-items-table-head'}
  <div id="order-items" class="col-md-12">
    <div class="row">
        <h3 class="card-title h3 col-md-6 col-12">{l s='Order items' mod='psbooking'}</h3>
        <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Unit price' mod='psbooking'}</h3>
        <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Quantity' mod='psbooking'}</h3>
        <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Total products' mod='psbooking'}</h3>
    </div>
  {/block}
    <div class="order-confirmation-table" style="text-align: center;">
      <table class="table">
        {foreach from=$orderProducts item=product}
          {if isset($product.isBookingProduct) && $product.isBookingProduct}
            {if isset($product.booking_product_data) && $product.booking_product_data}
              {foreach from=$product.booking_product_data item=bookingProduct}
                <div class="order-line row">
                  <div class="col-sm-2 col-xs-3">
                    <span class="image">
                      {if !empty($product.default_image)}
                        <img src="{$product.default_image.medium.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                      {else}
                        <img src="{$urls.no_picture_image.bySize.medium_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                      {/if}
                    </span>
                  </div>
                  <div class="col-sm-4 col-xs-9 details">
                    {if $add_product_link}<a href="{$product.url|escape:'htmlall':'UTF-8'}" target="_blank">{/if}
                      <span>{$product.name|escape:'htmlall':'UTF-8'}</span>
                    {if $add_product_link}</a>{/if}
                    {if $product.customizations|count}
                      {foreach from=$product.customizations item="customization"}
                        <div class="customizations">
                          <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                        </div>
                        <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                              </div>
                              <div class="modal-body">
                                {foreach from=$customization.fields item="field"}
                                  <div class="product-customization-line row">
                                    <div class="col-sm-3 col-xs-4 label">
                                      {$field.label|escape:'htmlall':'UTF-8'}
                                    </div>
                                    <div class="col-sm-9 col-xs-8 value">
                                      {if $field.type == 'text'}
                                        {if (int)$field.id_module}
                                          {$field.text|escape:'htmlall':'UTF-8'}
                                        {else}
                                          {$field.text|escape:'htmlall':'UTF-8'}
                                        {/if}
                                      {elseif $field.type == 'image'}
                                        <img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}">
                                      {/if}
                                    </div>
                                  </div>
                                {/foreach}
                              </div>
                            </div>
                          </div>
                        </div>
                      {/foreach}
                    {/if}
                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                  </div>
                  <div class="col-sm-6 col-xs-12 qty">
                    <div class="row">
                      <div class="col-xs-4 text-sm-center text-xs-left">
                        {if (!$priceDisplay || $priceDisplay == 2)}
                          {$bookingProduct['unit_feature_price_tax_incl_formated']|escape:'htmlall':'UTF-8'}
                        {else}
                          {$bookingProduct['unit_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}
                        {/if}
                      </div>
                      <div class="col-xs-4 text-sm-right text-xs-left booking_date_range" style="font-size: 14px; text-align: center!important;">
                        {$bookingProduct['quantity']|escape:'htmlall':'UTF-8'}
                        <br>
                        {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                          {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}</br>
                          {l s='to' mod='psbooking'}</br>
                          {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                        {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                          {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}</br>
                          {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                        {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                          {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'} {if $bookingProduct['time_from']!='00:00'}{$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}{/if}{if $bookingProduct['date_to'] != '0000-00-00 00:00:00'}</br> {l s='to' mod='psbooking'}</br>{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}{if $bookingProduct['time_to']!='00:00'}{$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}{/if}{/if}
                        {/if}
                      </div>
                      <div class="col-xs-4 text-sm-center text-xs-right bold">
                        {if (!$priceDisplay || $priceDisplay == 2)}
                          {$bookingProduct['total_range_feature_price_tax_incl_formated']|escape:'htmlall':'UTF-8'}
                        {else}
                          {$bookingProduct['total_range_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}
                        {/if}
                      </div>
                    </div>
                  </div>
                </div>
              {/foreach}
            {/if}
          {else}
            <div class="order-line row">
              <div class="col-sm-2 col-xs-3">
                <span class="image">
                  {if !empty($product.default_image)}
                    <img src="{$product.default_image.medium.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                  {else}
                    <img src="{$urls.no_picture_image.bySize.medium_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                  {/if}
                </span>
              </div>
              <div class="col-sm-4 col-xs-9 details">
                {if $add_product_link}<a href="{$product.url|escape:'htmlall':'UTF-8'}" target="_blank">{/if}
                  <span>{$product.name|escape:'htmlall':'UTF-8'}</span>
                {if $add_product_link}</a>{/if}
                {if $product.customizations|count}
                  {foreach from=$product.customizations item="customization"}
                    <div class="customizations">
                      <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                    </div>
                    <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                          </div>
                          <div class="modal-body">
                            {foreach from=$customization.fields item="field"}
                              <div class="product-customization-line row">
                                <div class="col-sm-3 col-xs-4 label">
                                  {$field.label|escape:'htmlall':'UTF-8'}
                                </div>
                                <div class="col-sm-9 col-xs-8 value">
                                  {if $field.type == 'text'}
                                    {if (int)$field.id_module}
                                      {$field.text|escape:'htmlall':'UTF-8'}
                                    {else}
                                      {$field.text|escape:'htmlall':'UTF-8'}
                                    {/if}
                                  {elseif $field.type == 'image'}
                                    <img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}">
                                  {/if}
                                </div>
                              </div>
                            {/foreach}
                          </div>
                        </div>
                      </div>
                    </div>
                  {/foreach}
                {/if}
                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
              </div>
              <div class="col-sm-6 col-xs-12 qty">
                <div class="row">
                  <div class="col-xs-4 text-sm-center text-xs-left">{$product.price|escape:'htmlall':'UTF-8'}</div>
                  <div class="col-xs-4 text-sm-center">{$product.quantity|escape:'htmlall':'UTF-8'}</div>
                  <div class="col-xs-4 text-sm-center text-xs-right bold">{$product.total|escape:'htmlall':'UTF-8'}</div>
                </div>
              </div>
            </div>
          {/if}
        {/foreach}
      <hr />
      <table>
        {foreach $subtotals as $subtotal}
          {if $subtotal.type !== 'tax'}
            <tr>
              <td align="left">{$subtotal.label|escape:'htmlall':'UTF-8'}</td>
              <td>{$subtotal.value|escape:'htmlall':'UTF-8'}</td>
            </tr>
          {/if}
        {/foreach}
        {if $subtotals.tax.label !== null}
          <tr class="sub">
            <td align="left">{$subtotals.tax.label|escape:'htmlall':'UTF-8'}</td>
            <td>{$subtotals.tax.value|escape:'htmlall':'UTF-8'}</td>
          </tr>
        {/if}
        <tr class="total-value font-weight-bold">
          <td align="left"><span class="text-uppercase">{$totals.total.label|escape:'htmlall':'UTF-8'}</span> {$labels.tax_short|escape:'htmlall':'UTF-8'}</td>
          <td>{$totals.total.value|escape:'htmlall':'UTF-8'}</td>
        </tr>
      </table>
    </div>
  </div>
{/block}
