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
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/

$(document).ready(function() {
    //var id_booking = $('.id_booking_product').val();
    var id_booking = $("#idTable").val();
    var catIds = $('#product_category').val();

    $('#categorycontainer').jstree({
            "checkbox": {
                "keep_selected_style": false, //if true then selection will display highlighted
                "three_state": false, //if true then selection will work according to parent
                "whole_node": true //if true then whole node (checkbox and content will work with click event)
            },
            "plugins": ["checkbox"],
            'core': {
                'data': {
                    "url": admin_booking_product_url,
                    "dataType": "json",
                    "data": function(node) {
                        return {
                            "catsingleId": node.id,
                            "id": id_booking,
                            "catIds": catIds,
                            "token": $('#wk-static-token').val(),
                            "ajax": true,
                            "action": "productCategory"
                        };
                    }
                }
            }
        })
        .on('changed.jstree', function(e, data) { //When category changed then default category options will change.
            $('#default_category').html('');
            var default_category_html = '';
            var i, j;
            for (i = 0, j = data.selected.length; i < j; i++) {

                var selected_category_id = data.instance.get_node(data.selected[i]).id;
                var selected_category_name = data.instance.get_node(data.selected[i]).text;

                default_category_html += '<option value="' + selected_category_id + '"id="default_cat' + selected_category_id + '" name="' + selected_category_name + '" ';

                if (typeof defaultIdCategory !== 'undefined' && defaultIdCategory == selected_category_id) {
                    default_category_html += 'Selected="selected"';
                }

                default_category_html += '>' + selected_category_name + '</option>';
            }

            $('#default_category').html(default_category_html);
        });
});