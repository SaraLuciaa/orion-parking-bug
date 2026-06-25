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

<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
        	<h4 class="modal-title" id="myModalLabel">
				{l s='Image' mod='psbooking'}
			</h4>
		</div>

		<div class="modal-body wk-productlist-images">
			<table id="imageTable" class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>{l s='Image ID' mod='psbooking'}</th>
						<th>{l s='Image' mod='psbooking'}</th>
						<th>{l s='Position' mod='psbooking'}</th>
						<th>{l s='Cover' mod='psbooking'}</th>
						<th>{l s='Action' mod='psbooking'}</th>
					</tr>
				</thead>
				{if isset($image_detail) && $image_detail}
					<tbody>
						{foreach $image_detail as $key => $image}
							<tr class="jFiler-items imageinforow{$image.id_image|escape:'htmlall':'UTF-8'}">
								<td>{$key+1|escape:'htmlall':'UTF-8'}</td>
								<td>{$image.id_image|escape:'htmlall':'UTF-8'}</td>
								<td>
									<a class="wk-img-preview" href="{$image.image_path|escape:'htmlall':'UTF-8'}">
										<img class="img-thumbnail" width="80" height="80" src="{if isset($image.image_link)}{$image.image_link|escape:'htmlall':'UTF-8'}{else}{$link->getImageLink($link_rewrite, $image.product_image, 'cart_default')|escape:'htmlall':'UTF-8'}{/if}"/>
									</a>
								</td>
								<td>{$image.position|escape:'htmlall':'UTF-8'}</td>
								<td>
									{if $image.cover == 1 }
										<img class="covered" id="changecoverimage{$image.id_image|escape:'htmlall':'UTF-8'}" alt="{$image.id_image|escape:'htmlall':'UTF-8'}" src="{$wk_image_dir|escape:'htmlall':'UTF-8'}icon-check.png" is_cover="1" id_pro="{$id_product|escape:'htmlall':'UTF-8'}"/>
									{else}
										<img style="cursor: pointer;" class="covered" id="changecoverimage{$image.id_image|escape:'htmlall':'UTF-8'}" alt="{$image.id_image|escape:'htmlall':'UTF-8'}" src="{$wk_image_dir|escape:'htmlall':'UTF-8'}forbbiden.gif" is_cover="0" id_pro="{$id_product|escape:'htmlall':'UTF-8'}"/>
									{/if}
								</td>
								<td>
									{if $image.cover == 1}
										<a class="delete_pro_image pull-left btn btn-default" href="" is_cover="1" id_pro="{$id_product|escape:'htmlall':'UTF-8'}" id_image="{$image.id_image|escape:'htmlall':'UTF-8'}">
											<i class="icon icon-trash"></i>
										</a>
									{else}
										<a class="delete_pro_image pull-left btn btn-default" href="" is_cover="0" id_pro="{$id_product|escape:'htmlall':'UTF-8'}" id_image="{$image.id_image|escape:'htmlall':'UTF-8'}">
											<i class="icon icon-trash"></i>
										</a>
									{/if}
								</td>
							</tr>
						{/foreach}
					</tbody>
				{else}
					<tbody>
						<tr align="center">
							<td colspan="6">{l s='No image available' mod='psbooking'}</td>
						</tr>
					</tbody>
				{/if}
			</table>
		</div>
	</div>
</div>
