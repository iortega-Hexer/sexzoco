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
{if $success == true}
    <style type="text/css">
        table.std td, table.table_block td table.custom td {
            padding: 5px 9px;
        }
        table.std th, table.table_block table.custom th {
            padding: 5px 9px;
        }
        span.total_pending {
            font-weight: 900;
        }
        td.codfee_total {
            font-weight: 900;
        }
    
    </style>
    {if version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
        <p class="alert alert-success">{l s='Your order is complete.' mod='codfee'}</p>
            <table class="table table-bordered footab default footable-loaded footable custom std">
                <thead>
                    <tr>
                        <th style="width: 55%">{l s='Product' mod='codfee'}</th>
                        <th style="width: 12%;text-align: center;">{l s='Qty' mod='codfee'}</th>
                        <th style="width: 33%">{l s='Price' mod='codfee'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$order_products item=product}
                        <tr>
                            <td>{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                            <td style="text-align: center;">{$product.product_quantity|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                {if $use_taxes}
                                    {displayPrice price=$product.total_price_tax_incl}
                                {else}
                                    {displayPrice price=$product.total_price_tax_excl}
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td style="text-align:right">
                            {l s='Products Total' mod='codfee'}
                        </td>
                        <td colspan="2">
                            {if $use_taxes}
                                {displayPrice price=$order->total_products_wt}
                            {else}
                                {displayPrice price=$order->total_products}
                            {/if}
                             
                        </td>
                    </tr> 
                    <tr>
                        <td></td>
                        <td style="text-align:right">
                            {l s='Shipping' mod='codfee'}
                        </td>
                        <td colspan="2">
                            {if $use_taxes}
                                {displayPrice price=$order->total_shipping_tax_incl} {if $fee_type != '3'}({l s='COD fee included:' mod='codfee'} {displayPrice price=$codfee}){/if}
                            {else}
                                {displayPrice price=$order->total_shipping_tax_excl} {if $fee_type != '3'}({l s='COD fee included:' mod='codfee'} {displayPrice price=$codfee}){/if}
                            {/if}
                        </td>
                    </tr>
                    {if $order->total_wrapping != '0.00'}
                        <tr>
                            <td></td>
                            <td style="text-align:right">
                                {l s='Gift wrapping' mod='codfee'}
                            </td>
                            <td colspan="2">
                                {if $use_taxes}
                                    {displayPrice price=$order->total_wrapping_tax_incl}
                                {else}
                                    {displayPrice price=$order->total_wrapping_tax_excl}
                                {/if}
                            </td>
                        </tr>
                    {/if}
                    {if $order->total_discounts != '0.00'}
                        <tr>
                            <td></td>
                            <td style="text-align:right">
                                {l s='Discounts' mod='codfee'}
                            </td>
                            <td colspan="2">-
                                {if $use_taxes}
                                    {displayPrice price=$order->total_discounts_tax_incl}
                                {else}
                                    {displayPrice price=$order->total_discounts_tax_excl}
                                {/if}
                            </td>
                        </tr>
                    {/if}
                    {if $use_taxes}
                        <tr>
                            <td></td>
                            <td style="text-align:right">
                                {l s='Taxes' mod='codfee'}
                            </td>
                            <td colspan="2">
                                {$taxamt = $order->total_paid_tax_incl - $order->total_paid_tax_excl}
                                {displayPrice price=$taxamt}
             
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td></td>
                        <td style="text-align:right">
                            {l s='TOTAL' mod='codfee'}
                        </td>
                        <td colspan="2">
                            {if $use_taxes}
                                {displayPrice price=$order->total_paid_tax_incl}
                            {else}
                                {displayPrice price=$order->total_paid_tax_excl}
                            {/if}
                        </td>
                    </tr>
                </tfoot>
            </table>
        <p>
    {/if}
        {if $fee_type == '3'}
            {l s='Payment is due upon cash on pickup.' mod='codfee'}
        {else}
            {l s='Payment is due upon receipt of order.' mod='codfee'}
        {/if}
        <br /><br /><span class="total_pending">{l s='Total payment pending:' mod='codfee'}</span> <span class="price total_pending">{convertPriceWithCurrency price=$total currency=$currency}</span>
        <br /><br />{l s='For any questions or for further information, please contact us.' mod='codfee'}
    </p>
    <p>
        {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}" class="button_large">{l s='Continue shopping' mod='codfee'}</a>
        {/if}
    </p>
{else}
    <p class="warning">
        {l s='We noticed a problem with your order. If you think this is an error, you can contact us.' mod='codfee'}
    </p>
{/if}
