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
    // Image preview popup
    $('.wk-img-preview').on('click', function(e) {
        e.preventDefault();
        $('.mp-image-popup').attr('src', $(this).attr('href'));
        $('#mp_image_preview').modal('show');
    });

    // delete active product image
    $(document).on('click', '.delete_pro_image', function(e) {
        e.preventDefault();
        var id_image = $(this).attr('id_image');
        var is_cover = $(this).attr('is_cover');
        var id_pro = $(this).attr('id_pro');

        if (confirm(confirm_delete_msg)) {
            $.ajax({
                type: 'POST',
                url: imageUploader,
                data: {
                    id_image: id_image,
                    is_cover: is_cover,
                    id_pro: id_pro,
                    is_delete: "1",
                    ajax: true,
                    token: $('input[name="token"]').val(),
                    action: 'deleteProductImage',
                },
                cache: true,
                success: function(data) {
                    if (data == 0) {
                        showErrorMessage(error_msg);
                    } else if (data == 1) {
                        $(".imageinforow" + id_image).fadeOut("normal", function() {
                            $(this).remove();
                            showSuccessMessage(deleteSuccess);
                        });
                    } else if (data == 2) {
                        showSuccessMessage(deleteSuccess);
                        location.reload();
                    }
                }
            });

        }
    });

    // change cover image
    $(document).on('click', '.covered', function(e) {
        e.preventDefault();
        var id_image = $(this).attr('alt');
        var is_cover = $(this).attr('is_cover');
        var id_pro = $(this).attr('id_pro');
        var prod_detail = $(this).attr('prod_detail'); // only if call from marketplace product details page
        if (is_cover == 0) {
            $.ajax({
                type: 'POST',
                url: imageUploader,
                data: {
                    id_image: id_image,
                    is_cover: is_cover,
                    id_pro: id_pro,
                    changecover: "1",
                    ajax: true,
                    token: $('input[name="token"]').val(),
                    action: 'changeImageCover',
                },
                cache: true,
                success: function(data) {
                    if (data == 0) {
                        $.growl.error({title: '', message: error_msg});
                    } else {
                        if (is_cover == 0) {
                            $('.covered').css('cursor','none');
                            $('.covered').attr('src', wk_image_dir + 'forbbiden.gif');
                            $('.covered').attr('is_cover', '0')
                            $('#changecoverimage' + id_image).attr('src', wk_image_dir + 'icon-check.png');
                            $('#changecoverimage' + id_image).attr('is_cover', '1');
                            $("[is_cover=0]").css('cursor','pointer');
                            // to change attribue is cover to 1 in delete image link
                            $('.delete_pro_image').attr('is_cover', '0');
                            $('.delete_pro_image[id_image=' + id_image + ']').attr('is_cover', '1');
                            if (typeof prod_detail != 'undefined') { //reload for show cover image on page
                                location.reload(true);
                            }
                        }
                    }
                }
            });
        }
    });
});