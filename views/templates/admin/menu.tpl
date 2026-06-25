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
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<style>
.wk-hide {
    display: none;
}
</style>
<div id="modulecontent" class="clearfix">
    <div id="booking-menu">
        <div class="col-lg-2">
            <div class="list-group" v-on:click.prevent>
                <a href="#" class="list-group-item" v-bind:class="{ 'active': isActive('generalSetting') }" v-on:click="makeActive('generalSetting')"><i class="icon-cogs"></i> {l s='General' mod='psbooking'}</a>

                <a href="#" class="list-group-item" v-bind:class="{ 'active': isActive('eventSetting') }" v-on:click="makeActive('eventSetting')"><i class="icon-file"></i> {l s='Event page' mod='psbooking'}</a>

                <a href="#" class="list-group-item" v-bind:class="{ 'active': isActive('mapSetting') }" v-on:click="makeActive('mapSetting')"><i class="icon-map-marker"></i> {l s='Google map' mod='psbooking'}</a>

            </div>
            <div class="list-group">
                 <a class="list-group-item"><i class="icon-puzzle-piece"></i> {l s='Module V' mod='psbooking'} {$module_version|escape:'htmlall':'UTF-8'}</a>
            </div>
        </div>
    </div>

    <div id="generalSetting" class="booking_menu wk-hide">
        {include file="./tabs/generalSetting.tpl"}
    </div>
    <div id="eventSetting" class="booking_menu wk-hide">
        {include file="./tabs/eventSetting.tpl"}
    </div>
    <div id="mapSetting" class="booking_menu wk-hide">
        {include file="./tabs/mapSetting.tpl"}
    </div>
</div>
