<?php
/**
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
*/

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../header.php');
include_once(dirname(__FILE__).'../../codfee.php');
/* Backward compatibility */
include_once(dirname(__FILE__).'../../backward_compatibility/backward.php');

$cashOnDelivery = new CodFee();
$context = Context::getContext();

$CODfee = $cashOnDelivery->getFeeCost($context->cart);
$cartcost = $context->cart->getOrderTotal(true, 3);
$total = $CODfee + $cartcost;

$context->smarty->assign(array(
    'currency' => new Currency((int)($context->cart->id_currency)),
    'total' => number_format((float)($total), 2, '.', ''),
    'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/codfee/'
));

$context->smarty->assign('this_path', __PS_BASE_URI__.'modules/codfee/');

echo $cashOnDelivery->execPayment($context->cart);

include(dirname(__FILE__).'/../../../footer.php');
