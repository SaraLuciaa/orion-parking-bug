{if !$jquery_loaded}
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
{/if}

<div class="row wk_search_row">
    <a name="buscador-home"></a>
    <div id="wrapper_header" class="col-md-12">
        <div class="wk-availability-heading wk-text-center">{l s='Availability' mod='psbooking'}</div>
        <div id="wrapper_header_right" class="row">
            <form method="post" action="{$link->getModuleLink('psbooking', 'bookingproduct')|escape:'htmlall':'UTF-8'}" style="width:100%;">
                <!--<div class="col-md-2 form-group">
                    <label>{l s='Type' mod='psbooking'}</label>
                    <div>
                        <select class="form-control" name="type">
                            <option value="0">{l s='All' mod='psbooking'}</option>
                            {foreach from=$booking_types item=booking_type key=key}
                                <option value="{$key|escape:'htmlall':'UTF-8'}" {if $type == $key}selected{/if}>{$booking_type|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>-->
                
                <div class="col-md-4 form-group">
    <label>{l s='Category' mod='psbooking'}</label>
    <div>
        <select class="form-control" name="category" id="id_category">
            {foreach from=$categories item=category}
                {if $category['id_category'] == 10}
                    <option value="{$category['id_category']|escape:'htmlall':'UTF-8'}"
                        {if $category['id_category'] == $selected_category}selected{/if}>
                        {$category['name']|escape:'htmlall':'UTF-8'}
                    </option>
                {/if}
            {/foreach}
        </select>
    </div>
</div>


                <div class="col-md-4 form-group">
                    <label>{l s='From' mod='psbooking'}</label>
                    <div>
                        <input id="search_date_from" type="text" class="form-control datepicker-input" autocomplete="off" placeholder="{l s='From' mod='psbooking'}" name="date_from" readonly value="{if isset($date_from)}{$date_from|escape:'htmlall':'UTF-8'}{/if}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>{l s='To' mod='psbooking'}</label>
                    <div>
                        <input id="search_date_to" type="text" class="form-control datepicker-input" autocomplete="off" placeholder="{l s='To' mod='psbooking'}" name="date_to" readonly value="{if isset($date_to)}{$date_to|escape:'htmlall':'UTF-8'}{/if}">
                    </div>
                </div>    
                <!--<div class="col-md-2 form-group">
  <label>{l s='Time From' mod='psbooking'}</label>
  <div>
    <input type="time" name="time_from" class="form-control" value="{$time_from|escape:'htmlall':'UTF-8'}">


  </div>
</div>-->

<!--<div class="col-md-2 form-group">
  <label>{l s='Time To' mod='psbooking'}</label>
  <div>
    <input type="time" name="time_to" class="form-control" value="{$time_to|escape:'htmlall':'UTF-8'}">
  </div>
</div>-->

               <!-- <div class="col-md-2 form-group">
                    <label>{l s='Quantity' mod='psbooking'}</label>
                    <div>
                        <input type="text" class="form-control" placeholder="{l s='Quantity' mod='psbooking'}" name="quantity" value="{if isset($quantity)}{$quantity|escape:'htmlall':'UTF-8'}{/if}">
                    </div>
                </div>-->
                <div class="col-md-12 wk_search_div">
                    <button type="submit" class="btn btn-primary wk_btn_extra" id="wk_store_search">
                        <span>{l s='Search' mod='psbooking'}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{if isset($total_items) && !empty($total_items)}
    <div class="row wk_search_row">
        <div id="js-product-list-top" class="row products-selection">
            <div class="col-md-6 hidden-sm-down total-products">
                {if $total_items > 1}
                    <p>{l s='There are %product_count% products.' mod='psbooking' sprintf=['%product_count%' => $total_items]}</p>
                {elseif $total_items > 0}
                    <p>{l s='There is 1 product.' mod='psbooking'}</p>
                {/if}
            </div>
            <div class="col-md-6">
                <div class="row sort-by-row">
                    <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by' mod='psbooking'}:</span>
                    <div class="col-sm-12 col-xs-12 col-md-9 products-sort-order dropdown">
                        <button class="btn-unstyle select-title" rel="nofollow" data-toggle="dropdown" aria-label="Sort by selection" aria-haspopup="true" aria-expanded="false">
                            {if $order_selected == 1}
                                {l s='Name, A to Z' mod='psbooking'}
                            {elseif $order_selected == 2}
                                {l s='Name, Z to A' mod='psbooking'}
                            {elseif $order_selected == 3}
                                {l s='Price, low to high' mod='psbooking'}
                            {elseif $order_selected == 4}
                                {l s='Price, high to low' mod='psbooking'}
                            {else}
                                {l s='Select' mod='psbooking'}
                            {/if}
                            <i class="material-icons float-xs-right"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', ['orderby' => 'name', 'orderway' => 'asc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to])|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Name, A to Z' mod='psbooking'}
                            </a>
                            <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', ['orderby' => 'name', 'orderway' => 'desc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to])|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Name, Z to A' mod='psbooking'}
                            </a>
                            <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', ['orderby' => 'price', 'orderway' => 'asc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to])|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Price, low to high' mod='psbooking'}
                            </a>
                            <a rel="nofollow" href="{$link->getModuleLink('psbooking', 'bookingproduct', ['orderby' => 'price', 'orderway' => 'desc', 'type' => $type, 'date_from' => $date_from, 'date_to' => $date_to])|escape:'htmlall':'UTF-8'}" class="select-list">
                                {l s='Price, high to low' mod='psbooking'}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="featured-products clearfix">
        <div class="products">
            {if count($products) > 0}
                {foreach from=$products item="product"}
                    {include file="catalog/_partials/miniatures/product.tpl" product=$product productClasses="col-xs-6 col-lg-4 col-xl-3"}
                {/foreach}
                {block name="pagination_dustomer"}
                    {include file="module:psbooking/views/templates/front/_partials/pagination.tpl"}
                {/block}
            {/if}
        </div>
    </section>
{else}
    <div class="col-md-12 wk-text-center">
        <h2>{l s='No booking product found' mod='psbooking'}</h2>
    </div>
{/if}


