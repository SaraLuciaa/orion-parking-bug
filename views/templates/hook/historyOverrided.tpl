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

{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <h6>{l s='Here are the orders you\'ve placed since your account was created.' d='Shop.Theme.Customeraccount'}</h6>

  {if $orders}
    <table class="table table-striped table-bordered table-labeled hidden-sm-down">
      <thead class="thead-default">
        <tr>
          <th>{l s='Order reference' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Status' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Invoice' d='Shop.Theme.Checkout'}</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$orders item=order}
          <tr>
            <th scope="row">{$order.details.reference|escape:'htmlall':'UTF-8'}</th>
            <td>{$order.details.order_date|escape:'htmlall':'UTF-8'}</td>
            <td class="text-xs-right">{$order.totals.total.value|escape:'htmlall':'UTF-8'}</td>
            <td class="hidden-md-down">{$order.details.payment|escape:'htmlall':'UTF-8'}</td>
            <td>
              <span
                class="label label-pill {$order.history.current.contrast|escape:'htmlall':'UTF-8'}"
                style="background-color:{$order.history.current.color|escape:'htmlall':'UTF-8'}"
              >
                {$order.history.current.ostate_name|escape:'htmlall':'UTF-8'}
              </span>
            </td>
            <td class="text-xs-center hidden-md-down">
              {if $order.details.invoice_url}
                <a href="{$order.details.invoice_url|escape:'htmlall':'UTF-8'}"><i class="material-icons">&#xE415;</i></a>
              {else}
                -
              {/if}
            </td>
            <td class="text-xs-center order-actions">
              <a href="{$order.details.details_url|escape:'htmlall':'UTF-8'}" data-link-action="view-order-details">
                {l s='Details' d='Shop.Theme.Customeraccount'}
              </a>
              {if isset($order.bookingProductExists)}
              {else}
                {if $order.details.reorder_url}
                    <a href="{$order.details.reorder_url|escape:'htmlall':'UTF-8'}">{l s='Reorder' d='Shop.Theme.Actions'}</a>
                {/if}
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>

    <div class="orders hidden-md-up">
      {foreach from=$orders item=order}
        <div class="order">
          <div class="row">
            <div class="col-xs-10">
              <a href="{$order.details.details_url|escape:'htmlall':'UTF-8'}"><h3>{$order.details.reference|escape:'htmlall':'UTF-8'}</h3></a>
              <div class="date">{$order.details.order_date|escape:'htmlall':'UTF-8'}</div>
              <div class="total">{$order.totals.total.value|escape:'htmlall':'UTF-8'}</div>
              <div class="status">
                <span
                  class="label label-pill {$order.history.current.contrast|escape:'htmlall':'UTF-8'}"
                  style="background-color:{$order.history.current.color|escape:'htmlall':'UTF-8'}"
                >
                  {$order.history.current.ostate_name|escape:'htmlall':'UTF-8'}
                </span>
              </div>
            </div>
            <div class="col-xs-2 text-xs-right">
                <div>
                  <a href="{$order.details.details_url|escape:'htmlall':'UTF-8'}" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.Customeraccount'}">
                    <i class="material-icons">&#xE8B6;</i>
                  </a>
                </div>
                {if $order.details.reorder_url}
                  <div>
                    <a href="{$order.details.reorder_url|escape:'htmlall':'UTF-8'}" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                      <i class="material-icons">&#xE863;</i>
                    </a>
                  </div>
                {/if}
            </div>
          </div>
        </div>
      {/foreach}
    </div>

  {/if}
{/block}
