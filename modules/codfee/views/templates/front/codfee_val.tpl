{**
* Cash On Delivery With Fee
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2017 idnovate
*  @license   See above
*}
<script type="text/javascript">
	{literal}
	$(window).load(function() {
		$.ajax({async:false});
		var shipping_cost_text = $('#cart_block_shipping_cost').text();
		if (shipping_cost_text!="")
		{{/literal}{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}{literal}
			$('#cart-prices').prepend('<span>{/literal}{l s='COD fee' mod='codfee'}{literal}</span><span id=cart_block_shipping_cost class=price ajax_cart_shipping_cost>{/literal}{convertPriceWithCurrency price=$fee currency=$currency}{literal}</span><br />');
			setTimeout(function() {
				$('#cart_block_total').html("{/literal}{convertPriceWithCurrency price=$total currency=$currency}{literal}");
			}, 1500);
		{/literal}{else}{literal}
			$('#cart-prices').prepend('<span id=cart_block_shipping_cost class=price ajax_cart_shipping_cost>{/literal}{convertPriceWithCurrency price=$fee currency=$currency}{literal}</span><span>{/literal}{l s='COD fee' mod='codfee'}{literal}</span><br />');
			setTimeout(function() {
				$('#cart_block_total').html("{/literal}{convertPriceWithCurrency price=$total currency=$currency}{literal}");
			}, 1500);
		{/literal}{/if}{literal}
		}
		setTimeout(function() {
			$('#cart_block_total').html("{/literal}{convertPriceWithCurrency price=$total currency=$currency}{literal}");
		}, 2500);
	});

	function loading()
	{
		$('*').css('cursor', 'wait');
		document.getElementById('cart_navigation').style.display = "none";
		document.getElementById('loading').style.display = "block";
	}
	{/literal}
</script>
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
{literal}
<style type="text/css">
	.codfee_val_container table#cart_summary {width: 100%;}
</style>
{/literal}
{/if}
{if $fee_type == '3'}
    {capture name=path}{l s='Cash on pickup' mod='codfee'}{/capture}
{else}
    {capture name=path}{l s='Cash on delivery with fee' mod='codfee'}{/capture}
{/if}

{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{if version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}
{/if}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your cart is empty.' mod='codfee'}</p>
{else}
	<!-- <h3>{l s='Cash on delivery with fee payment' mod='codfee'}</h3> -->
	<form action="{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}{$this_path_ssl|escape:'htmlall':'UTF-8'}payment.php{else}{$validation_ctrl|escape:'htmlall':'UTF-8'}{/if}" method="post">
		<div class="codfee_val_container">
			<img src="{$payment_logo|escape:'htmlall':'UTF-8'}" alt="{l s='Cash on delivery with fee payment' mod='codfee'}" style="float:left; margin: 0px 10px 5px 0px;" />
			<br />
            {if $fee_type == '3'}
				<h2>{l s='You have chosen the cash on pickup method.' mod='codfee'}</h2>
            {else}
				<h2>{l s='You have chosen the cash on delivery method.' mod='codfee'}</h2>
            {/if}
			<br /><br />
			<table id="cart_summary" class="table table-bordered footab default footable-loaded footable">
				<thead>
				<tr>
					<th class="cart_product first_item" colspan="7">{l s='Details of your order' mod='codfee'} {$taxes_included|escape:'htmlall':'UTF-8'}</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="cart_total_price">
					<td colspan="6">{l s='Order:' mod='codfee'}</td>
					<td colspan="1" class="price" id="total_product">{$cartwithoutshipping|escape:'quotes':'UTF-8'}</td>
				</tr>
				<tr class="cart_total_delivery">
					<td colspan="6">{l s='Shipping cost:' mod='codfee'}</td>
					<td colspan="1" class="price" id="total_shipping">{$shipping_cost|escape:'quotes':'UTF-8'}</td>
				</tr>
                {if $wrapping_val > 0}
					<tr class="cart_total_wrapping">
						<td colspan="6">{l s='Gift wrapping:' mod='codfee'}</td>
						<td colspan="1" class="price" id="total_wrapping">{$wrapping|escape:'quotes':'UTF-8'}</td>
					</tr>
                {/if}
                {if $discounts_val > 0}
					<tr class="cart_total_discounts">
						<td colspan="6">{l s='Discounts:' mod='codfee'}</td>
						<td colspan="1" class="price" id="total_discounts">{$discounts|escape:'quotes':'UTF-8'}</td>
					</tr>
                {/if}
                {if $fee_type != '3'}
					<tr class="cart_total_tax">
						{if $fee_amount != 0}
							<td colspan="6">{l s='Cash on delivery fee:' mod='codfee'}</td>
							<td colspan="1" class="price" id="total_tax">{$fee|escape:'quotes':'UTF-8'}</td>
						{elseif $free_on_freeshipping == '1'}
							<td colspan="6">{l s='Cash on delivery fee (free on free shipping orders!)' mod='codfee'}</td>
							<td colspan="1" class="price" id="total_tax">{$fee|escape:'quotes':'UTF-8'}</td>
						{else}
							<td colspan="6">{l s='Cash on delivery fee (free from' mod='codfee'} {$free_fee|escape:'quotes':'UTF-8'} {l s='of purchase):' mod='codfee'}</td>
							<td colspan="1" class="price" id="total_tax">{$fee|escape:'quotes':'UTF-8'}</td>
						{/if}
					</tr>
				{/if}
				<tr class="cart_total_price">
					<td colspan="6">
                        {if $fee_amount != 0}
                            {l s='*COD fee will be added to the shipping cost.' mod='codfee'}
                        {/if}
					</td>
					<td colspan="1" class="price total_price_container" id="total_price_container">
						<p>{l s='Total:' mod='codfee'}</p>
						<span id="total_price">{$total|escape:'quotes':'UTF-8'}</span>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
	<strong>{l s='Please confirm your order by clicking \'Confirm my order\'' mod='codfee'}.</strong>
	{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
	<p id="loading" style="text-align:center;display:none;"><br /><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}img/loadingAnimation.gif" width="208" height="13" />
		<p class="cart_navigation" id="cart_navigation">
			<a href="{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}{$base_dir_ssl|escape:'htmlall':'UTF-8'}order.php?step=3{else}{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}{/if}" class="button_large hideOnSubmit">{l s='Other payment methods' mod='codfee'}</a>
			<input type="submit" name="paymentSubmit" value="{l s='Confirm my order' mod='codfee'}" class="exclusive hideOnSubmit" onclick="loading();" />
		</p>
	{else}
		<p class="cart_navigation" id="cart_navigation">
			<button type="submit" class="button btn btn-default button-medium btn-primary">
				<span>{l s='Confirm my order' mod='codfee'}<i class="icon-chevron-right right"></i></span>
			</button>
			<a href="{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}" class="button-exclusive btn btn-default">
				<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='codfee'}
			</a>
		</p>
	{/if}
	</form>
{/if}
