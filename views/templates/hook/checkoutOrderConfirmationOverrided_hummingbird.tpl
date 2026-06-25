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
    {$componentName = 'order-confirmation'}

    <div class="{$componentName}__table{block name='order-confirmation-classes'}{/block}">
        <div class="{$componentName}__items">
            {foreach from=$orderProducts item=product}
                {if isset($product.isBookingProduct) && $product.isBookingProduct}
                    {if isset($product.booking_product_data) && $product.booking_product_data}
                        {foreach from=$product.booking_product_data item=bookingProduct}
                            <div class="item row gx-3">

                                <div class="item__image col-lg-1 col-md-2 col-sm-2 col-3 mb-2 mb-md-0">
                                    {if !empty($product.default_image)}
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
                                            loading="lazy"
                                            width="{$product.default_image.bySize.default_xs.width}"
                                            height="{$product.default_image.bySize.default_xs.height}"
                                            alt="{$product.default_image.legend}"
                                            title="{$product.default_image.legend}"
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
                                </div>

                                <div class="item__details col-lg-6 col-md-6 col-sm-10 col-7">
                                    {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
                                    <p class="item__title">{$product.name}</p>
                                    {if $add_product_link}</a>{/if}

                                    {if !empty($product.reference)}
                                    <p class="item__reference">{l s='Reference' d='Shop.Theme.Catalog'} {$product.reference}</p>
                                    {/if}

                                    {if is_array($product.customizations) && $product.customizations|count}
                                    {include file='catalog/_partials/product-customization-modal.tpl' product=$product}
                                    {/if}

                                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                                </div>
                                <div class="item__details col-lg-3 col-md-2 col-sm-12 col-12">
                                    {if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_RENTAL}
                                    {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}
                                    {l s='to' mod='psbooking'}
                                    {Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}
                                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT}
                                    {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'}
                                    {$bookingProduct['time_from']|escape:'htmlall':'UTF-8'} - {$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}
                                    {else if $bookingProduct['booking_type'] == WkBookingProductInformation::TYPE_EVENT}
                                    {Tools::displayDate($bookingProduct['date_from'])|escape:'htmlall':'UTF-8'} {if $bookingProduct['time_from']!='00:00'}{$bookingProduct['time_from']|escape:'htmlall':'UTF-8'}{/if}{if $bookingProduct['date_to'] != '0000-00-00 00:00:00'} {l s='to' mod='psbooking'}{Tools::displayDate($bookingProduct['date_to'])|escape:'htmlall':'UTF-8'}{if $bookingProduct['time_to']!='00:00'}{$bookingProduct['time_to']|escape:'htmlall':'UTF-8'}{/if}{/if}
                                    {/if}
                                </div>
                                <div class="item__prices col-md-2 col-sm-12 col-12">
                                    {if (!$priceDisplay || $priceDisplay == 2)}
                                        <div class="text-md-end">{l s='%product_price% (x%product_quantity%)' sprintf=['%product_price%' => $bookingProduct['unit_feature_price_tax_incl_formated']|escape:'htmlall':'UTF-8', '%product_quantity%' => $bookingProduct['quantity']] d='Shop.Theme.Catalog'}</div>
                                    {else}
                                        <div class="text-md-end">{l s='%product_price% (x%product_quantity%)' sprintf=['%product_price%' => $bookingProduct['unit_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8', '%product_quantity%' => $bookingProduct['quantity']] d='Shop.Theme.Catalog'}</div>
                                    {/if}
                                    <div class="text-md-end">
                                    {if (!$priceDisplay || $priceDisplay == 2)}
                                        {$bookingProduct['total_range_feature_price_tax_incl_formated']|escape:'htmlall':'UTF-8'}
                                    {else}
                                        {$bookingProduct['total_range_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}
                                    {/if}
                                    </div>
                                </div>

                            </div>
                        {/foreach}
                    {/if}
                {else}
                    <div class="item row gx-3">

                        <div class="item__image col-lg-1 col-md-2 col-sm-2 col-3 mb-2 mb-md-0">
                            {if !empty($product.default_image)}
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
                                    loading="lazy"
                                    width="{$product.default_image.bySize.default_xs.width}"
                                    height="{$product.default_image.bySize.default_xs.height}"
                                    alt="{$product.default_image.legend}"
                                    title="{$product.default_image.legend}"
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
                        </div>

                        <div class="item__details col-lg-9 col-md-8 col-sm-10 col-7">
                            {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
                            <p class="item__title">{$product.name}</p>
                            {if $add_product_link}</a>{/if}

                            {if !empty($product.reference)}
                            <p class="item__reference">{l s='Reference' d='Shop.Theme.Catalog'} {$product.reference}</p>
                            {/if}

                            {if is_array($product.customizations) && $product.customizations|count}
                            {include file='catalog/_partials/product-customization-modal.tpl' product=$product}
                            {/if}

                            {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                        </div>

                        <div class="item__prices col-md-2 col-sm-12 col-12">
                            <div class="text-md-end">{l s='%product_price% (x%product_quantity%)' sprintf=['%product_price%' => $product.price, '%product_quantity%' => $product.quantity] d='Shop.Theme.Catalog'}</div>
                            <div class="text-md-end">{$product.total}</div>
                        </div>

                    </div>
                {/if}
            {/foreach}
        </div>

        <hr>

        <div class="{$componentName}__subtotals">
            {foreach $subtotals as $subtotal}
            {if $subtotal !== null && $subtotal.type !== 'tax' && $subtotal.label !== null}
                <div class="row">
                <div class="col-6">{$subtotal.label}</div>
                <div class="col-6 text-end">{if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}</div>
                </div>
            {/if}
            {/foreach}
        </div>

        <hr>

        <div class="{$componentName}__totals fw-bold">
            {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
            <div class="row">
                <div class="col-6">{$totals.total.label}&nbsp;{$labels.tax_short}</div>
                <div class="col-6 text-end">{$totals.total.value}</div>
            </div>

            <div class="row fw-bold">
                <div class="col-6">{$totals.total_including_tax.label}</div>
                <div class="col-6 text-end">{$totals.total_including_tax.value}</div>
            </div>
            {else}
            <div class="row fw-bold">
                <div class="col-6">{$totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$labels.tax_short}{/if}</div>
                <div class="col-6 text-end">{$totals.total.value}</div>
            </div>
            {/if}

            {if $subtotals.tax !== null && $subtotals.tax.label !== null}
            <div class="row">
                <div class="col-6">{l s='%label%:' sprintf=['%label%' => $subtotals.tax.label] d='Shop.Theme.Global'}</div>
                <div class="col-6 text-end">{$subtotals.tax.value}</div>
            </div>
            {/if}
        </div>
    </div>
{/block}
