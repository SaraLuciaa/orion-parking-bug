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

{extends file=$checkout_template_file}
{block name='cart_summary_product_line'}
  <div class="media-left">
    <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
      {if $product.default_image}
        <img class="media-object" src="{$product.default_image.small.url|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'htmlall':'UTF-8'}" loading="lazy">
      {else}
        <img src="{$urls.no_picture_image.bySize.small_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
      {/if}
    </a>
  </div>
  <div class="media-body">
    <span class="product-name">
      {$product.name|escape:'htmlall':'UTF-8'}
      {if isset($product.isBookingProduct) && $product.isBookingProduct}
        </br>
        <span class="booking_product_label">{l s='Booking product' d='Shop.Theme.Catalog'}</span>
      {/if}
    </span>
    {if isset($product.isBookingProduct) && $product.isBookingProduct}
      <span class="product-price float-xs-right">{$product.total_price_tax_excl_formatted|escape:'htmlall':'UTF-8'}</span>
    {else}
      <span class="product-quantity">x{$product.quantity|escape:'htmlall':'UTF-8'}</span>
      <span class="product-price float-xs-right">{$product.price|escape:'htmlall':'UTF-8'}</span>
    {/if}
    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
  </div>
  <style>
    .booking_product_label {
      background-color: #24b9d7;
      color: #fff;
      font-size: 11px;
      padding: 0px 4px;
      border-radius: 4px;`
    }
  </style>
{/block}

  {block name='order_items_table_head'}
    <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
  {/block}
