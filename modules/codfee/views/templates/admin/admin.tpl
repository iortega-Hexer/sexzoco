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
<style type="text/css">
	.nobootstrap {
		min-width: 0 !important;
		padding: 100px 30px 0 !important;
	}
	.nobootstrap .margin-form {
		font-size: 0.9em !important;
	}

	.company {
		border: 1px solid black;
		background-color: #2A2A2A;
		color: white;
		overflow: hidden;
		padding: 20px;
		margin: 15px 0;
	}
	.company a{
		color: white;
		font-weight: bold;
	}
	.company ul {
		margin: 6px 0 12px;
		padding-left: 40px;
		list-style-type: disc;
	}
	.company ul li {
		color: #FFF;
	}
	.company .logo {
		padding-bottom: 10px;
	}
	.instructions {
		margin-bottom: 20px;
	}
</style>

{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}
<h2>{$displayName|escape:'htmlall':'UTF-8'} - {l s='Configuration' mod='codfee'}</h2>
{/if}

<div class="company">
	<div class="logo">
		<img src="../modules/codfee/views/img/logo_idnovate.png" title="idnovate.com" alt="idnovate.com" />
	</div>
	<div class="content">
		{l s='We offer you free assistance to install and set up the module. If you have any problem you can contact us at' mod='codfee'} <a href="http://addons.prestashop.com/contact-community.php?id_product=6337" target="_blank" title="{l s='Contact' mod='codfee'}">http://addons.prestashop.com/contact-community.php?id_product=6337</a>
	</div>
</div>

{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}
	<div class="instructions">
		<img src="{$cf_path|escape:'htmlall':'UTF-8'}views/img/codfee.gif" style="float:left; margin-right:15px;" />
		<strong>{l s='This module allows you to accept payments by cash on delivery and charge a extra fee' mod='codfee'}</strong><br />
		{l s='There are 3 methods to apply the fee: Fixed, percentage and fixed + percentage amount.' mod='codfee'}<br />
		{l s='You can define a max/min order amount to enable/disable this payment option.' mod='codfee'}<br />
		{l s='You can select wich delivery options accepts cash on delivery to enable this payment option.' mod='codfee'}<br />
	</div>
	{$html|escape:'quotes':'UTF-8'}
{/if}