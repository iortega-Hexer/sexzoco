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
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2014 idnovate.com
*  @license   See above
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/codfee.php');

$cashOnDelivery = new CodFee();

$CODfee = $cashOnDelivery->getFeeCost($cart);
$cartcost = $cart->getOrderTotal(true, 3);
$total = $CODfee + $cartcost;

$smarty->assign(array(
	'currency' => new Currency(intval($cart->id_currency)),
	'total' => number_format(floatval( $total ), 2, '.', ''),
	'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/codfee/'
));

$smarty->assign('this_path', __PS_BASE_URI__.'modules/codfee/');

echo $cashOnDelivery->execPayment($cart);

include(dirname(__FILE__).'/../../footer.php');
?>