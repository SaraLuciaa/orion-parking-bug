{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$order_details_template_file}


{block name='order_infos'}
<div class="order__details">
    <div class="order__header row align-items-end">
    <div class="order__header__left col-12 col-sm-6">
        <p class="order__reference">
        {l
                                                        s='Order Reference %reference% - placed on %date%'
                                                        d='Shop.Theme.Customeraccount'
                                                        sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
                                                    }
        </p>

        <p class="order__carrier">
        {l s='Carrier: %carrierName%' d='Shop.Theme.Checkout' sprintf=['%carrierName%' => $order.carrier.name]}
        </p>

        <p class="order__payment">
        {l s='Payment method: %paymentMethod%' d='Shop.Theme.Checkout' sprintf=['%paymentMethod%' => $order.details.payment]}
        </p>

        {if $order.details.invoice_url}
        <a href="{$order.details.invoice_url}">
            {l s='Download your invoice as a PDF file.' d='Shop.Theme.Customeraccount'}
        </a>
        {/if}

        {if $order.details.recyclable}
        <p>
            {l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.Customeraccount'}
        </p>
        {/if}

        {if $order.details.gift_message}
        <p>{l s='You have requested gift wrapping for this order.' d='Shop.Theme.Customeraccount'}</p>
        <p>{l s='Message' d='Shop.Theme.Customeraccount'} {$order.details.gift_message nofilter}</p>
        {/if}
    </div>
    </div>
</div>
{/block}


{block name='order_detail'}
    {if $order.details.is_returnable}
    {include file='customer/_partials/order-detail-return.tpl'}
    {else}
        {block name='order_products_table'}
            <div class="table-wrapper d-none d-sm-block d-md-block">
                <table id="order-products" class="table order-products">
                <thead class="thead-default">
                    <tr>
                    <th>{l s='Image' d='Shop.Theme.Catalog'}</th>
                    <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
                    <th class="text-xs-start text-end">{l s='Quantity' d='Shop.Theme.Catalog'}</th>
                    <th class="text-xs-start text-end">{l s='Unit price' d='Shop.Theme.Catalog'}</th>
                    <th class="text-xs-start text-end">{l s='Total price' d='Shop.Theme.Catalog'}</th>
                    </tr>
                </thead>
                {foreach from=$orderProducts item=product}
                    {if isset($product.isBookingProduct) && $product.isBookingProduct}
                        {if isset($product.booking_product_data) && $product.booking_product_data}
                            {foreach from=$product.booking_product_data item=bookingProduct}
                                <tr>
                                    <td>
                                        {if $product.cover}
                                        <picture>
                                            {if isset($product.cover.bySize.default_xs.sources.avif)}
                                            <source
                                                srcset="
                                                {$product.cover.bySize.default_xs.sources.avif},
                                                {$product.cover.bySize.default_m.sources.avif} 2x",
                                                type="image/avif"
                                            >
                                            {/if}

                                            {if isset($product.cover.bySize.default_xs.sources.webp)}
                                            <source
                                                srcset="
                                                {$product.cover.bySize.default_xs.sources.webp},
                                                {$product.cover.bySize.default_m.sources.webp} 2x"
                                                type="image/webp"
                                            >
                                            {/if}

                                            <img
                                            class="order-products__image card-img-top w-auto"
                                            srcset="
                                                {$product.cover.bySize.default_xs.url},
                                                {$product.cover.bySize.default_m.url} 2x"
                                            width="{$product.cover.bySize.default_xs.width}"
                                            height="{$product.cover.bySize.default_xs.height}"
                                            loading="lazy"
                                            alt="{$product.cover.legend}"
                                            title="{$product.cover.legend}"
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
                                            class="order-products__image card-img-top w-auto"
                                            srcset="
                                                {$urls.no_picture_image.bySize.default_xs.url},
                                                {$urls.no_picture_image.bySize.default_m.url} 2x"
                                            width="{$urls.no_picture_image.bySize.default_xs.width}"
                                            height="{$urls.no_picture_image.bySize.default_xs.height}"
                                            loading="lazy"
                                            >
                                        </picture>
                                        {/if}
                                    </td>

                                    <td>
                                        <p class="order__item__name fw-bold mb-0">
                                        <a href="{$link->getProductLink($product.id_product)}">
                                            {$product.name}
                                        </a>
                                        </p>

                                        {if $product.product_reference}
                                        <p class="order__item__ref">
                                            {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                                        </p>
                                        {/if}

                                        {if isset($product.download_link)}
                                        <p class="order__item__download my-2">
                                            <a href="{$product.download_link}">
                                            <i class="material-icons" aria-hidden="true">download</i> {l s='Download' d='Shop.Theme.Actions'}
                                            </a>
                                        </p>
                                        {/if}

                                        {if $product.customizations}
                                        {foreach from=$product.customizations item="customization"}
                                            <div class="customization">
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                                            </div>

                                            <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
                                            {include file='catalog/_partials/customization-modal.tpl' customization=$customization}
                                            </div>
                                        {/foreach}
                                        {/if}
                                    </td>

                                    <td class="text-xs-start text-end">
                                        {if $product.customizations}
                                        {foreach $product.customizations as $customization}
                                            {$customization.quantity}
                                        {/foreach}
                                        {else}
                                        {$product.quantity}
                                        <p class="booking_date_range" style="font-size: 14px;">
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
                                        </p>
                                        {/if}
                                    </td>
                                    <td class="text-xs-start text-end">{$bookingProduct['product_real_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}</td>
                                    <td class="text-xs-start text-end">{$bookingProduct['total_range_feature_price_tax_excl_formated']|escape:'htmlall':'UTF-8'}</td>
                                </tr>
                            {/foreach}
                        {/if}
                    {else}
                        <tr>
                            <td>
                                {if $product.cover}
                                <picture>
                                    {if isset($product.cover.bySize.default_xs.sources.avif)}
                                    <source
                                        srcset="
                                        {$product.cover.bySize.default_xs.sources.avif},
                                        {$product.cover.bySize.default_m.sources.avif} 2x",
                                        type="image/avif"
                                    >
                                    {/if}

                                    {if isset($product.cover.bySize.default_xs.sources.webp)}
                                    <source
                                        srcset="
                                        {$product.cover.bySize.default_xs.sources.webp},
                                        {$product.cover.bySize.default_m.sources.webp} 2x"
                                        type="image/webp"
                                    >
                                    {/if}

                                    <img
                                    class="order-products__image card-img-top w-auto"
                                    srcset="
                                        {$product.cover.bySize.default_xs.url},
                                        {$product.cover.bySize.default_m.url} 2x"
                                    width="{$product.cover.bySize.default_xs.width}"
                                    height="{$product.cover.bySize.default_xs.height}"
                                    loading="lazy"
                                    alt="{$product.cover.legend}"
                                    title="{$product.cover.legend}"
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
                                    class="order-products__image card-img-top w-auto"
                                    srcset="
                                        {$urls.no_picture_image.bySize.default_xs.url},
                                        {$urls.no_picture_image.bySize.default_m.url} 2x"
                                    width="{$urls.no_picture_image.bySize.default_xs.width}"
                                    height="{$urls.no_picture_image.bySize.default_xs.height}"
                                    loading="lazy"
                                    >
                                </picture>
                                {/if}
                            </td>

                            <td>
                                <p class="order__item__name fw-bold mb-0">
                                <a href="{$link->getProductLink($product.id_product)}">
                                    {$product.name}
                                </a>
                                </p>

                                {if $product.product_reference}
                                <p class="order__item__ref">
                                    {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                                </p>
                                {/if}

                                {if isset($product.download_link)}
                                <p class="order__item__download my-2">
                                    <a href="{$product.download_link}">
                                    <i class="material-icons" aria-hidden="true">download</i> {l s='Download' d='Shop.Theme.Actions'}
                                    </a>
                                </p>
                                {/if}

                                {if $product.customizations}
                                {foreach from=$product.customizations item="customization"}
                                    <div class="customization">
                                    <a href="#" data-bs-toggle="modal"
                                        data-bs-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                                    </div>

                                    <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
                                    {include file='catalog/_partials/customization-modal.tpl' customization=$customization}
                                    </div>
                                {/foreach}
                                {/if}
                            </td>

                            <td class="text-xs-start text-end">
                                {if $product.customizations}
                                {foreach $product.customizations as $customization}
                                    {$customization.quantity}
                                {/foreach}
                                {else}
                                {$product.quantity}
                                {/if}
                            </td>
                            <td class="text-xs-start text-end">{$product.price}</td>
                            <td class="text-xs-start text-end">{$product.total}</td>
                        </tr>
                    {/if}
                {/foreach}
                <tfoot>
                    {foreach $order.subtotals as $line}
                    {if $line.value}
                        <tr class="text-xs-end line-{$line.type}">
                        <td colspan="4">{$line.label}</td>
                        <td class="text-xs-start text-end">{$line.value}</td>
                        </tr>
                    {/if}
                    {/foreach}
                    <tr class="text-xs-end line-{$order.totals.total.type}">
                    <td colspan="4">{$order.totals.total.label}</td>
                    <td class="text-xs-start text-end">{$order.totals.total.value}</td>
                    </tr>
                </tfoot>
                </table>
            </div>

            <div class="order__items table-wrapper d-block d-sm-none">
                {foreach from=$order.products item=product}
                <div class="order__item">
                    <div class="row">
                    <div class="order__item__header col-12 row">
                        <div class="col-4">
                        {if $product.cover}
                            <img src="{$product.cover.bySize.small_default.url}"
                            alt="{$product.cover.legend}"
                            loading="lazy" data-full-size-image-url="{$product.cover.large.url}" width="64" height="64"
                            class="order-products__image card-img-top w-auto" />
                        {else}
                            <img src="{$urls.no_picture_image.bySize.small_default.url}" loading="lazy" width="64" height="64"
                            class="order-products__image card-img-top w-auto" />
                        {/if}
                        </div>

                        <div class="col-8">
                        <p class="order__item__name fw-bold mb-0">
                            <a href="{$link->getProductLink($product.id_product)}">
                            {$product.name}
                            </a>
                        </p>

                        {if $product.product_reference}
                            <p class="order__item__ref">
                            {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                            </p>
                        {/if}

                        {if isset($product.download_link)}
                            <p class="order__item__download my-2">
                            <a href="{$product.download_link}">
                                <i class="material-icons" aria-hidden="true">download</i> {l s='Download' d='Shop.Theme.Actions'}
                            </a>
                            </p>
                        {/if}

                        {if $product.customizations}
                            {foreach $product.customizations as $customization}
                            <div class="customization">
                                <a href="#" data-bs-toggle="modal"
                                data-bs-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                            </div>
                            <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization}">
                            </div>
                            {/foreach}
                        {/if}
                        </div>
                    </div>

                    <div class="col-12 order__item__qty">
                        <div class="order__item__line row">
                        <span class="order__item__label col">{l s='Quantity' d='Shop.Theme.Catalog'}</span>
                        <span class="order__item__value col text-end">
                            {if $product.customizations}
                            {foreach $product.customizations as $customization}
                                {$customization.quantity}
                            {/foreach}
                            {else}
                            {$product.quantity}
                            {/if}
                        </span>
                        </div>

                        <div class="order__item__line row">
                        <span class="order__item__label col">{l s='Unit price' d='Shop.Theme.Catalog'}</span>
                        <span class="order__item__value col text-end">
                            {$product.price}
                        </span>
                        </div>

                        <div class="order__item__line row">
                        <span class="order__item__label col">{l s='Total price' d='Shop.Theme.Catalog'}</span>
                        <span class="order__item__value col text-end">
                            {$product.price}
                        </span>
                        </div>
                    </div>
                    </div>
                </div>
                {/foreach}

                <hr>

                <div class="order__totals d-block d-sm-none">
                {foreach $order.subtotals as $line}
                    {if $line.value}
                    <div class="order__total row">
                        <div class="col">{$line.label}</div>
                        <div class="col text-end">{$line.value}</div>
                    </div>
                    {/if}
                {/foreach}
                <div class="order__total row">
                    <div class="col fw-bold">{$order.totals.total.label}</div>
                    <div class="col text-end fw-bold">{$order.totals.total.value}</div>
                </div>
                </div>
            </div>
        {/block}
    {/if}
{/block}
