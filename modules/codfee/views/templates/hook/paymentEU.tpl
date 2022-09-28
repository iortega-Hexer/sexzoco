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
*  @copyright 2019 idnovate
*  @license   See above
*}
<form method="POST" action="{$action|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="codfee_base" value="{$codfee_base|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="codfee_fee" value="{$codfee_fee|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="codfee_total" value="{$codfee_total|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="codfee_included" value="{$codfee_included|escape:'htmlall':'UTF-8'}" />
</form>
<script language="JavaScript">
    $("div#HOOK_ADVANCED_PAYMENT").find("input[name=\"codfee_base\"]").parent().parent().prev().find("img").css("vertical-align", "super");
    $("div#HOOK_ADVANCED_PAYMENT").find("input[name=\"codfee_base\"]").parent().parent().prev().find("span.payment_option_cta").css("max-width", "320px").css("display", "inline-block");
</script>