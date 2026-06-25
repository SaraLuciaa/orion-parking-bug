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
<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&add{$table|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
  <div class="panel">
    <div class="panel-heading">
        {l s='Customer' mod='psbooking'}
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel form-horizontal">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label col-lg-3 required">{l s='Search for a customer' mod='psbooking'}
                </label>
                <div class="col-lg-4">
                  <div class="row">
                    <div class="input-group">
                        <input type="text" id="customer-search-input" class="text" value="" autocomplete="off">
                        <span class="input-group-addon">
                            <i class="icon-search"></i>
                        </span>
                    </div>
                    <input type="hidden" name="id_cart" id="wk_id_cart" value="">
                    <input type="hidden" id="customer_id" name="customer_id" class="form-control" value=""/>
                    <div class="dropdown">
                      <ul class="customer_search_results_ul"></ul>
                    </div>
                    <p class="error-block-customer" style="display:none; color: #CD5D5D;">{l s='No match found for this search. Please try with an existing name.' mod='psbooking'}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 address_div">
              <div class="form-group">
                <label class="control-label col-lg-3 required">{l s='Address' mod='psbooking'}
                </label>
                <div class="col-lg-4">
                  <div class="row">
                    <select name="id_address_delivery" id="id_address_delivery">
                    </select>
                    <p class="error-block-address" style="display:none; color: #CD5D5D;">{l s='No address found for this customer. Please create address first.' mod='psbooking'}</p>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-lg-3">
                </label>
                <div class="col-lg-4">
                  <div id="address_delivery_detail">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel card_panel">
    <div class="panel-heading">
      {l s='Cart' mod='psbooking'}
    </div>
    <div class="row">
      <div class="panel form-horizontal">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label col-lg-3 required">{l s='Search for a product' mod='psbooking'}
              </label>
              <div class="col-lg-4">
                <div class="row">
                  <div class="input-group">
                      <input type="text" id="product-search-input" class="text" value="" autocomplete="off">
                      <span class="input-group-addon">
                          <i class="icon-search"></i>
                      </span>
                  </div>
                  <div class="dropdown">
                    <ul class="product_search_results_ul"></ul>
                  </div>
                  <p class="error-block-product" style="display:none; color: #CD5D5D;">{l s='No match found for this search. Please try with an existing name.' mod='psbooking'}</p>
                </div>
              </div>
            </div>
          </div>
          <div id="booking_product_interface">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="added_product_div">
    <div class="panel card">
      <div class="panel-heading card-header">
        {l s='Added product details' mod='psbooking'}
      </div>
      <div class="table-responsive card-body">
        <table class="table" id="orderProducts">
          <thead>
            <tr>
              <th>{l s='Product name' mod='psbooking'}</th>
              <th>{l s='Duration' mod='psbooking'}</th>
              <th>{l s='Quantity' mod='psbooking'}</th>
              <th>{l s='Unit price (tax excl.)' mod='psbooking'}</th>
              <th>{l s='Total price (tax excl.)' mod='psbooking'}</th>
              <th>{l s='Action' mod='psbooking'}</th>
            </tr>
          </thead>
          <tbody class="added_product_tbody">
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel card">
      <div class="panel-heading card-header">
        {l s='Payment' mod='psbooking'}
      </div>
      <div class="form-group">
          <label class="control-label col-lg-3 required">{l s='Payment' mod='psbooking'}</label>
          <div class="col-lg-5">
              <select name="payment_module_name" id="payment_module_name">
                  {foreach from=$payment_modules item='module'}
                  <option value="{$module['name']|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.payment_module_name) && $module['name'] == $smarty.post.payment_module_name}selected="selected"{/if}>{$module['display_name']|escape:'htmlall':'UTF-8'}</option>
                  {/foreach}
              </select>
          </div>
      </div>
      <div class="form-group">
          <label class="control-label col-lg-3 required">{l s='Order status' mod='psbooking'} </label>
          <div class="col-lg-5">
              <select name="order_status_id" id="order_status_id">
                  {foreach from=$order_status item='order_state'}
                  <option value="{$order_state['id_order_state']|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.order_status_id) && $order_state['name'] == $smarty.post.order_status_id}selected="selected"{/if}>{$order_state['name']|escape:'htmlall':'UTF-8'}</option>
                  {/foreach}
              </select>
          </div>
      </div>
      <div class="form-group row mt-4">
        <div class="col-sm offset-sm-3">
          <button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-primary submitBookingProduct">
          {l s='Create order' mod='psbooking'}
          </button>
          <div class="btn-group">
                <button class="btn btn-outline-primary dropdown-toggle" type="submit" id="dropdown-menu-actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {l s='More actions' mod='psbooking'}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdown-menu-actions" style="">
                  <button type="button" class="dropdown-item" id="js-send-process-order-email-btn">
                    {l s='Send pre-filled order to the customer by email' mod='psbooking'}
                  </button>
                  <a class="dropdown-item" id="js-process-order-link" target="_blank" href="">
                    {l s='Proceed to checkout in the front office' mod='psbooking'}
                  </a>


                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
