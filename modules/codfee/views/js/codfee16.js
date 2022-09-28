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
*  @version   2.1.2
*  @copyright 2016 idnovate
*  @license   See above
*/

$(document).ready(function() {
	if(typeof orderProcess !== 'undefined'){
		var key = $('input[class="delivery_option_radio"]:checked').data('key');
		var id_address = parseInt($('input[class="delivery_option_radio"]:checked').data('id_address'));
		if (orderProcess == 'order' && key && id_address)
			updateExtraCarrier(key, id_address);
		else if(orderProcess == 'order-opc' && typeof updateCarrierSelectionAndGift !== 'undefined' && typeof key !== 'undefined')
			updateCarrierSelectionAndGift();
	};
});
