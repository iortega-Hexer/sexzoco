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
*  @version   2.0.0
*  @copyright 2016 idnovate
*  @license   See above
*}
<script type="text/javascript">
	{literal}
	$(window).load(function() {
		$.ajax({async:false});
		var shipping_cost_text = $('#cart_block_shipping_cost').text();
		if (shipping_cost_text!="")
		{{/literal}{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}{literal}
			$('#cart-prices').prepend('<span>{/literal}{l s='COD fee' mod='codfee'}{literal}</span><span id=cart_block_shipping_cost class=price ajax_cart_shipping_cost>{/literal}{convertPriceWithCurrency price=$fee currency=$currency}{literal}</span>');
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
		document.getElementById('loading').style.display = "block";
	}
	{/literal}
</script>

{capture name=path}{l s='Cash on delivery with fee' mod='codfee'}{/capture}

{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<h2>{l s='Order summary' mod='codfee'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your cart is empty.' mod='codfee'}</p>
{else}
	{assign var='carrier_selected' value='0'}
	<h3>{l s='Cash on delivery with fee payment' mod='codfee'}</h3>
	<form action="{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}{$this_path_ssl|escape:'htmlall':'UTF-8'}controllers/payment.php{else}{$link->getModuleLink('codfee', 'validation', [], true)|escape:'htmlall':'UTF-8'}{/if}" method="post">
	<p>
		<img src="{$payment_logo|escape:'htmlall':'UTF-8'}" alt="{l s='Cash on delivery with fee payment' mod='codfee'}" style="float:left; margin: 0px 10px 5px 0px;" />
		<br />
		{l s='You have chosen the cash on delivery method.' mod='codfee'}
		<br /><br /><br /><br />
		<table id="cart_summary" class="std">
			<thead>
				<tr>
					<th class="cart_product first_item" colspan="7">{l s='Details of your order (taxes included)' mod='codfee'}</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="cart_total_price">
					<td colspan="6">{l s='Order:' mod='codfee'}</td>
					<td colspan="1" class="price" id="total_product">{convertPriceWithCurrency price=$cartwithoutshipping currency=$currency}</td>
				</tr>
				<tr class="cart_total_delivery">
					<td colspan="6">{l s='Shipping cost:' mod='codfee'}</td>
					<td colspan="1" class="price" id="total_shipping">{convertPriceWithCurrency price=$shippingcost currency=$currency}</td>
				</tr>
				<tr class="cart_total_tax">
					{if $fee_amount != 0}
						<td colspan="6">{l s='Cash on delivery fee:' mod='codfee'}</td>
						<td colspan="1" class="price" id="total_tax">{convertPriceWithCurrency price=$fee currency=$currency}</td>
					{else}
						<td colspan="6">{l s='Cash on delivery fee (free from' mod='codfee'} {convertPriceWithCurrency price=$free_fee currency=$currency} {l s='of purchase):' mod='codfee'}</td>
						<td colspan="1" class="price" id="total_tax">{convertPriceWithCurrency price=$fee currency=$currency}</td>
					{/if}
				</tr>
				<tr class="cart_total_price">
					<td colspan="6" id="cart_voucher" class="cart_voucher">
						{if $fee_amount != 0}
							{l s='*COD fee will be added to the shipping cost.' mod='codfee'}
						{/if}
					</td>
					<td colspan="1" class="price total_price_container" id="total_price_container">
						<p>{l s='Total:' mod='codfee'}</p>
						<span id="total_price">{convertPriceWithCurrency price=$total currency=$currency}</span>
					</td>
				</tr>
			</tfoot>
		</table>
	</p>
	<b>{l s='Please confirm your order by clicking \'Confirm my order\'' mod='codfee'}.</b>
	<p id="loading" style="text-align:center;display:none;"><br /><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}img/loadingAnimation.gif" width="208" height="13" />
	{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
		<p class="cart_navigation">
			<a href="{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}{$base_dir_ssl|escape:'htmlall':'UTF-8'}order.php?step=3{else}{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}{/if}" class="button_large hideOnSubmit">{l s='Other payment methods' mod='codfee'}</a>
			<input type="submit" name="paymentSubmit" value="{l s='Confirm my order' mod='codfee'}" class="exclusive hideOnSubmit" onclick="loading();" />
		</p>
	{else}
		<p class="cart_navigation" id="cart_navigation">
			<button type="submit" class="button btn btn-default button-medium">
				<span>{l s='Confirm my order' mod='codfee'}<i class="icon-chevron-right right"></i></span>
			</button>
			<a href="{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}" class="button-exclusive btn btn-default">
				<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='codfee'}
			</a>
		</p>
	{/if}
	</form>
{/if}
