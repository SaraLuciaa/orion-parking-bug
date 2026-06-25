/**
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
*/

 $(window).ready(function() {
    moduleAdminLink = moduleAdminLink.replace(/\amp;/g,'');

    window.vMenu = new Vue({
        el: '#booking-menu',
        data: {
            selectedTabName : currentPage,
        },
        watch: {
            content: {
                handler(content) {
                    $('input[name="WK_SHOW_BOOKING_EVENT_PAGE"]').each((key, value) => {
                            $(value).change(this.onDesignPageChange);
                        }
                    );
                    this.hideAndShowShareOnLoad();
                },
                immediate: true,
            },
        },
        methods: {
            makeActive: function(item){
                this.selectedTabName = item;
                window.history.pushState({} , '', moduleAdminLink+'&page='+item );
            },
            isActive : function(item){
                if (this.selectedTabName == item) {
                    $('.booking_menu').addClass('wk-hide');
                    $('#'+item).removeClass('wk-hide');
                    return true;
                }
            },
            onDesignPageChange(e) {
                this.hideAndShowShareOption(e.currentTarget.value)
            },
            hideAndShowShareOption(v) {
                if (v === '1') {
                    $('.share_option').show();
                } else {
                    $('.share_option').hide();
                }
            },
            hideAndShowShareOnLoad() {
                this.hideAndShowShareOption($('input[name="WK_SHOW_BOOKING_EVENT_PAGE"]:checked').val());
            }
        }
    });
});
