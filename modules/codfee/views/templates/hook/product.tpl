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
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}<br />{/if}
<div class="{if version_compare($smarty.const._PS_VERSION_, '1.7', '>=')}codfee_show_product17 tabs{else}codfee_show_product{/if}">
    <img src="{$icon|escape:'htmlall':'UTF-8'}" style="float: left;" />
    <span class="{if version_compare($smarty.const._PS_VERSION_, '1.7', '>=')}codfee_show_product_text17{else}codfee_show_product_text{/if}">
        {if $codfee_type == '3'}
            {l s='This product can be bought upon cash on pickup' mod='codfee'}
        {else}
            {l s='This product can be bought with COD' mod='codfee'}
        {/if}
    </span>
</div>
