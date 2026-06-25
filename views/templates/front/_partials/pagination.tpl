{*
**
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
*
*}
<nav class="pagination">
	<div class="col-md-4" style="padding-left: 20px;">
		{if isset($no_follow) AND $no_follow}
			{assign var='no_follow_text' value='rel="nofollow"'}
		{else}
			{assign var='no_follow_text' value=''}
		{/if}
		{if isset($p) AND $p}
			{if isset($smarty.get.id_category) && $smarty.get.id_category && isset($category)}
				{if !isset($current_url)}
					{assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
				{else}
					{assign var='requestPage' value=$current_url}
				{/if}
				{assign var='requestNb' value=$link->getPaginationLink('category', $category, true, false, false, true)}
			{else}
				{if !isset($current_url)}
					{assign var='requestPage' value=$link->getPaginationLink(false, false, false, false, true, false)}
				{else}
					{assign var='requestPage' value=$current_url}
				{/if}
				{assign var='requestNb' value=$link->getPaginationLink(false, false, true, false, false, true)}
			{/if}
		{/if}
		<div class="product-count">
			{if ($n*$p) < $nb_products }
				{assign var='productShowing' value=$n*$p}
			{else}
				{assign var='productShowing' value=($n*$p-$nb_products-$n*$p)*-1}
			{/if}
			{if $p==1}
				{assign var='productShowingStart' value=1}
			{else}
				{assign var='productShowingStart' value=$n*$p-$n+1}
			{/if}
			{if $nb_products > 1}
				{l s='Showing %1$d - %2$d of %3$d items' sprintf=[$productShowingStart, $productShowing, $nb_products] mod='psbooking'}
			{else}
				{l s='Showing %1$d - %2$d of 1 item' sprintf=[$productShowingStart, $productShowing] mod='psbooking'}
			{/if}
		</div>

	</div>
	<div class="col-md-6 offset-md-2 pr-0">
		{if $start!=$stop}
			<ul class="page-list clearfix" style="float: right;">
				{if $p != 1}
					{assign var='p_previous' value=$p-1}
					<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}">
						<a {$no_follow_text|escape:'htmlall':'UTF-8'}
							href="{$link->goPage($requestPage, $p_previous)|escape:'htmlall':'UTF-8'|replace:'amp;':''}" rel="prev">
							<i class="material-icons">&#xE314;</i> <b>{l s='Previous ' mod='psbooking'}</b>
						</a>
					</li>
				{else}
					<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}"
						class="disabled pagination_previous">
						<span>
							<i class="material-icons">&#xE314;</i> <b>{l s='Previous ' mod='psbooking'}</b>
						</span>
					</li>
				{/if}
				{if $start==3}
					<li>
						<a {$no_follow_text|escape:'htmlall':'UTF-8'} href="{$link->goPage($requestPage, 1)|replace:'amp;':''}">
							<span>1</span>
						</a>
					</li>
					<li>
						<a {$no_follow_text|escape:'htmlall':'UTF-8'} href="{$link->goPage($requestPage, 2)|replace:'amp;':''}">
							<span>2</span>
						</a>
					</li>
				{/if}
				{if $start==2}
					<li>
						<a {$no_follow_text|escape:'htmlall':'UTF-8'} href="{$link->goPage($requestPage, 1)|replace:'amp;':''}">
							<span>1</span>
						</a>
					</li>
				{/if}
				{if $start>3}
					<li>
						<a {$no_follow_text|escape:'htmlall':'UTF-8'} href="{$link->goPage($requestPage, 1)|replace:'amp;':''}">
							<span>1</span>
						</a>
					</li>
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
				{/if}
				{section name=pagination start=$start loop=$stop+1 step=1}
					{if $p == $smarty.section.pagination.index}
						<li class="active current">
							<span>
								<span>{$p|escape:'htmlall':'UTF-8'}</span>
							</span>
						</li>
					{else}
						<li>
							<a {$no_follow_text|escape:'htmlall':'UTF-8'}
								href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'htmlall':'UTF-8'|replace:'amp;':''}">
								<span>{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</span>
							</a>
						</li>
					{/if}
				{/section}
				{if $pages_nb>$stop+2}
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|escape:'htmlall':'UTF-8'|replace:'amp;':''}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+1}
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|escape:'htmlall':'UTF-8'|replace:'amp;':''}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+2}
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb-1)|escape:'htmlall':'UTF-8'|replace:'amp;':''}">
							<span>{$pages_nb-1|intval}</span>
						</a>
					</li>
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|escape:'htmlall':'UTF-8'|replace:'amp;':''}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb > 1 AND $p != $pages_nb}
					{assign var='p_next' value=$p+1}
					<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}">
						<a {$no_follow_text|escape:'htmlall':'UTF-8'}
							href="{$link->goPage($requestPage, $p_next)|escape:'htmlall':'UTF-8'|replace:'amp;':''}" rel="next">
							<b>{l s=' Next' mod='psbooking'}</b> <i class="material-icons">&#xE315;</i></i>
						</a>
					</li>
				{else}
					<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'htmlall':'UTF-8'}{/if}"
						class="disabled pagination_next">
						<span>
							<b>{l s=' Next' mod='psbooking'}</b> <i class="material-icons">&#xE315;</i></i>
						</span>
					</li>
				{/if}
			</ul>
		{/if}
	</div>

</nav>