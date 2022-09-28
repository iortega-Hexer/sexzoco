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
*  @copyright 2020 idnovate
*  @license   See above
*}
<script type="text/javascript">
    var selectAll = "{l s='Select all' mod='codfee' js=1}";
    var deselectAll = "{l s='Deselect all' mod='codfee' js=1}";
    var selected = "{l s='selected' mod='codfee' js=1}";
    var itemsSelected_nil = "{l s='no selected (rule applied to all)' mod='codfee' js=1}";
    var itemsSelected = "{l s='selected (rule applied to selection)' mod='codfee' js=1}";
    var itemsSelected_plural = "{l s=' selected (rule applied to selection)' mod='codfee' js=1}";
    var itemsAvailable_nil = "{l s='No items available' mod='codfee' js=1}";
    var itemsAvailable = "{l s=' available' mod='codfee' js=1}";
    var itemsAvailable_plural = "{l s=' available' mod='codfee' js=1}";
    var itemsFiltered_nil = "{l s='No options found' mod='codfee' js=1}";
    var itemsFiltered = "{l s='option found' mod='codfee' js=1}";
    var itemsFiltered_plural = "{l s='options found' mod='codfee' js=1}";
    var searchOptions = "{l s='Search Options' mod='codfee' js=1}";
    var collapseGroup = "{l s='Collapse Group' mod='codfee' js=1}";
    var expandGroup = "{l s='Expand Group' mod='codfee' js=1}";
    var searchAllGroup = "{l s='Select All Group' mod='codfee' js=1}";
    var deselectAllGroup = "{l s='Deselect All Group' mod='codfee' js=1}";
    {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    var AdminCodfeeAjaxController = "{$AdminCodfeeAjaxController|escape:'quotes':'UTF-8'}";
    {/if}
</script>