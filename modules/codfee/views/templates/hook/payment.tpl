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
*  @copyright 2021 idnovate
*  @license   See above
*}
<style type="text/css">
    p.payment_module a.cash {
        /*padding: 30px 30px 30px 79px;*/
        padding: 5px 0 5px 12px;
        /*background: url('{$payment_logo|escape:"htmlall":"UTF-8"}') left center no-repeat #fbfbfb;*/
        /*background-color: transparent;*/
        background-image: none!important;
        text-decoration: none;
        {if $payment_text neq ''}padding-bottom: 10px;{/if}
    }
    p.payment_module a.codfee_text:before {
        content: none;
    }
    p.payment_module a:hover {
        background-color: #f6f6f6;
    }
</style>
{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
<div class="row">
    <div {if isset($payment_size) && version_compare($smarty.const._PS_VERSION_,'1.5','<')}style="width:{$payment_size|escape:'htmlall':'UTF-8'}"{elseif isset($payment_size)}class="col-xs-12 {$payment_size|escape:'htmlall':'UTF-8'}" style="display: inline-block;"{/if} >
        <p class="payment_module">
            <a id="{$codfee_id|escape:'htmlall':'UTF-8'}" href="{if $show_conf_page}{$payment_ctrl|escape:'htmlall':'UTF-8'}{else}{$validation_ctrl|escape:'htmlall':'UTF-8'}{/if}" title="{$label_text|escape:'htmlall':'UTF-8'}" class="cash codfee_text {$codfeeconf_class|escape:'htmlall':'UTF-8'}">
                <img src="{$payment_logo|escape:'htmlall':'UTF-8'}" alt="{$label_text|escape:'htmlall':'UTF-8'}" />
                {$cta_text nofilter}
            </a>
        </p>
    </div>
</div>
{elseif version_compare($smarty.const._PS_VERSION_, '1.5', '>=')}
    <p class="payment_module">
        <a href="{if $show_conf_page}{$payment_ctrl|escape:'htmlall':'UTF-8'}{else}{$validation_ctrl|escape:'htmlall':'UTF-8'}{/if}" title="{$label_text|escape:'htmlall':'UTF-8'}" class="codfee_text {$codfeeconf_class|escape:'htmlall':'UTF-8'}">
            <img src="{$payment_logo|escape:'htmlall':'UTF-8'}" alt="{$label_text|escape:'htmlall':'UTF-8'}" />
            <span>{$cta_text|escape:'htmlall':'UTF-8'}</span>
        </a>
    </p>
{else}
    {assign var='carrier_ok' value=false}
    {foreach $carriers_array as $carrier_array}
        {if $carrier_selected == $carrier_array}
            {$carrier_ok = true}
            {break}
        {/if}
    {/foreach}
    {if $carrier_ok == true || !isset($carriers_array)}
        {if ($maximum_amount > 0 && $cartcost < $maximum_amount) || ($maximum_amount == 0)}
            {if $cartcost > $minimum_amount}
                <p class="payment_module">
                    <a href="{$this_path_ssl|escape:'htmlall':'UTF-8'}controllers/payment.php{if !$show_conf_page}?paymentSubmit{/if}" title="{l s='Cash on delivery with fee' mod='codfee'}">
                        <img src="{$payment_logo|escape:'htmlall':'UTF-8'}" alt="{l s='Cash on delivery with fee' mod='codfee'}" />
                        {l s='Cash on delivery:' mod='codfee'} {convertPriceWithCurrency price=$cartcost currency=$currency}
                        + {convertPriceWithCurrency price=$fee currency=$currency} {l s='(COD fee)' mod='codfee'}
                        = {convertPriceWithCurrency price=$total currency=$currency}
                    </a>
                </p>
            {/if}
        {/if}
    {/if}
{/if}
<script language="JavaScript">
    var payment_text = '{$payment_text|escape:"quotes":"UTF-8"}';
    var codfee_text = $('a.{$codfeeconf_class|escape:'htmlall':'UTF-8'} span').html();
    $('a.{$codfeeconf_class|escape:'htmlall':'UTF-8'} span').html(codfee_text + payment_text);
</script>