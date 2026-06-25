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

{block name='cart_detailed_product'}
  <div class="cart-overview js-cart"
    data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}"
    tabindex="-1"
  >
    <hr />

    {if $presentedCart.products}
      <ul class="cart__items">
        {foreach from=$presentedCart.products item=product}
            {if isset($product.isBookingProduct) && $product.isBookingProduct}
                {if isset($product.booking_product_data) && $product.booking_product_data}
                    {foreach from=$product.booking_product_data item=bookingProduct}
                        <li class="cart__item">
                            <div class="product-line row">
                                {assign var=product_line_alert_id value=10|mt_rand:100000}
                                <div id="js-product-line-alert--{$product_line_alert_id}"></div>
                                <div class="product-line__image col-4 col-sm-2">
                                    <a class="product-line__title product-line__item" href="{$product.url}"
                                        data-id_customization="{$product.id_customization|intval}">
                                        {if $product.default_image}
                                            <picture>
                                            {if isset($product.default_image.bySize.default_xs.sources.avif)}
                                                <source
                                                srcset="
                                                    {$product.default_image.bySize.default_xs.sources.avif},
                                                    {$product.default_image.bySize.default_m.sources.avif} 2x"
                                                type="image/avif"
                                                >
                                            {/if}

                                            {if isset($product.default_image.bySize.default_xs.sources.webp)}
                                                <source
                                                srcset="
                                                    {$product.default_image.bySize.default_xs.sources.webp},
                                                    {$product.default_image.bySize.default_m.sources.webp} 2x"
                                                type="image/webp"
                                                >
                                            {/if}

                                            <img
                                                class="img-fluid"
                                                srcset="
                                                {$product.default_image.bySize.default_xs.url},
                                                {$product.default_image.bySize.default_m.url} 2x"
                                                width="{$product.default_image.bySize.default_xs.width}"
                                                height="{$product.default_image.bySize.default_xs.height}"
                                                loading="lazy"
                                                alt="{$product.name|escape:'quotes'}"
                                                title="{$product.name|escape:'quotes'}"
                                            >
                                            </picture>
                                        {else}
                                            <picture>
                                            {if isset($urls.no_picture_image.bySize.default_xs.sources.avif)}
                                                <source
                                                srcset="
                                                    {$urls.no_picture_image.bySize.default_xs.sources.avif},
                                                    {$urls.no_picture_image.bySize.default_m.sources.avif} 2x"
                                                type="image/avif"
                                                >
                                            {/if}

                                            {if isset($urls.no_picture_image.bySize.default_xs.sources.webp)}
                                                <source
                                                srcset="
                                                    {$urls.no_picture_image.bySize.default_xs.sources.webp},
                                                    {$urls.no_picture_image.bySize.default_m.sources.webp} 2x"
                                                type="image/webp"
                                                >
                                            {/if}

                                            <img
                                                class="img-fluid"
                                                srcset="
                                                {$urls.no_picture_image.bySize.default_xs.url},
                                                {$urls.no_picture_image.bySize.default_m.url} 2x"
                                                width="{$urls.no_picture_image.bySize.default_xs.width}"
                                                height="{$urls.no_picture_image.bySize.default_xs.height}"
                                                loading="lazy"
                                            >
                                            </picture>
                                        {/if}
                                    </a>
                                </div>

                                <div class="product-line__content col-8 col-sm-4 col-md-6">
                                    <a class="product-line__title product-line__item" href="{$product.url}"
                                    data-id_customization="{$product.id_customization|intval}">
                                        {$product.name}
                                    </a>
                                    {if is_array($product.customizations) && $product.customizations|count}
                                        {include file='catalog/_partials/product-customization-modal.tpl' product=$product}
                                    {/if}
                                    {foreach from=$product.attributes key="attribute" item="value"}
                                        <div class="product-line__info product-line__item {$attribute|lower}">
                                            <span class="label">{$attribute}:</span>
                                            <span class="value">{$value}</span>
                                        </div>
                                    {/foreach}
                                    <div class="product-line__prices product-line__item">
                                        <div class="product-line__current">
                                            <span class="price">
                                                {if (!$priceDisplay || $priceDisplay == 2)}
                                                    {$bookingProduct['unit_feature_price_tax_incl_formated']|escape:'htmlall':'UTF-8'}
                                                {else}
                                                    {$bookingProduct['unit_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}
                                                {/if}
                                            </span>
                                            {if $product.unit_price_full}
                                            <div class="unit-price-cart">{$product.unit_price_full}</div>
                                            {/if}
                                        </div>
                                        {if $product.has_discount}
                                            <div class="product-line__basic">
                                            <span class="product-line__regular">{$product.regular_price}</span>

                                            {if $product.discount_type === 'percentage'}
                                                <span class="discount badge discount">
                                                -{$product.discount_percentage_absolute}
                                                </span>
                                            {else}
                                                <span class="discount badge discount">
                                                -{$product.discount_to_display}
                                                </span>
                                            {/if}
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                                <div class="col-4 d-block d-sm-none"></div>
                                <div class="product-line__informations col-8 col-sm-6 col-md-4">
                                    <div class="row">
                                        <div class="quantity-button js-quantity-button col-12">
                                            {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                                                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'} {l s='to' mod='psbooking'} {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                                            {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                                                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}
                                                {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                                            {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                                                {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}
                                                {if $bookingProduct['time_from']!='00:00'}
                                                    {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}
                                                {/if}
                                                {if $bookingProduct['date_to'] != '0000-00-00 00:00:00'}
                                                        {l s='to' mod='psbooking'} {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                                                    {if $bookingProduct['time_to']!='00:00'}
                                                    {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                                                    {/if}
                                                {/if}
                                            {/if}
                                            <br>
                                            {l s='Quantity' mod='psbooking'} - {$bookingProduct['quantity']|escape:'htmlall':'UTF-8'}
                                        </div>
                                        <div class="col-12">
                                            {if $product.has_discount}
                                                <div class="product-line__discount">
                                                    <div class="price">
                                                        <span class="product-line__price">
                                                            <strong>
                                                            {if !empty($product.is_gift)}
                                                                <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                                                            {else}
                                                                {$product.total}
                                                            {/if}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                </div>
                                            {else}
                                                {if !empty($product.is_gift)}
                                                    <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                                                {else}
                                                    {if (!$priceDisplay || $priceDisplay == 2)}
                                                        {$bookingProduct['totalPriceTI']|escape:'htmlall':'UTF-8'}
                                                    {else}
                                                        {$bookingProduct['totalPriceTE']|escape:'htmlall':'UTF-8'}
                                                    {/if}
                                                {/if}
                                            {/if}
                                            {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-sm-2"></div>
                                <div class="product-line__actions col-8 col-sm-10">
                                    {if empty($product.is_gift)}
                                        <a class="remove-from-cart remove-booking-product" href="#"
                                            id-cart-booking        = "{$bookingProduct['id']|escape:'htmlall':'UTF-8'}"
                                            id-product="{$product.id_product|escape:'javascript'}"
                                            id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
                                            >
                                            {l s='Remove' d='Shop.Theme.Checkout'}
                                        </a>
                                    {/if}

                                    {block name='hook_cart_extra_product_actions'}
                                        {hook h='displayCartExtraProductActions' product=$product}
                                    {/block}
                                </div>
                            </div>
                            <hr />
                        </li>
                    {/foreach}
                {/if}
            {else}
                <li class="cart__item">
                    {block name='cart_detailed_product_line'}
                        {include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}
                    {/block}
                    <hr />
                </li>
            {/if}

          {if is_array($product.customizations) && $product.customizations|count>1}
          <hr>{/if}
        {/foreach}
      </ul>
    {else}
      <p class="mb-3">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</p>
    {/if}
  </div>
{/block}
