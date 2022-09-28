{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block manufacturers module -->
<div id="manufacturers_block_home" class="block fmeblockmanufacturer">
	<h4 class="title_block">{if $display_link_manufacturer}<a href="{$link->getPageLink('manufacturer')}" title="{l s='Manufacturers' mod='fmeblockmanufacturer'}">{/if}{l s='Manufacturers' mod='fmeblockmanufacturer'}{if $display_link_manufacturer}</a>{/if}</h4>
	<div class="block_content">
{if $manufacturers}
    
    <ul id="mycarousel" class="jcarousel-skin-tango">
    	{foreach from=$manufacturers item=manufacturer name=manufacturer_list}
    	<li><a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" title="{$manufacturer.name}"><img src="{$img_manu_dir}{$manufacturer.id_manufacturer}-medium_default.jpg" /></a></li>
        {/foreach}
    </ul>
    
{else}
	<p>{l s='No manufacturer' mod='fmeblockmanufacturer'}</p>
{/if}
	</div>
{literal}
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery('#mycarousel').jcarousel(
{
	visible:5
	}
);
});
</script>
{/literal}
</div>
<!-- /Block manufacturers module -->
